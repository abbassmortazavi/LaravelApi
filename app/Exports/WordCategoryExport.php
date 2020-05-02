<?php

namespace App\Exports;

use App\WordCategory;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class WordCategoryExport implements FromCollection , WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return WordCategory::all();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return[
            'id',
            'word_id',
            'category_id',
            'book_category_id',
            'is_delete',
            'created_at',
            'updated_at',
        ];
    }
}
