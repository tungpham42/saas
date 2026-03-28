<?php

namespace App\Mail;

use App\Models\Bot;
use App\Models\SessionStat;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class ChatTranscriptMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     * * @param Bot $bot
     * @param SessionStat $session
     * @param Collection $messages
     */
    public function __construct(
        public Bot $bot,
        public SessionStat $session,
        public Collection $messages
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Your Chat Transcript - {$this->bot->name}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.chat-transcript', // Ensure this matches your file path
            with: [
                'bot' => $this->bot,
                'session' => $this->session,
                'messages' => $this->messages,
            ],
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
