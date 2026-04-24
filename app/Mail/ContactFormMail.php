<?php

namespace App\Mail;

use App\Models\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactFormMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public ContactMessage $contactMessage) {}

    public function envelope(): Envelope
    {
        $unitPart = $this->contactMessage->unit_name
            ? ' — ' . $this->contactMessage->unit_name
            : '';

        return new Envelope(
            subject: '[Trataplan' . $unitPart . '] ' . ($this->contactMessage->subject ?: 'Nova mensagem de contato'),
            replyTo: [new \Illuminate\Mail\Mailables\Address($this->contactMessage->email, $this->contactMessage->name)],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contact',
        );
    }
}
