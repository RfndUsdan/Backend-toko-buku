<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Import semua Controller yang dibutuhkan
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\WishlistController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\DashboardController;


// --- 1. RUTE PUBLIK (Bisa diakses tanpa login) ---
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::get('/categories', [BookController::class, 'categories']);
Route::get('/books', [BookController::class, 'index']);
Route::get('/books/{id}', [BookController::class, 'show']);



// --- 2. RUTE TERPROTEKSI (Harus Login / Sanctum) ---
Route::middleware('auth:sanctum')->group(function () {
     
    // Autentikasi & Profil
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Manajemen Wishlist
    Route::get('/wishlist', [WishlistController::class, 'index']);
    Route::post('/wishlist', [WishlistController::class, 'store']);
    Route::delete('/wishlist/{id}', [WishlistController::class, 'destroy']);

    // Manajemen Keranjang (Cart)
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart', [CartController::class, 'store']);
    Route::put('/cart/{id}', [CartController::class, 'update']);
    Route::delete('/cart/{id}', [CartController::class, 'destroy']);

    // Checkout & Pesanan
    Route::post('/checkout', [OrderController::class, 'checkout']);
    Route::get('/my-orders', [OrderController::class, 'userOrders']); // Pesanan milik user tersebut
    Route::delete('/orders/{id}/cancel', [OrderController::class, 'cancelOrder']);


    // --- 3. RUTE KHUSUS ADMIN (Login + Role Admin) ---
    // Pastikan Anda sudah membuat middleware 'admin'
    Route::middleware('admin')->group(function () {
        
        // Statistik Dashboard
        Route::get('/admin/statistics', [DashboardController::class, 'index']);

        // Kelola Buku (Admin bisa Tambah, Edit, Hapus)
        Route::post('/admin/books', [BookController::class, 'store']);
        Route::put('/admin/books/{id}', [BookController::class, 'update']);
        Route::delete('/admin/books/{id}', [BookController::class, 'destroy']);

        // Kelola Semua Pesanan
        Route::get('/admin/orders', [OrderController::class, 'index']);
        Route::put('/admin/orders/{id}/status', [OrderController::class, 'updateStatus']);
    });

});