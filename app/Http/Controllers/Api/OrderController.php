<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function checkout(Request $request)
    {
        $user = auth()->user();

        // 1. Validasi: Memastikan user mengirimkan array ID keranjang yang valid
        $request->validate([
            'cart_ids' => 'required|array',
            'cart_ids.*' => 'exists:carts,id'
        ]);

        // 2. Ambil hanya item keranjang yang dipilih DAN milik user tersebut
        $cartItems = Cart::with('book')
            ->where('user_id', $user->id)
            ->whereIn('id', $request->cart_ids) // Filter berdasarkan pilihan user
            ->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'Item yang dipilih tidak ditemukan'], 404);
        }

        return DB::transaction(function () use ($user, $cartItems, $request) {
            // 3. Hitung total harga hanya untuk barang yang dipilih
            $totalPrice = $cartItems->sum(function($item) {
                return $item->book->price * $item->quantity;
            });

            // 4. Buat Order
            $order = Order::create([
                'user_id' => $user->id,
                'total_price' => $totalPrice,
                'status' => 'pending'
            ]);

            // 5. Pindahkan item ke OrderItem
            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'book_id' => $item->book_id,
                    'quantity' => $item->quantity,
                    'price' => $item->book->price
                ]);
            }

            // 6. Hapus HANYA item yang baru saja diproses dari keranjang
            Cart::where('user_id', $user->id)
                ->whereIn('id', $request->cart_ids)
                ->delete();

            return response()->json([
                'message' => 'Checkout berhasil untuk item terpilih',
                'order' => $order->load('orderItems.book')
            ], 201);
        });
    }
    public function userOrders()
    {
        $user = auth()->user();
        
        // Mengambil pesanan beserta item dan detail bukunya
        $orders = Order::with(['orderItems.book'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }
}
