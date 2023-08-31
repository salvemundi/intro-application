<?php

namespace App\Mail;

use App\Models\Participant;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewMemberMail extends Mailable
{
    use Queueable, SerializesModels;

    private Participant $participant;
    private string $password;
    private string $upn;
    private string $coupon;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Participant $participant, string $password, string $upn, string $coupon)
    {
        $this->participant = $participant;
        $this->password = $password;
        $this->upn = $upn;
        $this->coupon = $coupon;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welkom bij Salve Mundi!',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content(): Content
    {
        return new Content(
            view: 'mails.newMember',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
