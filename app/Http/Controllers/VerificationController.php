<?php

namespace App\Http\Controllers;

use App\Models\VerificationToken;
use App\Models\Participant;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use App\Mail\emailVerificationResponse;
use Carbon\Carbon;
use App\Models\ConfirmationToken;
use App\Models\Setting;
use App\Mail\emailConfirmationSignup;

class VerificationController extends Controller
{
    public function verify(Request $request): Factory|View|Application
    {
        $token = $request->token;
        $verificationToken = VerificationToken::find($token);

        if ($token && $verificationToken !== null) {
            if($verificationToken->verified) {
                return view('verifyResponse', ['Response' => false]);
            }

            $verificationToken->verified = true;
            $verificationToken->save();

            $participant = $verificationToken->participant()->first();

            Mail::to($participant->email)
                ->send(new emailVerificationResponse($participant));

            $today = Carbon::now()->format('Y-m-d'); //yyyy-mm-dd

            if(Setting::where('name','AutoSendPaymentEmailDate')->first()->value <= $today) {
                if(!$verificationToken->participant->hasPaid()) {
                    $newConfirmationToken = new ConfirmationToken();
                    $newConfirmationToken->participant()->associate($participant);
                    $newConfirmationToken->save();

                    Mail::to($participant->email)
                        ->send(new emailConfirmationSignup($participant, $newConfirmationToken));
                }
            }

            return view('verifyResponse', ['Response' => true]);
        }

        return view('verifyResponse', ['Response' => false]);
    }

    public function getVerifiedParticipants(): Collection
    {
        $userArr = [];
        $participants = Participant::all();
        foreach($participants as $participant) {
            if($participant->isVerified()) {
                array_push($userArr, $participant);
            }
        }

        return collect($userArr)->unique();
    }

    public function getNonVerifiedParticipants(): Collection
    {
        $userArr = [];
        $participants = Participant::all();
        foreach($participants as $participant) {
            if(!$participant->isVerified()) {
                array_push($userArr, $participant);
            }
        }

        return collect($userArr)->unique();
    }

    public function createNewVerificationToken(Participant $participant): VerificationToken {
        $newVerificationToken = new VerificationToken();
        $newVerificationToken->participant()->associate($participant);
        $newVerificationToken->save();

        return $newVerificationToken;
    }

}
