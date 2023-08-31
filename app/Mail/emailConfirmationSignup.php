<?php

namespace App\Mail;

use App\Models\ConfirmationToken;
use App\Models\Participant;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;

class emailConfirmationSignup extends Mailable
{
    use Queueable, SerializesModels;

    private Participant $participant;
    private ConfirmationToken $confirmationToken;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Participant $participant, ConfirmationToken $confirmationToken)
    {
        $this->participant = $participant;
        $this->confirmationToken = $confirmationToken;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject("Afronding inschrijving voor de introductie!")
            ->from(new Address('info@salvemundi.nl','SaMu Intro'))
            ->markdown('mails/emailConfirmationResponse',['participant' => $this->participant, 'confirmationToken' => $this->confirmationToken]);
    }
}
