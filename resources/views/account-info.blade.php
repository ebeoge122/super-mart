<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Super Mart') }} - My Account</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
    @include('partials.user-banner')

    <nav class="main-nav">
        <img style="align-items: center; height: 40pt;" src="{{ asset('images/logo.png') }}" alt="Logo">
        <a href="{{ url('/') }}">Back to Home</a>
    </nav>

    <div class="account-card">
        <h1>My Account</h1>
        <p class="account-intro">Welcome back, {{ $user->name }}.</p>

        <div class="account-details">
            <div class="account-detail-row">
                <span class="account-label">Name</span>
                <strong>{{ $user->name }}</strong>
            </div>
            <div class="account-detail-row">
                <span class="account-label">Email</span>
                <strong>{{ $user->email }}</strong>
            </div>
            <div class="account-detail-row">
                <span class="account-label">Member Since</span>
                <strong>{{ $user->created_at->format('F j, Y') }}</strong>
            </div>
        </div>
    </div>
</body>
</html>
