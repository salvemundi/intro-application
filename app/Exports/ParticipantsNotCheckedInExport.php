<?php

namespace App\Exports;

use App\Models\Participant;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ParticipantsNotCheckedInExport implements FromCollection, ShouldAutoSize, WithMapping, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $userArr = [];
        $participants = Participant::select('id','firstName','lastName','email','fontysEmail','specials','medicalIssues','purpleOnly','role','birthday','phoneNumber','studyType')->get();
        foreach($participants as $participant)
        {
            if($participant->hasPaid() || $participant->purpleOnly) {
                $userArr[] = $participant;
            }
        }
        return collect($userArr)->unique('id');
    }

    public function headings(): array
    {
        return [
            'id',
            'firstName',
            'lastName',
            'email',
            'fontysEmail',
            'specials',
            'medicalIssues',
            'birthday',
            'phoneNumber',
            'studyType',
        ];
    }

    // here you select the row that you want in the file
    public function map($row): array {
        $fields = [
            $row->id,
            $row->firstName,
            $row->lastName,
            $row->email,
            $row->fontysEmail,
            $row->specials,
            $row->medicalIssues,
            $row->birthday,
            $row->phoneNumber,
            $row->studyType,
        ];
        return $fields;
    }
}
