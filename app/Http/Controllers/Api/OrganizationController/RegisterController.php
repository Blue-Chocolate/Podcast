<?php

namespace App\Http\Controllers\API\OrganizationController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Organization;
use App\Models\Submission;
use App\Mail\SubmissionResultMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Exception;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sector' => 'nullable|string|max:255',
            'established_at' => 'nullable|date',
            'email' => 'required|email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            // files
            'strategic_plan' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'financial_report' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            // answers
            'answers' => 'required|array',
            'answers.*' => 'numeric|min:0|max:25',
        ]);

        DB::beginTransaction();

        try {
            // Create or find organization
            $organization = Organization::firstOrCreate(
                ['email' => $validated['email']],
                [
                    'name' => $validated['name'],
                    'sector' => $validated['sector'] ?? null,
                    'established_at' => $validated['established_at'] ?? null,
                    'phone' => $validated['phone'] ?? null,
                    'address' => $validated['address'] ?? null,
                ]
            );

            // Upload files if exist
            $strategicPlanPath = null;
            $financialReportPath = null;

            if ($request->hasFile('strategic_plan')) {
                $strategicPlanPath = $request->file('strategic_plan')
                    ->store("uploads/organizations/{$organization->id}", 'public');
                // Update organization with file path
                $organization->update(['strategy_plan_path' => $strategicPlanPath]);
            }

            if ($request->hasFile('financial_report')) {
                $financialReportPath = $request->file('financial_report')
                    ->store("uploads/organizations/{$organization->id}", 'public');
                // Update organization with file path
                $organization->update(['financial_report_path' => $financialReportPath]);
            }

            // Calculate total score
            $totalScore = collect($validated['answers'])->sum();

            // Save submission
            $submission = Submission::create([
                'organization_id' => $organization->id,
                'total_score' => $totalScore,
                'strategic_plan' => $strategicPlanPath,
                'financial_report' => $financialReportPath,
                'status' => 'submitted',
            ]);

            DB::commit();

            // Send email with result
            Mail::to($organization->email)->queue(new SubmissionResultMail($organization, $totalScore, $submission));

            return response()->json([
                'success' => true,
                'submission_id' => $submission->id,
                'status' => 'submitted',
                'total_score' => $totalScore,
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}