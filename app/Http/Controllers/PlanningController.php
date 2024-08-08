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
        $shifts = $this->getShifts($request->input('shiftsRequested'));
        $parents = Participant::where('role',Roles::dad_mom)->get();
        $categories = ShiftCategory::all();
        $shiftLeaders = Participant::where('role',Roles::crew)->get();
        return view('admin.planning.index')->with([
            'requestedParticipants' => collect($request->input('shiftsRequested')),
            'requestedShifts' => $shifts,
            'shifts' => Shift::all(),
            'parents' => $parents,
            'categories' => $categories,
            'shiftLeaders' => $shiftLeaders
        ]);
    }

    public function getShifts($requestedShifts): array
    {
        if(!$requestedShifts) {
            return [];
        } else {
            $categories = ShiftCategory::all();
            $collection = $categories->each(function($category) use ($requestedShifts) {
                // Filter the shifts based on whether they have any participants in the requestedShifts array
                $category->shifts = $category->shifts->filter(function($shift) use ($requestedShifts) {
                    // Filter participants for the shift
                    $shift->participants = $shift->participants->filter(function($participant) use ($requestedShifts) {
                        return in_array($participant->id, $requestedShifts);
                    });

                    // Only keep the shift if it has any participants after filtering
                    return $shift->participants->isNotEmpty();
                });
            });
            $collection = $collection->map(function ($category) {
                return [
                    'name' => $category->name,
                    'color' => $category->color, // Assuming you have a color attribute in ShiftCategory
                    'shiftLeader' => $category->shiftLeader->firstName ?? 'Unknown', // Fetch shift leader name from the relationship
                    'events' => $category->shifts->map(function ($shift) {
                        return [
                            'shift' => $shift->name,
                            'start' => Carbon::parse($shift->start_time)->format('Y-m-d\TH:i'), // Format start time as ISO 8601
                            'end' => Carbon::parse($shift->end_time)->format('Y-m-d\TH:i'),     // Format end time as ISO 8601
                        ];
                    })->toArray(),
                ];
            })->toArray();
            return $collection;
        }

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
