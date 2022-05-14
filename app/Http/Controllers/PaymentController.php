<?php

namespace App\Http\Controllers;

use App\Models\ConfirmationToken;
use App\Models\Participant;
use \Mollie\Api\MollieApiClient;
use Illuminate\Support\Facades\Log;
use App\Models\Payment;
use App\Enums\PaymentStatus;
use \Mollie\Api\Exceptions\ApiException;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function payForIntro($token) {
        $confirmationToken = ConfirmationToken::findOrFail($token);
        try{
            $mollie = $this->createMollieInstance();
            $paymentObject = $this->createPaymentEntry($confirmationToken->participant);
            $payment = $mollie->payments->create([
                "amount" => [
                    "currency" => "EUR",
                    "value" => "90.00"
                ],
                "description" => "Introductie ". Date("Y"),
                "redirectUrl" => route('payment.success', ['confirmationID' => $confirmationToken->id]),
                "webhookUrl"  => route('webhooks.mollie'),
                "metadata" => [
                    "payment_id" => $paymentObject->id,
                ],
            ]);
            $paymentObject->mollie_transaction_id = $payment->id;
            $paymentObject->save();
            return redirect()->away($payment->getCheckoutUrl(), 303);
        } catch (ApiException $e) {
            Log::error($e);
            return response()->view('errors.500',['e' => $e],500);
        }
    }

    public function createMollieInstance() {
        $mollie = new MollieApiClient();
        $mollie->setApiKey(env('MOLLIE_KEY'));
        return $mollie;
    }

    private function createPaymentEntry(Participant $participant) {
        $payment = new Payment;
        $payment->save();
        $payment->participant()->associate($participant)->save();
        return $payment;
    }

    public function returnSuccessPage(Request $request) {
        $participant = Participant::find($request->userId);
        $participant->latestPayment = $participant->payments()->latest()->first();

        if ($participant != null) {
            if ($participant->latestPayment != null || $participant->latestPayment->paymentStatus == PaymentStatus::paid) {
                return view('successPage');
           }
           return view('paymentFailed');
        }
    }
}
