@extends('layouts.guapp')
@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-auto col-md-6 col-12 pl-5">
            <h1 class="display-5">Een <b class="purple">intro</b> die je niet kan missen en nooit zal vergeten.</h1>
            <p>
                <ul>
                    <li>Maak kennis met je medestudenten en leg de basis voor levenslange vriendschappen</li>
                    <li>Verken de activiteiten en evenementen die de studievereniging organiseert en leer alles over de faciliteiten en ondersteuning die de vereniging biedt</li>
                    <li>Ontdek de stad en de hotspots waar je als student zeker geweest moet zijn
                    <li>Doe mee aan teambuilding-activiteiten en leer effectief samenwerken met je medestudenten</li>
                    <li>Geniet van lekker eten en drinken, gezelligheid en onvergetelijke feestjes</li>
                    <li>Krijg een voorproefje van wat er allemaal op je af gaat komen tijdens je studententijd</li>
                    <li>Kom vol energie en inspiratie terug voor de start van je studie, klaar om alles uit je studententijd te halen</li>
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
            Op locatie hebben we een bar waar je terecht kunt voor lekkere snacks en tosti's, maar we willen ervoor zorgen dat je tijdens het introductiekamp ook goed gevoed wordt. Daarom zorgen we voor verschillende eetmomenten op verschillende dagen van het kamp.
            Op maandag zorgen we voor zowel lunch als avondeten en op dinsdag kun je bij ons terecht voor ontbijt, lunch en avondeten. Woensdagochtend zorgen we voor een ontbijt, en in de middag gaan we de stad in waar we deelnemen aan een Purple BBQ. Hier kun je natuurlijk ook zelf nog iets te eten kopen.
            Donderdagochtend starten we met een ontbijt en daarna gaan we naar Purple. Hier kun je zowel lunch als avondeten kopen. Vrijdag zorgen we weer voor ontbijt, lunch en avondeten en zaterdagochtend sluiten we af met een lekker ontbijtje.
            We zorgen ervoor dat er voor ieder wat wils is en dat er voldoende eten beschikbaar is gedurende het introductiekamp. Mocht je speciale dieetwensen hebben, laat het ons dan tijdig weten zodat we hier rekening mee kunnen houden. We willen er namelijk voor zorgen dat iedereen lekker en gezond kan eten tijdens het kamp.
            Geniet van het eten en maak er een fantastisch introductiekamp van!
        </div>
    </div>
</div>
@endsection
