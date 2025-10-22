<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendNewsEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $subscriber;
    public $news;

    public function __construct($subscriber, $news)
    {
        $this->subscriber = $subscriber;
        $this->news = $news;
    }

    public function handle()
    {
        Mail::raw($this->news->content, function ($message) {
            $message->to($this->subscriber->email, $this->subscriber->first_name)
                    ->subject($this->news->title);
        });
    }
}