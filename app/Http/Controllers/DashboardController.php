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

    $startDate = now()->subDays(30);

    // Grafik
    $startOfMonth = Carbon::now()->startOfMonth();
    $endOfMonth = Carbon::now()->endOfMonth();

    $weeklySales = \App\Models\OutDetail::whereHas('outStock', function ($q) use ($startOfMonth, $endOfMonth) {
            $q->whereBetween('date', [$startOfMonth, $endOfMonth]);
        })
        ->select('out_stocks.date', 'quantity')
        ->join('out_stocks', 'out_stocks.id', '=', 'out_details.out_stock_id')
        ->get()
        ->groupBy(function ($item) use ($startOfMonth) {
            return floor($startOfMonth->diffInDays(Carbon::parse($item->date)) / 7) + 1;
        })
        ->map(function ($group) {
            return $group->sum('quantity');
        })
        ->sortKeys();

    $chartLabels = $weeklySales->keys()->map(fn($week) => "Minggu ke-$week");
    $chartValues = $weeklySales->values();

    //Pie Chart

    $totalIn = \App\Models\InDetail::whereHas('inStock', function ($q) use ($startDate) {
    $q->where('date', '>=', $startDate);
    })->sum('quantity');

    $totalOut = \App\Models\OutDetail::whereHas('outStock', function ($q) use ($startDate) {
        $q->where('date', '>=', $startDate);
    })->sum('quantity');

    //Produk Expired
     $expiredStocks = InDetail::with('product')
        ->where('remaining_stock', '>', 0)
        ->where('expiry_date', '<', Carbon::today())
        ->get();

    if ($lowStocks->count()) {
    $lowStockList = $lowStocks->map(function ($product) {
        return "- {$product->name} (Stok: {$product->stock}, Minimum: {$product->min_stock})";
    })->implode('<br>');

    session()->flash('low_stock_warning', $lowStockList);
    }


    return view('dashboard', compact('topOut', 'topIn', 'lowestStock', 'lowStocks', 'expiredStocks'), [

                'areaLabels' => $chartLabels,   // ex: ['Week 1', 'Week 2']
                'areaValues' => $chartValues,    // ex: [10, 20]
                'pieLabels'  => ['Pembelian', 'Penjualan'],
                'pieValues'  => [$totalIn, $totalOut],
    ]);
}
}
