<?php

namespace App\Http\Controllers;

use App\Enums\AuditCategory;
use App\Models\Setting;
use App\Models\ShiftCategory;
use App\Models\Schedule;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Carbon\Carbon;
use DateTimeZone;

class ScheduleController extends Controller
{
    public function index(): Factory|View|Application|RedirectResponse
    {
        $planningPage = Setting::where('name', 'PlanningPage')->first();
        if (!$planningPage || $planningPage->value === 'false') {
            return redirect('/');
        }

        $category = ShiftCategory::where('name', 'LIKE', 'alg')->first();
        if (!$category) {
            // fallback: geen events gevonden
            return view('qr-code', [
                'events' => collect(),
                'currentEvent' => null,
                'nextEvent' => null,
                'startIntroductionDayNumber' => null,
                'endIntroductionDayNumber' => null,
            ]);
        }

        $events = $category->shifts()->orderBy('start_time', 'ASC')->get();

        $time = Carbon::now(new DateTimeZone('Europe/Amsterdam'));
        $currentEvent = null;

        foreach ($events as $event) {
            $eventBeginTime = $this->parseCarbon($event->start_time);
            $eventEndTime   = $this->parseCarbon($event->end_time);

            // kleine aanpassing voor uren < 7
            if ($eventBeginTime && $eventBeginTime->hour < 7) {
                $eventBeginTime->subDay();
            }
            if ($eventEndTime && $eventEndTime->hour < 7) {
                $eventEndTime->subDay();
            }

            $event->beginTimeCarbon = $eventBeginTime;
            $event->endTimeCarbon   = $eventEndTime;

            if ($eventBeginTime && $eventEndTime && $eventBeginTime <= $time && $eventEndTime >= $time) {
                $currentEvent = $event;
            }
        }

        // sorteersleutel defensief
        $sortedEvents = $events->sortBy(function ($obj) {
            $start = $this->parseCarbon($obj->start_time);
            return $start ? $start->startOfDay() : Carbon::minValue();
        });

        $startIntroSetting = Setting::where('name','DaysTillIntro')->first();
        $endIntroSetting   = Setting::where('name','EndIntroDate')->first();

        $startIntroductionDayNumber = $startIntroSetting ? $this->parseCarbon($startIntroSetting->value)->dayOfWeek - 1 : null;
        $endIntroductionDayNumber   = $endIntroSetting   ? $this->parseCarbon($endIntroSetting->value)->dayOfWeek - 1 : null;

        return view('qr-code', [
            'events'                     => $sortedEvents,
            'currentEvent'               => $currentEvent,
            'nextEvent'                  => $this->getNextEvent($category),
            'startIntroductionDayNumber' => $startIntroductionDayNumber,
            'endIntroductionDayNumber'   => $endIntroductionDayNumber,
        ]);
    }

    private function parseCarbon(?string $datetime): ?Carbon
    {
        try {
            return $datetime ? Carbon::createFromFormat('Y-m-d H:i:s', $datetime) : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    private function getNextEvent(ShiftCategory $category): ?Schedule
    {
        $currentDateTime = Carbon::now();
        $events = $category->shifts()->orderBy('start_time', 'ASC')->get();

        foreach ($events as $event) {
            $eventEndTime = $this->parseCarbon($event->end_time);
            if ($eventEndTime && $currentDateTime < $eventEndTime) {
                return $event;
            }
        }

        return null;
    }

    public function getAllEvents(): Factory|View|Application
    {
        $shiftCategory = ShiftCategory::where('name', 'LIKE', 'alg')->first();
        $schedules = $shiftCategory ? $shiftCategory->shifts()->get() : collect();

        return view('admin/schedule', ['events' => $schedules]);
    }

    public function saveEvent(Request $request): Redirector|Application|RedirectResponse
    {
        $request->validate([
            'name'        => 'required',
            'description' => 'required',
            'beginTime'   => 'required|date_format:Y-m-d\TH:i',
            'endTime'     => 'required|date_format:Y-m-d\TH:i',
        ]);

        $event = $request->input('eventId') ? Schedule::find($request->input('eventId')) : new Schedule;

        if (!$event) {
            return redirect('/events')->with('error', 'Event kon niet gevonden worden!');
        }

        $event->name        = $request->input('name');
        $event->description = $request->input('description');
        $event->beginTime   = $request->input('beginTime');
        $event->endTime     = $request->input('endTime');

        $event->save();

        $action = $request->input('eventId') ? 'Event aangepast' : 'Nieuw event aangemaakt';
        AuditLogController::Log(AuditCategory::ScheduleManagement(), $action, null, null, null, $event);

        return redirect('/events')->with('success', 'Event is opgeslagen!');
    }

    public function showEventInputs(Request $request): Factory|View|Application
    {
        $event = $request->eventId ? Schedule::find($request->eventId) : null;
        return view('admin/addSchedule', ['event' => $event]);
    }

    public function deleteEvent(Request $request): Redirector|Application|RedirectResponse
    {
        if (!$request->eventId) {
            return redirect('/events')->with('error', 'Er ging iets niet helemaal goed, probeer het later nog een keer.');
        }

        $event = Schedule::find($request->eventId);
        if (!$event) {
            return redirect('/events')->with('error', 'Event kon niet gevonden worden!');
        }

        $event->delete();
        AuditLogController::Log(AuditCategory::ScheduleManagement(), 'Event verwijderd', null, null, null, $event);

        return redirect('/events')->with('success', 'Event is verwijderd!');
    }
}
