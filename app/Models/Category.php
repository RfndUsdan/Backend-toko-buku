<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'slug', 'parent_id'];

    // Relasi ke Induk (Kategori Utama)
    public function parent() {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    // Relasi ke Anak (Sub-Kategori)
    public function children() {
        return $this->hasMany(Category::class, 'parent_id');
    }

    // Relasi ke Buku
    public function books() {
        return $this->hasMany(Book::class);
    }
}
