<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Customer;
use App\Models\TransactionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Display reports dashboard
     */
    public function index()
    {
        return view('reports.index');
    }

    /**
     * Sales Report
     */
    public function sales(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        // Get sales transactions
        $transactions = Transaction::where('type', 'SALE')
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->with(['items.product', 'user', 'customer'])
            ->latest()
            ->get();

        // Calculate totals
        $totalSales = $transactions->sum('total');
        $totalDiscount = $transactions->sum('discount');
        $totalTransactions = $transactions->count();
        $averageTransactionValue = $totalTransactions > 0 ? $totalSales / $totalTransactions : 0;

        // Group by date
        $salesByDate = $transactions->groupBy(function($transaction) {
            return $transaction->created_at->format('Y-m-d');
        })->map(function($dayTransactions) {
            return [
                'date' => $dayTransactions->first()->created_at->format('M d, Y'),
                'transactions' => $dayTransactions->count(),
                'total' => $dayTransactions->sum('total'),
            ];
        });

        // Payment method breakdown
        $paymentMethods = $transactions->groupBy('payment_method')->map(function($methodTransactions) {
            return [
                'count' => $methodTransactions->count(),
                'total' => $methodTransactions->sum('total'),
            ];
        });

        return view('reports.sales', compact(
            'transactions',
            'totalSales',
            'totalDiscount',
            'totalTransactions',
            'averageTransactionValue',
            'salesByDate',
            'paymentMethods',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Profit Report
     */
    public function profit(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        // Get transaction items with profit calculation
        $items = TransactionItem::whereHas('transaction', function($query) use ($startDate, $endDate) {
            $query->where('type', 'SALE')
                ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        })
        ->with(['product', 'transaction'])
        ->get();

        // Calculate totals
        $totalRevenue = $items->sum('total');
        $totalCost = $items->sum(function($item) {
            return $item->quantity * $item->product->purchase_price;
        });
        $totalProfit = $totalRevenue - $totalCost;
        $profitMargin = $totalRevenue > 0 ? ($totalProfit / $totalRevenue) * 100 : 0;

        // Group by product
        $profitByProduct = $items->groupBy('product_id')->map(function($productItems) {
            $product = $productItems->first()->product;
            $revenue = $productItems->sum('total');
            $cost = $productItems->sum(function($item) {
                return $item->quantity * $item->product->purchase_price;
            });
            $profit = $revenue - $cost;
            $quantitySold = $productItems->sum('quantity');

            return [
                'name' => $product->name,
                'quantity_sold' => $quantitySold,
                'revenue' => $revenue,
                'cost' => $cost,
                'profit' => $profit,
                'margin' => $revenue > 0 ? ($profit / $revenue) * 100 : 0,
            ];
        })->sortByDesc('profit')->take(20);

        return view('reports.profit', compact(
            'totalRevenue',
            'totalCost',
            'totalProfit',
            'profitMargin',
            'profitByProduct',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Inventory Report
     */
    public function inventory()
    {
        // Get all active products with batch information
        $products = Product::with(['batches' => function($query) {
            $query->where('quantity_remaining', '>', 0)->orderBy('expiry_date');
        }])
        ->where('is_active', true)
        ->get();

        // Calculate inventory value
        $totalInventoryValue = $products->sum(function($product) {
            return $product->current_stock * $product->purchase_price;
        });

        $totalRetailValue = $products->sum(function($product) {
            return $product->current_stock * $product->selling_price;
        });

        $potentialProfit = $totalRetailValue - $totalInventoryValue;

        // Low stock products
        $lowStockProducts = $products->filter(function($product) {
            return $product->isLowStock();
        });

        // Products by value
        $productsByValue = $products->map(function($product) {
            return [
                'name' => $product->name,
                'sku' => $product->sku,
                'stock' => $product->current_stock,
                'purchase_price' => $product->purchase_price,
                'selling_price' => $product->selling_price,
                'total_value' => $product->current_stock * $product->purchase_price,
            ];
        })->sortByDesc('total_value')->take(20);

        return view('reports.inventory', compact(
            'products',
            'totalInventoryValue',
            'totalRetailValue',
            'potentialProfit',
            'lowStockProducts',
            'productsByValue'
        ));
    }

    /**
     * Top Selling Products Report
     */
    public function topProducts(Request $request)
    {
        $period = $request->input('period', 'month'); // week, month, year

        $startDate = match($period) {
            'week' => Carbon::now()->startOfWeek(),
            'year' => Carbon::now()->startOfYear(),
            default => Carbon::now()->startOfMonth(),
        };

        // Get top selling products
        $topProducts = TransactionItem::whereHas('transaction', function($query) use ($startDate) {
            $query->where('type', 'SALE')
                ->where('created_at', '>=', $startDate);
        })
        ->select('product_id', DB::raw('SUM(quantity) as total_quantity'), DB::raw('SUM(total) as total_revenue'))
        ->groupBy('product_id')
        ->orderByDesc('total_quantity')
        ->with('product')
        ->limit(20)
        ->get()
        ->map(function($item) {
            return [
                'product' => $item->product,
                'quantity_sold' => $item->total_quantity,
                'revenue' => $item->total_revenue,
                'average_price' => $item->total_revenue / $item->total_quantity,
            ];
        });

        return view('reports.top-products', compact('topProducts', 'period', 'startDate'));
    }

    /**
     * Supplier Performance Report
     */
    public function suppliers(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        // Get suppliers with purchase order statistics
        $suppliers = Supplier::withCount(['purchaseOrders' => function($query) use ($startDate, $endDate) {
            $query->whereBetween('order_date', [$startDate, $endDate]);
        }])
        ->with(['purchaseOrders' => function($query) use ($startDate, $endDate) {
            $query->whereBetween('order_date', [$startDate, $endDate]);
        }])
        ->get()
        ->map(function($supplier) {
            $totalSpent = $supplier->purchaseOrders->sum('total');
            $receivedOrders = $supplier->purchaseOrders->where('status', 'RECEIVED')->count();
            $pendingOrders = $supplier->purchaseOrders->whereIn('status', ['PENDING', 'ORDERED'])->count();

            return [
                'supplier' => $supplier,
                'total_orders' => $supplier->purchase_orders_count,
                'total_spent' => $totalSpent,
                'received_orders' => $receivedOrders,
                'pending_orders' => $pendingOrders,
            ];
        })
        ->sortByDesc('total_spent');

        $totalSpent = $suppliers->sum('total_spent');
        $totalOrders = $suppliers->sum('total_orders');

        return view('reports.suppliers', compact(
            'suppliers',
            'totalSpent',
            'totalOrders',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Customer Credit Report
     */
    public function customers()
    {
        // Get customers with credit enabled
        $customers = Customer::where('credit_enabled', true)
            ->withCount('transactions')
            ->get()
            ->map(function($customer) {
                return [
                    'customer' => $customer,
                    'credit_limit' => $customer->credit_limit,
                    'current_balance' => $customer->current_balance,
                    'available_credit' => $customer->availableCredit(),
                    'utilization' => $customer->credit_limit > 0
                        ? ($customer->current_balance / $customer->credit_limit) * 100
                        : 0,
                    'is_overdue' => $customer->isOverdue(),
                    'total_transactions' => $customer->transactions_count,
                ];
            });

        $totalCreditLimit = $customers->sum('credit_limit');
        $totalOutstanding = $customers->sum('current_balance');
        $totalAvailable = $customers->sum('available_credit');
        $overdueCustomers = $customers->filter(fn($c) => $c['is_overdue']);

        return view('reports.customers', compact(
            'customers',
            'totalCreditLimit',
            'totalOutstanding',
            'totalAvailable',
            'overdueCustomers'
        ));
    }
}
