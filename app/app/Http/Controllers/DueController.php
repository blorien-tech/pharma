<?php

namespace App\Http\Controllers;

use App\Models\Due;
use App\Models\DuePayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DueController extends Controller
{
    /**
     * Display a list of all dues
     */
    public function index(Request $request)
    {
        $query = Due::with(['user', 'payments']);

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'OVERDUE') {
                $query->overdue();
            } else {
                $query->where('status', $request->status);
            }
        }

        // Search by customer name or phone
        if ($request->filled('search')) {
            $query->searchCustomer($request->search);
        }

        // Order by creation date (newest first)
        $dues = $query->orderBy('created_at', 'desc')->paginate(20);

        // Calculate summary statistics
        $summary = [
            'total_pending' => Due::where('status', '!=', 'PAID')->sum('amount_remaining'),
            'total_overdue' => Due::overdue()->sum('amount_remaining'),
            'count_pending' => Due::pending()->count(),
            'count_partial' => Due::partial()->count(),
            'count_overdue' => Due::overdue()->count(),
        ];

        return view('dues.index', compact('dues', 'summary'));
    }

    /**
     * Show form to create a new due
     */
    public function create()
    {
        return view('dues.create');
    }

    /**
     * Store a new due (quick entry from POS or manual)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'transaction_id' => 'nullable|exists:transactions,id',
            'amount' => 'required|numeric|min:0.01',
            'due_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['amount_paid'] = 0;
        $validated['amount_remaining'] = $validated['amount'];
        $validated['status'] = 'PENDING';

        // Auto-create or link customer if phone is provided
        $customerId = null;
        if (!empty($validated['customer_phone'])) {
            $customer = \App\Models\Customer::where('phone', $validated['customer_phone'])->first();

            if ($customer) {
                // Customer exists - link to it and update name if different
                $customerId = $customer->id;
                if ($customer->name !== $validated['customer_name']) {
                    $customer->update(['name' => $validated['customer_name']]);
                }
            } else {
                // Customer doesn't exist - create new customer
                $customer = \App\Models\Customer::create([
                    'name' => $validated['customer_name'],
                    'phone' => $validated['customer_phone'],
                    'credit_limit' => 0,
                    'current_balance' => 0,
                    'credit_enabled' => false,
                    'is_active' => true,
                    'notes' => 'Auto-created from due entry',
                ]);
                $customerId = $customer->id;
            }
        }

        $validated['customer_id'] = $customerId;
        $due = Due::create($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Due recorded successfully',
                'due' => $due,
            ]);
        }

        return redirect()->route('dues.index')
            ->with('success', 'Due recorded successfully');
    }

    /**
     * Display details of a specific due
     */
    public function show(Due $due)
    {
        $due->load(['user', 'transaction', 'payments.user']);

        return view('dues.show', compact('due'));
    }

    /**
     * Show form to edit a due
     */
    public function edit(Due $due)
    {
        return view('dues.edit', compact('due'));
    }

    /**
     * Update a due
     */
    public function update(Request $request, Due $due)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'due_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $due->update($validated);

        return redirect()->route('dues.show', $due)
            ->with('success', 'Due updated successfully');
    }

    /**
     * Show payment form for a due
     */
    public function showPayment(Due $due)
    {
        if ($due->isPaid()) {
            return redirect()->route('dues.show', $due)
                ->with('error', 'This due is already fully paid');
        }

        return view('dues.payment', compact('due'));
    }

    /**
     * Record a payment for a due
     */
    public function recordPayment(Request $request, Due $due)
    {
        if ($due->isPaid()) {
            return back()->with('error', 'This due is already fully paid');
        }

        $validated = $request->validate([
            'amount' => [
                'required',
                'numeric',
                'min:0.01',
                'max:' . $due->amount_remaining,
            ],
            'payment_method' => 'required|in:CASH,CARD,MOBILE,OTHER',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $payment = $due->recordPayment(
                $validated['amount'],
                $validated['payment_method'],
                $validated['notes'] ?? null,
                Auth::id()
            );

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Payment recorded successfully',
                    'payment' => $payment,
                    'due' => $due->fresh(),
                ]);
            }

            $message = $due->isPaid()
                ? 'Payment recorded. Due is now fully paid!'
                : 'Partial payment recorded successfully';

            return redirect()->route('dues.show', $due)
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Quick lookup of customer dues by phone
     */
    public function lookupByPhone(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
        ]);

        $dues = Due::where('customer_phone', $request->phone)
            ->where('status', '!=', 'PAID')
            ->with('payments')
            ->orderBy('created_at', 'desc')
            ->get();

        $summary = [
            'total_pending' => $dues->sum('amount_remaining'),
            'count' => $dues->count(),
        ];

        return response()->json([
            'success' => true,
            'dues' => $dues,
            'summary' => $summary,
        ]);
    }

    /**
     * Delete a due (soft delete or cancel)
     */
    public function destroy(Due $due)
    {
        if ($due->payments()->count() > 0) {
            return back()->with('error', 'Cannot delete a due with payment history. Please contact administrator.');
        }

        $due->delete();

        return redirect()->route('dues.index')
            ->with('success', 'Due deleted successfully');
    }

    /**
     * Get due statistics (for dashboard widgets)
     */
    public function statistics()
    {
        $stats = [
            'total_pending_amount' => Due::where('status', '!=', 'PAID')->sum('amount_remaining'),
            'total_overdue_amount' => Due::overdue()->sum('amount_remaining'),
            'total_collected_today' => DuePayment::whereDate('created_at', today())->sum('amount'),
            'pending_count' => Due::pending()->count(),
            'partial_count' => Due::partial()->count(),
            'overdue_count' => Due::overdue()->count(),
            'paid_today_count' => DuePayment::whereDate('created_at', today())->count(),
        ];

        return response()->json($stats);
    }
}
