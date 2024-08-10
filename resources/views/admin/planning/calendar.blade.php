<link href="{{ mix('css/calendar.css') }}" rel="stylesheet">
@foreach($filteredShifts as $shift)
    @include('admin.planning.modals.calInfo', ['event' => $shift])
@endforeach
<style>
    .container-fluid {
        overflow-x: auto;
        white-space: nowrap; /* Prevents wrapping of columns */
    }

    .timetable {
        display: flex; /* Aligns children in a row */
        min-width: 1000px; /* Adjust as needed */
    }

    .col {
        flex: 0 0 auto; /* Prevents columns from shrinking */
    }

    .day-column {
        padding: 10px; /* Optional: Add padding for spacing */
    }
</style>
<div class="container-fluid">
    <div class="timetable-wrapper" style="overflow-x: auto;">
        <div class="timetable d-flex justify-content-between" style="min-width: 1200px;">
            <div class="col text-center" style="flex: 1 0 auto;">Time</div>
            <div class="col text-center" style="flex: 1 0 auto;">Monday</div>
            <div class="col text-center" style="flex: 1 0 auto;">Tuesday</div>
            <div class="col text-center" style="flex: 1 0 auto;">Wednesday</div>
            <div class="col text-center" style="flex: 1 0 auto;">Thursday</div>
            <div class="col text-center" style="flex: 1 0 auto;">Friday</div>
            <div class="col text-center" style="flex: 1 0 auto;">Saturday</div>
            <div class="col text-center" style="flex: 1 0 auto;">Sunday</div>
        </div>
        <div class="timetable d-flex justify-content-between" style="min-width: 1200px;">
            <div class="timecollum col day-column" style="flex: 1 0 auto;">
                <!-- Time slots -->
            </div>
            <!-- Day columns -->
            <div class="col day-column" id="monday" style="flex: 1 0 auto;"></div>
            <div class="col day-column" id="tuesday" style="flex: 1 0 auto;"></div>
            <div class="col day-column" id="wednesday" style="flex: 1 0 auto;"></div>
            <div class="col day-column" id="thursday" style="flex: 1 0 auto;"></div>
            <div class="col day-column" id="friday" style="flex: 1 0 auto;"></div>
            <div class="col day-column" id="saturday" style="flex: 1 0 auto;"></div>
            <div class="col day-column" id="sunday" style="flex: 1 0 auto;"></div>
        </div>
    </div>
</div>
<script>
    const agendas = @json($requestedShifts);
</script>
<script src="{{ mix('js/calendar.js') }}"></script>

