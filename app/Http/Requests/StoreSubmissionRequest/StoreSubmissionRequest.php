<?php


namespace App\Http\Requests\StoreSubmissionRequest;


use Illuminate\Foundation\Http\FormRequest;


class StoreSubmissionRequest extends FormRequest
{
public function authorize() { return true; }


public function rules()
{
return [
'organization.name' => 'required|string|max:255',
'organization.sector' => 'nullable|string|max:255',
'organization.established_at' => 'nullable|date|before_or_equal:today',
'organization.email' => 'nullable|email|max:255',
'organization.phone' => 'nullable|string|max:30',
'organization.address' => 'nullable|string',


// answers: array of axes
'answers' => 'required|array',
'answers.*.axis' => 'required|in:strategy,social,finance,leadership',
'answers.*.q1' => 'nullable|boolean',
'answers.*.q2' => 'nullable|boolean',
'answers.*.q3' => 'nullable|boolean',
'answers.*.q4' => 'nullable|boolean',
'answers.*.notes' => 'nullable|string',


// attachments optional
'attachments' => 'nullable|array',
'attachments.*' => 'file|max:10240|mimes:pdf,doc,docx,xlsx,jpg,jpeg,png',
'attachments_axes' => 'nullable|array',


'meta' => 'nullable|array',
'save_as_draft' => 'nullable|boolean',
];
}


public function prepareForValidation()
{
if ($this->has('save_as_draft')) {
$this->merge(['save_as_draft' => (bool)$this->input('save_as_draft')]);
}
}
}

