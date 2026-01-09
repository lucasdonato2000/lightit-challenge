<?php

namespace App\Mail;

use App\Models\Patient;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PatientWelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Patient $patient
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to Patient Registration System',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.patient-welcome',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
