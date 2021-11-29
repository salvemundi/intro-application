<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Participant;
use App\Enums\CovidProof;
use App\Enums\Roles;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ParticipantsExport;
use App\Mail\VerificationMail;
use App\Models\VerificationToken;
use Illuminate\Auth\Notifications\VerifyEmail;

class RegistrationController extends Controller
{
    public function getRegistrationsWithInformation(Request $request) {
        $participants = Participant::join('verify_email', 'verify_email.participantId', '=',  'participants.id')
               ->get(['participants.*', 'verify_email.verified', 'verify_email.updated_at']);

        $participantsWhoDidntVerify = $participants->where('verified', 0);

        $participantsWhoDidVerify = $participants->where('verified', 1);
        //dd($participantsWhoDidVerify);

        $dateToday = Carbon::now()->toDate();
        foreach($participants as $participant) {
            $participant->dateDifference = $dateToday->diff($participant->created_at)->d;
        }

        return view('registrations', ['participantsWhoDidntVerify' => $participantsWhoDidntVerify, 'participantsWhoDidVerify' => $participantsWhoDidVerify]);
    }
}