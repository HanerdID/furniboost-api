<?php

namespace App\Http\Controllers\Api;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{
    public function checkout()
    {
        $user_id = Auth::id();
        $carts = Cart::where('user_id', $user_id)->get();

        if ($carts->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Cart is empty.',
            ], Response::HTTP_BAD_REQUEST);
        }

        $order = Order::create([
            'user_id' => $user_id
        ]);

        foreach ($carts as $cart) {
            $product = Product::find($cart->product_id);
            $product->update([
                'stock' => $product->stock - $cart->amount,
            ]);

            Transaction::create([
                'amount' => $cart->amount,
                'product_id' => $cart->product_id,
                'order_id' => $order->id,
            ]);

            $cart->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Checkout successful!',
            'order' => $order,
        ], Response::HTTP_CREATED);
    }

    public function index_order()
    {
        $user = Auth::user();
        $admin = $user->is_admin;

        if ($admin) {
            $orders = Order::all();
        } else {
            $orders = Order::where('user_id', $user->id)->get();
        }

        return response()->json([
            'success' => true,
            'orders' => $orders,
        ], Response::HTTP_OK);
    }

    public function show_order(Order $order)
    {
        $user = Auth::user();
        $admin = $user->is_admin;

        if ($admin || $order->user_id == $user->id) {
            return response()->json([
                'success' => true,
                'order' => $order,
            ], Response::HTTP_OK);
        }

        return response()->json([
            'success' => false,
            'message' => 'Unauthorized.',
        ], Response::HTTP_UNAUTHORIZED);
    }

    public function submit_payment_receipt(Order $order, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'payment_receipt' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $file = $request->file('payment_receipt');
        $path = time() . '_' . $order->id . '.' . $file->getClientOriginalExtension();

        Storage::disk('public')->put($path, file_get_contents($file));

        $order->update([
            'payment_receipt' => $path,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Payment receipt submitted successfully!',
            'order' => $order,
        ], Response::HTTP_OK);
    }

    public function confirm_payment(Order $order)
    {
        $order->update([
            'is_paid' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Payment confirmed!',
        ], Response::HTTP_OK);
    }
}

