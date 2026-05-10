<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    // Show all products
    public function index(Request $request)
{
    $search = $request->search;

    $products = Product::where('name', 'LIKE', "%$search%")->get();

    return view('admin.products.index', compact('products'));
}

    // Show add product form
    public function create()
    {
        return view('products.create');
    }

    // Store product in database
    public function store(Request $request)
    {
        Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock,
            'image' => $request->image
        ]);

        return redirect('/products');
    }

    // Show edit form
    public function edit($id)
    {
        $product = Product::findOrFail($id);

        return view('products.edit', compact('product'));
    }

    // Update product
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $product->update([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock,
            'image' => $request->image
        ]);

        return redirect('/products');
    }

    // Delete product
    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        $product->delete();

        return redirect('/products');
    }
}