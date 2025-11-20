<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\Transaction;
use App\Models\Due;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the dashboard
     */
    public function index()
    {
        // Calculate dues statistics
        $pendingDues = Due::where('status', 'PENDING')->get();
        $totalPendingDues = $pendingDues->sum('remaining_amount');
        $overdueDues = $pendingDues->filter(function($due) {
            return $due->due_date && Carbon::parse($due->due_date)->isPast();
        })->count();

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
            // Phase 3B: Dues statistics
            'totalPendingDues' => $totalPendingDues,
            'pendingDuesCount' => $pendingDues->count(),
            'overdueDuesCount' => $overdueDues,
            'recentDues' => Due::with('payments')
                               ->latest()
                               ->take(5)
                               ->get(),
        ];

        return view('dashboard.index', $data);
    }

    /**
     * Get dashboard statistics via API (Phase 3B: Cached for 5 minutes)
     */
    public function stats()
    {
        // Cache key includes current date for daily stats
        $cacheKey = 'dashboard_stats_' . now()->format('Y-m-d-H-i');

        $stats = Cache::remember($cacheKey, 300, function () {
            return [
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
        });

        return response()->json($stats);
    }
}
