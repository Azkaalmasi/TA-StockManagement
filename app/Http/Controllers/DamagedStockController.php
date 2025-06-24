<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\DamagedStock;
use App\Models\DamagedDetail;
use App\Models\Product;
use App\Models\InDetail;
use Illuminate\Support\Facades\DB;

class DamagedStockController extends Controller
{
    public function index(Request $request)
    {
         $availableYears = DB::table('damaged_stocks')
        ->selectRaw('YEAR(date) as year')
        ->distinct()
        ->orderBy('year', 'desc')
        ->pluck('year');

        //filter
        $month = $request->get('month', now()->month);
        $year  = $request->get('year', now()->year);

        $query = DamagedDetail::with(['product', 'damagedStock.user'])
            ->whereHas('damagedStock', function ($q) use ($month, $year) {
                $q->whereYear('date', $year);
                if ($month) {
                    $q->whereMonth('date', $month);
                }
            });

        $damagedDetails = $query->get();

        return view('damaged.index-damaged', compact('damagedDetails','availableYears', 'month', 'year'));
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

    public function create()
    {
        $products = Product::all();
        return view('damaged.create', compact('products'));
    }

    public function batchStore(Request $request)
    {

        $request->validate([
            'date'                  => 'required|date',
            'items'                 => 'required|array|min:1',
            'items.*.code'          => 'required|string|exists:products,code',
            'items.*.in_detail_id'  => 'required|string|exists:in_details,id',
            'items.*.quantity'      => 'required|integer|min:1',
            'items.*.information' => 'required|string|max:255',
        ]);

        $errors = [];
        $valid  = [];

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
                'information' => $item['information'],
            ];
        }

        if (empty($valid)) {
            return back()->with('error', 'Semua item gagal divalidasi.<br>'.implode('<br>', $errors));
        }

        DB::beginTransaction();
        try {
            $damagedStock = DamagedStock::create([
                'id'      => (string) Str::uuid(),
                'user_id' => auth()->id(),
                'date'    => $request->date,
            ]);

            foreach ($valid as $v) {
                $batch    = $v['inDetail'];
                $qty      = $v['quantity'];
                $product  = $v['product'];

                DamagedDetail::create([
                    'damaged_stock_id' => $damagedStock->id,
                    'product_id'       => $product->id,
                    'in_detail_id'     => $batch->id,
                    'quantity'         => $qty,
                    'expiry_date'      => $batch->expiry_date,
                    'in_stock_id'      => $batch->in_stock_id,
                    'information'     => $v['information'],
                ]);

                $product->decrement('stock', $qty);
                $batch->decrement('remaining_stock', $qty);
            }

            DB::commit();

            $msg = 'Barang rusak berhasil disimpan.';
            if ($errors) {
                $msg .= '<br>Tetapi beberapa item gagal:<br>' . implode('<br>', $errors);
                return back()->with('warning', $msg);
            }
            return back()->with('success', $msg);

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }
}
