<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SubmissionScoreMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $score;

    /**
     * Create a new message instance.
     */
    public function __construct($user, float $score)
    {
        $this->user = $user;
        $this->score = $score;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Your Submission Result')
                    ->view('emails.submission_score')
                    ->with([
                        'name' => $this->user->name ?? 'Participant',
                        'score' => $this->score,
                    ]);
    }
}
