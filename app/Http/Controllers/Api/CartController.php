<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    // Lihat isi keranjang
    public function index() {
        // 1. Ambil data keranjang beserta data bukunya
        $carts = Cart::with('book')->where('user_id', auth()->id())->get();

        // 2. Hitung total harga seluruh isi keranjang
        $totalPrice = $carts->sum(function($item) {
            return $item->book->price * $item->quantity;
        });

        // 3. Kembalikan respon JSON yang rapi
        return response()->json([
            'message' => 'Isi keranjang berhasil diambil',
            'total_cart_price' => $totalPrice, // Total harga keseluruhan
            'data' => $carts
        ], 200);
    }

    // Tambah ke keranjang
    public function store(Request $request) {
        if (auth()->user()->role === 'admin') {
            return response()->json(['message' => 'Admin tidak bisa belanja'], 403);
        }

        $request->validate([
            'items' => 'required|array',
            'items.*.book_id' => 'required|exists:books,id',
            'items.*.quantity' => 'required|integer|min:1'
        ]);

        $results = [];

        foreach ($request->items as $item) {
            // 1. Cari apakah buku ini sudah ada di keranjang user
            $cart = \App\Models\Cart::where('user_id', auth()->id())
                                    ->where('book_id', $item['book_id'])
                                    ->first();

            if ($cart) {
                // 2. Jika ada, UPDATE jumlahnya (jumlah lama + jumlah baru)
                $cart->update([
                    'quantity' => $cart->quantity + $item['quantity']
                ]);
            } else {
                // 3. Jika tidak ada, CREATE baru (jumlah sesuai input)
                $cart = \App\Models\Cart::create([
                    'user_id' => auth()->id(),
                    'book_id' => $item['book_id'],
                    'quantity' => $item['quantity']
                ]);
            }

            $results[] = $cart;
        }

        return response()->json([
            'message' => 'Keranjang berhasil diperbarui',
            'data' => $results
        ], 201);
    }

    // Update jumlah (quantity)
    public function update(Request $request, $id) {
        $cart = Cart::where('user_id', Auth::id())->findOrFail($id);
        $cart->update(['quantity' => $request->quantity]);
        
        return response()->json(['message' => 'Jumlah diperbarui']);
    }

    // Hapus dari keranjang
    public function destroy($id) {
        Cart::where('user_id', Auth::id())->findOrFail($id)->delete();
        return response()->json(['message' => 'Item dihapus dari keranjang']);
    }
}
