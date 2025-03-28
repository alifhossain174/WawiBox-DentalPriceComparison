<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Product;
use App\Models\Supplier;
use App\Models\SupplierProductPrice;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);


        $supplierA = Supplier::create(['name' => 'Supplier A']);
        $supplierB = Supplier::create(['name' => 'Supplier B']);

        $dentalFloss = Product::create(['name' => 'Dental Floss']);
        $ibuprofen = Product::create(['name' => 'Ibuprofen']);

        // Supplier A Prices
        SupplierProductPrice::create(['supplier_id' => $supplierA->id, 'product_id' => $dentalFloss->id, 'size' => 1, 'price' => 9]);
        SupplierProductPrice::create(['supplier_id' => $supplierA->id, 'product_id' => $dentalFloss->id, 'size' => 20, 'price' => 160]);
        SupplierProductPrice::create(['supplier_id' => $supplierA->id, 'product_id' => $ibuprofen->id, 'size' => 1, 'price' => 5]);
        SupplierProductPrice::create(['supplier_id' => $supplierA->id, 'product_id' => $ibuprofen->id, 'size' => 10, 'price' => 48]);

        // Supplier B Prices
        SupplierProductPrice::create(['supplier_id' => $supplierB->id, 'product_id' => $dentalFloss->id, 'size' => 1, 'price' => 8]);
        SupplierProductPrice::create(['supplier_id' => $supplierB->id, 'product_id' => $dentalFloss->id, 'size' => 10, 'price' => 71]);
        SupplierProductPrice::create(['supplier_id' => $supplierB->id, 'product_id' => $ibuprofen->id, 'size' => 1, 'price' => 6]);
        SupplierProductPrice::create(['supplier_id' => $supplierB->id, 'product_id' => $ibuprofen->id, 'size' => 5, 'price' => 25]);
        SupplierProductPrice::create(['supplier_id' => $supplierB->id, 'product_id' => $ibuprofen->id, 'size' => 100, 'price' => 410]);
    }
}
