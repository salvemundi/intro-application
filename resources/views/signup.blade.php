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

        </div>
        <div class="col-md-6 px-md-5">
            <div class="box px-md-5 py-3">
                @if(\App\Models\Setting::where('name', 'ToggleFebAndMainIntro')->first()->value == "true")
                <h2 class="mt-3 text-center">Kom je mee op <b class="purple"> februari intro</b>? <br> <b>Schrijf je hier in!</b></h2>

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
                @else
                    <h2 class="mt-3 text-center">Kom je mee op <b class="purple"> intro</b>? <br> <b>Schrijf je hier in!</b></h2>
                    <a href="https://purple.tactile.events/event/purple-introductieweek-2025-ict-nl-eindhoven" class="btn btn-primary center"><b>INSCHRIJVEN <i class="fas fa-sign-in-alt"></i></b></a>
                @endif
            </div>
            <div class="box-purple p-3 mt-5">
                <b>Datum:</b> 25 t/m 29 augustus <br><b>Kosten:</b> 50 euro
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
        @if(\App\Models\Setting::where('name', 'ToggleFebAndMainIntro')->first()->value == "false")
        <hr class="hr">
        <div class="row mb-4">
            <div class="col-12 col-md-6 px-md-5 my-4">
                <h3>Wat is de introductie?</h3>
                Salve Mundi organiseert jaarlijks een introductieweek: De FHICT-introductie.<br> Het is een week vol avontuur en teambuilding in Eindhoven. Zo leer je ook de stad beter kennen.
                Salve Mundi is druk bezig geweest om dit allemaal mogelijk te maken voor de nieuwe studenten dit jaar.<br><br>
                Houd na het inschrijven je mail in de gaten voor updates!<br>
                De introductie duurt 5 dagen. Er is geen slaapmogelijkheid vanuit ons voor de intro. Deze moet je dus zelf regelen.<br>
            </div>
            <div class="col-12 col-md-6 px-md-5 my-4">
                <h3>Wat hebben wij nu voor jullie georganiseerd?</h3>
                Maandag beginnen we op school waar jullie een soort speurtocht doen. Vervolgens gaan we lunchen en richting de Ijzerenman waar we een spellenmiddag hebben met de leukste sprinkussen!
                Als we uitgesport zijn hebben we een BBQ en daarna een feest en een buiten bioscoop. <br>
                Dinsdag beginnen we in de Vue (bioscoop) waar we een Pubquiz hebben voorbereid. Na de Pubquiz gaan we de stad in waar we een Crazy88 gaan doen. Tijdens de crazy88 is het de bedoeling dat je zelf met je groepje eten regelt. Na de Crazy88 gaan we gezamelijk avondeten waarna we een feest hebben bij onze stamkroeg (De Borrelbar) Op stratum. Voor degene die niet van feesten houden hebben we Laserquest afgehuurd waar je kan lasergamen en poolen.<br>
                Woensdag ochtend beginnen we met een sport ochtend. Dit is vanuit Purple geregeld. Naast de spellenmiddag hebben we samen met Proxy (De engelstalige studievereniging voor ICT) een bordspellen / game ochtend op school. In de middag gaan we richting Stadhuisplein waar het Vibes festival georganiseerd is voor MBO, HBO en Universiteit Samen!<br>
                Donderdag gaan we naar het Purple Festival in Tilburg, waar de leukste artiesten optreden en waar alle Fontys studies naartoe gaan. Dit doen we met de bus.<br>
                Op vrijdag gaan we nog met zijn allen en samen met Proxy naar de Efteling! <br>
                Het wordt dus gegarandeerd een leuke week, dus schrijf je vooral in en dan zien we je daar!
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
