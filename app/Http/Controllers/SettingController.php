<?php

namespace App\Http\Controllers;

use App\Enums\AuditCategory;
use App\Enums\SettingTypes;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\Setting;

class SettingController extends Controller
{
    public function showSettings(): Factory|View|Application {
        $settings = Setting::all();
        return view('admin/settings', ['settings' => $settings]);
    }

    public function storeSetting(Request $request): RedirectResponse
    {
        $setting = Setting::find($request->settingId);
        if($setting->valueType == SettingTypes::date()->value) {
            $setting->value = new Carbon($request->input('value'));
        } else {
            $setting->value = $request->input('value');
        }
        $setting->save();
        AuditLogController::Log(AuditCategory::SettingManagement(), "Heeft instelling ".  $setting->description. " bijgewerkt naar " . " $setting->value", null, null, $setting);
        return back()->with('success','Instelling is opgeslagen!');
    }
}
