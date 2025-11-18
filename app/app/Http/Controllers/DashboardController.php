<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the dashboard
     */
    public function index()
    {
        $data = [
            'totalProducts' => Product::where('is_active', true)->count(),
            'lowStockProducts' => Product::whereColumn('current_stock', '<=', 'min_stock')
                                        ->where('is_active', true)
                                        ->count(),
            'expiredBatches' => ProductBatch::expired()->count(),
            'expiringSoonBatches' => ProductBatch::expiringSoon()->count(),
            'todaySales' => Transaction::today()->sales()->sum('total'),
            'todayTransactions' => Transaction::today()->sales()->count(),
            'recentTransactions' => Transaction::with('user')
                                             ->latest()
                                             ->take(5)
                                             ->get(),
        ];

        return view('dashboard.index', $data);
    }

    /**
     * Get dashboard statistics via API
     */
    public function stats()
    {
        $stats = [
            'products' => [
                'total' => Product::where('is_active', true)->count(),
                'low_stock' => Product::whereColumn('current_stock', '<=', 'min_stock')
                                     ->where('is_active', true)
                                     ->count(),
            ],
            'batches' => [
                'expired' => ProductBatch::expired()->count(),
                'expiring_soon' => ProductBatch::expiringSoon()->count(),
            ],
            'sales' => [
                'today_total' => Transaction::today()->sales()->sum('total'),
                'today_count' => Transaction::today()->sales()->count(),
                'month_total' => Transaction::sales()
                                           ->whereMonth('created_at', Carbon::now()->month)
                                           ->sum('total'),
            ],
        ];

        return response()->json($stats);
    }
}
