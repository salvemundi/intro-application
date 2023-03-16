@extends('layouts.guapp')
@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-auto col-md-6 col-12 pl-5">
            <h1 class="display-5">Een <b class="purple">intro</b> die je niet kan missen en nooit zal vergeten.</h1>
            <p>
                <ul>
                    <li>
                        Avontuur en teambuilding
                    </li>
                    <li>
                        Alcohol en bier (18+) & frisdrank te koop
                    </li>
                    <li>
                        Lunch & avond eten worden voor gezorgd
                    </li>
                </ul>
            </p>
        <div class="box-purple p-3 mb-3">
            <b>Datum:</b> 22 t/m 26 augustus <br><b>Kosten:</b> wordt nader bepaald
        </div>
        </div>
        <div class="col-md-6 px-md-5">
            <div class="box px-md-5 py-3">
                <h2 class="mt-3 text-center">Kom je mee op <b class="purple">intro</b>? <br> <b>Schrijf je hier in!</b></h2>

                <div class="mb-3">
                    <form action="/inschrijven" method="post">
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

                        @if(session()->has('error'))
                            <div class="alert alert-danger">
                                {{ session()->get('error') }}
                            </div>
                        @endif

                        <label for="firstName" class="mt-2">Voornaam</label>
                        <input type="text" class="form-control{{ $errors->has('firstName') ? ' is-invalid' : '' }}" value="{{ old('firstName') }}" name="firstName" id="firstName" placeholder="Voornaam">

                        <label for="insertion" class="mt-2">Tussenvoegsel</label>
                        <input type="text" class="form-control{{ $errors->has('insertion') ? ' is-invalid' : '' }}" value="{{ old('insertion') }}" name="insertion" id="insertion" placeholder="Tussenvoegsel">

                        <label for="lastName" class="mt-2">Achternaam</label>
                        <input type="text" class="form-control{{ $errors->has('lastName') ? ' is-invalid' : '' }}" value="{{ old('lastName') }}" name="lastName" id="lastName" placeholder="Achternaam">

                        <label for="email" class="mt-2">Email</label>
                        <input type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" value="{{ old('email') }}" name="email" id="email" placeholder="name@example.com">

                        <label for="phoneNumber" class="mt-2">Telefoonnummer</label>
                        <input type="text" minlength="10" maxlength="15" class="form-control{{ $errors->has('phoneNumber') ? ' is-invalid' : '' }}" max="15" value="{{ old('phoneNumber') }}" name="phoneNumber" id="phoneNumber" placeholder="0612345678">

                        <button data-bs-toggle="tooltip" data-bs-placement="right" title="Tooltip on right" class="btn btn-primary my-3 w-100" type="submit">Inschrijven</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="imgSlider my-5 mx-auto" id="imgSlider" data-slick='{"slidesToShow": 1, "slidesToScroll": 1}'>
            <div>
                <img class="imgIndex" src="images/introFotos/Intro2021-080.jpg">
            </div>
            <div>
                <img class="imgIndex" src="images/introFotos/Intro2021-101.jpg">
            </div>
            <div>
                <img class="imgIndex" src="images/introFotos/Intro2021-143.jpg">
            </div>
            <div>
                <img class="imgIndex" src="images/introFotos/Intro2021-193.jpg">
            </div>
        </div>
        <script>
            // $('.imgSlider').slick();
        </script>
        <hr class="hr">

        <div class="col-12 col-md-6 px-md-5 my-4">
            <h3>Wat is de introductie?</h3>
            Salve Mundi organiseert jaarlijks een introductieweek: De FHICT-introductie.<br> Het is een week vol avontuur en teambuilding in Eindhoven. Zo leer je ook de stad beter kennen.
            Salve Mundi is druk bezig geweest om dit allemaal mogelijk te maken voor de nieuwe studenten dit jaar.<br><br>
            Houd na het inschrijven je mail in de gaten voor updates, je zult later namelijk een mail ontvangen met daarin de betalingsdetails en aanvullende informatie!<br>
            De introductie duurt 5 dagen. Op de locatie is een grote evenementenzaal met bar waar zowel alcohol (18+) als frisdrank verkocht zal worden door middel van consumptiebonnen. De locatie bevindt zich bij een bosrand en een mooi open veld. Genoeg ruimte voor activiteiten dus.<br>
        </div>
        <div class="col-12 col-md-6 px-md-5 my-4">
            <h3>Wat hebben wij nu voor jullie georganiseerd?</h3>
            De introductie van Salve Mundi bestaat uit een aantal dagen vol informatie, spellen en te gekke feesten op onze vette intro locatie.<br><br>
            Wat je kan verwachten is een spelletjes middag om al je studiegenoten te leren kennen en vette feesten in onze evenementezaal.
            Op woensdag gaan we naar Fontys voor alle nodige informatie over de studie en daarna gaan we de stad in om Eindhoven te leren kennen doormiddel van een Crazy88.
            Op donderdag gaan we naar het Purple Festival. Hier zullen we de hele dag lekker gaan feesten, relaxen en genieten voordat we 's avonds de dag zullen afsluiten met een dikke afterparty op onze intro locatie.<br><br>
            Tijdens de introductie zal er doorgaans voor ontbijt, lunch en avondeten worden gezorgd. Op woensdag en donderdag zal het avondeten zelf moeten worden verzorgd.
        </div>
    </div>
</div>
@endsection
