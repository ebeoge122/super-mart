<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Super Mart') }} - Login</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>

<body>
    @include('partials.user-banner')

    <nav class="main-nav">
        <img style="align-items: center; height: 40pt;" src="{{ asset('images/logo.png') }}" alt="Logo">
        <a href="{{ url('/') }}">Back to Home</a>
    </nav>

    <div class="login-form">
        <h1 style="text-align:center">Login</h1>

        @if(session('error'))
            <p class="form-message form-message--error">{{ session('error') }}</p>
        @endif

        @if(session('success'))
            <p class="form-message form-message--success">{{ session('success') }}</p>
        @endif

        <form method="POST" action="{{ route('login.post') }}" class="formm">
            @csrf
            <label for="email">Email Address:</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" class="input-text" required>

            <label for="password">Password:</label>
            <input type="password" name="password" id="password" class="input-text" required>

            <button type="submit" class="input-button">Login</button>
        </form>

        <h4 class="fh2" style="text-align: center;">
            Don't have an account,
            <a id="underline" href="{{ route('register.get') }}">Create Account</a>
        </h4>
    </div>
</body>
</html>

