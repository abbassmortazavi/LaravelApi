<?php

namespace App\Exports;

use App\Category;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
class CategoryExport implements FromCollection , WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Category::all();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return[
            'id',
            'order_number',
            'parent_category_id',
            'category_name',
            'description',
            'list_type',
            'image_path',
            'is_free',
            'is_published',
            'is_delete',
            'created_at',
            'updated_at',
        ];
    }
}
