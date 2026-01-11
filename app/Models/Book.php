<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // 1. Import ini

// app/Models/Book.php

class Book extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title', 
        'author', 
        'publisher', 
        'published_year', 
        'language', 
        'pages', 
        'price', 
        'category_id', // PASTIKAN INI ADA, BUKAN 'category'
        'description', 
        'image'
    ];

    public function category() 
    {
        // Secara otomatis Laravel akan mencocokkan fungsi ini dengan kolom 'category_id'
        return $this->belongsTo(Category::class);
    }
}