<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\InStock;
use App\Models\InDetail;
use App\Models\Product;
use App\Models\User;
use App\Models\Manufacturer;
use Illuminate\Support\Carbon;

class InputSeeder extends Seeder
{
    public function run(): void
    {
        $admin1 = User::where('email', 'admin1@example.com')->first();
        $admin2 = User::where('email', 'admin2@example.com')->first();
        $distributors = Manufacturer::get();

        if (!$admin1 || !$admin2 || $distributors->count() < 2) {
            $this->command->error('Pastikan admin1, admin2, dan minimal 2 distributor sudah ada.');
            return;
        }

        $admins = [$admin1, $admin2];
        $expiry = Carbon::create(2026, 12, 30);

        $weekDates = [
            Carbon::now()->startOfMonth()->addDays(1),
            Carbon::now()->startOfMonth()->addDays(8),
            Carbon::now()->startOfMonth()->addDays(15),
            Carbon::now()->startOfMonth()->addDays(22),
        ];

        $products = Product::all();

        foreach ($products as $index => $product) {
            for ($i = 0; $i < 4; $i++) {
               $today = Carbon::today();
                $product = Product::inRandomOrder()->first();
                $admin = $admins[array_rand($admins)];
                $manufacturer = $distributors->random();
                $qty = rand(20, 100);

                $inStock = InStock::create([
                    'user_id' => $admin->id,
                    'date' => $today,
                ]);

                InDetail::create([
                    'in_stock_id' => $inStock->id,
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'remaining_stock' => $qty,
                    'expiry_date' => Carbon::create(2026, 12, 30),
                    'manufacturer_id' => $manufacturer->id,
                ]);
            }
        }
    }
}
