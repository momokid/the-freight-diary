<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Services\CompanyService;

class EtaDigestMail extends Mailable
{
    use Queueable, SerializesModels;

    // Public — Laravel makes these available in the Blade view automatically
    public array $digest;
    public ?object $company;

    public function __construct(array $digest)
    {
        $this->digest = $digest;

        // No logged-in user on a scheduled send, so the view composer that
        // normally shares $company never runs.
        $this->company = CompanyService::institution();
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: trim(($this->company->InstName ?? '') . ' Daily ETA Digest')
                . ' — ' . now()->format('d M Y'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.eta-digest',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
