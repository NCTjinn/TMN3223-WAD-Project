<?php
// app/Http/Controllers/TransactionController.php
namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Resources\TransactionResource;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::where('user_id', auth()->id())
            ->with(['details.product', 'order'])
            ->paginate(15);
        return TransactionResource::collection($transactions);
    }

    public function show(Transaction $transaction)
    {
        $this->authorize('view', $transaction);
        return new TransactionResource($transaction->load(['details.product', 'order']));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'total_amount' => 'required|numeric',
            'delivery_fee' => 'required|numeric',
            'tax_amount' => 'required|numeric',
            'delivery_address' => 'required|string',
            'shipping_method' => 'required|string',
            'voucher_code' => 'nullable|exists:vouchers,voucher_code'
        ]);

        $validated['user_id'] = auth()->id();
        $validated['payment_status'] = 'pending';

        $transaction = Transaction::create($validated);
        return new TransactionResource($transaction);
    }

    public function verify(Request $request)
    {
        $validated = $request->validate([
            'transaction_id' => 'required|exists:transactions,id',
            'payment_status' => 'required|in:successful,failed'
        ]);

        $transaction = Transaction::findOrFail($validated['transaction_id']);
        $this->authorize('update', $transaction);

        $transaction->update(['payment_status' => $validated['payment_status']]);
        return new TransactionResource($transaction);
    }
}
