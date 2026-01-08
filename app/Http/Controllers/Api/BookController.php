<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // Tambahkan ini untuk hapus file
use Illuminate\Support\Facades\DB;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $query = Book::query();

        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('author', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $books = $query->latest()->paginate(12);

        return response()->json([
            'message' => 'Daftar buku berhasil diambil',
            'data' => $books
        ], 200);
    }

    public function store(Request $request)
    {
        // 1. Validasi semua field (termasuk yang baru)
        $request->validate([
            'title'          => 'required|string|max:255',
            'author'         => 'required|string|max:255',
            'publisher'      => 'required|string|max:255',      // Baru
            'published_year' => 'required|integer',             // Baru
            'language'       => 'required|string',               // Baru
            'pages'          => 'required|integer',             // Baru
            'price'          => 'required|integer',
            'category'       => 'required|string',
            'description'    => 'required|string',
            'image'          => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // 2. Proses Upload Gambar
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('books', 'public');
        }

        // 3. Simpan ke Database
        $book = \App\Models\Book::create([
            'title'          => $request->title,
            'author'         => $request->author,
            'publisher'      => $request->publisher,      // Tambahkan ini
            'published_year' => $request->published_year, // Tambahkan ini
            'language'       => $request->language,       // Tambahkan ini
            'pages'          => $request->pages,          // Tambahkan ini
            'price'          => $request->price,
            'category'       => $request->category,
            'description'    => $request->description,
            'image'          => $imagePath,
        ]);

        return response()->json([
            'message' => 'Buku berhasil ditambahkan!',
            'data'    => $book
        ], 201);
    }
    public function show($id)
    {
        $book = Book::findOrFail($id);
        return response()->json([
            'message' => 'Detail buku berhasil diambil',
            'data' => $book
        ]);
    }

    public function categories()
    {
        // Mengambil nama kategori unik dari tabel books yang tidak bernilai null
        $categories = Book::select('category')
            ->distinct()
            ->whereNotNull('category')
            ->get();

        return response()->json([
            'message' => 'Daftar kategori berhasil diambil',
            'data' => $categories
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $book = Book::findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'author' => 'sometimes|required',
            'category' => 'sometimes|required',
            'price' => 'sometimes|required|numeric',
            'description' => 'sometimes|required',
        ]);

        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada
            if ($book->image) {
                Storage::disk('public')->delete($book->image);
            }
            // Simpan gambar baru
            $path = $request->file('image')->store('books', 'public');
            $validated['image'] = $path;
        }

        $book->update($validated);

        return response()->json([
            'message' => 'Buku berhasil diperbarui',
            'data' => $book
        ]);
    }

    public function destroy($id)
    {
        try {
            $book = Book::findOrFail($id);

            // Laravel otomatis hanya mengisi deleted_at, tidak menghapus baris data
            // File gambar tetap aman di folder storage
            $book->delete();

            return response()->json([
                'message' => 'Buku berhasil dipindahkan ke tempat sampah (Soft Delete)'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal menghapus buku',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
