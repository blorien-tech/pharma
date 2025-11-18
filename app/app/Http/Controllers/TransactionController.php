<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Services\PosService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TransactionController extends Controller
{
    protected $posService;

    public function __construct(PosService $posService)
    {
        $this->posService = $posService;
    }

    /**
     * Display list of transactions
     */
    public function index(Request $request)
    {
        $query = Transaction::with('user')->latest();

        // Filter by type
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        // Filter by date
        if ($request->has('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $transactions = $query->paginate(20);

        return view('transactions.index', compact('transactions'));
    }

    /**
     * Display a single transaction
     */
    public function show(Transaction $transaction)
    {
        $transaction->load('items.product', 'items.batch', 'user');
        return view('transactions.show', compact('transaction'));
    }

    /**
     * API: Complete a sale
     */
    public function complete(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'payment_method' => 'required|in:CASH,CARD,MOBILE,OTHER',
            'discount' => 'nullable|numeric|min:0',
            'amount_paid' => 'nullable|numeric|min:0',
        ]);

        try {
            $transaction = $this->posService->processSale(
                $validated['items'],
                [
                    'payment_method' => $validated['payment_method'],
                    'discount' => $validated['discount'] ?? 0,
                    'amount_paid' => $validated['amount_paid'] ?? null,
                ]
            );

            return response()->json([
                'message' => 'Sale completed successfully',
                'transaction' => $transaction
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * API: Get today's transactions
     */
    public function today()
    {
        $transactions = Transaction::with('user')
            ->today()
            ->latest()
            ->get();

        return response()->json($transactions);
    }

    /**
     * API: Get recent transactions
     */
    public function recent()
    {
        $transactions = Transaction::with('user')
            ->latest()
            ->take(10)
            ->get();

        return response()->json($transactions);
    }

    /**
     * API: Show transaction details
     */
    public function apiShow(Transaction $transaction)
    {
        $transaction->load('items.product', 'items.batch', 'user');
        return response()->json($transaction);
    }

    /**
     * API: Process return
     */
    public function processReturn(Transaction $transaction)
    {
        try {
            $returnTransaction = $this->posService->processReturn($transaction);

            return response()->json([
                'message' => 'Return processed successfully',
                'transaction' => $returnTransaction
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
