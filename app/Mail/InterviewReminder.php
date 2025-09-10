<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Application;

class InterviewReminder extends Mailable
{
    use Queueable, SerializesModels;

    public Application $application;
    public int $daysUntilInterview;

    /**
     * Create a new message instance.
     */
    public function __construct(Application $application, int $daysUntilInterview = 3)
    {
        $this->application = $application;
        $this->daysUntilInterview = $daysUntilInterview;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = match($this->daysUntilInterview) {
            1 => "🚨 Rappel urgent : Entretien demain chez {$this->application->company}",
            2 => "⏰ Rappel : Entretien dans 2 jours chez {$this->application->company}",
            3 => "📅 Rappel : Entretien dans 3 jours chez {$this->application->company}",
            default => "Rappel d'entretien chez {$this->application->company}"
        };

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.interview-reminder',
            with: [
                'application' => $this->application,
                'daysUntilInterview' => $this->daysUntilInterview,
                'user' => $this->application->user,
                'urgencyLevel' => $this->getUrgencyLevel()
            ]
        );
    }

    /**
     * Détermine le niveau d'urgence selon les jours restants
     */
    private function getUrgencyLevel(): string
    {
        return match($this->daysUntilInterview) {
            1 => 'urgent',
            2 => 'important',
            3 => 'normal',
            default => 'normal'
        };
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