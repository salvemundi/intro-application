<?php

namespace App\Http\Controllers;

use App\Enums\Roles;
use App\Models\Participant;
use App\Models\Shift;
use App\Models\ShiftCategory;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PlanningController extends Controller
{
    public function index(): Factory|View|Application
    {
        $parents = Participant::where('role',Roles::dad_mom)->get();
        $categories = ShiftCategory::all();
        $shiftLeaders = Participant::where('role',Roles::crew)->get();
        return view('admin.planning.index')->with(['shifts' => Shift::all(), 'parents' => $parents, 'categories' => $categories, 'shiftLeaders' => $shiftLeaders]);
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
