<?php

namespace App\Mail;

use App\Models\Participant;
use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;

class paymentFailed extends Mailable
{
    use Queueable, SerializesModels;

    private Participant $participant;
    private Payment $payment;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Participant $participant, Payment $payment)
    {
        $this->participant = $participant;
        $this->payment = $payment;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->from(new Address('info@salvemundi.nl','SaMu Intro'))
            ->subject("Betaling is niet gelukt")
            ->markdown('mails/paymentFailed',['participant' => $this->participant, 'payment' => $this->payment]);
    }
}
