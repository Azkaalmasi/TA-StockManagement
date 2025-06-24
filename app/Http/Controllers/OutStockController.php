<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\OutStock;
use App\Models\OutDetail;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\InDetail; 

class OutStockController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->get('month',now()->month);
        $year  = $request->get('year', now()->year);

        $query = OutDetail::with(['product', 'outStock.user'])
            ->whereHas('outStock', function ($q) use ($month, $year) {
                if ($month) {
                    $q->whereMonth('date', $month);
                }
                $q->whereYear('date', $year);
            });

        $outDetails = $query->get();

        return view('outstock.index-output', compact('outDetails'));
    }


public function getBatches(Request $request)
{
    $request->validate([
        'code' => 'required|string|exists:products,code',
    ]);

    
    $product = Product::where('code', $request->code)->firstOrFail();

  
    $batches = InDetail::where('product_id', $product->id)
        ->where('remaining_stock', '>', 0)
        ->select('id', 'expiry_date', 'remaining_stock')
        ->orderBy('expiry_date', 'asc')
        ->get();

    return response()->json($batches);
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
        for ($i = 5; $i < count($data); $i++) {
            $row = $data[$i];
        
            $code = $row[1] ?? null; // Kolom B
            $code = $code ? ltrim(trim($code), "'") : null;
        
            $pcs = (int)($row[5] ?? 0); // Kolom F
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
            'date'                  => 'required|date',
            'items'                 => 'required|array|min:1',
            'items.*.code'          => 'required|string|exists:products,code',
            'items.*.in_detail_id' => 'required|string|exists:in_details,id',
            'items.*.quantity'      => 'required|integer|min:1',
        ]);
    
        $errors   = [];
        $valid    = [];
    
        // slide 1: validasi awal tiap item
        foreach ($request->items as $i => $item) {
            $product  = Product::where('code', $item['code'])->first();
            $inDetail = InDetail::find($item['in_detail_id']);
    
            if (! $product) {
                $errors[] = "Baris #{$i}: kode <strong>{$item['code']}</strong> tidak valid.";
                continue;
            }
    
            if (! $inDetail || $inDetail->remaining_stock < $item['quantity']) {
                $sisa = $inDetail ? $inDetail->remaining_stock : 0;
                $errors[] = "Baris #{$i}: batch <strong>{$item['in_detail_id']}</strong> (sisa {$sisa}) tidak cukup.";
                continue;
            }
    
            $valid[] = [
                'product'   => $product,
                'inDetail'  => $inDetail,
                'quantity'  => $item['quantity'],
            ];
        }
    
        if (empty($valid)) {
            return back()->with('error', 'Semua item gagal divalidasi.<br>'.implode('<br>',$errors));
        }
    
        DB::beginTransaction();
        try {
            // slide 2: buat header OutStock
            $outStock = OutStock::create([
                'id'      => (string) Str::uuid(),
                'user_id' => auth()->id(),
                'date'    => $request->date,
            ]);
    
            // slide 3: loop untuk save OutDetail + decrement
            foreach ($valid as $v) {
                /** @var \App\Models\InDetail $batch */
                $batch    = $v['inDetail'];
                $qty      = $v['quantity'];
                $product  = $v['product'];
    
                OutDetail::create([
                    'out_stock_id' => $outStock->id,
                    'product_id'   => $product->id,
                    'in_detail_id' => $batch->id,
                    'quantity'     => $qty,
                    'expiry_date'  => $batch->expiry_date,
                    'in_stock_id'  => $batch->in_stock_id,
                ]);
    
                // decrement stok global & batch
                $product->decrement('stock', $qty);
                $batch->decrement('remaining_stock', $qty);
            }
    
            DB::commit();
    
            $msg = 'Barang keluar berhasil disimpan.';
            if ($errors) {
                $msg .= '<br>Tetapi beberapa item gagal:<br>'.implode('<br>',$errors);
                return back()->with('warning', $msg);
            }
            return back()->with('success', $msg);
    
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error','Gagal menyimpan: '.$e->getMessage());
        }
    }
    
    

    public function create()
    {
        $products = Product::all();
        return view('outstock.output', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|exists:products,code',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::where('code', $validated['code'])->firstOrFail();

        if ($product->stock < $validated['quantity']) {
            return redirect()->back()->with('error', 'Stok produk tidak mencukupi!');
        }

        $outStockId = (string) Str::uuid();

        OutStock::create([
            'id' => $outStockId,
            'user_id' => auth()->id(),
            'date' => now()->toDateString(),
        ]);

        OutDetail::create([
            'out_stock_id' => $outStockId,
            'product_id' => $product->id,
            'quantity' => $validated['quantity'],
        ]);

        // Kurangi stok
        $product->stock -= $validated['quantity'];
        $product->save();

        return redirect()->back()->with('success', 'Barang keluar berhasil disimpan!');
    }
}
