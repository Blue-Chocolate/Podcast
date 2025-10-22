<?php

namespace App\Actions\News;

use App\Models\Subscriber;
use App\Jobs\SendNewsEmailJob;

class SendNewsToSubscribersAction
{
    public function execute($news)
    {
        $subscribers = Subscriber::all();

        foreach ($subscribers as $subscriber) {
            dispatch(new SendNewsEmailJob($subscriber, $news))
                ->onQueue('emails');
        }
    }
}
