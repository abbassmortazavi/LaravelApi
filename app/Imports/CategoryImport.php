<?php

namespace App\Imports;

use App\Category;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CategoryImport implements ToModel , WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Category([
            'parentcategoryid'            => $row['parentcategoryid'],
            'category_name'                 => $row['category_name'],
            'list_type'                     => $row['list_type'],
            'image_path'                    => $row['image_path']
        ]);
    }
}
