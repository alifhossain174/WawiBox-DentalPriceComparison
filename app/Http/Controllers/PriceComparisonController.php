<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Supplier;
use App\Models\SupplierProductPrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class PriceComparisonController extends Controller
{

    public function homePage(Request $request)
    {

        if ($request->ajax()) {

            $data = DB::table('supplier_product_prices')
                        ->join('products', 'supplier_product_prices.product_id', '=', 'products.id')
                        ->join('suppliers', 'supplier_product_prices.supplier_id', '=', 'suppliers.id')
                        ->select('products.name as product', 'suppliers.name as supplier', 'supplier_product_prices.size', 'supplier_product_prices.price')
                        ->get();

            return Datatables::of($data)
                    ->editColumn('price', function($data) {
                        return $data->price." EUR";
                    })
                    ->addIndexColumn()
                    ->make(true);

        }

        $products = Product::all();

        return view('welcome', compact('products'));
    }

    public function addAnotherProduct()
    {
        $products = Product::all();
        $returnHTML = view('more_product', compact('products'))->render();
        return response()->json(['variant' => $returnHTML]);
    }

    public function compareSupplierPrice(Request $request)
    {

        $product_ids = explode(',', $request->product_ids);
        $product_qts = explode(',', $request->product_qtys);

        $orders = []; // Example: [{"product": "Dental Floss", "quantity": 5}, {"product": "Ibuprofen", "quantity": 12}]
        foreach ($product_ids as $index => $product_id) {
            $orders[] = [
                "product_id" => (int) $product_id,
                "quantity" => (int) $product_qts[$index] // Convert to integer
            ];
        }

        // suppose Two supplier have same price in that case we can implement different logic here like supplier rating to get the best supplier
        $suppliers = Supplier::orderBy('id', 'desc')->get();
        $bestSupplier = null;
        $bestPrice = PHP_INT_MAX;

        foreach ($suppliers as $supplier) {
            $totalCost = 0;

            foreach ($orders as $order) {
                $product = Product::where('id', $order['product_id'])->first();
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

    public function findBestPrice(Request $request){

        $productName = $request->product_name;
        $quantity = $request->qty;

    }
}
