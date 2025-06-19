<?php

namespace App\Http\Controllers;

use App\Models\ProductVariant;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function add(Request $request)
    {
        $request->validate([
            'variant_id' => 'required|exists:product_variants,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $variant = ProductVariant::with(['product', 'values.attribute'])->findOrFail($request->variant_id);
        $product = $variant->product;

        $cart = session()->get('cart', []);

        $variantId = $variant->id;
        $productId = $product->id;
        $productPrice = ($variant->price + $product->base_price) * $request->quantity;
        // Unique key by variant (as you want variant-wise entries)
        $key = $variantId;

        $attributes = $variant->values->pluck('value')->toArray();

        if (isset($cart['items'][$key])) {
            $cart['items'][$key]['quantity'] = $request->quantity;
            $cart['items'][$key]['total_price'] = $productPrice;
            $this->recalculateCart($cart);
        } else {
            $cart['items'][$key] = [
                'product_id' => $productId,
                'variant_id' => $variantId,
                'quantity' => $request->quantity,
                'total_price' => $productPrice,
                'product' => $product, // Optional: Reduce payload
                'variant' => [
                    'price' => $variant->price,
                    'attributes' => $attributes
                ]
            ];
        }
        $this->recalculateCart($cart);
        return response()->json([
            'status' => 'success',
            'message' => 'Product added to cart.',
        ]);
    }

    private function recalculateCart($cart)
    {


        $cart['total_amount'] = array_sum(array_column($cart['items'], 'total_price')) ?? 0;
        $cart['total_items'] = count($cart['items'])  ?? 0;

        session()->put('cart', $cart);
    }
}
