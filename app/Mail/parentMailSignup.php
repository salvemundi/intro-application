<?php

namespace App\Mail;

use App\Models\Participant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;
use Intervention\Image\Facades\Image;
use Milon\Barcode\Facades\DNS2DFacade as DNS2D;

class parentMailSignup extends Mailable
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
        return $this->markdown('mails/parentSignup', ['participant' => $this->participant])->subject('Intro 2022 bevestiging')
            ->from(new Address('info@salvemundi.nl','SaMu Intro'))
            ->attachData((string)Image::canvas(290,290,"#fff")->insert(base64_decode(DNS2D::getBarcodePNG($this->participant->id, 'QRCODE', 10,10)))->resizeCanvas(20*2, 20*2, 'center', true, "#fff")->encode('jpg'),'qrcode.jpg', [
                'as' => 'qrcode.jpg',
                'mime' => 'application/jpg',
            ]);
    }
}
