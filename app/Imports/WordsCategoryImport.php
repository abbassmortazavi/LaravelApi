<?php

namespace App\Imports;

use App\WordCategory;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class WordsCategoryImport implements ToModel , WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new WordCategory([
            'word_id'                     => $row['word_id'],
            'category_id'                 => $row['category_id'],
            'book_category_id'                 => $row['book_category_id']
        ]);
    }
}
