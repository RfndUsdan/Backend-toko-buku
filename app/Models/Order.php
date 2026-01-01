<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    // Tambahkan baris ini agar kolom bisa diisi
    protected $fillable = [
        'user_id',
        'total_price',
        'status',
        'snap_token'
    ];

    // Relasi ke User
    public function user() {
        return $this->belongsTo(User::class);
    }

    // Relasi ke Item Pesanan
    public function orderItems() {
        return $this->hasMany(OrderItem::class);
    }
}