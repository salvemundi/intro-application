@extends('layouts.guapp')
@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-auto col-md-6 col-12 pl-5">
            <h1 class="display-5">Het <b class="purple">Purple Festival</b></h1>
            <p style="text-align: left; white-space: pre-line"> Beste nieuwe student!
                De kans is groot dat je op de verkeerde pagina zit om je in te schrijven voor de introductie week! Als jij je namelijk <b><a href="/">hier</a></b> inschrijft dan draai je de volledige week activiteiten, kennismakingen, feesten en gezelligheid mee samen met studie vereniging <b class="purple">Salve Mundi</b>!

                Mocht je toch alleen op de donderdag aanwezig willen zijn bij het Purple festival, dan kun je hier je studenten nummer achterlaten die je als het goed is per mail van Fontys hebt ontvangen! Je zult dan later van ons een mail ontvangen met je (gratis) purple ticket!
            </p>
        <div class="box-purple p-3 mb-3">
            <b>Datum:</b> 25 augustus 14:00 - 23:00
        </div>
        </div>
        <div class="col-md-6 px-md-5">
            <div class="box px-md-5 py-3">
                <h2 class="mt-3 text-center">Kom je mee naar <b class="purple">Purple Festival</b>? <br> <b>Schrijf je hier in!</b></h2>

                <div class="mb-3">
                    <form action="/purpleInschrijven" method="post">
                        @csrf

                        @if(session()->has('message'))
                            <div class="alert alert-primary">
                                {{ session()->get('message') }}
                            </div>
                        @endif

                        @if(session()->has('warning'))
                            <div class="alert alert-danger">
                                {{ session()->get('warning') }}
                            </div>
                        @endif
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <label for="firstName" class="form-label">Naam*</label>
                        <input class="form-control{{ $errors->has('firstName') ? ' is-invalid' : '' }}" value="{{ old('firstName') }}" id="firstName" name="firstName" placeholder="Naam...">

                        <label for="insertion" class="form-label">Tussenvoegsel</label>
                        <input class="form-control{{ $errors->has('insertion') ? ' is-invalid' : '' }}" value="{{ old('insertion') }}" id="insertion" name="insertion" placeholder="Tussenvoegsel...">

                        <label for="lastName" class="form-label">Achternaam*</label>
                        <input class="form-control{{ $errors->has('lastName') ? ' is-invalid' : '' }}" value="{{ old('lastName') }}" id="lastName" name="lastName" placeholder="Achternaam...">

                        <label for="fontysEmail" class="form-label">Je fontys email adres*</label>
                        <input class="form-control{{ $errors->has('fontysEmail') ? ' is-invalid' : '' }}" value="{{ old('fontysEmail') }}" id="fontysEmail" name="fontysEmail" placeholder="123456@student.fontys.nl...">

                        <label for="email" class="form-label">Persoonlijke email*</label>
                        <input type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" value="{{ old('email') }}" name="email" id="email" placeholder="name@example.com">

                        <button class="btn btn-primary my-3 w-100" type="submit">Inschrijven</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</dsiv>
@endsection
