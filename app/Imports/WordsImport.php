<?php

namespace App\Imports;

use App\Word;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class WordsImport implements ToModel , WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Word([
            'word'                            => $row['word'],
            'phonetic'                        => $row['phonetic'],
            'english_meaning'                 => $row['english_meaning'],
            'persian_meaning'                 => $row['persian_meaning'],
            'english_example'                 => $row['english_example'],
            'persian_example'                 => $row['persian_example'],
            //'user_note'                       => $row['user_note'],
            'choices_question'                => $row['choices_question'],
            'correspondence_question'         => $row['correspondence_question'],
            //'image_path'                      => $row['image_path'],
            //'is_delete'                       => $row['is_delete'],
        ]);
    }
}
