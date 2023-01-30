<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Participant;
use Intervention\Image\Facades\Image;
use Milon\Barcode\Facades\DNS2DFacade as DNS2D;

class resendQRCode extends Mailable
{
    use Queueable, SerializesModels;

    private $participant;
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
            ->subject("QR-code")
            ->attachData((string)Image::canvas(290,290,"#fff")->insert(base64_decode(DNS2D::getBarcodePNG($this->participant->id, 'QRCODE', 10,10)))->resizeCanvas(20*2, 20*2, 'center', true, "#fff")->encode('jpg'),'qrcode.jpg', [
                'as' => 'qrcode.jpg',
                'mime' => 'application/jpg',
            ])
            ->markdown('mails/resendQRcode', ['participant' => $this->participant]);
    }
}
