<?php

namespace App\Exports;

use App\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
class UserExport implements FromCollection , WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return User::all();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return[
            'id',
            'name',
            'email',
            'mobile_number',
            'mac_address',
            'token',
            'mobile_verification_code',
            'password',
            'remember_token',
            'created_at',
            'updated_at',
        ];
    }
}
