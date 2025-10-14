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

        // ✅ حفظ الملفات
        $strategy = $request->file('strategy_plan')?->store('submissions', 'public');
        $financial = $request->file('financial_report')?->store('submissions', 'public');
        $structure = $request->file('structure_chart')?->store('submissions', 'public');

        // ✅ إنشاء المنظمة أو تحديثها حسب الإيميل
        $organization = Organization::updateOrCreate(
            ['email' => $request->email],
            [
                'name' => $request->name,
                'sector' => $request->sector,
                'established_at' => $request->established_at,
                'phone' => $request->phone,
                'address' => $request->address,
                'strategy_plan_path' => $strategy,
                'financial_report_path' => $financial,
                'structure_chart_path' => $structure,
            ]
        );

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
