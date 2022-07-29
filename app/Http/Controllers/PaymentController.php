<?php

namespace App\Http\Controllers;

use App\Enums\Roles;
use App\Models\ConfirmationToken;
use App\Models\Participant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use \Mollie\Api\MollieApiClient;
use Illuminate\Support\Facades\Log;
use App\Models\Payment;
use App\Enums\PaymentStatus;
use \Mollie\Api\Exceptions\ApiException;
use Illuminate\Http\Request;

class PaymentController extends Controller
{

    private VerificationController $verificationController;

    public function __construct() {
        $this->verificationController = new VerificationController();
    }

    public function payForIntro($token): Response|RedirectResponse
    {
        $confirmationToken = ConfirmationToken::findOrFail($token);
        try{
            $mollie = $this->createMollieInstance();
            $paymentObject = $this->createPaymentEntry($confirmationToken->participant);
            $payment = $mollie->payments->create([
                "amount" => [
                    "currency" => "EUR",
                    "value" => ($confirmationToken->participant->alreadyPaidForMembership) ? "70.00" : "90.00"
                ],
                "description" => "Introductie ". Date("Y"),
                "redirectUrl" => route('payment.success', ['userId' => $confirmationToken->participant->id]),
                "webhookUrl"  => env('NGROK_LINK') ?? route('webhooks.mollie'),
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
        try {
            $participant = Participant::findOrFail($request->userId);
            $participant->latestPayment = $participant->payments()->latest()->first();

            if ($participant != null) {
                if ($participant->latestPayment != null || $participant->latestPayment->paymentStatus == PaymentStatus::paid) {
                    return view('SuccessPage');
                }
            return view('paymentFailed');
            }
        }
        catch (\Exception $e) {
            Log::error($e);
            return view('paymentFailed');
        }
    }

    public function getAllPaidUsers(): Collection {
        $userArr = [];
        $verifiedParticipants = $this->verificationController->getVerifiedParticipants();

        if($verifiedParticipants == null) {
            return collect($userArr);
        }

        foreach($verifiedParticipants as $participant) {
            if($participant->hasPaid() && $participant->role = Roles::child()) {
                array_push($userArr, $participant);
            }
        }
        return collect($userArr)->unique();
    }

    public function getAllNonPaidUsers(): Collection
    {
        $verifiedParticipants = $this->verificationController->getVerifiedParticipants();
        $userArr = [];
        foreach($verifiedParticipants as $participant) {
            if (!$participant->hasPaid() && $participant->role = Roles::child()) {
                array_push($userArr, $participant);
            }
        }
        return collect($userArr)->unique();
    }

    public function checkIfParticipantPaid(Participant $participant):bool {
        $participant->latestPayment = $participant->payments()->latest()->first();

        if ($participant->latestPayment != null) {
            if($participant->latestPayment->paymentStatus == PaymentStatus::paid) {
                return true;
            }
        }
        return false;
    }

}
