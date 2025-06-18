<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Product;
use App\Models\InDetail;
use App\Models\OutDetail;

class DashboardController extends Controller
{
 public function index()
{
    $startOfWeek = Carbon::now()->startOfWeek(); // Senin
    $endOfWeek = Carbon::now()->endOfWeek();     // Minggu

    // Produk dengan pengeluaran terbanyak minggu ini berdasarkan `out_stocks.date`
    $topOut = OutDetail::whereHas('outStock', function ($query) use ($startOfWeek, $endOfWeek) {
            $query->whereBetween('date', [$startOfWeek, $endOfWeek]);
        })
        ->selectRaw('product_id, SUM(quantity) as total')
        ->groupBy('product_id')
        ->orderByDesc('total')
        ->with('product')
        ->first();

    // Produk dengan pemasukan terbanyak minggu ini berdasarkan `in_stocks.date`
    $topIn = InDetail::whereHas('inStock', function ($query) use ($startOfWeek, $endOfWeek) {
            $query->whereBetween('date', [$startOfWeek, $endOfWeek]);
        })
        ->selectRaw('product_id, SUM(quantity) as total')
        ->groupBy('product_id')
        ->orderByDesc('total')
        ->with('product')
        ->first();

    // Produk dengan stok terendah
    $lowestStock = Product::orderBy('stock')->first();

    // Produk dengan stok di bawah minimum
    $lowStocks = Product::whereColumn('stock', '<', 'min_stock')->get();

    return view('dashboard', compact('topOut', 'topIn', 'lowestStock', 'lowStocks'));
}
}
