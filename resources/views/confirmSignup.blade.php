@extends('layouts.guapp')
@section('content')
<div class="container">
    @if(session()->has('message'))
        <div class="alert alert-primary">
            {{ session()->get('message') }}
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
            <input class="form-control{{ $errors->has('firstName') ? ' is-invalid' : '' }}" value="{{ $confirmationToken->participant->firstName }}" disabled>
        </div><br>
        @if($confirmationToken->participant->insertion != "" || $confirmationToken->participant->insertion != null)
            <div class="form-group">
                <label for="voornaam">Tussenvoegsel</label>
                <input class="form-control{{ $errors->has('insertion') ? ' is-invalid' : '' }}" value="{{ $confirmationToken->participant->insertion }}" disabled>
            </div><br>
        @endif

        <div class="form-group">
            <label for="voornaam">Achternaam*</label>
            <input class="form-control{{ $errors->has('lastName') ? ' is-invalid' : '' }}" value="{{ $confirmationToken->participant->lastName }}" disabled>
        </div><br>

        <div class="form-group">
            <label for="voornaam">Geboortedatum*</label>
            <input class="form-control{{ $errors->has('birthday') ? ' is-invalid' : '' }}" value="{{ old('birthday') }}" type="date" id="birthday" name="birthday" placeholder="MM-DD-JJJJ..." onblur="getAge()">
        </div><br>

        <div class="form-group">
            <label for="voornaam">Email</label>
            <input class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" value="{{ $confirmationToken->participant->email }}" id="email" name="email" placeholder="Email...">
        </div><br>

        <div class="form-group">
            <label for="voornaam">Telefoonnummer</label>
            <input class="form-control{{ $errors->has('phoneNumber') ? ' is-invalid' : '' }}" value="{{ old('phoneNumber') }}" id="phoneNumber" name="phoneNumber" placeholder="Telefoonnummer...">
        </div>
        <br>
        <div id="ShowIfBelow18" style="display: none;">
            <label for="VoornaamVoogd">Voornaam ouder/verzorger*</label>
            <input class="form-control{{ $errors->has('firstNameParent') ? ' is-invalid' : '' }}" value="{{ old('firstNameParent') }}" type="text" id="firstNameParent" name="firstNameParent" placeholder="Voornaam ouder/verzorger...">

            <br>
            <label for="AchternaamVoogd">Achternaam ouder/verzorger*</label>
            <input class="form-control{{ $errors->has('lastNameParent') ? ' is-invalid' : '' }}" value="{{ old('lastNameParent') }}" type="text" id="lastNameParent" name="lastNameParent" placeholder="Achternaam ouder/verzorger...">

            <br>
            <label for="AdresVoogd">Adres ouder/verzorger*</label>
            <input class="form-control{{ $errors->has('adressParent') ? ' is-invalid' : '' }}" value="{{ old('adressParent') }}" type="text" id="addressParent" name="addressParent" placeholder="Adres ouder/verzorger...">

            <br>
            <label for="TelefoonnummerVoogd">Telefoonnummer ouder/verzorger*</label>
            <input class="form-control{{ $errors->has('phoneNumberParent') ? ' is-invalid' : '' }}" value="{{ old('phoneNumberParent') }}" type="text" id="phoneNumberParent" name="phoneNumberParent" placeholder="Telefoonnummer ouder/verzorger...">
        </div>

        <div id="ShowIfAbove18" style="display: none;">
            <label for="VoornaamVoogd">Voornaam contactpersoon*</label>
            <input class="form-control{{ $errors->has('firstNameParent') ? ' is-invalid' : '' }}" value="{{ old('firstNameParent') }}" type="text" id="firstNameContact" name="firstNameContact" placeholder="Voornaam contactpersoon...">

            <br>
            <label for="AchternaamVoogd">Achternaam contactpersoon*</label>
            <input class="form-control{{ $errors->has('lastNameParent') ? ' is-invalid' : '' }}" value="{{ old('lastNameParent') }}" type="text" id="lastNameContact" name="lastNameContact" placeholder="Achternaam contactpersoon...">

            <br>
            <label for="TelefoonnummerVoogd">Telefoonnummer contactpersoon*</label>
            <input class="form-control{{ $errors->has('phoneNumberParent') ? ' is-invalid' : '' }}" value="{{ old('phoneNumberParent') }}" type="text" id="phoneNumberContact" name="phoneNumberContact" placeholder="Telefoonnummer contactpersoon...">
        </div>

        <div class="form-group">
            <label for="voornaam">Allergieën</label>
            <input class="form-control{{ $errors->has('medicalIssues') ? ' is-invalid' : '' }}" value="{{ old('medicalIssues') }}" id="medicalIssues" name="medicalIssues" placeholder="Allergieën...">
        </div><br>

        <div class="form-group">
            <label for="voornaam">Bijzonderheden</label>
            <input class="form-control{{ $errors->has('specials') ? ' is-invalid' : '' }}" value="{{ old('specials') }}" id="specials" name="specials" placeholder="Bijzonderheden...">
        </div><br>

        <div class="form-group mb-5">
            <br>
            <input class="btn btn-primary" type="submit" value="Toevoegen">
        </div>
    </form>
</div>
<script>
    function getAge() {
        var dateString = document.getElementById("birthday").value;
        if(dateString !="")
        {
            var today = new Date();
            var birthDate = new Date(dateString);
            var age = today.getFullYear() - birthDate.getFullYear();
            var month = today.getMonth() - birthDate.getMonth();
            var date = today.getDate() - birthDate.getDate();

            if (month < 0 || (month === 0 && today.getDate() < birthDate.getDate()))
            {
                age--;
            }
            if(month< 0)
            {
                month += 12;
            }
            if(date< 0)
            {
                date += 30;
            }
            if(age < 18 || age > 100)
            {
                document.getElementById("ShowIfBelow18").style.display = "inline";
                document.getElementById("ShowIfAbove18").style.display = "none";
            }
            else
            {
                document.getElementById("ShowIfBelow18").style.display = "none";
                document.getElementById("ShowIfAbove18").style.display = "inline";
            }
        }
    }
</script>
@endsection