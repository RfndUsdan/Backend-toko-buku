<?php

namespace App\Filament\Widgets;

use App\Models\Book;
use App\Models\Category;
use Filament\Widgets\ChartWidget;

class CategoryChart extends ChartWidget
{
    protected static ?string $heading = 'Distribusi Buku (Kategori Utama)';
    protected static ?int $sort = 2; // Agar muncul setelah Stats Overview
    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        // 1. Ambil semua Kategori Induk (yang tidak punya parent_id)
        $parentCategories = Category::whereNull('parent_id')->with('children')->get();

        $labels = [];
        $counts = [];

        foreach ($parentCategories as $parent) {
            // 2. Ambil ID kategori induk dan semua ID sub-kategorinya
            $categoryIds = $parent->children->pluck('id')->toArray();
            $categoryIds[] = $parent->id;

            // 3. Hitung total buku yang ada di kategori induk ATAU sub-kategorinya
            $bookCount = Book::whereIn('category_id', $categoryIds)->count();

            // Hanya tampilkan di chart jika ada bukunya (opsional)
            if ($bookCount > 0) {
                $labels[] = $parent->name;
                $counts[] = $bookCount;
            }
        }

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Buku',
                    'data' => $counts,
                    'backgroundColor' => [
                        '#3B82F6', '#10B981', '#F59E0B', '#8B5CF6', '#EC4899', '#06B6D4', '#F43F5E'
                    ],
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}