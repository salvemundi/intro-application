<?php

namespace App\Http\Controllers;

use App\Models\Participant;
use App\Enums\Roles;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    private PaymentController $paymentController;

    public function __construct() {
        $this->paymentController = new PaymentController();
    }

    public function index(): Factory|View|Application
    {
        $viewVars = [];

        $viewVars['amountTotalCheckedIn']       = Participant::where('checkedIn', true)->count();
        $viewVars['amountCrew']                 = Participant::where('role', Roles::crew)->count();
        $viewVars['amountCrewCheckedIn']        = Participant::where('role', Roles::crew)->where('checkedIn', true)->count();
        $viewVars['amountChildren']             = Participant::where([['role', Roles::child],['purpleOnly',false]])->count();
        $viewVars['amountChildrenPaid']         = count($this->paymentController->getAllPaidUsers());
        $viewVars['amountChildrenCheckedIn']    = Participant::where('role', Roles::child)->where('checkedIn', true)->count();
        $viewVars['amountParents']              = Participant::where('role', Roles::dad_mom)->count();
        $viewVars['amountParentsCheckedIn']     = Participant::where('role', Roles::dad_mom)->where('checkedIn', true)->count();
        $viewVars['amountTeachers']             = Participant::where('role', Roles::teacher)->count();
        $viewVars['amountTeachersCheckedIn']    = Participant::where('role', Roles::teacher)->where('checkedIn', true)->count();
        $viewVars['amountEveryone']             = $viewVars['amountChildrenPaid'] + $viewVars['amountTeachers'] + $viewVars['amountCrew'] + $viewVars['amountParents'];

        return view('admin/dashboard', $viewVars);
    }
}
