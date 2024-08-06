<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use App\Models\ShiftCategory;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class PlanningController extends Controller
{
    public function index(): Factory|View|Application
    {
        return view('admin.planning.index')->with('shifts', Shift::all());
    }

    // look at my web.php file and implement the rest of the routes under the // Planning comment
    public function showShiftCategory($id): Factory|View|Application
    {
        return view('planning.shiftCategory')->with('shiftCategory', ShiftCategory::find($id));
    }

    public function addShiftCategory()
    {

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
