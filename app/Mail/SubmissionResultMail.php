<?php

namespace App\Mail;

use App\Models\Organization;
use App\Models\Submission;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SubmissionResultMail extends Mailable
{
    use Queueable, SerializesModels;

    public $organization;
    public $score;
    public $submission;

    public function __construct(Organization $organization, $score, Submission $submission)
    {
        $this->organization = $organization;
        $this->score = $score;
        $this->submission = $submission;
    }

    public function build()
    {
        return $this->subject('Your Performance Submission Result')
            ->view('emails.submission_result')
            ->with([
                'name' => $this->organization->name,
                'score' => $this->score,
                'submission' => $this->submission,
            ]);
    }
}