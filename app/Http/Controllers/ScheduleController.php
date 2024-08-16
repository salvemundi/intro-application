<?php

namespace App\Http\Controllers;

use App\Enums\AuditCategory;
use App\Models\Setting;
use App\Models\ShiftCategory;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\Schedule;
use Carbon\Carbon;
use DateTimeZone;
use Illuminate\Routing\Redirector;

class ScheduleController extends Controller
{
    public function index(): Factory|View|Application|RedirectResponse
    {
        if(Setting::where('name','PlanningPage')->first()->value == 'false') {
            return redirect('/');
        }
        $events = ShiftCategory::where('name','LIKE','alg')->first()->shifts()->orderBy('start_time', 'ASC')->get();
        $time = Carbon::now();
        $time->tz = new DateTimeZone('Europe/Amsterdam');
        $currentEvent = null;
        foreach ($events as $event)
        {
            $eventBeginTime = Carbon::createFromFormat('Y-m-d H:i:s',$event->start_time);
            $eventEndTime = Carbon::createFromFormat('Y-m-d H:i:s',$event->end_time);

            if ($eventBeginTime->hour < 7) {
                $eventBeginTime->subDay();
            }
            if ($eventEndTime->hour < 7) {
                $eventEndTime->subDay();
            }
            $event->beginTimeCarbon = $eventBeginTime;
            $event->endTimeCarbon = $eventEndTime;

            if ($event->beginTime <= $time)
            {
                if($event->endTime >= $time)
                {
                    $currentEvent = $event;
                }
            }
        }
        $sortedEvents = $events->sortBy(function ($obj, $key) {
            return  Carbon::createFromFormat('Y-m-d H:i:s',$obj->start_time)->startOfCustomDay();
        });
        $startIntroductionDayNumber = Carbon::createFromFormat('Y-m-d H:i:s',Setting::where('name','DaysTillIntro')->first()->value);
        $endIntroductionDayNumber = Carbon::createFromFormat('Y-m-d H:i:s',Setting::where('name','EndIntroDate')->first()->value);
        return view('qr-code', ['events' => $sortedEvents, 'currentEvent' => $currentEvent, 'nextEvent' => $this->getNextEvent(),'startIntroductionDayNumber' => $startIntroductionDayNumber->dayOfWeek - 1,'endIntroductionDayNumber' => $endIntroductionDayNumber->dayOfWeek - 1]);
    }

    private function getNextEvent() {
        $currentDateTime = Carbon::now();
        $events = ShiftCategory::where('name','LIKE','alg')->first()->shifts()->orderBy('start_time', 'ASC')->get();

        foreach($events as $event) {
            if($currentDateTime < Carbon::createFromFormat('Y-m-d H:i:s',$event->end_time)) {
                return $event;
            }
        }
        return null;
    }

    public function getAllEvents(): Factory|View|Application
    {
        $schedules = ShiftCategory::where('name','LIKE','alg')->first()->shifts()->get();
        return view('admin/schedule', ['events' => $schedules]);
    }

    public function saveEvent(Request $request): Redirector|Application|RedirectResponse
    {
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'beginTime' => 'required', 'date_format:Y-m-d\TH:i',
            'endTime' => 'required', 'date_format:Y-m-d\TH:i',
        ]);
        $new = true;
        if($request->input('eventId')) {
            $new = false;
            $event = Schedule::find($request->input('eventId'));
        } else {
            $event = new Schedule;
        }

        $event->name =  $request->input('name');
        $event->description =  $request->input('description');
        $event->beginTime =  $request->input('beginTime');
        $event->endTime =  $request->input('endTime');

        $event->save();
        if($new) {
            AuditLogController::Log(AuditCategory::ScheduleManagement(), 'Nieuw event aangemaakt',null, null, null, $event);
        } else {
            AuditLogController::Log(AuditCategory::ScheduleManagement(), 'Event aangepast',null, null, null, $event);
        }
        return redirect('/events')->with('success', 'Event is opgeslagen!');
    }


    public function showEventInputs(Request $request): Factory|View|Application
    {
        $event = null;
        if($request->eventId){
            $event = Schedule::find($request->eventId);
        }
        return view('admin/addSchedule',['event' => $event]);
    }

    public function deleteEvent(Request $request): Redirector|Application|RedirectResponse
    {
        if($request->eventId) {
            $event = Schedule::find($request->eventId);
            if($event != null) {
                $event->delete();
                AuditLogController::Log(AuditCategory::ScheduleManagement(), 'Event verwijderd',null, null, null, $event);
                return redirect('/events')->with('success', 'Event is verwijderd!');
            }
            return redirect('/events')->with('error', 'Event kon niet gevonden worden!');

        }
        return redirect('/events')->with('error', 'Er ging iets niet helemaal goed, probeer het later nog een keer.');
    }
}
