<?php

namespace App\Exports;

use App\Word;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class WordsExport implements FromCollection , WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Word::all();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return[
          'id',
          'word',
          'phonetic',
          'english_meaning',
          'persian_meaning',
          'english_example',
          'persian_example',
          'user_note',
          'choices_question',
          'correspondence_question',
          'image_path',
          'is_delete',
          'created_at',
          'updated_at',
        ];
    }
}
