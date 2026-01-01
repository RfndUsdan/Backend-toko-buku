<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    // 1. Melihat Wishlist (Admin melihat semua, User melihat miliknya sendiri)
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            // Admin bisa melihat semua wishlist milik user (ditambah data user-nya)
            $wishlist = Wishlist::with(['user', 'book'])->latest()->get();
            $message = 'Daftar seluruh wishlist user berhasil diambil (Mode Admin)';
        } else {
            // User biasa hanya melihat miliknya sendiri
            $wishlist = Wishlist::with('book')->where('user_id', $user->id)->latest()->get();
            $message = 'Daftar wishlist Anda berhasil diambil';
        }

        return response()->json([
            'message' => $message,
            'data' => $wishlist
        ], 200);
    }

    // 2. Menambah Wishlist (DIBATASI: Admin tidak boleh)
    public function store(Request $request)
    {
        // Proteksi: Cek jika yang login adalah admin
        if (Auth::user()->role === 'admin') {
            return response()->json([
                'message' => 'Akses ditolak. Admin tidak diperbolehkan menambah wishlist.'
            ], 403);
        }

        $request->validate([
            'book_id' => 'required|exists:books,id',
        ]);

        $wishlist = Wishlist::updateOrCreate([
            'user_id' => Auth::id(),
            'book_id' => $request->book_id,
        ]);

        return response()->json([
            'message' => 'Buku berhasil ditambahkan ke wishlist',
            'data' => $wishlist
        ], 201);
    }

    // 3. Menghapus Wishlist (Hanya pemilik yang bisa)
    public function destroy($id)
    {
        $wishlist = Wishlist::where('user_id', Auth::id())->where('id', $id)->first();

        if (!$wishlist) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        $wishlist->delete();
        return response()->json(['message' => 'Berhasil dihapus dari wishlist']);
    }
}