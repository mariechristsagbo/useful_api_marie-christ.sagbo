<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Erreur de validation',
                'errors' => $validator->errors()
            ], 422);
        }

        $product = Product::create([
            'user_id' => $request->user()->id,
            'name' => $request->name,
            'price' => $request->price,
            'stock' => $request->stock,
            'min_stock' => $request->min_stock,
        ]);

        return response()->json($product, 201);
    }

    public function index(Request $request)
    {
        $products = Product::where('user_id', $request->user()->id)->get();
        
        return response()->json($products);
    }

    public function restock(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Erreur de validation',
                'errors' => $validator->errors()
            ], 422);
        }

        $product = Product::where('id', $id)
                         ->where('user_id', $request->user()->id)
                         ->first();

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $product->stock += $request->quantity;
        $product->save();

        return response()->json([
            'product_id' => $product->id,
            'new_stock' => $product->stock,
            'restocked_quantity' => $request->quantity
        ]);
    }
}
