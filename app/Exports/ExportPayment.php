<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use App\Models\Participant;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use App\Enums\Roles;

class ExportPayment implements FromCollection, ShouldAutoSize, WithMapping, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $userArr = [];
        $participants = Participant::select('id', 'firstName', 'insertion', 'lastName','email','fontysEmail','specials','medicalIssues','purpleOnly','role','birthday','phoneNumber','studyType','removedFromIntro')->get();
        foreach($participants as $participant)
        {
            if ($participant->hasPaid() && !$participant->purpleOnly && $participant->role == Roles::child && !$participant->alreadyPaidForMembership && !$participant->removedFromIntro) {
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
            'insertion',
            'lastName',
            'email',
            'birthday',
            'phoneNumber',
        ];
    }

    // here you select the row that you want in the file
    public function map($row): array {
        $fields = [
            $row->id,
            $row->firstName,
            $row->insertion,
            $row->lastName,
            $row->email,
            $row->birthday,
            $row->phoneNumber,
        ];
        return $fields;
    }
}
