<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // 1. Import ini

class Book extends Model
{
    use SoftDeletes; // 2. Gunakan ini

    protected $fillable = [
    'title', 'author', 'publisher', 'published_year', 
    'language', 'pages', 'price', 'category', 'description', 'image'
    ];

    public function category() {
    return $this->belongsTo(Category::class);
}
}
