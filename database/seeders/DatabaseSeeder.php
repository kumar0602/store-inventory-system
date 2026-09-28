<?php
namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Product;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder {
    public function run(): void {
        Customer::factory(5)->create();

        Product::factory()->create([
            'name' => 'Colgate Toothpaste',
            'code' => 'COLG-01',
            'price_per_unit' => 50.00,
            'tax_percentage' => 5.00,
            'stock_on_hand' => 15,
        ]);

        Product::factory()->create([
            'name' => 'Parle-G Biscuit',
            'code' => 'PARL-01',
            'price_per_unit' => 10.00,
            'tax_percentage' => 5.00,
            'stock_on_hand' => 3, // Low stock example
        ]);

        Product::factory(3)->create();
    }
}