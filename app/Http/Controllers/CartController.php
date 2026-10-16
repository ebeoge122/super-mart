<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    private function getCart(): array
    {
        $cart = Session::get('cart', []);
        return is_array($cart) ? $cart : [];
    }

    private function setCart(array $cart): void
    {
        Session::put('cart', $cart);
    }

    public function add(Request $request, int $itemId)
    {
        $quantity = (int) $request->input('quantity', 1);
        if ($quantity < 1) {
            $quantity = 1;
        }

        $item = Item::query()->findOrFail($itemId);

        $cart = $this->getCart();
        $slug = (string) $item->id;

        if (!isset($cart[$slug])) {
            $cart[$slug] = [
                'item_id' => $item->id,
                'name' => $item->name,
                'image' => $item->image,
                'price' => (float) ($item->sale_price ?? $item->price),
                'quantity' => 0,
            ];
        }

        $cart[$slug]['quantity'] += $quantity;

        $this->setCart($cart);

        return redirect()->route('cart.index')->with('success', 'Added to cart');
    }

    public function remove(int $itemId)
    {
        $cart = $this->getCart();
        $key = (string) $itemId;

        unset($cart[$key]);
        $this->setCart($cart);

        return redirect()->route('cart.index');
    }

    public function update(Request $request)
    {
        $cart = $this->getCart();
        $payload = $request->input('items', []);

        foreach ($payload as $itemId => $row) {
            $qty = isset($row['quantity']) ? (int) $row['quantity'] : 0;
            $key = (string) $itemId;

            if ($qty < 1) {
                unset($cart[$key]);
                continue;
            }

            if (isset($cart[$key])) {
                $cart[$key]['quantity'] = $qty;
            }
        }

        $this->setCart($cart);

        return redirect()->route('cart.index')->with('success', 'Cart updated');
    }

    public function index()
    {
        $cart = $this->getCart();

        $totals = [
            'subtotal' => 0.0,
        ];

        foreach ($cart as $row) {
            $totals['subtotal'] += ((float) $row['price']) * ((int) $row['quantity']);
        }

        return view('cart', [
            'cart' => $cart,
            'totals' => $totals,
        ]);
    }
}

