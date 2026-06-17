<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();
        $totalQuantity = $products->sum('quantity');
        $totalValue = $products->sum(function($product) {
            return $product->quantity * $product->value;
        });

        $averageValue = $totalQuantity > 0 ? $totalValue / $totalQuantity : 0;

        return view('products.index', compact('products', 'totalQuantity', 'averageValue'));
    }



    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'quantity' => 'required|integer|min:0',
            'value' => 'required|numeric|min:0|max:999999.99',
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        $product = Product::create([
            'name' => $validated['name'],
            'quantity' => $validated['quantity'],
            'value' => $validated['value'],
        ]);

        $this->storeImage($request, $product);

        return redirect()->route('products.index')->with('success', 'Товар додано.');
    }

    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        $product->update([
            'name' => $validated['name'],
        ]);

        $this->storeImage($request, $product);

        return redirect()->route('products.index')->with('success', 'Товар оновлено.');
    }

    public function quickUpdate(Request $request, Product $product)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:0',
            'value' => 'required|numeric|min:0|max:999999.99',
        ]);

        $product->update($validated);

        return redirect()->route('products.index')->with('success', 'Кількість і цінність оновлено.');
    }

    public function destroy(Product $product)
    {
        $this->deleteImage($product->image_path);

        $product->delete();

        return redirect()->route('products.index')->with('success', 'Товар видалено.');
    }

    private function storeImage(Request $request, Product $product): void
    {
        if (!$request->hasFile('image')) {
            return;
        }

        $directory = public_path('fort/images/products');
        File::ensureDirectoryExists($directory);

        $oldPath = $product->image_path;
        $file = $request->file('image');
        $filename = $product->id . '-' . Str::random(12) . '.' . $file->extension();

        $file->move($directory, $filename);

        $product->update([
            'image_path' => 'fort/images/products/' . $filename,
        ]);

        $this->deleteImage($oldPath);
    }

    private function deleteImage(?string $path): void
    {
        if (!$path) {
            return;
        }

        $path = str_replace('\\', '/', $path);

        if (!str_starts_with($path, 'fort/images/products/')) {
            return;
        }

        $fullPath = public_path($path);

        if (File::exists($fullPath)) {
            File::delete($fullPath);
        }
    }
}
