<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Category;
use App\Models\InDetail;
use App\Models\OutDetail;

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
    
        // Ambil histori stok masuk untuk produk ini
        $inDetails = InDetail::with('inStock.user')
                        ->where('product_id', $id)
                        ->orderByDesc('created_at')
                        ->get();
    
        // Ambil histori stok keluar untuk produk ini
        $outDetails = OutDetail::with('outStock.user')
                        ->where('product_id', $id)
                        ->orderByDesc('created_at')
                        ->get();
    
        return view('products.product-show', compact('product', 'inDetails', 'outDetails'));
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
    
}
