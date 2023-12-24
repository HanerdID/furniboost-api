<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use App\Models\Cart;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\ProductResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Response;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductController extends Controller
{
    /**
     * index
     *
     * @return void
     */

    // public function create_product()
    // {
    //     return view('create_product');
    // }

    /**
     * store
     *
     * @param  mixed $request
     * @return void
     */
    public function store_product(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required',
            'description' => 'required',
            'image' => 'required',
            'stock' => 'required'
        ]);

        $file = $request->file('image');
        $path = time() . '_' . $request->name . '.' . $file->getClientOriginalExtension();

        Storage::disk('local')->put('public/products' . $path, file_get_contents($file));

        $product = Product::create([
            'name' => $request->name,
            'price' => $request->price,
            'description' => $request->description,
            'image' => $path,
            'stock' => $request->stock
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Product Data Successfully Added!',
            'data' => $product,
        ], 201); // 201 adalah kode status Created
    }

    public function index_product()
    {
        $products = Product::all();

        return response()->json([
            'success' => true,
            'message' => 'List Data Products',
            'data' => $products,
        ]);
    }

    public function show_product(Product $product)
    {
        if ($product) {
            return response()->json([
                'success' => true,
                'message' => 'Data Product Found!',
                'data' => $product,
            ], Response::HTTP_OK);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Product not found.',
            ], Response::HTTP_NOT_FOUND);
        }
    }

    public function update_product(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required',
            'description' => 'required',
            'image' => 'required',
            'stock' => 'required'
        ]);

        $file = $request->file('image');
        $path = time() . '_' . $request->name . '.' . $file->getClientOriginalExtension();

        Storage::disk('local')->put('public/' . $path, file_get_contents($file));

        $product->update([
            'name' => $request->name,
            'price' => $request->price,
            'description' => $request->description,
            'image' => $path,
            'stock' => $request->stock
        ]);

        return response()->json([
            'success' => true,
            'message'=> 'Product Updated',
            'data' => $product,
        ]);
    }

    public function delete_product(Product $product)
    {
        if ($product) {
            Storage::delete('public/products/' . $product->image);
            $product->delete();

            return response()->json([
                'success' => true,
                'message' => 'Product data deleted successfully!',
            ], Response::HTTP_OK);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Product not found.',
            ], Response::HTTP_NOT_FOUND);
        }
    }
}
