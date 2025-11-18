<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductBatch;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BatchController extends Controller
{
    /**
     * Display batches for a product
     */
    public function index(Product $product)
    {
        $batches = $product->batches()->orderBy('expiry_date')->paginate(20);
        return view('products.batches', compact('product', 'batches'));
    }

    /**
     * Store a new batch for a product
     */
    public function store(Request $request, Product $product)
    {
        $validated = $request->validate([
            'batch_number' => 'required|string|max:255',
            'expiry_date' => 'required|date|after:today',
            'quantity_received' => 'required|integer|min:1',
            'purchase_price' => 'nullable|numeric|min:0',
        ]);

        $validated['product_id'] = $product->id;
        $validated['quantity_remaining'] = $validated['quantity_received'];
        $validated['is_active'] = true;

        // If no purchase price provided, use product's purchase price
        if (!isset($validated['purchase_price'])) {
            $validated['purchase_price'] = $product->purchase_price;
        }

        $batch = ProductBatch::create($validated);

        // Update product stock
        $product->increment('current_stock', $validated['quantity_received']);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Batch added successfully',
                'batch' => $batch
            ], 201);
        }

        return back()->with('success', 'Batch added successfully.');
    }

    /**
     * Get expiring batches (API)
     */
    public function expiring(Request $request)
    {
        $days = $request->input('days', 30);

        $batches = ProductBatch::with('product')
            ->expiringSoon($days)
            ->where('quantity_remaining', '>', 0)
            ->orderBy('expiry_date')
            ->get();

        return response()->json($batches);
    }

    /**
     * Get expired batches (API)
     */
    public function expired()
    {
        $batches = ProductBatch::with('product')
            ->expired()
            ->where('quantity_remaining', '>', 0)
            ->orderBy('expiry_date')
            ->get();

        return response()->json($batches);
    }

    /**
     * API: Create batch
     */
    public function apiStore(Request $request, Product $product)
    {
        return $this->store($request, $product);
    }
}
