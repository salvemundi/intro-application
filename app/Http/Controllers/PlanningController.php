<?php

namespace App\Http\Controllers;

use App\Enums\Roles;
use App\Models\Participant;
use App\Models\Shift;
use App\Models\ShiftCategory;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Spatie\IcalendarGenerator\Components\Calendar;
use Spatie\IcalendarGenerator\Components\Event;

class PlanningController extends Controller
{
    public function icalGenerator(Request $request): Response|Application|ResponseFactory
    {
        $calendar = Calendar::create('intro ' . Carbon::now()->year)->refreshInterval(5);;
        $calendarIdsToArray = explode(',',$request->query('calendars'));
        $events = $this->getShifts($calendarIdsToArray);

        foreach($events as $event) {
            if($event instanceof ShiftCategory) {
                foreach($event->shifts as $shift) {
                    $shift->participants = Shift::find($shift->id)->participants;
                    $parents = $shift->participants->map(function($participant) {
                        return $participant->displayName();
                    })->implode(" \n ");
                    $calendar->event(Event::create($shift->name)
                        ->startsAt(Carbon::parse($shift->start_time))
                        ->endsAt(Carbon::parse($shift->end_time))
                        ->description("Dienstleider: " . $event->shiftLeader->displayName() . "\n\n Ouders: \n " . $parents));
                }
            } else {
                $event->participants = Shift::find($shift->id)->participants;
                $parents = $shift->participants->map(function($participant) {
                    return $participant->displayName();
                })->implode(" \n ");
                $calendar->event(Event::create($event->name)
                    ->startsAt(Carbon::parse($event->start_time))
                    ->endsAt(Carbon::parse($event->end_time))
                    ->description("Dienstleider: " . $event->shiftLeader->displayName() . "\n\n Ouders: \n " . $parents));
            }

        }

        return response($calendar->get())->header('Content-Type', 'text/calendar');
    }


    public function index(Request $request): Factory|View|Application
    {
        $parents = Participant::where('role',Roles::dad_mom)->get();
        $categoriesFiltered = $this->getShifts($request->input('shiftsRequested'));
        $shifts = collect();
        if($categoriesFiltered) {
            $categoriesFiltered->each(function ($category) use ($shifts) {
                $category->shifts->each(function ($shift) use ($shifts) {
                    $shift->participants = Shift::find($shift->id)->participants;
                    $shifts->push($shift);
                });
            });
        }
        $parentsAndCrew = Participant::where('role',Roles::dad_mom)->orWhere('role', Roles::crew)->get();
        $categories = ShiftCategory::all();
        $shiftLeaders = Participant::where('role',Roles::crew)->get();
        return view('admin.planning.index')->with([
            'requestedParticipants' => collect($request->input('shiftsRequested')),
            'filteredShifts' => $shifts,
            'requestedShifts' => $this->formatShifts($categoriesFiltered),
            'shifts' => Shift::all()->sortBy('start_time'),
            'parentsAndCrew' => $parentsAndCrew,
            'parents' => $parents,
            'categories' => $categories,
            'overlappingShifts' => $this->detectOverLappedShifts($parentsAndCrew),
            'shiftLeaders' => $shiftLeaders
        ]);
    }

    public function detectOverLappedShifts($participantsToCheck): Collection
    {
        $overlappedShifts = collect();
        $shiftMap = [];

        $participantsToCheck->each(function($participant) use ($overlappedShifts, &$shiftMap) {
            $shifts = $participant->shifts;
            $shifts->each(function($shift) use ($overlappedShifts, &$shiftMap, $participant) {
                $shiftStart = Carbon::parse($shift->start_time);
                $shiftEnd = Carbon::parse($shift->end_time);

                foreach ($shiftMap as $existingShift) {
                    $existingShiftStart = Carbon::parse($existingShift->start_time);
                    $existingShiftEnd = Carbon::parse($existingShift->end_time);

                    if ($shift->id != $existingShift->id) {
                        if (($shiftStart->gt($existingShiftStart) && $shiftStart->lt($existingShiftEnd)) ||
                            ($shiftEnd->gt($existingShiftStart) && $shiftEnd->lt($existingShiftEnd)) ||
                            ($shiftStart->lte($existingShiftStart) && $shiftEnd->gte($existingShiftEnd))) {
                            $overlappedShifts->push([
                                'overlappedShifts' => [$shift->name, $existingShift->name],
                                'participant' => $participant->displayName(),
                            ]);
                            break;
                        }
                    }
                }
                $shiftMap[$shift->id] = $shift;
            });
            $shiftMap = [];
        });
        return $overlappedShifts;
    }

    public function getShifts($requestedShifts)
    {
        if (!$requestedShifts) {
            return [];
        }
        // Fetch categories with related shifts and participants
        $categories = ShiftCategory::with(['shifts.participants', 'shiftLeader'])->get();
        // Filter categories based on whether they match requested categories or contain shifts with requested participants
        $filteredCategories = $categories->filter(function($category) use ($requestedShifts) {
            // Check if the category itself is in the requestedShifts array
            $isCategoryRequested = in_array($category->id, $requestedShifts);
            // Filter shifts based on whether they have any participants in the requestedShifts array
            $filteredShifts = $category->shifts->filter(function($shift) use ($requestedShifts, $isCategoryRequested) {
                if ($isCategoryRequested) {
                    // If the category is requested, return all its shifts
                    return true;
                }
                // Otherwise, filter shifts based on participants
                $shift->participants = $shift->participants->filter(function($participant) use ($requestedShifts) {
                    return in_array($participant->id, $requestedShifts);
                });
                // Keep the shift if it has any participants after filtering
                return $shift->participants->isNotEmpty();
            });
            // Replace the category's shifts with the filtered shifts
            $category->shifts = $filteredShifts;
            // Keep the category if it's requested or has any shifts after filtering
            return $isCategoryRequested || $category->shifts->isNotEmpty();
        });
        return $filteredCategories->values(); // Return the filtered categories with reset keys
    }


    private function formatShifts($shifts)
    {
        if(!$shifts) {
            return [];
        }
        return $shifts->map(function ($category) {
            return [
                'name' => $category->name,
                'color' => $category->color, // Assuming you have a color attribute in ShiftCategory
                'shiftLeader' => $category->shiftLeader->firstName ?? 'Unknown', // Fetch shift leader name from the relationship
                'events' => $category->shifts->map(function ($shift) {
                    return [
                        'id' => $shift->id,
                        'shift' => $shift->name,
                        'start' => Carbon::parse($shift->start_time)->format('Y-m-d\TH:i'), // Format start time as ISO 8601
                        'end' => Carbon::parse($shift->end_time)->format('Y-m-d\TH:i'),     // Format end time as ISO 8601
                    ];
                })->values()->toArray(),
            ];
        })->values()->toArray();
    }
    public function saveShiftCategory(Request $request): RedirectResponse
    {
        $objects = $request->input('objects', []);
        $deletedObjects = json_decode($request->input('deleted_objects', '[]'), true);

        // Handle updating or creating objects
        foreach ($objects as $object) {
            if (!empty($object['id'])) {
                ShiftCategory::updateOrCreate(
                    ['id' => $object['id']],
                    ['name' => $object['name'], 'shift_leader' => $object['shiftLeader'], 'color' => $object['color']]
                );
            } else {
                ShiftCategory::create(
                    ['name' => $object['name'], 'shift_leader' => $object['shiftLeader'], 'color' => $object['color']]
                );
            }
        }

        // Handle deleting objects
        if (!empty($deletedObjects)) {
            ShiftCategory::destroy($deletedObjects);
        }

        return redirect()->back()->with('success', 'Objects updated successfully!');
    }

    public function saveShifts(Request $request): RedirectResponse
    {
        $objects = $request->input('shifts', []);
        $deletedObjects = json_decode($request->input('deleted_shifts', '[]'), true);
        // Handle updating or creating objects
        foreach ($objects as $object) {
            if (!empty($object['id'])) {
                Shift::updateOrCreate(
                    ['id' => $object['id']],
                    ['name' => $object['name'], 'start_time' => $object['start_time'], 'end_time' => $object['end_time'], 'max_participants' => $object['max_participants'], 'shift_cat' => $object['shiftCategory']]
                );
            } else {
                Shift::create(
                    ['name' => $object['name'], 'start_time' => $object['start_time'], 'end_time' => $object['end_time'], 'max_participants' => $object['max_participants'], 'shift_cat' => $object['shiftCategory']]
                );
            }
        }

        // Handle deleting objects
        if (!empty($deletedObjects)) {
            Shift::destroy($deletedObjects);
        }

        return redirect()->back()->with('success', 'Objects updated successfully!');
    }

    public function saveShiftParticipants(Request $request): RedirectResponse
    {
        $objects = $request->input('shiftParticipants', []);
        foreach ($objects as $object) {
            $shift = Shift::find($object['id']);
            $shift->participants()->sync($object['shiftParticipants'] ?? []);
        }
        return redirect()->back()->with('success', 'Participants updated successfully!');
    }
}
