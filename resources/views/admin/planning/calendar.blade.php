<link href="{{ mix('css/calendar.css') }}" rel="stylesheet">

<div class="container-fluid">
    <div class="timetable row text-center">
        <div class=" col">Time</div>
        <div class=" col">Monday</div>
        <div class=" col">Tuesday</div>
        <div class=" col">Wednesday</div>
        <div class=" col">Thursday</div>
        <div class=" col">Friday</div>
        <div class=" col">Saturday</div>
        <div class=" col">Sunday</div>
    </div>
    <div class="timetable row">
        <div class="timecollum col day-column">
            <!-- Time slots -->

        </div>
        <!-- Day columns -->
        <div class=" col day-column" id="monday">
        </div>
        <div class="timecollum col day-column" id="tuesday">

        </div>
        <div class="timecollum col day-column" id="wednesday">

        </div>
        <div class="timecollum col day-column" id="thursday">

        </div>
        <div class="timecollum col day-column" id="friday">

        </div>
        <div class="timecollum col day-column" id="saturday">

        </div>
        <div class="timecollum col day-column" id="sunday">

        </div>
    </div>
</div>
<script src="{{ mix('js/calendar.js') }}"></script>

