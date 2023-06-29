@extends('layouts.app')
@section('content')
<script>
setActive("add");
</script>
<div class="container center">
    <div id="contact" class="col-md-6">
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
        <form action="/add/store" method="post" enctype="multipart/form-data">
            @csrf
            <br>
            <h2 class="h2">Deelnemer toevoegen</h2>

            <div class="form-group">
                <label for="role">Soort persoon*</label>
                <select id="role" class="form-control" name="role" onchange="getRole()">
                    @foreach (\App\Enums\Roles::getInstances() as $item)
                        <option value="{{ $item->value }}">{{$item->description}}</option>
                    @endforeach
                </select><br>
            </div>

            <div class="form-group">
                <label for="voornaam">Voornaam*</label>
                <input class="form-control{{ $errors->has('firstName') ? ' is-invalid' : '' }}" value="{{ old('firstName') }}" id="firstName" name="firstName" placeholder="Voornaam...">
            </div><br>

            <div class="form-group">
                <label for="voornaam">Tussenvoegsel</label>
                <input class="form-control{{ $errors->has('insertion') ? ' is-invalid' : '' }}" value="{{ old('insertion') }}" id="insertion" name="insertion" placeholder="Tussenvoegsel...">
            </div><br>

            <div class="form-group">
                <label for="voornaam">Achternaam*</label>
                <input class="form-control{{ $errors->has('lastName') ? ' is-invalid' : '' }}" value="{{ old('lastName') }}" id="lastName" name="lastName" placeholder="Achternaam...">
            </div><br>

            <div class="form-group">
                <label for="voornaam">Geboortedatum*</label>
                <input class="form-control{{ $errors->has('birthday') ? ' is-invalid' : '' }}" value="{{ old('birthday') }}" type="date" id="birthday" name="birthday" placeholder="MM-DD-JJJJ...">
            </div><br>

            <div class="form-group">
                    <label for="voornaam">Email</label>
                    <input class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" value="{{ old('email') }}" id="email" name="email" placeholder="Email...">
            </div><br>

            <div class="form-group">
                <label for="fontysEmail">Je fontys email adres</label>
                <input class="form-control{{ $errors->has('fontysEmail') ? ' is-invalid' : '' }}" value="{{ old('fontysEmail') }}" id="fontysEmail" name="fontysEmail" placeholder="123456@student.fontys.nl...">
            </div>

            <div class="form-group">
                    <label for="voornaam">Telefoonnummer</label>
                    <input class="form-control{{ $errors->has('phoneNumber') ? ' is-invalid' : '' }}" value="{{ old('phoneNumber') }}" id="phoneNumber" name="phoneNumber" placeholder="Telefoonnummer...">
            </div><br>

            <div id="child" style="display: none;">
                <label for="StudyType">Leervorm*</label>
                <div class="form-group">
                    <select class="form-control" name="studyType">
                        @foreach (App\Enums\StudyType::getInstances() as $item)
                            <option value="{{ $item->value }}">{{$item->description}}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="voornaam">Voornaam Ouder</label>
                    <input class="form-control{{ $errors->has('firstNameParent') ? ' is-invalid' : '' }}" value="{{ old('firstNameParent') }}" id="firstNameParent" name="firstNameParent" placeholder="Voornaam Ouder...">
                </div><br>

                <div class="form-group">
                    <label for="voornaam">Achternaam Ouder</label>
                    <input class="form-control{{ $errors->has('lastNameParent') ? ' is-invalid' : '' }}" value="{{ old('lastNameParent') }}" id="lastNameParent" name="lastNameParent" placeholder="Achternaam Ouder...">
                </div><br>

                <div class="form-group">
                    <label for="voornaam">Adres Ouder</label>
                    <input class="form-control{{ $errors->has('addressParent') ? ' is-invalid' : '' }}" value="{{ old('addressParent') }}" id="addressParent" name="addressParent" placeholder="Adres Ouder...">
                </div><br>

                <div class="form-group">
                    <label for="voornaam">Telefoon Ouder</label>
                    <input class="form-control{{ $errors->has('phoneNumberParent') ? ' is-invalid' : '' }}" value="{{ old('phoneNumberParent') }}" id="phoneNumberParent" name="phoneNumberParent" placeholder="Titel...">
                </div><br>
            </div>
            <div id="dad_mom" style="display: none;">
            </div>

            <div class="form-group">
                <label for="voornaam">Allergieën</label>
                <textarea class="form-control{{ $errors->has('medicalIssues') ? ' is-invalid' : '' }}" value="{{{ old('medicalIssues') }}}" type="textarea" id="medicalIssues" name="medicalIssues" placeholder="Allergieën..."></textarea>
            </div><br>

            <div class="form-group">
                <label for="voornaam">Bijzonderheden</label>
                <textarea class="form-control{{ $errors->has('specials') ? ' is-invalid' : '' }}" value="{{{ old('specials') }}}" type="textarea" id="specials" name="specials" placeholder="Bijzonderheden..."></textarea>
            </div><br>

            <div class="form-group">
                <label for="voornaam">Checkedin*</label>
                <select class="form-control" name="checkedIn">
                    <option value="1">Check in</option>
                    <option value="0">Check niet in</option>
                </select>
            </div>

            <div class="form-group mb-5">
                <br>
                <input class="btn btn-primary" type="submit" value="Toevoegen">
            </div>
        </form>
    </div>
</div>
<script>
    function getRole() {
        var role = document.getElementById("role").value;
        if(role == {{ App\Enums\Roles::coerce("child")->value }})
        {
            document.getElementById("child").style.display = "inline";
            document.getElementById("dad_mom").style.display = "none";
        }
        else if(role == {{ App\Enums\Roles::coerce("dad_mom")->value }})
        {
            document.getElementById("child").style.display = "none";
            document.getElementById("dad_mom").style.display = "inline";
        }
        else
        {
            document.getElementById("child").style.display = "none";
            document.getElementById("dad_mom").style.display = "none";
        }
    }

    getRole();
</script>
@endsection
