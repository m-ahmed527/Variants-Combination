<?php

namespace App\Http\Controllers;

use App\Models\Attribute;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function create()
    {
        $attributes = Attribute::with('values')->get();
        return view('products.create', compact('attributes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'base_price' => 'nullable|numeric',
            'variants' => 'required|array',
        ]);
        // dd($request->all());
        $product = Product::create([
            'name' => $request->name,
            'base_price' => $request->base_price,
            'description' => $request->description,
        ]);

        foreach ($request->variants as $variant) {
            $productVariant = ProductVariant::create([
                'product_id' => $product->id,
                'sku' => $variant['sku'],
                'price' => $variant['price'],
                'stock' => $variant['stock'],
            ]);

            $productVariant->values()->attach($variant['attribute_value_ids']);
        }

        return redirect()->back()->with('success', 'Product created successfully.');
    }


    // public function show(Product $product)
    // {
    //     $product->load('variants.values.attribute');
    //     $attributes = Attribute::with('values')->get();
    //     return view('products.show', compact('product', 'attributes'));
    // }
    public function show(Product $product)
    {
        $product->load('variants.values.attribute');
        $attributes = Attribute::with('values')->get();

        $variantMap = [];
        foreach ($product->variants as $variant) {
            $combo = [];
            foreach ($variant->values as $value) {
                // dd($value);
                $combo[$value->attribute->id] = $value->id;
            }
            $variantMap[] = $combo;
        }
        // dd($variantMap);
        return view('products.show', compact('product', 'attributes', 'variantMap'));
    }
    public function getVariant(Request $request)
    {
        $valueIds = $request->input('attribute_value_ids');
        // dd($request->all());
        $variant = ProductVariant::where('product_id', $request->product_id)
            ->whereHas('values', function ($q) use ($valueIds) {
                $q->whereIn('attribute_value_id', $valueIds);
            }, '=', count($valueIds))
            ->with('values')
            ->first();
        // dd($variant);
        if ($variant) {
            return response()->json([
                'price' => $variant->price,
                'stock' => $variant->stock,
                'variant_id' => $variant->id,
            ]);
        }

        return response()->json(['error' => 'Variant not found'], 404);
    }
}
