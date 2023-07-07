<?php

namespace App\Jobs;

use App\Mail\NewArticleNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendNewArticleNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $article;
    protected $subscribeEmails;
    /**
     * Create a new job instance.
     */
    public function __construct($article, $subscribeEmails)
    {
        //
        $this->article = $article;
        $this->subscribeEmails = $subscribeEmails;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
        
        foreach($this->subscribeEmails as $mail) {
            Mail::to($mail)->send(new NewArticleNotification($this->article));
        }
    }
}
