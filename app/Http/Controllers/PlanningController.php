<?php

namespace App\Http\Controllers;

use App\Enums\Roles;
use App\Models\Participant;
use App\Models\Shift;
use App\Models\ShiftCategory;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PlanningController extends Controller
{
    public function index(Request $request): Factory|View|Application
    {
        $categoriesFiltered = $this->getShifts($request->input('shiftsRequested'));
        $shifts = collect();
        $categoriesFiltered->each(function($category) use ($shifts) {
            $category->shifts->each(function($shift) use ($shifts) {
                $shift->participants = Shift::find($shift->id)->participants;
                $shifts->push($shift);
            });
        });
        $parents = Participant::where('role',Roles::dad_mom)->get();
        $categories = ShiftCategory::all();
        $shiftLeaders = Participant::where('role',Roles::crew)->get();
        return view('admin.planning.index')->with([
            'requestedParticipants' => collect($request->input('shiftsRequested')),
            'filteredShifts' => $shifts,
            'requestedShifts' => $this->formatShifts($categoriesFiltered),
            'shifts' => Shift::all(),
            'parents' => $parents,
            'categories' => $categories,
            'shiftLeaders' => $shiftLeaders
        ]);
    }

    public function getShifts($requestedShifts)
    {
        if(!$requestedShifts) {
            return [];
        } else {
            // Fetch categories with related shifts and participants
            $categories = ShiftCategory::with(['shifts.participants', 'shiftLeader'])->get();
            // Filter categories based on whether they match requested categories or contain shifts with requested participants
            $filteredCategories = $categories->filter(function($category) use ($requestedShifts) {
                // Check if the category itself is in the requestedShifts array
                $isCategoryRequested = in_array($category->id, $requestedShifts);
                // Filter shifts based on whether they have any participants in the requestedShifts array
                $filteredShifts = $category->shifts->filter(function($shift) use ($requestedShifts) {
                    // Filter participants for the shift
                    $shift->participants = $shift->participants->filter(function($participant) use ($requestedShifts) {
                        return in_array($participant->id, $requestedShifts);
                    });
                    // Only keep the shift if it has any participants after filtering
                    return $shift->participants->isNotEmpty();
                });
                // Keep the category only if it is in requestedShifts or has any shifts after filtering
                return $isCategoryRequested || $filteredShifts->isNotEmpty();
            });
            return $filteredCategories;
        }
    }

    private function formatShifts($shifts)
    {
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
                })->toArray(),
            ];
        })->values()->toArray();
    }
    // look at my web.php file and implement the rest of the routes under the // Planning comment
    public function showShiftCategory($id): Factory|View|Application
    {
        return view('planning.shiftCategory')->with('shiftCategory', ShiftCategory::find($id));
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

    public function deleteShiftCategory()
    {

    }

    public function savePlanning()
    {

    }

    public function deletePlanning()
    {

    }

    public function addParticipants()
    {

    }

    public function removeParticipants()
    {

    }

    public function addAllParticipants()
    {

    }

    public function removeAllParticipants()
    {

    }
}
