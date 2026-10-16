<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Super Mart') }}</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
    {{-- User Login Banner --}}
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

    {{-- Page content --}}
    <main>
        @if(session('success'))
            <div style="padding: 10px 5%; margin: 0 0 10px; color: #027b9c; font-weight: bold;">{{ session('success') }}</div>
        @endif

        <section class="hero" style="--hero-image-url: url('{{ $hero['image'] ?? asset('images/categories/default.png') }}');">
            <div class="hero-content">
                <h1>{{ $hero['title'] ?? 'Welcome to Super Mart' }}</h1>
                <h2>{{ $hero['subtitle'] ?? 'Fresh Groceries, Delivered Fast' }}</h2>
                <p>{{ $hero['description'] ?? 'Shop fruits, dairy, pastries and more — all in one place.' }}</p>

                <div class="hero-actions">
                    <a href="{{ route('categories.index') }}" class="btn primary">
                        Shop Now
                    </a>

                    <a href="#categories" class="btn secondary">
                        Browse Categories
                    </a>
                </div>
            </div>
        </section>

        <section>
            <h1>Featured Products</h1>

            <div class="product-grid">
                @foreach($featuredItems as $item)
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

                        <form method="POST" action="{{ route('cart.add', (int)$item->id) }}" style="display:inline;">
                            @csrf
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" class="btn primary" style="cursor:pointer; border:none;">Buy Now</button>
                        </form>
                    </div>
                @endforeach
            </div>
        </section>

        <section>
            <h1>Clearance Sales</h1>

            <div class="product-grid">
                @foreach($clearanceItems as $item)
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
                        <form method="POST" action="{{ route('cart.add', (int)$item->id) }}" style="display:inline;">
                            @csrf
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" class="btn primary" style="cursor:pointer; border:none;">Buy Now</button>
                        </form>
                    </div>
                @endforeach
            </div>
        </section>
    </main>
</body>
</html>

