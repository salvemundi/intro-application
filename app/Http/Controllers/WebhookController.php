<?php

namespace App\Http\Controllers;

use App\Enums\PaymentStatus;
use App\Mail\emailPaymentSucceeded;
use App\Mail\paymentFailed;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Mollie\Api\Exceptions\ApiException;
use Mollie\Laravel\Facades\Mollie;

class WebhookController extends Controller
{
    public function handle(Request $request) {
        if(!$request->has('id')) {
            return;
        }
        try {
            $payment = Mollie::api()->payments()->get($request->input('id'));

            // Update the status of the payment in the database
            $paymentStorage = Payment::find($payment->metadata->payment_id);
            $paymentStorage->paymentStatus = PaymentStatus::coerce($payment->status)->value;
            $paymentStorage->save();

            if ($payment->isPaid() && ! $payment->hasRefunds() && ! $payment->hasChargebacks()) {
                /*
                 * The payment is paid and isn't refunded or charged back.
                 * At this point you'd probably want to start the process of delivering the product to the customer.
                 */
                $participant = $paymentStorage->participant;
                Mail::to($participant)
                    ->send(new emailPaymentSucceeded($participant));

                return response(null, 200);
            } elseif ($payment->isOpen()) {
                /*
                 * The payment is open.
                 */
            } elseif ($payment->isPending()) {
                /*
                 * The payment is pending.
                 */
            } elseif ($payment->isFailed()) {
                $participant = $paymentStorage->participant;
                Mail::to($participant)
                    ->send(new paymentFailed($participant, $paymentStorage));
                return response(null, 200);
            } elseif ($payment->isExpired()) {
                /*
                 * The payment is expired.
                 */
            } elseif ($payment->isCanceled()) {
                /*
                 * The payment has been canceled.
                 */
            } elseif ($payment->hasRefunds()) {
                /*
                 * The payment has been (partially) refunded.
                 * The status of the payment is still "paid"
                 */
            } elseif ($payment->hasChargebacks()) {
                /*
                 * The payment has been (partially) charged back.
                 * The status of the payment is still "paid"
                 */
            }
        } catch (ApiException $e) {
            return response()->view('errors.500',['e' => $e],500);
        }
    }
}
