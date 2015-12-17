<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Product;
use App\Module;
use App\Service;

class ProductController extends Controller
{

    public function index() {
        return response()->json(Product::all());
    }

    public function products() {
        return view('products/index', ['products' => Product::all()]);
    }

    public function profile($id) {

        $product = Product::find($id);
        
        return view('products/profile', ['product'=> $product]);
    }

    public function add(Request $request) {
        $product = new Product();

        $product->name = $request->input('name');
        $product->description = $request->input('description');

        if ($product->save()) {
            return response()->json(true);
        }
    }

    public function edit(Request $request) {
        $product = Product::find($request->input('product_id'));
        $product->name = $request->input('name');
        $product->description = $request->input('description');

        if ($product->save()) {
            return redirect()->back();
        }
     }

    public function delete(Request $request) {

        $product = Product::find($request->input('id'));

        if ($product->delete()) {
            return response()->json(true);
        }
    }
}