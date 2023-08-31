<?php

namespace App\Mail;

use App\Models\Participant;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;

class firstSignup extends Mailable
{
    use Queueable, SerializesModels;

    private Participant $participant;


    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Participant $participant)
    {
        $this->participant = $participant;
    }
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject("Vrijblijvende inschrijving intro")
            ->from(new Address('info@salvemundi.nl','SaMu Intro'))
            ->markdown('mails/signup',['participant' => $this->participant]);
    }
}
