<nav class="main-nav">
    <img style="align-items: center; height: 40pt;" src="{{ asset('images/logo.png') }}" alt="Logo">
    <a href="{{ url('/') }}">Home</a>
    <a href="{{ route('categories.index') }}">All Categories</a>
    <a href="{{ route('cart.index') }}">Cart</a>
    <a href="{{ route('account') }}">Account</a>
</nav>