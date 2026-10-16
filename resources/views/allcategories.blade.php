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
<main>
    <h1>Categories</h1>
    <div class="product-grid">

    @foreach ($categories as $category)
   <div class="product-card">
      <a href="{{ route('category.show', $category->slug) }}"
         class="category-item {{ request()->is('category/'.$category->slug) ? 'active' : '' }}">
        <img class="product-image" src="{{ $category->hero_image_url }}" alt="{{ $category->name }}">
        <span class="label">{{ $category->icon }} {{ $category->name }}</span>
      </a>
   </div>
    @endforeach
    </div>
</main>
</body>
</html>