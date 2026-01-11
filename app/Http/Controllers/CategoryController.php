<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        // Ambil kategori utama (parent_id null) beserta sub-kategorinya
        $categories = Category::whereNull('parent_id')
            ->with('children')
            ->get();

        return response()->json([
            'data' => Category::whereNull('parent_id')->with('children')->get()
        ]);
    }
}