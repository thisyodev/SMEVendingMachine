<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        return Product::all();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string',
            'price' => 'required|integer|min:1',
            'stock' => 'required|integer|min:0',
        ]);

        return Product::create($request->only(['name', 'price', 'stock']));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name'  => 'string',
            'price' => 'integer|min:1',
            'stock' => 'integer|min:0',
        ]);

        $product->update($request->only(['name', 'price', 'stock']));
        return $product;
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return response()->noContent();
    }
}

