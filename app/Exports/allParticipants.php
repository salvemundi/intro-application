<?php

namespace App\Exports;

use App\Enums\Roles;
use App\Models\Participant;
use Maatwebsite\Excel\Concerns\FromCollection;

class allParticipants implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Participant::select('id','firstName','insertion','lastName','email')->where('role', Roles::child)->orderBy('lastName')->get();
    }
    public function headings(): array
    {
        return [
            'id',
            'firstName',
            'insertion',
            'lastName',
            'email',
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
        ];
        return $fields;
    }
}
