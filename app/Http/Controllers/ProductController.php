<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Category;
use App\Models\InDetail;
use App\Models\OutDetail;
use Barryvdh\DomPDF\Facade\Pdf;

class ProductController extends Controller
{

    public function create()
{
    $categories = Category::all();
    return view('products.add-product', compact('categories'));
}
    public function index()
    {
        $products = Product::with('category')->get(); 
    return view('products.index-product', compact('products'));
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'stock' => 'required|integer',
            'min_stock' => 'required|integer',
            'pcs_per_box' => 'required|integer',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:products,code',
        ]);

        $validated['id'] = (string) Str::uuid();

        $product = Product::create($validated);
        return redirect()->route('products.index')->with('success', 'Produk berhasil ditambahkan');
        
    }


public function show($id)
{
    $product = Product::with('category')->findOrFail($id);

    $month = request('month');
    $year = request('year', now()->year);

    $inDetails = InDetail::with('inStock.user')
        ->where('product_id', $product->id)
        ->when($year, function ($q) use ($year) {
            $q->whereHas('inStock', fn($sub) => $sub->whereYear('date', $year));
        })
        ->when($month, function ($q) use ($month) {
            $q->whereHas('inStock', fn($sub) => $sub->whereMonth('date', $month));
        })
        ->get();

    $outDetails = OutDetail::with('outStock.user')
        ->where('product_id', $product->id)
        ->when($year, function ($q) use ($year) {
            $q->whereHas('outStock', fn($sub) => $sub->whereYear('date', $year));
        })
        ->when($month, function ($q) use ($month) {
            $q->whereHas('outStock', fn($sub) => $sub->whereMonth('date', $month));
        })
        ->get();


    $weeklyData = OutDetail::where('product_id', $product->id)
        ->join('out_stocks', 'out_details.out_stock_id', '=', 'out_stocks.id')
        ->selectRaw('YEARWEEK(out_stocks.date, 1) as week, SUM(out_details.quantity) as total')
        ->groupBy('week')
        ->orderBy('week')
        ->get();

    $weeklyChart = OutDetail::where('product_id', $product->id)
        ->join('out_stocks', 'out_details.out_stock_id', '=', 'out_stocks.id')
        ->selectRaw('YEARWEEK(out_stocks.date, 1) as week, DATE_FORMAT(MIN(out_stocks.date), "%d-%b") as label, SUM(quantity) as total')
        ->groupBy('week')
        ->orderBy('week')
        ->get();

    $chartLabels = $weeklyChart->pluck('label');
    $chartData   = $weeklyChart->pluck('total');

    if ($weeklyData->count() >= 2) {
        $x = range(1, $weeklyData->count());
        $y = $weeklyData->pluck('total')->toArray();
        $n = count($x);

        $sumX = array_sum($x);
        $sumY = array_sum($y);
        $sumXY = array_sum(array_map(fn($i) => $x[$i] * $y[$i], array_keys($x)));
        $sumX2 = array_sum(array_map(fn($i) => pow($x[$i], 2), array_keys($x)));

        $b = ($n * $sumXY - $sumX * $sumY) / ($n * $sumX2 - pow($sumX, 2));
        $a = ($sumY - $b * $sumX) / $n;

        $forecast = round($a + $b * ($n + 1));
    } else {
        $forecast = null;
    }

    return view('products.product-show', compact(
        'product',
        'inDetails',
        'outDetails',
        'forecast',
        'chartLabels',
        'chartData'
    ));
}


    
    public function edit($id)
{
    $product = Product::findOrFail($id);
    $categories = Category::all();

    return view('products.edit-product', compact('product', 'categories'));
}



    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'stock' => 'required|integer',
            'min_stock' => 'required|integer',
            'pcs_per_box' => 'required|integer',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:products,code,' . $id,
        ]);

        $product->update($validated);
        return redirect()->route('products.index')->with('success', 'Produk berhasil diperbarui');
    }


    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Produk berhasil dihapus');
    }

    public function exportPdf($id)
{
    $product = Product::with('category')->findOrFail($id);

    $inDetails = InDetail::with('inStock.user')
        ->where('product_id', $product->id)->get();

    $outDetails = OutDetail::with('outStock.user')
        ->where('product_id', $product->id)->get();

    $pdf = Pdf::loadView('products.pdf-detail', compact('product', 'inDetails', 'outDetails'));

    return $pdf->download('detail_produk_'.$product->code.'.pdf');
}
    
}
