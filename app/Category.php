<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $guarded = [];

    public function word()
    {
        return $this->belongsTo(Word::class);
    }
}
