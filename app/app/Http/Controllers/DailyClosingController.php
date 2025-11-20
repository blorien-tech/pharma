<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Due;
use App\Models\DuePayment;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DailyClosingController extends Controller
{
    /**
     * Show daily closing summary
     */
    public function index(Request $request)
    {
        $date = $request->input('date', today()->toDateString());
        $summaryDate = Carbon::parse($date);

        // Get today's transactions
        $transactions = Transaction::whereDate('created_at', $summaryDate)
            ->where('type', 'SALE')
            ->get();

        // Calculate totals by payment method
        $cashSales = $transactions->where('payment_method', 'CASH')->sum('total');
        $cardSales = $transactions->where('payment_method', 'CARD')->sum('total');
        $mobileSales = $transactions->where('payment_method', 'MOBILE')->sum('total');
        $creditSales = $transactions->where('is_credit', true)->sum('total');
        $otherSales = $transactions->where('payment_method', 'OTHER')
            ->where('is_credit', false)
            ->sum('total');

        // Get dues created today
        $duesCreated = Due::whereDate('created_at', $summaryDate)->get();
        $totalDuesCreated = $duesCreated->sum('amount');

        // Get due payments collected today
        $duePayments = DuePayment::whereDate('created_at', $summaryDate)->get();
        $totalDuePaymentsCollected = $duePayments->sum('amount');

        // Payment breakdown for due payments
        $duePaymentsByCash = $duePayments->where('payment_method', 'CASH')->sum('amount');
        $duePaymentsByCard = $duePayments->where('payment_method', 'CARD')->sum('amount');
        $duePaymentsByMobile = $duePayments->where('payment_method', 'MOBILE')->sum('amount');
        $duePaymentsByOther = $duePayments->where('payment_method', 'OTHER')->sum('amount');

        // Total cash in hand (cash sales + cash due payments)
        $totalCashInHand = $cashSales + $duePaymentsByCash;

        $summary = [
            'date' => $summaryDate,
            'total_sales' => $transactions->sum('total'),
            'total_transactions' => $transactions->count(),
            'cash_sales' => $cashSales,
            'card_sales' => $cardSales,
            'mobile_sales' => $mobileSales,
            'credit_sales' => $creditSales,
            'other_sales' => $otherSales,
            'dues_created_count' => $duesCreated->count(),
            'total_dues_created' => $totalDuesCreated,
            'due_payments_count' => $duePayments->count(),
            'total_due_payments_collected' => $totalDuePaymentsCollected,
            'due_payments_by_cash' => $duePaymentsByCash,
            'due_payments_by_card' => $duePaymentsByCard,
            'due_payments_by_mobile' => $duePaymentsByMobile,
            'due_payments_by_other' => $duePaymentsByOther,
            'total_cash_in_hand' => $totalCashInHand,
            'total_revenue' => $transactions->sum('total') + $totalDuePaymentsCollected,
        ];

        return view('daily-closing.index', compact('summary'));
    }

    /**
     * API endpoint for daily closing data
     */
    public function getData(Request $request)
    {
        $date = $request->input('date', today()->toDateString());
        $summaryDate = Carbon::parse($date);

        // Get today's transactions
        $transactions = Transaction::whereDate('created_at', $summaryDate)
            ->where('type', 'SALE')
            ->get();

        // Calculate totals by payment method
        $cashSales = $transactions->where('payment_method', 'CASH')->sum('total');
        $cardSales = $transactions->where('payment_method', 'CARD')->sum('total');
        $mobileSales = $transactions->where('payment_method', 'MOBILE')->sum('total');
        $creditSales = $transactions->where('is_credit', true)->sum('total');
        $otherSales = $transactions->where('payment_method', 'OTHER')
            ->where('is_credit', false)
            ->sum('total');

        // Get dues created today
        $duesCreated = Due::whereDate('created_at', $summaryDate)->get();
        $totalDuesCreated = $duesCreated->sum('amount');

        // Get due payments collected today
        $duePayments = DuePayment::whereDate('created_at', $summaryDate)->get();
        $totalDuePaymentsCollected = $duePayments->sum('amount');

        // Payment breakdown for due payments
        $duePaymentsByCash = $duePayments->where('payment_method', 'CASH')->sum('amount');
        $duePaymentsByCard = $duePayments->where('payment_method', 'CARD')->sum('amount');
        $duePaymentsByMobile = $duePayments->where('payment_method', 'MOBILE')->sum('amount');
        $duePaymentsByOther = $duePayments->where('payment_method', 'OTHER')->sum('amount');

        // Total cash in hand
        $totalCashInHand = $cashSales + $duePaymentsByCash;

        return response()->json([
            'date' => $summaryDate->toDateString(),
            'total_sales' => $transactions->sum('total'),
            'total_transactions' => $transactions->count(),
            'cash_sales' => $cashSales,
            'card_sales' => $cardSales,
            'mobile_sales' => $mobileSales,
            'credit_sales' => $creditSales,
            'other_sales' => $otherSales,
            'dues_created_count' => $duesCreated->count(),
            'total_dues_created' => $totalDuesCreated,
            'due_payments_count' => $duePayments->count(),
            'total_due_payments_collected' => $totalDuePaymentsCollected,
            'due_payments_by_cash' => $duePaymentsByCash,
            'due_payments_by_card' => $duePaymentsByCard,
            'due_payments_by_mobile' => $duePaymentsByMobile,
            'due_payments_by_other' => $duePaymentsByOther,
            'total_cash_in_hand' => $totalCashInHand,
            'total_revenue' => $transactions->sum('total') + $totalDuePaymentsCollected,
        ]);
    }
}
