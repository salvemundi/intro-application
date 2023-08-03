@extends('layouts.guapp')
@section('content')
<style>

</style>
<div class="max-width mx-auto">
    @if ($currentEvent != null)
        <div class="mt-2">
            <div class="card mx-2 p-2 px-md-3">
                <div class="row">
                    <div class="col-6">
                        <h4 class="purple">Nu bezig</h4>
                    </div>
                    <div class="col-6">
                        <h4 class="purple float-end">{{ date("H:i", strtotime($currentEvent->beginTime)) }} - {{ date("H:i", strtotime($currentEvent->endTime)) }}</h4>
                        <h5>{{ ucfirst(\Carbon\Carbon::createFromFormat('Y-m-d H:i:s',$currentEvent->beginTime)->locale("nl_NL")->dayName) }}</h5>
{{--                        <p>{{ ucfirst($currentEvent->beginTimeCarbon->locale("nl_NL")->dayName) }}</p>--}}
                    </div>
                </div>
                {{ucfirst($currentEvent->name)}}
            </div>
        </div>
   @endif
        @if ($nextEvent != null)
            <div class="max-width mx-auto">
                @if($currentEvent != null)
                    <div class="card mx-2 p-2 px-md-3 muted">
                @else
                    <div class="card mx-2 p-2 px-md-3">
                @endif
                    <div class="row">
                        <div class="col-6">
                            <h4 class="purple">
                                Volgende event
                            </h4>
                        </div>
                        <div class="col-6">
                            <h4 class="purple float-end">{{ date("H:i", strtotime($nextEvent->beginTime)) }} - {{ date("H:i", strtotime($nextEvent->endTime)) }}</h4>
                            <h5>{{ ucfirst(\Carbon\Carbon::createFromFormat('Y-m-d H:i:s',$nextEvent->beginTime)->locale("nl_NL")->dayName) }}</h5>

                        </div>
                    </div>
                    {{ucfirst($nextEvent->name)}}
                </div>
            </div>
        @endif
        @if($currentEvent == null && $nextEvent == null)
            <p class="text-center mt-4">
                Er is zijn geen activiteiten geplanned
            </p>
        @endif
    </div>


<div class="text-center">
    <a href="#timetable" class="link-qr">Bekijk volledige planning</a>
</div>

<div class="mx-3 my-2 justify-content-center text-center">
    <div class="max-width mx-auto">
        <h2 class="purple">Problemen of vragen?</h2>
        Er is altijd een BHV'er of crewlid ter beschikking om jouw vraag te beantwoorden! Bel bij nood het onderstaande telefoonnummer, in andere gevallen kan je een appje sturen.
        <br><a class="btn btn-primary" href="tel:+31 6 24827777"><i class="fa fa-phone"></i> +31 6 24827777</a>
    </div>
    <div id="timetable" class="mx-3 my-4 justify-content-center text-center">
        <div class="max-width mx-auto">
            <h2 class="purple">Belangrijke WhatsApp groepen</h2>
            <div class="row">
                <div class="col-6">
                    <div class="card p-2 m-0">
                        <a class="link-qr" href="https://chat.whatsapp.com/ENiJQsnAEdB5rob0ql0UpM" target="_blank">Announcements</a>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card p-2 m-0">
                        <a class="link-qr" href="https://chat.whatsapp.com/COL9as6Ik6xDJE7x6P1S0n" target="_blank">Kletsgroep</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="max-width mx-auto my-3">
        <h2 class="purple">Vertrouwenspersonen</h2>
        Wij doen ons uiterste best om de intro zo leuk en veilig mogelijk te maken voor iedere deelnemer. Hier hoort bij dat wij alle deelnemers een mogelijkheid willen geven om in alle vertrouwen contact te kunnen zoeken over iets wat ze dwars zit. Dit kan alles zijn; je klikt niet met de groep, je voelt je onder druk gezet of je bent slachtoffer geworden van ongewenst gedrag. Voor alles wat je dwarszit kan je een van onze contactpersonen benaderen.
        <div class="row justify-content-center mt-4">
            <div class="col-md-6 col-6">
                <img class="imgQR img-fluid teun" src="images/vertrouwensPersonen/teun.jpg">
                <b><p class="vertrouwensPersoonText">Teun Spithoven</p></b>
            </div>
            <div class="col-md-6 col-6">
                <img class="imgQR img-fluid damhuis" src="images/vertrouwensPersonen/rik.jpg">
                <b><p class="vertrouwensPersoonText">Rik Damhuis</p></b>
            </div>
            <div class="col-md-6 col-6">
                <img class="imgQR img-fluid imani" src="images/vertrouwensPersonen/imani.jpg">
                <b><p class="vertrouwensPersoonText">Imani Leemans</p></b>
            </div>
        </div>
    </div>
</div>

<div class="mx-3 my-2 justify-content-center text-center">
    <div class="max-width mx-auto">
        <h2 class="purple">Infoboekje</h2>
        Hieronder kan je ons infoboekje downloaden waarin alle informatie te vinden is voor de intro:
        <br><a class="btn btn-primary mt-2" href="pdf/informatieboekje.pdf" download><i class="fa fa-download"></i> Download</a>
    </div>
</div>

<div class="mx-3 my-2 justify-content-center text-center">
    <div class="max-width mx-auto">
        <h2 class="purple">Aanvragen muziek</h2>
        Is er aangegeven dat je een nummer kan aanvragen? Dan kan dit hier!
        <br><a class="btn btn-primary mt-2" href="/muziek" ><i class="fa fa-music"></i> Vraag aan</a>
    </div>
</div>

<div class="">
    <h2 class="purple text-center ">Planning</h2>
    <div class="">
        <div class="center">
            <ul class="nav nav-tabs"  style="flex-direction: row; float: left" id="myTab" role="tablist">
                @for ($day = $startIntroductionDayNumber; $day <= $endIntroductionDayNumber; $day++)
                    @php
                        $currentDay = \Carbon\Carbon::today()->startOfWeek()->addDays($day);
                    @endphp
                    <li class="nav-item" role="presentation">
                        @if($currentDay->format('l') == \Carbon\Carbon::now()->format('l'))
                            <button class="nav-link active samu-tab" id="{{$currentDay->locale("nl_NL")->dayName}}-tab" data-bs-toggle="tab" data-bs-target="#{{$currentDay->locale("nl_NL")->dayName}}" type="button" role="tab" aria-controls="{{$currentDay->locale("nl_NL")->dayName}}" aria-selected="true">{{substr(ucfirst($currentDay->locale("nl_NL")->dayName),0,2)}}</button>
                        @else
                            <button class="nav-link samu-tab" id="{{$currentDay->locale("nl_NL")->dayName}}-tab" data-bs-toggle="tab" data-bs-target="#{{$currentDay->locale("nl_NL")->dayName}}" type="button" role="tab" aria-controls="{{$currentDay->locale("nl_NL")->dayName}}" aria-selected="true">{{substr(ucfirst($currentDay->locale("nl_NL")->dayName),0,2)}}</button>
                        @endif
                    </li>
                @endfor
            </ul>
        </div>
        <div class="tab-content center mx-auto" id="myTabContent">

            @for ($day = $startIntroductionDayNumber; $day <= $endIntroductionDayNumber; $day++)
                @php
                    $currentDay = \Carbon\Carbon::today()->startOfWeek()->addDays($day);
                @endphp
                @if($currentDay->format('l') == \Carbon\Carbon::now()->format('l'))
                    <div class="tab-pane w-25 fade show active text-black" id="{{ $currentDay->locale('nl_NL')->dayName }}" role="tabpanel" aria-labelledby="home-tab">
                @else
                    <div class="tab-pane w-25 fade show text-black" id="{{ $currentDay->locale('nl_NL')->dayName }}" role="tabpanel" aria-labelledby="home-tab">
                @endif
                    <table class="table table-events table-striped">
                        <tbody>
                        @foreach ($events as $event)
                            @if ($event->beginTimeCarbon->format('l') == $currentDay->format('l'))
                                @if ($event == $currentEvent)
                                    <tr class="currentEvent">
                                        <th class="mytable" style="width: 35%" scope="row">{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s',$event->beginTime)->format('H:i') }} - {{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s',$event->endTime)->format('H:i') }}</th>
                                        <td class="mytable text-left" style="width: 65%">{{$event->name}}</td>
                                    </tr>
                                @else
                                    <tr>
                                        <th class="purple mytable" style="width: 35%" scope="row">{{ date("H:i", strtotime($event->beginTime)) }} - {{ date("H:i", strtotime($event->endTime)) }}</th>
                                        <td class="mytable text-left" style="width: 65%">{{$event->name}}</td>
                                    </tr>
                                @endif
                            @endif
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endfor
        </div>
    </div>

</div>
</div>
<div class="text-center">
    <a href="#video" class="link-qr">Meer informatie vind je onderaan de pagina</a>
</div>


<div class="veryMuchMargin max-width mx-auto">
    <video id="video" class="navImg" autoplay muted loop disablePictureInPicture id="vid">
        <source src="{{asset('/images/rickroll.mp4')}}" type="video/mp4">
        Your browser does not support the video tag.
    </video>
</div>


<!--Bootstrap icons-->
<link rel="stylesheet" href="node_modules/bootstrap-icons/1.7.2/font/bootstrap-icons.min.css">

<!--External library-->
<script src="node_modules/move-js/move.min.js"></script>
<!--https://visionmedia.github.io/move.js/-->

<!--Scrollable libs-->
<link href="node_modules/scrollable-tabs-bootstrap-5/dist/scrollable-tabs.css" rel="stylesheet">
<script src="node_modules/scrollable-tabs-bootstrap-5/dist/scrollable-tabs.js"></script>

@endsection
