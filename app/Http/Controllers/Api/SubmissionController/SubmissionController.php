<?php
namespace App\Http\Controllers\Api\SubmissionController;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSubmissionRequest\StoreSubmissionRequest;
use App\Models\Organization;
use App\Models\Submission;
use App\Models\Submission_Answer;
use App\Models\Attachment;
use App\Models\Audit_Log;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class SubmissionController extends Controller
{
    /**
     * Store a new submission (or save as draft).
     */
    public function store(StoreSubmissionRequest $req): JsonResponse
    {
        $data = $req->validated();

        DB::beginTransaction();
        try {
            // create or update organization by email if exists
            $orgData = $data['organization'];
            $organization = null;
            if (!empty($orgData['email'])) {
                $organization = Organization::firstOrCreate(
                    ['email' => $orgData['email']],
                    $orgData
                );
            } else {
                $organization = Organization::create($orgData);
            }

            $submission = Submission::create([
                'organization_id' => $organization->id,
                'status' => ($data['save_as_draft'] ?? false) ? 'draft' : 'submitted',
                'submitted_at' => ($data['save_as_draft'] ?? false) ? null : now(),
                'meta' => $data['meta'] ?? null,
            ]);

            // handle answers
            $totalCombinedPoints = 0;
            foreach ($data['answers'] as $axisAnswer) {
                // calculate axis points
                $q1 = isset($axisAnswer['q1']) ? (bool)$axisAnswer['q1'] : false;
                $q2 = isset($axisAnswer['q2']) ? (bool)$axisAnswer['q2'] : false;
                $q3 = isset($axisAnswer['q3']) ? (bool)$axisAnswer['q3'] : false;
                $q4 = isset($axisAnswer['q4']) ? (bool)$axisAnswer['q4'] : false;

                $axisPoints = ($q1?5:0) + ($q2?5:0) + ($q3?5:0) + ($q4?5:0); // 0..20

                Submission_Answer::create([
                    'submission_id' => $submission->id,
                    'axis' => $axisAnswer['axis'],
                    'q1' => $q1,
                    'q2' => $q2,
                    'q3' => $q3,
                    'q4' => $q4,
                    'axis_points' => $axisPoints,
                    'notes' => $axisAnswer['notes'] ?? null,
                ]);

                $totalCombinedPoints += $axisPoints;
            }

            // compute weighted total only if full submission (not draft)
            if (!($data['save_as_draft'] ?? false)) {
                // Each axis is 25 points weight. Each axis points out of 20.
                $totalScore = 0;
                // totalCombinedPoints is sum of 4 axes, 0..80. We compute per-axis contribution.
                // Simpler: compute per axis as (axis_points/20)*25 and sum.
                $answers = $submission->answers()->get();
                foreach ($answers as $ans) {
                    $totalScore += ($ans->axis_points / 20) * 25;
                }
                $submission->total_score = round($totalScore, 2);
                $submission->save();
            }

            // attachments handling
            if ($req->hasFile('attachments')) {
                $files = $req->file('attachments');
                $axes = $req->input('attachments_axes', []);
                foreach ($files as $idx => $file) {
                    if (!$file->isValid()) continue;
                    $original = $file->getClientOriginalName();
                    $path = $file->storeAs('submissions/'. $submission->id, Str::random(12) . '_' . $original, 'public');
                    $attachment = Attachment::create([
                        'submission_id' => $submission->id,
                        'axis' => $axes[$idx] ?? null,
                        'original_name' => $original,
                        'path' => $path,
                        'mime_type' => $file->getClientMimeType(),
                        'size' => $file->getSize(),
                        'uploaded_by' => $req->user()?->id ?? null,
                    ]);
                }
            }

            Audit_Log::create([
                'entity_type' => Submission::class,
                'entity_id' => $submission->id,
                'action' => ($data['save_as_draft'] ?? false) ? 'created_draft' : 'submitted',
                'user_id' => $req->user()?->id ?? null,
                'notes' => 'Submission saved via API',
            ]);

            DB::commit();

            return response()->json([ 'success' => true, 'submission_id' => $submission->id, 'status' => $submission->status, 'total_score' => $submission->total_score ], Response::HTTP_CREATED);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Submission create failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([ 'success' => false, 'message' => 'Failed to create submission', 'error' => $e->getMessage() ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Show a submission with related data. Admins only (middleware assumed).
     */
    public function show(Request $req, $id): JsonResponse
    {
        try {
            $submission = Submission::with(['organization','answers','attachments','reviews'])->findOrFail($id);
            return response()->json([ 'success' => true, 'data' => $submission ], Response::HTTP_OK);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([ 'success' => false, 'message' => 'Submission not found' ], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            Log::error('Submission show failed: '.$e->getMessage());
            return response()->json([ 'success' => false, 'message' => 'Failed to fetch submission' ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * List submissions with filters and pagination for admin.
     */
    public function index(Request $req): JsonResponse
    {
        $query = Submission::with('organization');

        if ($req->has('status')) $query->where('status', $req->query('status'));
        if ($req->has('min_score')) $query->where('total_score', '>=', (float)$req->query('min_score'));
        if ($req->has('max_score')) $query->where('total_score', '<=', (float)$req->query('max_score'));
        if ($req->has('sector')) $query->whereHas('organization', function($q) use ($req){ $q->where('sector', $req->query('sector')); });

        $perPage = max(10, min(100, (int)$req->query('per_page', 20)));

        $res = $query->orderBy('created_at','desc')->paginate($perPage);
        return response()->json($res, Response::HTTP_OK);
    }

    /**
     * Admin: set status and optionally announce winners
     */
    public function updateStatus(Request $req, $id): JsonResponse
    {
        $this->validate($req, [ 'status' => 'required|in:draft,submitted,under_review,shortlisted,winner,rejected', 'announced_at' => 'nullable|date' ]);
        try {
            $submission = Submission::findOrFail($id);
            $old = $submission->status;
            $submission->status = $req->input('status');
            if ($req->filled('announced_at')) $submission->announced_at = $req->input('announced_at');
            $submission->save();

            Audit_Log::create(['entity_type' => Submission::class, 'entity_id' => $submission->id, 'action' => 'status_changed', 'user_id' => $req->user()?->id ?? null, 'notes' => "from {$old} to {$submission->status}"]);

            return response()->json(['success'=>true,'submission_id'=>$submission->id,'status'=>$submission->status], Response::HTTP_OK);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['success'=>false,'message'=>'Submission not found'], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            Log::error('Update status failed: '.$e->getMessage());
            return response()->json(['success'=>false,'message'=>'Failed to update status'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

