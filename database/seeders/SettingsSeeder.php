<?php

namespace Database\Seeders;

use App\Enums\SettingTypes;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // Signup page
        if(!Setting::where('name','SignupPageEnabled')->exists()) {
            $setting = new Setting();
            $setting->name = "SignupPageEnabled";
            $setting->value = "true";
            $setting->description = "Zet de vrijblijvende inschrijf pagina aan of uit.";
            $setting->valueType = SettingTypes::boolean();
            $setting->save();
        }

        // Payment page
        if(!Setting::where('name', 'ConfirmationEnabled')->exists()) {
            $setting = new Setting();
            $setting->name = "ConfirmationEnabled";
            $setting->value = "true";
            $setting->description = "Zet de betalings pagina aan of uit.";
            $setting->valueType = SettingTypes::boolean();
            $setting->save();
        }

        // Send automatic mails after opening date
        if(!Setting::where('name', 'AutoSendPaymentEmailDate')->exists()) {
            $setting = new Setting();
            $setting->name = "AutoSendPaymentEmailDate";
            $setting->value = new Carbon('2022-06-14');
            $setting->description = "Stel de datum in waarop de betalings email automatisch wordt verzonden.";
            $setting->valueType = SettingTypes::date();
            $setting->save();
        }

        if(!Setting::where('name', 'TeacherSignupLink')->exists()) {
            $setting = new Setting();
            $setting->name = "TeacherSignupLink";
            $setting->value = "https://salvemundi.sharepoint.com/:x:/s/intro/EdTW8HrswNZHr-Q95EZ3enQBQ31z167zKwisy4KM3la5Zg?e=yeKE6o";
            $setting->description = "Stel de link in waar docenten naar toe gaan als zij zich willen inschrijven.";
            $setting->valueType = SettingTypes::string();
            $setting->save();
        }

        if(!Setting::where('name', 'DaysTillIntro')->exists()) {
            $setting = new Setting();
            $setting->name = "DaysTillIntro";
            $setting->value = new Carbon('2023-08-22');
            $setting->description = "Stel de datum in wanneer de introductie begint.";
            $setting->valueType = SettingTypes::date();
            $setting->save();
        }

        if(!Setting::where('name', 'EndIntroDate')->exists()) {
            $setting = new Setting();
            $setting->name = "EndIntroDate";
            $setting->value = new Carbon('2023-08-26');
            $setting->description = "Stel de datum in wanneer de introductie eindigd.";
            $setting->valueType = SettingTypes::date();
            $setting->save();
        }
        if(!Setting::where('name', 'PlanningPage')->exists()) {
            $setting = new Setting();
            $setting->name = "PlanningPage";
            $setting->value = "false";
            $setting->description = "Stel in of de qr-code pagina aan of uit moet staan.";
            $setting->valueType = SettingTypes::boolean();
            $setting->save();
        }
    }
}
