<?php
// SaaS MVP - add workspace support
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    // عرض كل المنتجات
    public function index()
    {
        $products = Product::latest()->get();
        return view('products.index', compact('products'));
    }

    // عرض نموذج إضافة منتج جديد
    public function create()
    {
        return view('products.create');
    }

    // تخزين المنتج الجديد
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'quantity' => 'required|integer',
        ]);

        Product::create($request->all());

        return redirect()->route('products.index')->with('success', 'Product added successfully.');
    }

    // عرض نموذج تعديل المنتج
    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    // حفظ التعديل
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'quantity' => 'required|integer',
        ]);

        $product->update($request->all());

        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    // حذف المنتج
    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }
}
