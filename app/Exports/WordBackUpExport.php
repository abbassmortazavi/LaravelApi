<?php

namespace App\Exports;

use App\WordBackUp;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class WordBackUpExport implements FromCollection , WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return WordBackUp::all();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return[
            'id',
            'user_id',
            'word_id',
            'user_note',
            'leitner_level',
            'last_leitner_date',
            'created_at',
            'updated_at',
        ];
    }
}
