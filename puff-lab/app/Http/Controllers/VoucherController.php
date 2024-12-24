<?php
// app/Http/Controllers/VoucherController.php
namespace App\Http\Controllers;

use App\Models\Voucher;
use Illuminate\Http\Request;
use App\Http\Resources\VoucherResource;

class VoucherController extends Controller
{
    public function validate(Request $request)
    {
        $validated = $request->validate([
            'voucher_code' => 'required|string'
        ]);

        $voucher = Voucher::where('voucher_code', $validated['voucher_code'])
            ->where('expiry_date', '>', now())
            ->whereNull('redeemed_by')
            ->first();

        if (!$voucher) {
            return response()->json(['valid' => false], 404);
        }

        return new VoucherResource($voucher);
    }

    public function apply(Request $request)
    {
        $validated = $request->validate([
            'voucher_code' => 'required|exists:vouchers,voucher_code'
        ]);

        $voucher = Voucher::where('voucher_code', $validated['voucher_code'])
            ->where('expiry_date', '>', now())
            ->whereNull('redeemed_by')
            ->firstOrFail();

        $voucher->update(['redeemed_by' => auth()->id()]);
        return new VoucherResource($voucher);
    }
}
