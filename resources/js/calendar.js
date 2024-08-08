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

function createEventElement(event, agenda) {
    const eventDiv = document.createElement('div');
    eventDiv.className = 'event';
    eventDiv.style.backgroundColor = agenda.color;
    eventDiv.style.whiteSpace = 'pre';
    eventDiv.textContent = `${new Date(event.start).toLocaleTimeString([], {hour: '2-digit', minute: '2-digit'})} - ${new Date(event.end).toLocaleTimeString([], {hour: '2-digit', minute: '2-digit'})}` + "\r\n" + event.shift + "\r\nContact: " + agenda.shiftLeader;
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

function handleOverlappingEvents(dayColumn) {
    const events = dayColumn.querySelectorAll('.event');
    if (events.length === 0) return;

    let levels = [];
    events.forEach(event => {
        let placed = false;
        for (let i = 0; i < levels.length; i++) {
            if (!isOverlapping(event, levels[i])) {
                levels[i].push(event);
                placed = true;
                break;
            }
        }
        if (!placed) {
            levels.push([event]);
        }
    });

    levels.forEach(level => {
        const width = 100 / level.length;
        level.forEach((event, index) => {
            event.style.width = `${width}%`;
            event.style.left = `${width * index}%`;
        });
    });
}

function isOverlapping(event, level) {
    const rect1 = event.getBoundingClientRect();
    for (let i = 0; i < level.length; i++) {
        const rect2 = level[i].getBoundingClientRect();
        if (!(rect1.right <= rect2.left || rect1.left >= rect2.right || rect1.bottom <= rect2.top || rect1.top >= rect2.bottom)) {
            return false;
        }
    }
    return true;
}
agendas.forEach(agenda => {
    agenda.events.forEach(event => {
        const start = new Date(event.start);
        const end = new Date(event.end);
        const eventDiv = createEventElement(event, agenda);

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
days.forEach(day => {
    const dayColumn = document.getElementById(day);
    handleOverlappingEvents(dayColumn);
});
