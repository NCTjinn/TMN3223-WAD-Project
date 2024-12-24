<?php
// app/Http/Controllers/CartController.php
namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;
use App\Http\Resources\CartResource;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        $cart = Cart::where('user_id', auth()->id())
            ->with('product')
            ->get();
        return CartResource::collection($cart);
    }

    public function add(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $cart = Cart::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'product_id' => $validated['product_id']
            ],
            ['quantity' => $validated['quantity']]
        );

        return new CartResource($cart->load('product'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $cart = Cart::where('user_id', auth()->id())
            ->where('product_id', $validated['product_id'])
            ->firstOrFail();

        $cart->update(['quantity' => $validated['quantity']]);
        return new CartResource($cart->load('product'));
    }

    public function remove($product)
    {
        Cart::where('user_id', auth()->id())
            ->where('product_id', $product)
            ->delete();
        
        return response()->noContent();
    }
}
