<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Product;
use App\Models\Category;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil ID kategori "Pakan Ternak"
        $category = Category::where('name', 'Pakan Ternak')->first();

        if (!$category) {
            $this->command->error('Kategori "Pakan Ternak" tidak ditemukan. Jalankan CategorySeeder dulu.');
            return;
        }

        $products = [
            ['code' => '002AD1', 'name' => 'AD 1'],
            ['code' => '002AD2', 'name' => 'AD 2'],
            ['code' => '002BR0', 'name' => 'BR 0 Comfeed'],
            ['code' => '002BR1SL', 'name' => 'BR1 COMFEED SL'],
            ['code' => '002BRC', 'name' => 'BR 1 COMFEED'],
        ];

        foreach ($products as $product) {
            Product::create([
                'category_id' => $category->id,
                'name' => $product['name'],
                'code' => $product['code'],
                'stock' => rand(10, 50),
                'min_stock' => rand(2, 10),
                'pcs_per_box' => rand(1, 5),
            ]);
        }
    }
}
