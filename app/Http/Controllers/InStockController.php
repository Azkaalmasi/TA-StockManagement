<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\InStock;
use App\Models\InDetail;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\IOFactory;

class InStockController extends Controller
{



public function index()
{
    $inDetails = InDetail::with(['product', 'inStock.user'])->get();

    return view('instock.index-input', compact('inDetails'));
}

public function previewExcel(Request $request)
{
    $file = $request->file('excel_file');

    if (!$file) {
        return response()->json(['error' => 'File tidak ditemukan'], 400);
    }

    $spreadsheet = IOFactory::load($file->getPathname());
    $sheet = $spreadsheet->getActiveSheet();
    $data = $sheet->toArray();

    $processedData = [];

    // Mulai dari baris ke-9 (index 8)
    for ($i = 8; $i < count($data); $i++) {
        $row = $data[$i];

        $code = $row[1] ?? null; // Kolom B
        $pcs = (int)($row[3] ?? 0); // Kolom D
        $box = (int)($row[4] ?? 0); // Kolom E

        if ($code) {
            $product = Product::where('code', $code)->first();
            $pcsPerBox = $product ? $product->pcs_per_box : 0;
            $quantity = $pcs + ($box * $pcsPerBox);

            $processedData[] = [
                'code' => $code,
                'quantity' => $quantity,
                'name' => $product ? $product->name : '-', 
            ];
        }
    }

    return response()->json($processedData);
}

    
public function batchStore(Request $request)
{
    $request->validate([
        'date'                        => 'required|date',
        'items'                       => 'required|array|min:1',
        'items.*.code'                => 'required|string|exists:products,code',
        'items.*.quantity'            => 'required|integer|min:1',
        'items.*.expiry_date'         => 'required|date',
        'items.*.manufacturer_id'     => 'required|uuid|exists:manufacturers,id',
    ]);

    DB::beginTransaction();
    try {
        $inStock = InStock::create([
            'id'      => (string) Str::uuid(),
            'user_id' => auth()->id(),
            'date'    => $request->date,
        ]);

        $errors = [];
        foreach ($request->items as $item) {
            $product = Product::where('code', $item['code'])->first();
            if (! $product) {
                $errors[] = "Kode {$item['code']} tidak ditemukan.";
                continue;
            }

            InDetail::create([
                'in_stock_id'     => $inStock->id,
                'product_id'      => $product->id,
                'quantity'        => $item['quantity'],
                'remaining_stock' => $item['quantity'],
                'expiry_date'     => $item['expiry_date'],
                'manufacturer_id' => $item['manufacturer_id'],
            ]);
        }
        DB::commit();

        $msg = 'Barang masuk berhasil disimpan.';
        if ($errors) {
            $msg .= '<br>Namun beberapa item gagal:<br>' . implode('<br>', $errors);
            return redirect()->back()->with('warning', $msg);
        }
        return redirect()->back()->with('success', $msg);
    } catch (\Exception $e) {
        DB::rollback();
        return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
}


    public function create()
{
    $manufacturers = \App\Models\Manufacturer::all();
    return view('instock.input', compact('manufacturers'));
}
public function store(Request $request)
{
    $validated = $request->validate([
        'code'             => 'required|string|exists:products,code',
        'quantity'         => 'required|integer|min:1',
        'remaining_stock'  => 'required|integer|min:0',
        'items.*.expiry_date' => 'required|date|after:today',
        'manufacturer_id'  => 'required|uuid|exists:manufacturers,id',
    ]);

    $product = Product::where('code', $validated['code'])->firstOrFail();
    $inStockId = (string) Str::uuid();

    InStock::create([
        'id'      => $inStockId,
        'user_id' => auth()->id(),
        'date'    => now()->toDateString(),
    ]);

    InDetail::create([
        'in_stock_id'     => $inStockId,
        'product_id'      => $product->id,
        'quantity'        => $validated['quantity'],
        'remaining_stock' => $validated['remaining_stock'],
        'expiry_date'     => $validated['expiry_date'],
        'manufacturer_id' => $validated['manufacturer_id'],
    ]);

    return redirect()->back()->with('success', 'Stok berhasil ditambahkan!');
}
}
