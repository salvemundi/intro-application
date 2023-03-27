@extends('layouts.guapp')
@section('content')
<div class="container">
    @if(session()->has('message'))
        <div class="alert alert-primary">
            {{ session()->get('message') }}
        </div>
    @endif
    @if(session()->has('error'))
        <div class="alert alert-danger">
            {{ session()->get('error') }}
        </div>
    @endif

    <form action="/inschrijven/betalen/{{ $confirmationToken->id }}" method="post" enctype="multipart/form-data">
        @csrf
        <br>
        <h2 class="h2">Graag de aanvullende informatie invullen</h2>
        <input type="hidden" name="uid" id="uid" value="{{ $confirmationToken->participant->id }}">
        <input type="hidden" name="confirmation" id="confirmation" value="1">
        <div class="form-group">
            <label for="voornaam">Voornaam*</label>
            <input class="form-control{{ $errors->has('firstName') ? ' is-invalid' : '' }}" name="firstName" value="{{ $confirmationToken->participant->firstName }}" disabled>
        </div><br>
        @if($confirmationToken->participant->insertion != "" || $confirmationToken->participant->insertion != null)
            <div class="form-group">
                <label for="voornaam">Tussenvoegsel</label>
                <input class="form-control{{ $errors->has('insertion') ? ' is-invalid' : '' }}" name="insertion" value="{{ $confirmationToken->participant->insertion }}" disabled>
            </div><br>
        @endif

        <div class="form-group">
            <label for="voornaam">Achternaam*</label>
            <input class="form-control{{ $errors->has('lastName') ? ' is-invalid' : '' }}" name="lastName" value="{{ $confirmationToken->participant->lastName }}" disabled>
        </div><br>

        <div class="form-group">
            <label for="voornaam">Geboortedatum*</label>
            <input class="form-control{{ $errors->has('birthday') ? ' is-invalid' : '' }}" value="{{ old('birthday') }}" type="date" id="birthday" name="birthday" placeholder="MM-DD-JJJJ..." onblur="getAge()">
        </div><br>

        <div class="form-group">
            <label for="voornaam">Email*</label>
            <input class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" value="{{ $confirmationToken->participant->email }}" id="email" name="email" placeholder="Email...">
        </div><br>

        <div class="form-group">
            <label for="voornaam">Telefoonnummer*</label>
            <input class="form-control{{ $errors->has('phoneNumber') ? ' is-invalid' : '' }}" value="{{ $confirmationToken->participant->phoneNumber }}" id="phoneNumber" name="phoneNumber" placeholder="Telefoonnummer...">
        </div>

        <label for="StudyType">Leervorm*</label>
        <div class="form-group">
            <select class="form-control" name="studyType">
                @foreach (App\Enums\StudyType::getInstances() as $item)
                    @if($item->value != 2)
                        <option value="{{ $item->value }}">{{$item->description}}</option>
                    @endif
                @endforeach
            </select>
        </div>

        <label for="studentYear">Leerjaar*</label>
        <div class="form-group">
            <select class="form-control" name="studentYear" id="studentYear">
                @foreach (App\Enums\StudentYear::getInstances() as $item)
                    <option value="{{ $item->value }}">{{$item->description}}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="fontysEmail">Je fontys email adres*</label>
            <input class="form-control{{ $errors->has('fontysEmail') ? ' is-invalid' : '' }}" value="{{ old('fontysEmail') }}" id="fontysEmail" name="fontysEmail" placeholder="123456@student.fontys.nl...">
        </div>

        <br>
        <div id="ShowIfBelow18" style="display: none;">
            <label for="VoornaamVoogd">Voornaam ouder/verzorger*</label>
            <input class="form-control{{ $errors->has('firstNameParent') ? ' is-invalid' : '' }}" value="{{ old('firstNameParent') }}" type="text" id="firstNameParentBelow18" name="firstNameParent" placeholder="Voornaam ouder/verzorger...">

            <br>
            <label for="AchternaamVoogd">Achternaam ouder/verzorger*</label>
            <input class="form-control{{ $errors->has('lastNameParent') ? ' is-invalid' : '' }}" value="{{ old('lastNameParent') }}" type="text" id="lastNameParentBelow18" name="lastNameParent" placeholder="Achternaam ouder/verzorger...">

            <br>
            <label for="AdresVoogd">Adres ouder/verzorger*</label>
            <input class="form-control{{ $errors->has('addressParent') ? ' is-invalid' : '' }}" value="{{ old('addressParent') }}" type="text" id="addressParentBelow18" name="addressParent" placeholder="Adres ouder/verzorger...">

            <br>
            <label for="TelefoonnummerVoogd">Telefoonnummer ouder/verzorger*</label>
            <input class="form-control{{ $errors->has('phoneNumberParent') ? ' is-invalid' : '' }}" value="{{ old('phoneNumberParent') }}" type="text" id="phoneNumberParentBelow18" name="phoneNumberParent" placeholder="Telefoonnummer ouder/verzorger...">
        </div>

        <div id="ShowIfAbove18" style="display: none;">
            <label for="VoornaamVoogd">Voornaam contactpersoon*</label>
            <input class="form-control{{ $errors->has('firstNameParent') ? ' is-invalid' : '' }}" value="{{ old('firstNameParent') }}" type="text" id="firstNameParentAbove18" name="firstNameParent" placeholder="Voornaam contactpersoon...">

            <br>
            <label for="AchternaamVoogd">Achternaam contactpersoon*</label>
            <input class="form-control{{ $errors->has('lastNameParent') ? ' is-invalid' : '' }}" value="{{ old('lastNameParent') }}" type="text" id="lastNameParentAbove18" name="lastNameParent" placeholder="Achternaam contactpersoon...">

            <br>
            <label for="TelefoonnummerVoogd">Telefoonnummer contactpersoon*</label>
            <input class="form-control{{ $errors->has('phoneNumberParent') ? ' is-invalid' : '' }}" value="{{ old('phoneNumberParent') }}" type="text" id="phoneNumberParentAbove18" name="phoneNumberParent" placeholder="Telefoonnummer contactpersoon...">
        </div>

        <div class="form-group">
            <label for="voornaam">Allergieën</label>
            <textarea class="form-control{{ $errors->has('medicalIssues') ? ' is-invalid' : '' }}" value="{{{ old('medicalIssues') }}}" type="textarea" id="medicalIssues" name="medicalIssues" placeholder="Allergieën..."></textarea>
        </div><br>

        <div class="form-group">
            <label for="voornaam">Bijzonderheden</label>
            <textarea class="form-control{{ $errors->has('specials') ? ' is-invalid' : '' }}" value="{{{ old('specials') }}}" type="textarea" id="specials" name="specials" placeholder="Bijzonderheden..."></textarea>
        </div><br>

        <div class="form-check mt-2">
            <input class="form-check-input" name="Tos" value="Tos" required type="checkbox" id="flexCheckDefaultTos">
            <label class="form-check-label" for="flexCheckDefaultTos">
                Ik ga akkoord met de <a href="{{ asset('pdf/Algemenevoorwaarden.pdf') }}" download>algemene voorwaarden</a> & de <a href="{{ asset('pdf/privacy.pdf') }}" download>privacy voorwaarden</a>
            </label>
        </div>

        <div class="form-group mb-5">
            <br>
            <input class="btn btn-primary" type="submit" value="Betalen €90">
        </div>
    </form>
</div>
<script>
    function getAge() {
        var dateString = document.getElementById("birthday").value;

        if(dateString !="") {
            var today = new Date();
            var birthDate = new Date(dateString);
            var age = today.getFullYear() - birthDate.getFullYear();
            var month = today.getMonth() - birthDate.getMonth();
            var date = today.getDate() - birthDate.getDate();

            if (month < 0 || (month === 0 && today.getDate() < birthDate.getDate())) {
                age--;
            }
            if(month< 0) {
                month += 12;
            }
            if(date< 0) {
                date += 30;
            }
            if(age < 18 || age > 100) {
                document.getElementById("ShowIfBelow18").style.display = "inline";
                document.getElementById("ShowIfAbove18").style.display = "none";

                document.getElementById("firstNameParentBelow18").disabled = false;
                document.getElementById("lastNameParentBelow18").disabled = false;
                document.getElementById("addressParentBelow18").disabled = false;
                document.getElementById("phoneNumberParentBelow18").disabled = false;

                document.getElementById("firstNameParentAbove18").disabled = true;
                document.getElementById("lastNameParentAbove18").disabled = true;
                document.getElementById("phoneNumberParentAbove18").disabled = true;
            } else {
                document.getElementById("ShowIfBelow18").style.display = "none";
                document.getElementById("ShowIfAbove18").style.display = "inline";

                document.getElementById("firstNameParentBelow18").disabled = true;
                document.getElementById("lastNameParentBelow18").disabled = true;
                document.getElementById("addressParentBelow18").disabled = true;
                document.getElementById("phoneNumberParentBelow18").disabled = true;

                document.getElementById("firstNameParentAbove18").disabled = false;
                document.getElementById("lastNameParentAbove18").disabled = false;
                document.getElementById("phoneNumberParentAbove18").disabled = false;
            }
        }
    }

    getAge();
</script>
@endsection
