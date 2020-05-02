<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Listening extends Model
{
    protected $fillable = [
        'questions',
        'description',
        'title',
        'category_id',
        'file_path',
        'category_id',
        'book_category_id',
    ];

    public function listeningCategory()
    {
        return $this->belongsTo(ListeningCategory::class);
    }
}
