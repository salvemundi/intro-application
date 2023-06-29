<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Models\Participant;

class APIController extends Controller
{
    public function GetParticipants()
    {
        $client = new \GuzzleHttp\Client([
            'verify' => false
        ]);
        $url = "https://localhost/api/participants";
        $res = $client->get($url);
        $content = json_decode($res->getBody()->getContents());
        $arr = collect();
        foreach($content as $item)
        {
            // Try to find the participant first, then update their records instead of creating a new object
            $tryToFind = Participant::where('samuId',$item->id)->first();
            if($tryToFind) {
                $tryToFind->firstName = $item->firstName;
                $tryToFind->insertion = $item->insertion;
                $tryToFind->lastName = $item->lastName;
                $tryToFind->birthday = date("Y-m-d", strtotime($item->birthday));
                $tryToFind->email = $item->email;
                $tryToFind->phoneNumber = $item->phoneNumber;
                $tryToFind->firstNameParent = $item->firstNameParent;
                $tryToFind->lastNameParent = $item->lastNameParent;
                $tryToFind->addressParent = $item->addressParent;
                $tryToFind->medicalIssues = $item->medicalIssues;
                $tryToFind->specials = $item->specials;
                $tryToFind->phoneNumberParent = $item->phoneNumberParent;
                $arr->push($tryToFind);
                $tryToFind->save();
            }

            $newParticipant = new Participant();
            $newParticipant->firstName = $item->firstName;
            $newParticipant->insertion = $item->insertion;
            $newParticipant->lastName = $item->lastName;
            $newParticipant->birthday = date("Y-m-d", strtotime($item->birthday));
            $newParticipant->email = $item->email;
            $newParticipant->phoneNumber = $item->phoneNumber;
            $newParticipant->firstNameParent = $item->firstNameParent;
            $newParticipant->lastNameParent = $item->lastNameParent;
            $newParticipant->addressParent = $item->addressParent;
            $newParticipant->medicalIssues = $item->medicalIssues;
            $newParticipant->specials = $item->specials;
            $newParticipant->phoneNumberParent = $item->phoneNumberParent;
            $newParticipant->samuId = $item->id;
            $arr->push($newParticipant);
            $newParticipant->save();
        }
        return redirect('/participants');
    }
}
