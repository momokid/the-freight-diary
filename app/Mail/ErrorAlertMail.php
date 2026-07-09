<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Services\CompanyService;

class ErrorAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public array $error) {}

    public function envelope(): Envelope
    {
        $company = CompanyService::get();

        return new Envelope(
            subject: ($company->InstName ?? 'Freight Diary') . ' — New Error Ticket: ' . $this->error['ExceptionClass'],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.error-alert',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
