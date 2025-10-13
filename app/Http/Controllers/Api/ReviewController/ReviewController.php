<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use App\Models\Review;
use App\Models\Audit_Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ReviewController extends Controller
{
    /** Store a review from a judge and recalc submission score (if desired) */
    public function store(Request $req, $submissionId)
    {
        $payload = $this->validate($req, [
            'judge_id' => 'required|exists:judges,id',
            'answers' => 'required|array', // expected structure: ['strategy'=>[q1=>1,q2=>0,...], ...]
            'comment' => 'nullable|string',
            'apply_to_submission' => 'nullable|boolean'
        ]);

        DB::beginTransaction();
        try {
            $submission = Submission::findOrFail($submissionId);

            $totalPoints = 0;
            foreach ($payload['answers'] as $axis => $qs) {
                $axisTotal = 0;
                foreach (['q1','q2','q3','q4'] as $q) {
                    $axisTotal += !empty($qs[$q]) ? 5 : 0;
                }
                $totalPoints += $axisTotal;
            }

            $review = Review::create([
                'submission_id' => $submission->id,
                'judge_id' => $payload['judge_id'],
                'answers' => $payload['answers'],
                'total_points' => $totalPoints,
                'comment' => $payload['comment'] ?? null,
            ]);

            // Optionally compute average across all reviews to set submission total_score
            if (!empty($payload['apply_to_submission'])) {
                $avg = Review::where('submission_id', $submission->id)->avg('total_points'); // avg out of 80
                $computed = 0;
                // convert avg (0..80) into 0..100 based on per-axis weighting (same as before)
                // compute per-axis by assuming equal split:
                $computed = ($avg / 80) * 100; // simpler alternative
                $submission->total_score = round($computed,2);
                $submission->save();
            }

            Audit_Log::create(['entity_type'=>Review::class,'entity_id'=>$review->id,'action'=>'created','user_id'=>$req->user()?->id ?? null,'notes'=>'judge review stored']);

            DB::commit();
            return response()->json(['success'=>true,'review_id'=>$review->id], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Review store failed: '.$e->getMessage());
            return response()->json(['success'=>false,'message'=>'Failed to store review'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}