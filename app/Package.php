<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $guarded = [];

    public function packageBooks()
    {
        return $this->hasMany(PackageBooks::class);
    }
}
