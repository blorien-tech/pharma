<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of products
     */
    public function index(Request $request)
    {
        $query = Product::query();

        if ($request->has('search')) {
            $query->search($request->search);
        }

        $products = $query->latest()->paginate(20);

        return view('products.index', compact('products'));
    }

    /**
     * Show the form for creating a new product
     */
    public function create()
    {
        return view('products.create');
    }

    /**
     * Store a newly created product
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'generic_name' => 'nullable|string|max:255',
            'brand_name' => 'nullable|string|max:255',
            'sku' => 'required|string|max:255|unique:products',
            'barcode' => 'nullable|string|max:255|unique:products',
            'description' => 'nullable|string',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'current_stock' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        // Auto-generate SKU if needed
        if (empty($validated['sku'])) {
            $validated['sku'] = 'PRD-' . strtoupper(Str::random(8));
        }

        Product::create($validated);

        return redirect()->route('products.index')
            ->with('success', 'Product created successfully.');
    }

    /**
     * Show the form for editing the specified product
     */
    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    /**
     * Update the specified product
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'generic_name' => 'nullable|string|max:255',
            'brand_name' => 'nullable|string|max:255',
            'sku' => 'required|string|max:255|unique:products,sku,' . $product->id,
            'barcode' => 'nullable|string|max:255|unique:products,barcode,' . $product->id,
            'description' => 'nullable|string',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'current_stock' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $product->update($validated);

        return redirect()->route('products.index')
            ->with('success', 'Product updated successfully.');
    }

    /**
     * Remove the specified product
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Product deleted successfully.');
    }

    /**
     * API: Get products list
     */
    public function apiIndex(Request $request)
    {
        $query = Product::where('is_active', true);

        if ($request->has('search')) {
            $query->search($request->search);
        }

        $products = $query->get();

        return response()->json($products);
    }

    /**
     * API: Search products
     */
    public function search(Request $request)
    {
        $search = $request->input('q', '');

        $products = Product::where('is_active', true)
            ->search($search)
            ->with('activeBatches')
            ->limit(20)
            ->get();

        return response()->json($products);
    }

    /**
     * API: Create product
     */
    public function apiStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'generic_name' => 'nullable|string|max:255',
            'brand_name' => 'nullable|string|max:255',
            'sku' => 'required|string|max:255|unique:products',
            'barcode' => 'nullable|string|max:255|unique:products',
            'description' => 'nullable|string',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'current_stock' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
        ]);

        $product = Product::create($validated);

        return response()->json([
            'message' => 'Product created successfully',
            'product' => $product
        ], 201);
    }

    /**
     * API: Update product
     */
    public function apiUpdate(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'generic_name' => 'nullable|string|max:255',
            'brand_name' => 'nullable|string|max:255',
            'sku' => 'sometimes|string|max:255|unique:products,sku,' . $product->id,
            'barcode' => 'nullable|string|max:255|unique:products,barcode,' . $product->id,
            'description' => 'nullable|string',
            'purchase_price' => 'sometimes|numeric|min:0',
            'selling_price' => 'sometimes|numeric|min:0',
            'current_stock' => 'sometimes|integer|min:0',
            'min_stock' => 'sometimes|integer|min:0',
        ]);

        $product->update($validated);

        return response()->json([
            'message' => 'Product updated successfully',
            'product' => $product
        ]);
    }

    /**
     * API: Delete product
     */
    public function apiDestroy(Product $product)
    {
        $product->delete();

        return response()->json([
            'message' => 'Product deleted successfully'
        ]);
    }

    /**
     * API: Quick add stock (Phase 3B)
     * Quickly add stock to existing product with batch creation
     */
    public function quickAddStock(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'batch_number' => 'required|string|max:255',
            'expiry_date' => 'required|date|after:today',
            'purchase_price' => 'nullable|numeric|min:0',
        ]);

        try {
            $product = Product::findOrFail($validated['product_id']);

            // Use product's purchase price if not provided
            $purchasePrice = $validated['purchase_price'] ?? $product->purchase_price;

            // Create batch
            $batch = $product->batches()->create([
                'batch_number' => $validated['batch_number'],
                'expiry_date' => $validated['expiry_date'],
                'quantity_received' => $validated['quantity'],
                'quantity_remaining' => $validated['quantity'],
                'purchase_price' => $purchasePrice,
                'is_active' => true,
            ]);

            // Update product stock
            $product->increment('current_stock', $validated['quantity']);

            return response()->json([
                'success' => true,
                'message' => "Successfully added {$validated['quantity']} units of {$product->name}",
                'batch' => $batch,
                'product' => $product->fresh(),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error adding stock: ' . $e->getMessage()
            ], 422);
        }
    }
}
