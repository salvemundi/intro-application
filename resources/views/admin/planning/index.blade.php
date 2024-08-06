@extends('layouts.app')
@section('content')

<script>
    setActive("events");
</script>
<style>
    .day-column {
        border: 1px solid #ddd;
        min-height: 100vh;
        position: relative; /* Needed for absolute positioning of events */
        display: flex;
        flex-direction: column;
    }
    .hour-row {
        border-bottom: 1px solid #ddd;
        flex: 1; /* Each hour row takes up an equal portion of the column */
        height: 10%; /* 10% of the column height, assuming 10 rows for simplicity */
        min-height: 15px; /* Minimum height for each row */
    }
    .event {
        position: absolute;
        background-color: #007bff;
        color: white;
        padding: 5px;
        border-radius: 5px;
        width: 90%;
        left: 5%;
    }
    .event-monday {
        top: calc(20%); /* Adjusting for border width */
        height: calc(20%); /* Adjusting for border width */
    }
</style>
<div>
    <div class="row">
        <div class="col-12 container">
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
        </div>
    </div>

    </div>
<script>
    const hours = [
        "00:30", "01:00", "01:30", "02:00", "02:30", "03:00", "03:30", "04:00",
        "04:30", "05:00", "05:30", "06:00", "06:30", "07:00", "07:30", "08:00",
        "08:30", "09:00", "09:30", "10:00", "10:30", "11:00", "11:30", "12:00",
        "12:30", "13:00", "13:30", "14:00", "14:30", "15:00", "15:30", "16:00",
        "16:30", "17:00", "17:30", "18:00", "18:30", "19:00", "19:30", "20:00",
        "20:30", "21:00", "21:30", "22:00", "22:30", "23:00", "23:30", "00:00"
    ];

    const timeColumn = document.querySelector('.timetable .timecollum:first-child');
    hours.forEach(hour => {
        const hourDiv = document.createElement('div');
        hourDiv.className = 'hour-row';
        hourDiv.textContent = hour;
        timeColumn.appendChild(hourDiv);
    });

    const days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
    days.forEach(day => {
        const dayColumn = document.getElementById(day);
        hours.forEach(() => {
            const hourDiv = document.createElement('div');
            hourDiv.className = 'hour-row';
            dayColumn.appendChild(hourDiv);
        });
    });

    // good luck everyone
    const agendas = [
        {
            name: "Agenda 1",
            color: "#007bff",
            events: [
                { shift: "pc 1" ,start: "2024-08-05T10:30", end: "2024-08-05T14:00" },
                { shift: "pc 2", start: "2024-08-05T23:00", end: "2024-08-06T02:00" }
            ]
        },
        {
            name: "Agenda 2",
            color: "#28a745",
            events: [
                { shift: "pc 3", start: "2024-08-06T08:00", end: "2024-08-06T10:00" }
            ]
        }
    ];

    function createEventElement(event, color) {
        const eventDiv = document.createElement('div');
        eventDiv.className = 'event';
        eventDiv.style.backgroundColor = color;
        eventDiv.textContent = event.shift;
        // eventDiv.textContent = `${new Date(event.start).toLocaleTimeString([], {hour: '2-digit', minute: '2-digit'})} - ${new Date(event.end).toLocaleTimeString([], {hour: '2-digit', minute: '2-digit'})}`;
        return eventDiv;
    }

    function addEventToDay(dayColumn, eventDiv, start, end) {
        const startHour = new Date(start).getHours() + new Date(start).getMinutes() / 60;
        const endHour = new Date(end).getHours() + new Date(end).getMinutes() / 60;
        const totalHours = 24;
        const top = (startHour / totalHours) * 100;
        const height = ((endHour - startHour) / totalHours) * 100;

        eventDiv.style.top = `${top}%`;
        eventDiv.style.height = `${height}%`;

        dayColumn.appendChild(eventDiv);
    }

    agendas.forEach(agenda => {
        agenda.events.forEach(event => {
            const start = new Date(event.start);
            const end = new Date(event.end);
            const eventDiv = createEventElement(event, agenda.color);

            if (start.getDate() === end.getDate()) {
                const dayColumn = document.getElementById(days[start.getDay() - 1]);
                addEventToDay(dayColumn, eventDiv, event.start, event.end);
            } else {
                const firstDayColumn = document.getElementById(days[start.getDay() - 1]);
                const secondDayColumn = document.getElementById(days[end.getDay() - 1]);

                const firstDayEnd = new Date(event.start);
                firstDayEnd.setHours(23, 59, 59, 999);

                const secondDayStart = new Date(event.end);
                secondDayStart.setHours(0, 0, 0, 0);

                addEventToDay(firstDayColumn, eventDiv.cloneNode(true), event.start, firstDayEnd.toISOString());
                addEventToDay(secondDayColumn, eventDiv, secondDayStart.toISOString(), event.end);
            }
        });
    });


</script>
@endsection
