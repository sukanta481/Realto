<?php

namespace App\Mail;

use App\Models\FollowUp;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FollowUpReminder extends Mailable
{
    use Queueable, SerializesModels;

    public FollowUp $followUp;
    public User $user;

    /**
     * Create a new message instance.
     */
    public function __construct(FollowUp $followUp, User $user)
    {
        $this->followUp = $followUp;
        $this->user = $user;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Follow-up Reminder: ' . $this->followUp->purpose,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.follow-up-reminder',
            with: [
                'followUp' => $this->followUp,
                'user' => $this->user,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
