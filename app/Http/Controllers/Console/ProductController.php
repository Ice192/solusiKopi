<?php

namespace App\Http\Controllers\Console;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Outlet;
use App\Models\Category;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use Illuminate\Http\Request;
use App\DataTables\ProductDataTable;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(ProductDataTable $dataTable)
    {
        return $dataTable->render('console.products.index');
    }

    public function create()
    {
        $outlets = Outlet::all();
        $categories = Category::all();
        return view('console.products.create', compact('outlets', 'categories'));
    }

    public function store(StoreProductRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image_url'] = handleUpload('image', 'products/images');
        }

        Product::create($data);

        return redirect()->route('products.index')->with('success', 'Product created successfully.');
    }

    public function show(Product $product)
    {
        return view('console.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $outlets = Outlet::all();
        $categories = Category::all();
        return view('console.products.edit', compact('product', 'outlets', 'categories'));
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image_url'] = handleUpload('image', 'products/images', $product->image_url);
        } elseif (isset($data['remove_image']) && $data['remove_image'] == 1) {
            if ($product->image_url) {
                deleteFileIfExist(public_path($product->image_url));
                $data['image_url'] = null;
            }
        }

        $product->update($data);

        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        if ($product->image_url) {
            deleteFileIfExist(public_path($product->image_url));
        }
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }
}
