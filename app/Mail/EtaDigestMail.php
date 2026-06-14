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

    // Public — Laravel makes this available in the Blade view automatically
    public function __construct(public array $digest) {}

    public function envelope(): Envelope
    {
        $company = CompanyService::get();

        return new Envelope(
            subject: ($company->InstName ?? 'PSIL') . ' Daily ETA Digest — ' . now()->format('d M Y'),
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
