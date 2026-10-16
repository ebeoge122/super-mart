<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Super Mart') }} - Cart</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
    @include('partials.user-banner')
    <nav class="main-nav">
        <img style="align-items: center; height: 40pt;" src="{{ asset('images/logo.png') }}" alt="Logo">
        <a href="{{ url('/') }}">Home</a>
        <a href="{{ route('allcategories') }}">All Categories</a>
        <a href="{{ route('cart.index') }}">Cart</a>
        <a href="{{ route('account') }}">Account</a>
    </nav>

    <main>
        <h1>Cart</h1>
        <div style="padding: 0 5%; margin: 10px 0 18px;">
            <a href="{{ route('home') }}" class="btn secondary">Back to Shop</a>
        </div>

        @if(session('success'))
            <div style="padding: 10px 5%; margin: 0 0 10px; color: #027b9c; font-weight: bold;">{{ session('success') }}</div>
        @endif

        @if(empty($cart))
            <p>Your cart is empty.</p>
        @else
            <form method="POST" action="{{ route('cart.update') }}">
                @csrf

                <div class="cart-grid" style="padding: 0 5% 30px;">
                    @foreach($cart as $key => $row)
                        <div class="product-card" style="flex-direction: row; align-items: center; justify-content: space-between; gap: 18px;">
                            <div style="min-width: 220px;">
                                <h3 style="margin: 0 0 8px 0;">{{ $row['name'] }}</h3>
                                <div class="product-price" style="margin-bottom: 8px;">
                                    ₦{{ number_format((float)$row['price'], 2) }}
                                </div>
                                <img
                                    class="product-image"
                                    style="height: 90px; width: 180px; object-fit: contain;"
                                    src="{{ !empty($row['image']) ? asset('images/items/' . $row['image']) : '' }}"
                                    alt="{{ $row['name'] }}"
                                >
                            </div>

                            <div style="display:flex; gap:10px; align-items: center;">
                                <label style="font-weight: bold;">Qty</label>
                                <input
                                    type="number"
                                    name="items[{{ $key }}][quantity]"
                                    min="1"
                                    value="{{ (int)$row['quantity'] }}"
                                    style="width: 90px; height: 38px; border-radius: 6px; border: none; padding: 8px;"
                                >
                            </div>

                            <div style="text-align:right; min-width: 140px;">
                                @php
                                    $lineTotal = ((float)$row['price']) * ((int)$row['quantity']);
                                @endphp
                                <div style="font-weight: bold;">Line: ₦{{ number_format($lineTotal, 2) }}</div>

                                <form method="POST" action="{{ route('cart.remove', (int)$row['item_id']) }}" style="margin-top: 10px;">

                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn secondary" style="padding: 10px 14px; border: none; cursor: pointer;">Remove</button>
                                </form>
                            </div>
                        </div>
                        <div style="height: 12px;"></div>
                    @endforeach
                </div>

                <div style="padding: 0 5% 40px;">
                    <h2 style="margin-top: 20px;">Subtotal: ₦{{ number_format((float)$totals['subtotal'], 2) }}</h2>
                    <button class="btn primary" type="submit" style="border: none; cursor:pointer;">Update Cart</button>
                </div>
            </form>
        @endif
    </main>
</body>
</html>

