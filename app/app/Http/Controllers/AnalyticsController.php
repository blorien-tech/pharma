<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Product;
use App\Models\Customer;
use App\Models\TransactionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    /**
     * Display analytics dashboard
     */
    public function index()
    {
        // Sales trend data (last 30 days)
        $salesTrend = $this->getSalesTrendData();

        // Revenue by payment method
        $paymentMethodData = $this->getPaymentMethodData();

        // Top 10 products by revenue
        $topProductsData = $this->getTopProductsData();

        // Inventory status
        $inventoryData = $this->getInventoryData();

        // Customer credit status
        $creditData = $this->getCreditData();

        // Monthly comparison
        $monthlyComparison = $this->getMonthlyComparison();

        return view('analytics.index', compact(
            'salesTrend',
            'paymentMethodData',
            'topProductsData',
            'inventoryData',
            'creditData',
            'monthlyComparison'
        ));
    }

    /**
     * Get sales trend data for last 30 days
     */
    private function getSalesTrendData()
    {
        $days = [];
        $sales = [];
        $transactions = [];

        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dayTransactions = Transaction::where('type', 'SALE')
                ->whereDate('created_at', $date)
                ->get();

            $days[] = $date->format('M d');
            $sales[] = $dayTransactions->sum('total');
            $transactions[] = $dayTransactions->count();
        }

        return [
            'labels' => $days,
            'sales' => $sales,
            'transactions' => $transactions,
        ];
    }

    /**
     * Get payment method breakdown
     */
    private function getPaymentMethodData()
    {
        $data = Transaction::where('type', 'SALE')
            ->where('created_at', '>=', Carbon::now()->startOfMonth())
            ->select('payment_method', DB::raw('SUM(total) as total'))
            ->groupBy('payment_method')
            ->get();

        return [
            'labels' => $data->pluck('payment_method')->toArray(),
            'values' => $data->pluck('total')->toArray(),
        ];
    }

    /**
     * Get top products by revenue
     */
    private function getTopProductsData()
    {
        $data = TransactionItem::whereHas('transaction', function($query) {
            $query->where('type', 'SALE')
                ->where('created_at', '>=', Carbon::now()->startOfMonth());
        })
        ->select('product_id', DB::raw('SUM(total) as revenue'))
        ->groupBy('product_id')
        ->orderByDesc('revenue')
        ->limit(10)
        ->with('product')
        ->get();

        return [
            'labels' => $data->pluck('product.name')->toArray(),
            'values' => $data->pluck('revenue')->toArray(),
        ];
    }

    /**
     * Get inventory data
     */
    private function getInventoryData()
    {
        $products = Product::where('is_active', true)->get();

        $lowStock = $products->filter(fn($p) => $p->isLowStock())->count();
        $adequateStock = $products->filter(fn($p) => !$p->isLowStock() && $p->current_stock > 0)->count();
        $outOfStock = $products->filter(fn($p) => $p->current_stock == 0)->count();

        return [
            'labels' => ['Low Stock', 'Adequate Stock', 'Out of Stock'],
            'values' => [$lowStock, $adequateStock, $outOfStock],
        ];
    }

    /**
     * Get customer credit data
     */
    private function getCreditData()
    {
        $customers = Customer::where('credit_enabled', true)->get();

        $totalCredit = $customers->sum('credit_limit');
        $usedCredit = $customers->sum('current_balance');
        $availableCredit = $totalCredit - $usedCredit;

        return [
            'labels' => ['Used Credit', 'Available Credit'],
            'values' => [$usedCredit, $availableCredit],
        ];
    }

    /**
     * Get monthly comparison data
     */
    private function getMonthlyComparison()
    {
        $currentMonth = Transaction::where('type', 'SALE')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('total');

        $lastMonth = Transaction::where('type', 'SALE')
            ->whereMonth('created_at', Carbon::now()->subMonth()->month)
            ->whereYear('created_at', Carbon::now()->subMonth()->year)
            ->sum('total');

        $growth = $lastMonth > 0 ? (($currentMonth - $lastMonth) / $lastMonth) * 100 : 0;

        return [
            'current' => $currentMonth,
            'last' => $lastMonth,
            'growth' => $growth,
        ];
    }

    /**
     * API endpoint for real-time sales data
     */
    public function salesData(Request $request)
    {
        $period = $request->input('period', '7days');

        $days = match($period) {
            '7days' => 7,
            '30days' => 30,
            '90days' => 90,
            default => 7,
        };

        $data = [];
        $labels = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $total = Transaction::where('type', 'SALE')
                ->whereDate('created_at', $date)
                ->sum('total');

            $labels[] = $date->format('M d');
            $data[] = $total;
        }

        return response()->json([
            'labels' => $labels,
            'data' => $data,
        ]);
    }
}
