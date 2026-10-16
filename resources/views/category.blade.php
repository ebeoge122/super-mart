<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Super Mart') }}</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
    @include('partials.user-banner')

    {{-- Primary Navbar --}}
    <nav class="main-nav">
        <img style="align-items: center; height: 40pt;" src="{{ asset('images/logo.png') }}" alt="Logo">
        <a href="{{ url('/') }}">Home</a>
        <a href="{{ route('allcategories') }}">All Categories</a>
        <a href="{{ route('cart.index') }}">Cart</a>
        <a href="{{ route('account') }}">Account</a>
    </nav>

    {{-- Secondary Navbar --}}
    @include('partials.category-nav')

    <main>
        <section class="hero" style="--hero-image-url: url('{{ $hero['image'] ?? asset('images/categories/default.png') }}');">
            <div class="hero-content">
                <h1>{{ $hero['title'] ?? $category->name }}</h1>
                <h2>{{ $hero['subtitle'] ?? 'Fresh picks for you' }}</h2>
            </div>
        </section>

        <h1>{{ $category->name }}</h1>

        <div class="product-grid">
            @forelse($items as $item)
                <div class="product-card">
                    <div class="product-image">
                        @if(!empty($item->image))
                            <img class="product-image" src="{{ asset('images/items/' . $item->image) }}" alt="{{ $item->name }}">
                        @endif
                    </div>
                    <h3>{{ $item->name }}</h3>

                    @php
                        $price = $item->sale_price ?? $item->price;
                    @endphp

                    <div class="product-price">
                        ₦{{ number_format($price, 2) }}
                    </div>
                    <form method="POST" action="{{ route('cart.add', (int)$item->id) }}" style="margin-top: 10px;">
                        @csrf
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit" class="btn primary" style="cursor:pointer; border:none;">Buy Now</button>
                    </form>
                </div>
            @empty
                <p>No products found in this category.</p>
            @endforelse
        </div>
    </main>
</body>
</html>

