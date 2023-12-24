<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\ProductController;

class CartController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function add_to_cart(Product $product, Request $request)
    {
        $user_id = Auth::id();
        $product_id = $product->id;

        $existing_cart = Cart::where('product_id', $product->id)
            ->where('user_id', $user_id)
            ->first();

        if ($existing_cart == null) {
            $request->validate([
                'amount' => 'required|gte:1|lte:' . $product->stock
            ]);

            $cart = Cart::create([
                'user_id' => $user_id,
                'product_id' => $product_id,
                'amount' => $request->amount
            ]);
        } else {
            $request->validate([
                'amount' => 'required|gte:1|lte:' . ($product->stock - $existing_cart->amount)
            ]);

            $existing_cart->update([
                'amount' => $existing_cart->amount + $request->amount
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Cart updated successfully!',
            'data' => $existing_cart,
        ]);
    }


    public function show_cart()
    {
        $user_id = Auth::id();
        $carts = Cart::where('user_id', $user_id)->get();

        return response()->json([
            'success' => true,
            'message' => 'Cart items retrieved successfully!',
            'data' => $carts,
        ], Response::HTTP_OK);
    }

    public function update_cart(Cart $cart, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|gte:1|lte:' . $cart->product->stock,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $cart->update([
            'amount' => $request->amount,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cart item updated successfully!',
            'data' => $cart,
        ], Response::HTTP_OK);
    }

    public function delete_cart(Cart $cart)
    {
        $cart->delete();

        return response()->json([
            'success' => true,
            'message' => 'Cart item deleted successfully!',
        ], Response::HTTP_OK);
    }
}
