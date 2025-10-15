<?php

namespace App\Http\Controllers\API\OrganizationController;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\Submission;
use App\Mail\SubmissionResultMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class OrganizationSubmissionController extends Controller
{
    public function store(Request $request)
    {
        // ✅ التحقق من البيانات
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'sector' => 'nullable|string|max:255',
            'established_at' => 'nullable|date',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'strategy_plan' => 'nullable|file|mimes:pdf,docx|max:20480',
            'financial_report' => 'nullable|file|mimes:pdf,docx,xlsx|max:20480',
            'structure_chart' => 'nullable|file|mimes:pdf,docx|max:20480',
            'answers' => 'required|array', // الأسئلة الأربعة
        ]);

        // ✅ العثور على المنظمة حسب الإيميل
        $organization = Organization::where('email', $request->email)->first();

        if (!$organization) {
            return response()->json(['message' => 'Organization not found'], 404);
        }

        // ✅ التحقق من ملكية المستخدم
        if ($organization->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // ✅ حفظ الملفات
        $strategy = $request->file('strategy_plan')?->store('submissions', 'public');
        $financial = $request->file('financial_report')?->store('submissions', 'public');
        $structure = $request->file('structure_chart')?->store('submissions', 'public');

        // ✅ تحديث بيانات المنظمة مع الملفات الجديدة (إذا وجدت)
        $organization->update([
            'name' => $request->name,
            'sector' => $request->sector,
            'established_at' => $request->established_at,
            'phone' => $request->phone,
            'address' => $request->address,
            'strategy_plan_path' => $strategy ?? $organization->strategy_plan_path,
            'financial_report_path' => $financial ?? $organization->financial_report_path,
            'structure_chart_path' => $structure ?? $organization->structure_chart_path,
        ]);

        // ✅ حساب النتيجة بناءً على الإجابات
        $totalScore = collect($request->answers)->avg();

        // ✅ حفظ التقديم
        $submission = Submission::create([
            'organization_id' => $organization->id,
            'answers' => json_encode($request->answers),
            'total_score' => $totalScore,
            'status' => 'submitted',
        ]);

        // ✅ إرسال الإيميل بالنتيجة
        Mail::to($organization->email)->send(new SubmissionResultMail($organization, $totalScore, $submission));

        return response()->json([
            'message' => 'Submission received successfully',
            'organization' => $organization,
            'submission' => $submission,
        ]);
    }
}
