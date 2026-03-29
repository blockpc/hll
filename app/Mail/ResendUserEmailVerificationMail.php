<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

final class ResendUserEmailVerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public User $user
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('system.users.mails.resend.verification_email_subject'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.resend-user_email_verification',
            with: [
                'name' => $this->user->name,
                'email' => $this->user->email,
                'verificationUrl' => $this->verificationUrl(),
            ],
        );
    }

    /**
     * Generate a temporary signed URL for email verification.
     */
    private function verificationUrl(): string
    {
        return URL::temporarySignedRoute(
            'verification.invitation.verify',
            Carbon::now()->addMinutes(Config::get('auth.passwords.users.expire', 60)),
            [
                'id' => $this->user->getKey(),
                'hash' => sha1($this->user->getEmailForVerification()),
            ],
        );
    }
}
