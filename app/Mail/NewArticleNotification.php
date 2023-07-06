<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class NewArticleNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $article;
    /**
     * Create a new message instance.
     */
    public function __construct(public $specificArticle)
    {
        //
        $this->article = $specificArticle;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Article (FGWM)',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        Log::info('article img', [
            $this->article->imgURL
        ]);
        return new Content(
            view: 'emails.new-article-notification',
            with: [
                'title' => $this->article->title,
                'imgURL' => $this->article->imgURL,
                'bodyText' => $this->article->bodyText
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
