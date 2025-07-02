<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Manufacturer;

class ManufacturerSeeder extends Seeder
{
    public function run(): void
    {
        Manufacturer::create([
            'name' => 'Distributor 1',
            'address' => 'Jl. Merpati No. 123, Klaten',
            'phone' => '0812' . rand(10000000, 99999999),
        ]);

        Manufacturer::create([
            'name' => 'Distributor 2',
            'address' => 'Jl. Rajawali No. 456, Surakarta',
            'phone' => '0821' . rand(10000000, 99999999),
        ]);
    }
}
