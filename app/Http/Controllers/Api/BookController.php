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
        // 1. Inisialisasi query dengan eager loading
        $query = Book::with('category'); 

        // 2. Logika Search
        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                ->orWhere('author', 'like', '%' . $request->search . '%');
            });
        }

        // 3. Logika Filter Kategori
        if ($request->filled('category')) {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('slug', $request->category); 
            });
        }

        // 4. Eksekusi query dengan pagination
        $books = $query->latest()->paginate(12);

        // 5. Kembalikan response JSON
        return response()->json([
            'message' => 'Daftar buku berhasil diambil',
            'data' => $books
        ], 200);
    }

    public function categories()
    {
        // Mengambil data langsung dari tabel categories, bukan distinct string dari tabel books
        $categories = \App\Models\Category::select('id', 'name')->get();

        return response()->json([
            'message' => 'Daftar kategori berhasil diambil',
            'data' => $categories
        ], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'          => 'required|string|max:255',
            'author'         => 'required|string|max:255',
            'publisher'      => 'required|string|max:255',
            'published_year' => 'required|integer',
            'language'       => 'required|string',
            'pages'          => 'required|integer',
            'price'          => 'required|integer',
            'category_id'    => 'required|exists:categories,id', // Ubah ke category_id dan pastikan ID-nya ada di tabel categories
            'description'    => 'required|string',
            'image'          => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('books', 'public');
        }

        $book = Book::create([
            'title'          => $request->title,
            'author'         => $request->author,
            'publisher'      => $request->publisher,
            'published_year' => $request->published_year,
            'language'       => $request->language,
            'pages'          => $request->pages,
            'price'          => $request->price,
            'category_id'    => $request->category_id, // Gunakan category_id
            'description'    => $request->description,
            'image'          => $imagePath,
        ]);

        return response()->json(['message' => 'Buku berhasil ditambahkan!', 'data' => $book], 201);
    }
    
    public function show($id)
    {
        $book = Book::with('category')->findOrFail($id);

        return response()->json([
            'message' => 'Detail buku berhasil diambil',
            'data' => $book
        ]);
    }

    public function update(Request $request, $id)
    {
        $book = Book::findOrFail($id);

        $validated = $request->validate([
            'title'       => 'sometimes|required',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'author'      => 'sometimes|required',
            'category_id' => 'sometimes|required|exists:categories,id', // GANTI INI
            'price'       => 'sometimes|required|numeric',
            'description' => 'sometimes|required',
        ]);

        if ($request->hasFile('image')) {
            if ($book->image) {
                Storage::disk('public')->delete($book->image);
            }
            $path = $request->file('image')->store('books', 'public');
            $validated['image'] = $path;
        }

        $book->update($validated); // Sekarang $validated berisi 'category_id', jadi aman.

        return response()->json([
            'message' => 'Buku berhasil diperbarui',
            'data' => $book->load('category') // Tambahkan load agar response-nya lengkap
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
