<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\User;
use App\Models\Order;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            // 1. Hitung Card Statistik Utama
            $totalBooks = Book::count();
            $totalUsers = User::where('role', 'user')->count();
            $totalOrders = Order::count();

            // 2. Hitung Jumlah Buku per Kategori untuk Doughnut Chart
            $categories = ['Novel', 'Sejarah', 'Filosofi', 'Pendidikan', 'Biografi'];
            $categoryCounts = [];

            foreach ($categories as $category) {
                $categoryCounts[$category] = Book::where('category', $category)->count();
            }

            // 3. Ambil 5 Pesanan Terbaru untuk Tabel Ringkasan Aktivitas
            $bookActivities = Book::latest('updated_at')
                ->take(5)
                ->get()
                ->map(function ($book) {
                    // Jika created_at sama dengan updated_at, berarti buku baru (Added)
                    // Jika berbeda, berarti buku hasil edit (Updated)
                    $isNew = $book->created_at->equalTo($book->updated_at);

                    return [
                        'id' => $book->id,
                        'title' => $book->title,
                        'author' => $book->author,
                        'type' => $isNew ? 'Ditambahkan' : 'Diperbarui',
                        'time' => $book->updated_at->diffForHumans(), // Contoh: "2 minutes ago"
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => [
                    'total_books' => $totalBooks,
                    'total_users' => $totalUsers,
                    'total_orders' => $totalOrders,
                    'category_counts' => $categoryCounts,
                    'book_activities' => $bookActivities, // Kirim ke frontend
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}