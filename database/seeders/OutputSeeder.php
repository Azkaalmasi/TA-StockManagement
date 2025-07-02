<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\OutStock;
use App\Models\OutDetail;
use App\Models\InDetail;
use App\Models\User;
use Illuminate\Support\Carbon;

class OutputSeeder extends Seeder
{
    public function run(): void
    {
        $admin1 = User::where('email', 'admin1@example.com')->first();
        $admin2 = User::where('email', 'admin2@example.com')->first();

        if (!$admin1 || !$admin2) {
            $this->command->error('Admin 1 dan Admin 2 harus tersedia.');
            return;
        }

        $admins = [$admin1, $admin2];
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $daysInMonth = $now->daysInMonth;

        $inDetails = \App\Models\InDetail::with('inStock')->where('remaining_stock', '>', 0)->get();

        foreach ($inDetails as $i => $inDetail) {
            // Jumlah acak antara 5–15 tapi tidak boleh lebih dari remaining
            $maxQty = min($inDetail->remaining_stock, 15);
            $qty = rand(5, $maxQty);

            // Buat OutStock (keluar) untuk admin yang bergantian
            $admin = $admins[$i % 2];

            $outStock = OutStock::create([
                'user_id' => $admin->id,
                'date' => $startOfMonth->copy()->addDays(rand(0, $daysInMonth - 1)),
            ]);

            OutDetail::create([
                'out_stock_id' => $outStock->id,
                'product_id' => $inDetail->product_id,
                'quantity' => $qty,
                'in_detail_id' => $inDetail->id,
                'in_stock_id' => $inDetail->in_stock_id,
                'expiry_date' => $inDetail->expiry_date,
            ]);

            // Kurangi remaining_stock
            $inDetail->decrement('remaining_stock', $qty);
        }
    }
}
