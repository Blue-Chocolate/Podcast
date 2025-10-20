# Submission Confirmation

Hello **{{ $name }}**,

Thank you for submitting your registration form.  
Here are your details:

**Total Score:** {{ $score }}

@if($submission->strategic_plan)
📄 **Strategic Plan:** [View File]({{ asset('storage/' . $submission->strategic_plan) }})
@endif

@if($submission->financial_report)
📊 **Financial Report:** [View File]({{ asset('storage/' . $submission->financial_report) }})
@endif

We will contact you soon for the next steps.

Thanks,  
**{{ config('app.name') }}** Team