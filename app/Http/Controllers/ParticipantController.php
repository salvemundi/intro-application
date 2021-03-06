<?php

namespace App\Http\Controllers;

use App\Jobs\resendQRCodeEmails;
use App\Jobs\resendVerificationEmail;
use App\Jobs\sendQRCodesToNonParticipants;
use App\Models\Setting;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\Participant;
use App\Enums\Roles;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ParticipantsExport;
use App\Exports\StudentFontysEmailExport;
use App\Mail\emailNonVerifiedParticipants;
use App\Mail\VerificationMail;
use App\Models\VerificationToken;
use App\Models\ConfirmationToken;
use App\Enums\StudyType;
use App\Mail\parentMailSignup;
use App\Mail\manuallyAddedMail;
use App\Mail\resendQRCode;
use App\Mail\emailConfirmationSignup;

class ParticipantController extends Controller {
    private VerificationController $verificationController;
    private PaymentController $paymentController;

    public function __construct() {
        $this->verificationController = new VerificationController();
        $this->paymentController = new PaymentController();
    }

    public function getParticipantsWithInformation(Request $request): View|Factory|Redirector|RedirectResponse|Application
    {
        $participants = Participant::all();

        if ($request->userId) {
            $selectedParticipant = Participant::find($request->userId);
            $dateToday = Carbon::now()->toDate();

            if(!isset($selectedParticipant)) {
                return redirect("/participants");
            }

            foreach($participants as $participant) {
                if($participant->payments != null) {
                    $participant->latestPayment = $participant->payments()->latest()->first();
                }
                $participant->dateDifference = $dateToday->diff($participant->created_at)->d;
            }

            $age = Carbon::parse($selectedParticipant->birthday)->diff(Carbon::now())->format('%y years');
            return view('admin/participants', ['participants' => $participants, 'selectedParticipant' => $selectedParticipant, 'age' => $age]);
        } else {
            $dateToday = Carbon::now()->toDate();
            foreach($participants as $participant) {
                if($participant->payments != null) {
                    $participant->latestPayment = $participant->payments()->latest()->first();
                }
                $participant->dateDifference = $dateToday->diff($participant->created_at)->d;
            }
        }
        return view('admin/participants', ['participants' => $participants]);
    }

    public function checkedInView(Request $request){
        $availableParticipants = Participant::where('checkedIn', 1)->get();

        if ($request->userId) {
            $selectedParticipant = Participant::find($request->userId);
            if(!isset($selectedParticipant)) {
                return redirect("/participants");
            }

            $age = Carbon::parse($selectedParticipant->birthday)->diff(Carbon::now())->format('%y years');

            return view('admin/participantCheckedIn', ['participants' => $availableParticipants, 'selectedParticipant' => $selectedParticipant, 'age' => $age]);
        }

        return view('admin/participantCheckedIn', ['participants' => $availableParticipants]);
    }

    public function getParticipant($token) {
        $participant = Participant::find($token);
        $age = Carbon::parse($participant->birthday)->diff(Carbon::now())->format('%y');
        if($age >= 18) {
            $participant->above18 = true;
        } else {
            $participant->above18 = false;
        }
        $participant->age = $age;
        return $participant->toJson();
    }

    public function checkIn(Request $request) {
        $participant = Participant::find($request->userId);
        if($participant->removedFromintro){
            return back();
        }
        $participant->checkedIn = true;
        $participant->save();

        return back();
    }

    public function checkOut(Request $request) {
        $participant = Participant::find($request->userId);
        $participant->checkedIn = false;
        $participant->save();

        return back();
    }

    public function checkOutEveryone() {
        Participant::query()->update(['checkedIn' => false]);
        return back();
    }

    public function delete(Request $request) {
        $participant = Participant::find($request->userId);
        $participant->delete();

        return redirect("/participants");
    }

    public function viewAdd() {
        return view('admin/addParticipants');
    }

    public function store(Request $request) {
        if($request->input('confirmation') == null) {
            $request->validate([
                'firstName' => 'required', 'regex:/^[a-zA-Z ]+$/',
                'insertion' => ['nullable','max:32','regex:/^[a-zA-Z ]+$/'],
                'lastName' => 'required', 'regex:/^[a-zA-Z ]+$/',
                'birthday' => 'required',
                'email' => 'required|email:rfc,dns|max:65',
                'phoneNumber' => 'required|max:15|regex:/(^[0-9]+$)+/',
                'firstNameParent' => ['nullable', 'max:65', 'regex:/^[a-zA-Z ]+$/'],
                'lastNameParent' => ['nullable', 'max:65', 'regex:/^[a-zA-Z ]+$/'],
                'addressParent' => ['nullable', 'max:65', 'regex:/^[a-zA-Z0-9 ]+$/'],
                'phoneNumberParent' => 'nullable|max:15|regex:/(^[0-9]+$)+/',
                'medicalIssues' => 'nullable|max:250|regex:/^[a-zA-Z0-9\s ,-]+$/',
                'specials' => 'nullable|max:250|regex:/^[a-zA-Z0-9\s ,-]+$/',
                'role' => 'nullable',
                'checkedIn' => 'nullable'
            ]);
        } else {
            $request->validate([
                'firstName' => ['nullable', 'regex:/^[a-zA-Z ]+$/]'],
                'insertion' => ['nullable','max:32','regex:/^[a-zA-Z ]+$/'],
                'lastName' =>  ['nullable', 'regex:/^[a-zA-Z ]+$/]'],
                'birthday' => 'required',
                'email' => 'required|email:rfc,dns|max:65',
                'fontysEmail' => 'required|email:rfc,dns|max:65|ends_with:student.fontys.nl',
                'phoneNumber' => 'required|max:15|regex:/(^[0-9]+$)+/',
                'firstNameParent' => ['nullable', 'max:65', 'regex:/^[a-zA-Z ]+$/'],
                'lastNameParent' => ['nullable', 'max:65', 'regex:/^[a-zA-Z ]+$/'],
                'addressParent' => ['nullable', 'max:65', 'regex:/^[a-zA-Z0-9 ]+$/'],
                'phoneNumberParent' => 'nullable|max:15|regex:/(^[0-9]+$)+/',
                'medicalIssues' => 'nullable|max:250|regex:/^[a-zA-Z0-9\s ,-]+$/',
                'specials' => 'nullable|max:250|regex:/^[a-zA-Z0-9\s ,-]+$/',
                'role' => 'nullable',
                'checkedIn' => 'nullable',
                'studyType' => 'required'
            ]);
        }

        if($request->input('uid') != null) {
            $participant = Participant::find($request->input('uid'));
        } else {
            $participant = new Participant;
            $participant->id = Str::uuid()->toString();
        }

        if($request->input('confirmation') == null) {
            if(Setting::where('name','SignupPageEnabled')->first()->value == 'false') {
                return back()->with('error','Inschrijvingen zijn helaas gesloten!');
            }
            $participant->firstName = $request->input('firstName');
            $participant->insertion = $request->input('insertion');
            $participant->lastName = $request->input('lastName');
        } else {
            if(Setting::where('name','ConfirmationEnabled')->first()->value == 'false') {
                return back()->with('error','Inschrijvingen zijn helaas gesloten!');
            }
            $participant->fontysEmail = $request->input('fontysEmail');
        }

        $participant->birthday = $request->input('birthday');
        $participant->email = $request->input('email');
        $participant->phoneNumber = $request->input('phoneNumber');
        $participant->studyType = StudyType::coerce((int)$request->input('studyType'));

        if($request->input('studentYear') != null) {
            $participant->studentYear = $request->input('studentYear');
        } else {
            $participant->studentYear = 0;
        }

        $participant->firstNameParent = $request->input('firstNameParent');
        $participant->lastNameParent = $request->input('lastNameParent');
        $participant->addressParent = $request->input('addressParent');
        $participant->phoneNumberParent = $request->input('phoneNumberParent');
        $participant->medicalIssues = $request->input('medicalIssues');
        $participant->specials = $request->input('specials');
        $participant->studyType = $request->input('participantStudyType') ?? 0;

        if($request->input('role') != null) {
            $participant->role = $request->input('role');
        } else {
            $participant->role = 0;
        }

        // what is this shit
        if($request->input('checkedIn') != null) {
            $participant->checkedIn = Roles::coerce((int)$request->input('checkedIn'));
        } else {
            $participant->checkedIn = Roles::coerce(0);
        }

        $participant->save();

        return back()->with('message', 'Informatie is opgeslagen!');
    }

    function excel() {
        return Excel::download(new ParticipantsExport, 'deelnemersInformatie.xlsx');
    }

    function studentFontysEmails() {
        return Excel::download(new StudentFontysEmailExport, 'fontysEmails.xlsx');
    }

    public function storeNote(Request $request): RedirectResponse {
        $participant = Participant::find($request->userId);
        $participant->note = $request->input('participantNote');
        $participant->save();
        return back();
    }

    public function storeRemove(Request $request) {
        $participant = Participant::find($request->userId);
        $participant->removedFromIntro = !$participant->removedFromIntro;

        if($participant->removedFromIntro) {
            $participant->checkedIn = false;
        }

        $participant->save();
        return back();
    }

    public function signup(Request $request) {
        $request->validate([
            'firstName' => ['required', 'max:65', 'regex:/^[a-zA-Z ]+$/'],
            'insertion' => ['nullable','max:32','regex:/^[a-zA-Z ]+$/'],
            'lastName' => ['required', 'max:65', 'regex:/^[a-zA-Z ]+$/'],
            'email' => 'required|email:rfc,dns|max:65',
        ]);

        if(Setting::where('name','SignupPageEnabled')->first()->value == 'false') {
            return back()->with('error','Inschrijvingen zijn helaas gesloten!');
        }

        if (Participant::where('email', $request->input('email'))->count() > 0) {
            return back()->with('warning', 'Dit email adres bestaat al!');
        }

        $participant = new Participant;
        $participant->firstName = $request->input('firstName');
        $participant->insertion = $request->input('insertion');
        $participant->lastName = $request->input('lastName');
        $participant->email = $request->input('email');
        $participant->save();

        $token = new VerificationToken;
        $token->participant()->associate($participant);
        $token->save();

        Mail::to($participant->email)
            ->send(new VerificationMail($participant, $token));

        return back()->with('message', 'Je hebt je ingeschreven! Check je mail om jou email te verifi??ren');
    }

    //Create participant(purple only)
    public function purpleSignup(Request $request) {
        $request->validate([
            'fontysEmail' => 'required|email:rfc,dns|max:65|ends_with:student.fontys.nl',
            'email' => 'required|email:rfc,dns|max:65'
        ]);
        if(Setting::where('name','SignupPageEnabled')->first()->value == 'false') {
            return back()->with('error','Inschrijvingen zijn helaas gesloten!');
        }
        if (Participant::where('fontysEmail', $request->input('fontysEmail'))->count() > 0) {
            return back()->with('warning', 'Jij hebt je waarschijnlijk al ingeschreven voor purple!');
        }

        $participant = new Participant();
        $participant->fontysEmail= $request->input('fontysEmail');
        $participant->email = $request->input('email');
        $participant->save();

        return back()->with('message', 'Je hebt je succesvol opgegeven voor Purple!');
    }

    public function sendEmailsToNonVerified(): RedirectResponse {
        $nonVerifiedParticipants = $this->verificationController->getNonVerifiedParticipants();

        foreach($nonVerifiedParticipants as $participant) {
            $verificationToken = $this->verificationController->createNewVerificationToken($participant);
            $verificationToken->save();

            resendVerificationEmail::dispatch($participant, $verificationToken);
        }

        return back()->with('message', 'De mails zijn verstuurd!');
    }

    public function resendQRCodeEmails(): RedirectResponse {
        $paidParticipants = $this->paymentController->getAllPaidUsers();

        foreach($paidParticipants as $participant) {
            resendQRCodeEmails::dispatch($participant);
        }

        return back()->with('message', 'De mails zijn verstuurd!');
    }

    public function sendQRCodesToNonParticipants(): RedirectResponse {
        $paidParticipants = Participant::where('role','!=',Roles::child())->get();

        foreach($paidParticipants as $participant) {
            sendQRCodesToNonParticipants::dispatch($participant);
        }

        return back()->with('message', 'De mails zijn verstuurd!');
    }

    public function storeEdit(Request $request): RedirectResponse
    {
        $request->validate([
            'participantFirstName' => 'required', 'regex:/^[a-zA-Z ]+$/',
            'participantInsertion' => ['nullable','max:32','regex:/^[a-zA-Z ]+$/'],
            'participantLastName' => 'required', 'regex:/^[a-zA-Z ]+$/',
            'participantBirthday' => 'nullable',
            'participantEmail' => 'required|email:rfc,dns|max:65',
            'participantPhoneNumber' => 'nullable|max:15|regex:/(^[0-9]+$)+/',
            'participantFirstNameParent' => ['nullable', 'max:65', 'regex:/^[a-zA-Z ]+$/'],
            'participantLastNameParent' => ['nullable', 'max:65', 'regex:/^[a-zA-Z ]+$/'],
            'participantAddress' => ['nullable', 'max:65', 'regex:/^[a-zA-Z0-9 ]+$/'],
            'participantParentPhoneNumber' => 'nullable|max:15|regex:/(^[0-9]+$)+/',
            'participantMedicalIssues' => 'nullable|max:250|regex:/^[a-zA-Z0-9\s ,-]+$/',
            'participantSpecial' => 'nullable|max:250|regex:/^[a-zA-Z0-9\s ,-]+$/'
        ]);

        $participant = Participant::find($request->userId);
        if($participant == null) {
            return back()->with('error','Deelnemer niet gevonden!');
        }
        $participant->firstName = $request->input('participantFirstName');
        $participant->insertion = $request->input('participantInsertion');
        $participant->lastName = $request->input('participantLastName');
        $participant->email = $request->input('participantEmail');
        $participant->birthday = $request->input('participantBirthday');
        $participant->phoneNumber = $request->input('participantPhoneNumber');
        $participant->firstNameParent = $request->input('participantFirstNameParent');
        $participant->lastNameParent = $request->input('participantLastNameParent');
        $participant->addressParent = $request->input('participantAddress');
        $participant->phoneNumberParent = $request->input('participantParentPhoneNumber');
        $participant->medicalIssues = $request->input('participantMedicalIssues');
        $participant->role = $request->input('participantRole') ?? 0;
        $participant->studyType = $request->input('participantStudyType') ?? 0;
        $participant->alreadyPaidForMembership = isset($request->participantAlreadyPaid);
        $participant->save();
        return back()->with('success','Deelnemer gegevens opgeslagen!');
    }

    public function daddyIndex(){
        return view('daddy');
    }

    public function daddyStore(Request $request){
        $request->validate([
            'firstName' => ['required', 'regex:/^[a-zA-Z ]+$/'],
            'insertion' => ['nullable','max:32','regex:/^[a-zA-Z ]+$/'],
            'lastName' =>  ['required', 'regex:/^[a-zA-Z ]+$/'],
            'email' => 'required|email:rfc,dns|max:65',
            'birthday' => 'required',
            'fontysEmail' => 'nullable|email:rfc,dns|max:65|ends_with:student.fontys.nl',
            'phoneNumber' => 'required|max:15|regex:/(^[0-9]+$)+/',
            'firstNameParent' => ['required', 'max:65', 'regex:/^[a-zA-Z ]+$/'],
            'lastNameParent' => ['required', 'max:65', 'regex:/^[a-zA-Z ]+$/'],
            'phoneNumberParent' => 'required|max:15|regex:/(^[0-9]+$)+/',
            'medicalIssues' => 'nullable|max:250|regex:/^[a-zA-Z0-9\s ,-]+$/',
            'specials' => 'nullable|max:250|regex:/^[a-zA-Z0-9\s ,-]+$/',
        ]);

        $parent = new Participant;
        $parent->firstName = $request->input('firstName');
        $parent->insertion = $request->input('insertion');
        $parent->lastName = $request->input('lastName');
        $parent->email = $request->input('email');
        $parent->birthday = $request->input('birthday');
        $parent->phoneNumber = $request->input('phoneNumber');
        $parent->firstNameParent = $request->input('firstNameParent');
        $parent->lastNameParent = $request->input('lastNameParent');
        $parent->phoneNumberParent = $request->input('phoneNumberParent');
        $parent->medicalIssues = $request->input('medicalIssues');
        $parent->specials = $request->input('specials');
        $parent->role = Roles::dad_mom();
        $parent->save();

        Mail::to($parent->email)
            ->send(new parentMailSignup($parent));

        return back()->with('success','Deelnemer gegevens opgeslagen!');
    }

    public function storeSelfAddedParticipant(Request $request): RedirectResponse
    {
        $request->validate([
            'firstName' => ['required', 'regex:/^[a-zA-Z ]+$/'],
            'insertion' => ['nullable','max:32','regex:/^[a-zA-Z ]+$/'],
            'lastName' =>  ['required', 'regex:/^[a-zA-Z ]+$/'],
            'birthday' => 'required',
            'email' => 'required|email:rfc,dns|max:65',
            'fontysEmail' => 'required|email:rfc,dns|max:65|ends_with:student.fontys.nl',
            'phoneNumber' => 'required|max:15|regex:/(^[0-9]+$)+/',
            'studyType' => 'nullable',
            'studentYear' => 'nullable',
            'firstNameParent' => ['nullable', 'max:65', 'regex:/^[a-zA-Z ]+$/'],
            'lastNameParent' => ['nullable', 'max:65', 'regex:/^[a-zA-Z ]+$/'],
            'addressParent' => ['nullable', 'max:65', 'regex:/^[a-zA-Z0-9 ]+$/'],
            'phoneNumberParent' => 'nullable|max:15|regex:/(^[0-9]+$)+/',
            'medicalIssues' => 'nullable|max:250|regex:/^[a-zA-Z0-9\s ,-]+$/',
            'specials' => 'nullable|max:250|regex:/^[a-zA-Z0-9\s ,-]+$/',
            'role' => 'nullable',
            'checkedIn' => 'required',
        ]);

        $participant = new Participant;
        $participant->id = Str::uuid()->toString();

        $participant->firstName = $request->input('firstName');
        $participant->insertion = $request->input('insertion');
        $participant->lastName = $request->input('lastName');
        $participant->fontysEmail = $request->input('fontysEmail');

        $participant->birthday = $request->input('birthday');
        $participant->email = $request->input('email');
        $participant->phoneNumber = $request->input('phoneNumber');
        $participant->studyType = StudyType::coerce((int)$request->input('studyType'));

        if($request->input('studentYear') != null) {
            $participant->studentYear = $request->input('studentYear');
        } else {
            $participant->studentYear = 0;
        }

        $participant->firstNameParent = $request->input('firstNameParent');
        $participant->lastNameParent = $request->input('lastNameParent');
        $participant->addressParent = $request->input('addressParent');
        $participant->phoneNumberParent = $request->input('phoneNumberParent');
        $participant->medicalIssues = $request->input('medicalIssues');
        $participant->specials = $request->input('specials');
        $participant->studyType = $request->input('participantStudyType') ?? 0;

        if($request->input('role') != null) {
            $participant->role = $request->input('role');
        } else {
            $participant->role = 0;
        }

        if($request->input('checkedIn') != null) {
            $participant->checkedIn = Roles::coerce((int)$request->input('checkedIn'));
        } else {
            $participant->checkedIn = Roles::coerce(0);
        }

        $participant->save();

        if ($participant->role != Roles::child) {
            Mail::to($participant->email)
                ->send(new manuallyAddedMail($participant));
        } else {
            $verificationToken = $this->verificationController->createNewVerificationToken($participant);
            $verificationToken->verified = true;
            $verificationToken->save();

            $newConfirmationToken = new ConfirmationToken();
            $newConfirmationToken->participant()->associate($participant);
            $newConfirmationToken->save();

            Mail::to($participant->email)
                ->send(new emailConfirmationSignup($participant, $newConfirmationToken));
        }

        return back()->with('message', 'Deelnemer is opgeslagen!');
    }

    public function sendParticipantConfirmationEmail(Request $request): RedirectResponse
    {
        $participant = Participant::find($request->userId);
        if(!$participant->hasPaid()) {
            Mail::to($participant->email)
                ->send(new emailConfirmationSignup($participant, $this->createConfirmationToken($participant)));
            return back()->with('success','Confirmatie email verstuurd!');
        }
        return back()->with('error','Deelnemer heeft al betaald!');
    }

    private function createConfirmationToken(Participant $participant): ConfirmationToken
    {
        $confirmationToken =  new ConfirmationToken();
        $confirmationToken->participant()->associate($participant);
        $confirmationToken->save();

        return $confirmationToken;
    }
}
