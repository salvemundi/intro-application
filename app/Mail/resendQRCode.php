<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Participant;
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
    public function __construct()
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
            ->attachData(base64_decode(DNS2D::getBarcodePNG($this->participant->id, 'QRCODE', 10,10)),'qrcode.png', [
                'as' => 'qrcode.png',
                'mime' => 'application/png',
            ])
            ->markdown('mails/resendQRcode', ['participant' => $this->participant]);
    }
}