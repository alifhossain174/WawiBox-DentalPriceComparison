<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\SupplierProductPrice;

class ApiController extends Controller
{
    public function compareSupplierPrice(Request $request)
    {

        $orders = $request->input('orders'); // Example Json Body: [{"product": "Dental Floss", "quantity": 5}, {"product": "Ibuprofen", "quantity": 12}]

        // suppose Two supplier have same price in that case we can implement different logic here like supplier rating to get the best supplier
        $suppliers = Supplier::orderBy('id', 'desc')->get();
        $bestSupplier = null;
        $bestPrice = PHP_INT_MAX;

        foreach ($suppliers as $supplier) {
            $totalCost = 0;

            foreach ($orders as $order) {
                $product = Product::where('name', $order['product'])->first();
                if (!$product) {
                    continue;
                }

                $quantity = $order['quantity'];
                $prices = SupplierProductPrice::where('supplier_id', $supplier->id)
                    ->where('product_id', $product->id)
                    ->orderBy('size', 'desc')
                    ->get();

                $remaining = $quantity;
                foreach ($prices as $price) {
                    while ($remaining >= $price->size) {
                        $remaining -= $price->size;
                        $totalCost += $price->price;
                    }
                }

                // If there are remaining units, buy the smallest available pack
                // we will check this if minimum pack size by the supplier is not 1 and we need to buy 1
                if ($remaining > 0) {
                    $smallestPack = $prices->last();
                    if ($smallestPack) {
                        $totalCost += $smallestPack->price;
                    }
                }
            }

            if ($totalCost < $bestPrice) {
                $bestPrice = $totalCost;
                $bestSupplier = $supplier->name;
            }
        }

        return response()->json([
            'best_supplier' => $bestSupplier,
            'total_cost' => $bestPrice
        ]);
    }
}
