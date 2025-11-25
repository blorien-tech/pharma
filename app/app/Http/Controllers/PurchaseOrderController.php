<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\StorageLocation;
use App\Services\PurchaseOrderService;
use App\Services\LocationService;
use Illuminate\Http\Request;

class PurchaseOrderController extends Controller
{
    protected $purchaseOrderService;
    protected $locationService;

    public function __construct(PurchaseOrderService $purchaseOrderService, LocationService $locationService = null)
    {
        $this->purchaseOrderService = $purchaseOrderService;
        $this->locationService = $locationService;
    }

    /**
     * Display a listing of purchase orders
     */
    public function index(Request $request)
    {
        $query = PurchaseOrder::with(['supplier', 'user']);

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Filter by supplier
        if ($request->has('supplier') && $request->supplier !== '') {
            $query->where('supplier_id', $request->supplier);
        }

        // Filter by date
        if ($request->has('date') && $request->date !== '') {
            $query->whereDate('order_date', $request->date);
        }

        $purchaseOrders = $query->latest('order_date')->paginate(20);
        $suppliers = Supplier::active()->orderBy('name')->get();

        return view('purchase-orders.index', compact('purchaseOrders', 'suppliers'));
    }

    /**
     * Show the form for creating a new purchase order
     */
    public function create()
    {
        $suppliers = Supplier::active()->orderBy('name')->get();
        $products = Product::where('is_active', true)->orderBy('name')->get();

        return view('purchase-orders.create', compact('suppliers', 'products'));
    }

    /**
     * Store a newly created purchase order
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'order_date' => 'required|date',
            'expected_delivery_date' => 'nullable|date|after:order_date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'shipping' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        try {
            $purchaseOrder = $this->purchaseOrderService->createPurchaseOrder($validated);

            return redirect()->route('purchase-orders.show', $purchaseOrder)
                ->with('success', 'Purchase order created successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    /**
     * Display the specified purchase order
     */
    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['supplier', 'user', 'items.product']);

        return view('purchase-orders.show', compact('purchaseOrder'));
    }

    /**
     * Show the form for receiving stock
     */
    public function showReceive(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status === 'RECEIVED') {
            return redirect()->route('purchase-orders.show', $purchaseOrder)
                ->with('error', 'This purchase order has already been received.');
        }

        $purchaseOrder->load(['supplier', 'items.product']);

        // Get available storage locations
        $availableLocations = StorageLocation::active()
            ->whereNotNull('capacity') // Only show locations with capacity
            ->with('parent')
            ->get()
            ->filter(function ($location) {
                return !$location->isFull();
            });

        // Get location suggestions for each product
        $suggestions = [];
        if ($this->locationService) {
            foreach ($purchaseOrder->items as $item) {
                $suggested = $this->locationService->suggestLocationForProduct($item->product);
                if ($suggested) {
                    $suggestions[$item->id] = [
                        'location' => $suggested,
                        'reason' => $this->getSuggestionReason($item->product, $suggested)
                    ];
                }
            }
        }

        return view('purchase-orders.receive', compact('purchaseOrder', 'availableLocations', 'suggestions'));
    }

    /**
     * Get the reason for location suggestion
     */
    protected function getSuggestionReason($product, $location): string
    {
        // Check if product already has batches in this location
        $existingBatches = $product->batches()
            ->where('storage_location_id', $location->id)
            ->count();

        if ($existingBatches > 0) {
            return __('locations.suggestion_same_product');
        }

        if ($location->getRemainingCapacity() !== null) {
            return __('locations.suggestion_available_space');
        }

        return __('locations.suggested_location');
    }

    /**
     * Process receiving stock
     */
    public function receive(Request $request, PurchaseOrder $purchaseOrder)
    {
        $validated = $request->validate([
            'received_date' => 'required|date',
            'items' => 'required|array',
            'items.*.id' => 'required|exists:purchase_order_items,id',
            'items.*.quantity_received' => 'required|integer|min:0',
            'items.*.batch_number' => 'required|string',
            'items.*.expiry_date' => 'required|date|after:today',
            'items.*.storage_location_id' => 'nullable|exists:storage_locations,id',
        ]);

        try {
            $this->purchaseOrderService->receiveStock($purchaseOrder, $validated);

            return redirect()->route('purchase-orders.show', $purchaseOrder)
                ->with('success', 'Stock received successfully. Inventory has been updated.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    /**
     * Cancel a purchase order
     */
    public function cancel(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status === 'RECEIVED') {
            return back()->with('error', 'Cannot cancel a received purchase order.');
        }

        $purchaseOrder->update(['status' => 'CANCELLED']);

        return redirect()->route('purchase-orders.index')
            ->with('success', 'Purchase order cancelled successfully.');
    }

    /**
     * API: Get purchase order details
     */
    public function apiShow(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['supplier', 'user', 'items.product']);
        return response()->json($purchaseOrder);
    }
}
