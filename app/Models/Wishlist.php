<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    use HasFactory;

    // Menentukan kolom yang boleh diisi secara massal
    protected $fillable = [
        'user_id',
        'book_id'
    ];

    // Relasi ke Model Book (Satu wishlist punya satu buku)
    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    // Relasi ke Model User (Satu wishlist milik satu user)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}