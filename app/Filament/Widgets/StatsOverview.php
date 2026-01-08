<?php

namespace App\Filament\Widgets;

use App\Models\Book;
use App\Models\User;
use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('Total Buku', Book::count())
                ->description('Total koleksi di database')
                ->descriptionIcon('heroicon-m-book-open')
                ->color('success'),

            Stat::make('Total Pelanggan', User::where('role', 'user')->count())
                ->description('User terdaftar')
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),

            Stat::make('Total Pesanan', Order::count())
                ->description('Semua transaksi')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color('warning'),
        ];
    }
}