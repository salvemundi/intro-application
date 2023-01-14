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
        <h2 class="h2">Fill in the following information</h2>
        <input type="hidden" name="uid" id="uid" value="{{ $confirmationToken->participant->id }}">
        <input type="hidden" name="confirmation" id="confirmation" value="1">
        <div class="form-group">
            <label for="voornaam">Firstname*</label>
            <input class="form-control{{ $errors->has('firstName') ? ' is-invalid' : '' }}" name="firstName" value="{{ $confirmationToken->participant->firstName }}" disabled>
        </div><br>
        @if($confirmationToken->participant->insertion != "" || $confirmationToken->participant->insertion != null)
            <div class="form-group">
                <label for="voornaam">Insertion</label>
                <input class="form-control{{ $errors->has('insertion') ? ' is-invalid' : '' }}" name="insertion" value="{{ $confirmationToken->participant->insertion }}" disabled>
            </div><br>
        @endif

        <div class="form-group">
            <label for="voornaam">Lastname*</label>
            <input class="form-control{{ $errors->has('lastName') ? ' is-invalid' : '' }}" name="lastName" value="{{ $confirmationToken->participant->lastName }}" disabled>
        </div><br>

        <div class="form-group">
            <label for="voornaam">Birthday*</label>
            <input class="form-control{{ $errors->has('birthday') ? ' is-invalid' : '' }}" value="{{ old('birthday') }}" type="date" id="birthday" name="birthday" placeholder="MM-DD-JJJJ..." onblur="getAge()">
        </div><br>

        <div class="form-group">
            <label for="voornaam">Email*</label>
            <input class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" value="{{ $confirmationToken->participant->email }}" id="email" name="email" placeholder="Email...">
        </div><br>

        <div class="form-group">
            <label for="voornaam">Phonenumber*</label>
            <input class="form-control{{ $errors->has('phoneNumber') ? ' is-invalid' : '' }}" value="{{ $confirmationToken->participant->phoneNumber }}" id="phoneNumber" name="phoneNumber" placeholder="Phonenumber...">
        </div><br>

        <div class="form-group">
            <label for="activities">Choose the activities you would like to join</label>
            @foreach($activities as $activity)
                <div class="form-check mt-2">
                    <input class="form-check-input" name="activities[]" value="{{ $activity->id }}" type="checkbox" id="flexCheckDefault{{ $activity->id }}">
                    <label class="form-check-label" for="flexCheckDefault{{ $activity->id }}">
                        {{ $activity->name }}: price: €{{ $activity->price }}
                    </label>
                </div>
            @endforeach
        </div>

        <div class="form-group">
            <label for="voornaam">Allergies</label>
            <textarea class="form-control{{ $errors->has('medicalIssues') ? ' is-invalid' : '' }}" value="{{{ old('medicalIssues') }}}" type="textarea" id="medicalIssues" name="medicalIssues" placeholder="Allergies..."></textarea>
        </div><br>

        <div class="form-group">
            <label for="voornaam">Particularities</label>
            <textarea class="form-control{{ $errors->has('specials') ? ' is-invalid' : '' }}" value="{{{ old('specials') }}}" type="textarea" id="specials" name="specials" placeholder="Particularities..."></textarea>
        </div><br>

        <div class="form-group mb-5">
            <br>
            <input class="btn btn-primary" type="submit" value="Pay">
        </div>
    </form>
</div>
@endsection
