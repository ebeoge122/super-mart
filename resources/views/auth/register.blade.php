<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Super Mart') }} - Create Account</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
    @include('partials.user-banner')
    <nav class="main-nav">
        <img style="align-items: center; height: 40pt;" src="{{ asset('images/logo.png') }}" alt="Logo">
        <a href="{{ url('/') }}">Back to Home</a>
        <a href="{{ route('account') }}">Login</a>
    </nav>

    <div class="login-form">
        <h1 style="text-align:center">Create Account</h1>

        @if(session('success'))
            <div style="padding: 10px 5%; margin: 0 0 10px; color: #027b9c; font-weight: bold;">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div style="padding: 10px 5%; margin: 0 0 10px; color: #b91c1c; font-weight: bold;">
                <ul style="margin: 0; padding-left: 18px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register.post') }}" class="formm">
            @csrf

            <label for="name">Name:</label>
            <input type="text" name="name" id="name" class="input-text" value="{{ old('name') }}" required>

            <label for="email">Email Address:</label>
            <input type="email" name="email" id="email" class="input-text" value="{{ old('email') }}" required>

            <label for="password">Password:</label>
            <input type="password" name="password" id="password" class="input-text" required>

            <label for="password_confirmation">Confirm Password:</label>
            <input type="password" name="password_confirmation" id="password_confirmation" class="input-text" required>

            <input type="submit" value="Create Account" class="input-button">
        </form>
    </div>
</body>
</html>

