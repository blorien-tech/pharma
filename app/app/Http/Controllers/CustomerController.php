<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerCreditTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    /**
     * Display a listing of customers
     */
    public function index(Request $request)
    {
        $query = Customer::query();

        // Search
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filter by credit enabled
        if ($request->filled('credit_enabled')) {
            $query->where('credit_enabled', $request->credit_enabled);
        }

        // Filter by active status
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $customers = $query->latest()->paginate(15);

        return view('customers.index', compact('customers'));
    }

    /**
     * Show the form for creating a new customer
     */
    public function create()
    {
        return view('customers.create');
    }

    /**
     * Store a newly created customer
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:customers',
            'email' => 'nullable|email|max:255|unique:customers',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'id_number' => 'nullable|string|max:50',
            'credit_limit' => 'nullable|numeric|min:0',
            'credit_enabled' => 'boolean',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $validated['credit_limit'] = $validated['credit_limit'] ?? 0;
        $validated['current_balance'] = 0;
        $validated['credit_enabled'] = $request->has('credit_enabled');
        $validated['is_active'] = $request->has('is_active');

        $customer = Customer::create($validated);

        return redirect()->route('customers.show', $customer)
            ->with('success', 'Customer created successfully.');
    }

    /**
     * Display the specified customer
     */
    public function show(Customer $customer)
    {
        $customer->load(['transactions', 'creditTransactions.creator']);

        // Get recent transactions
        $recentTransactions = $customer->transactions()
            ->with(['items.product'])
            ->latest()
            ->take(10)
            ->get();

        // Get credit history
        $creditHistory = $customer->creditTransactions()
            ->with(['transaction', 'creator'])
            ->latest()
            ->paginate(15);

        return view('customers.show', compact('customer', 'recentTransactions', 'creditHistory'));
    }

    /**
     * Show the form for editing the specified customer
     */
    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    /**
     * Update the specified customer
     */
    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:customers,phone,' . $customer->id,
            'email' => 'nullable|email|max:255|unique:customers,email,' . $customer->id,
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'id_number' => 'nullable|string|max:50',
            'credit_limit' => 'nullable|numeric|min:0',
            'credit_enabled' => 'boolean',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $validated['credit_enabled'] = $request->has('credit_enabled');
        $validated['is_active'] = $request->has('is_active');

        $customer->update($validated);

        return redirect()->route('customers.show', $customer)
            ->with('success', 'Customer updated successfully.');
    }

    /**
     * Remove the specified customer
     */
    public function destroy(Customer $customer)
    {
        if ($customer->current_balance > 0) {
            return back()->with('error', 'Cannot delete customer with outstanding balance.');
        }

        $customer->delete();

        return redirect()->route('customers.index')
            ->with('success', 'Customer deleted successfully.');
    }

    /**
     * Show payment form
     */
    public function showPayment(Customer $customer)
    {
        return view('customers.payment', compact('customer'));
    }

    /**
     * Record a payment from customer
     */
    public function recordPayment(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:CASH,CARD,MOBILE,OTHER',
            'notes' => 'nullable|string',
        ]);

        if ($validated['amount'] > $customer->current_balance) {
            return back()->with('error', 'Payment amount cannot exceed current balance.');
        }

        DB::transaction(function () use ($customer, $validated) {
            $balanceBefore = $customer->current_balance;
            $balanceAfter = $balanceBefore - $validated['amount'];

            // Create credit transaction
            CustomerCreditTransaction::create([
                'customer_id' => $customer->id,
                'type' => 'PAYMENT',
                'amount' => $validated['amount'],
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'notes' => $validated['notes'] ?? "Payment via {$validated['payment_method']}",
                'created_by' => auth()->id(),
            ]);

            // Update customer balance
            $customer->decrement('current_balance', $validated['amount']);
        });

        return redirect()->route('customers.show', $customer)
            ->with('success', "Payment of ৳{$validated['amount']} recorded successfully.");
    }

    /**
     * Show adjustment form
     */
    public function showAdjustment(Customer $customer)
    {
        return view('customers.adjustment', compact('customer'));
    }

    /**
     * Record a balance adjustment
     */
    public function recordAdjustment(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric',
            'notes' => 'required|string|max:500',
        ]);

        DB::transaction(function () use ($customer, $validated) {
            $balanceBefore = $customer->current_balance;
            $amount = $validated['amount'];
            $balanceAfter = $balanceBefore + $amount;

            // Create credit transaction
            CustomerCreditTransaction::create([
                'customer_id' => $customer->id,
                'type' => 'ADJUSTMENT',
                'amount' => $amount,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'notes' => $validated['notes'],
                'created_by' => auth()->id(),
            ]);

            // Update customer balance
            $customer->increment('current_balance', $amount);
        });

        return redirect()->route('customers.show', $customer)
            ->with('success', 'Balance adjustment recorded successfully.');
    }
}
