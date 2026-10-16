<!DOCTYPE html>
<html>
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
        <a href="{{ route('register.get') }}">Create Account</a>
    </nav>

    <div class="login-form">
        <h1 style="text-align:center">Login</h1>

        @if(session('success'))
            <div style="padding: 10px 5%; margin: 0 0 10px; color: #027b9c; font-weight: bold;">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div style="padding: 10px 5%; margin: 0 0 10px; color: #b91c1c; font-weight: bold;">
                {{ session('error') }}
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

        <form method="POST" action="{{ route('login.post') }}" class="formm">
            @csrf

            <label for="email">Email Address:</label>
            <input type="email" name="email" id="email" class="input-text" value="{{ old('email') }}" required>

            <label for="password">Password:</label>
            <input type="password" name="password" id="password" class="input-text" required>

            <input type="submit" value="Login" class="input-button">
        </form>

        <h4 class="fh2" style="text-align: center;">
            Don't have an account, <a id="underline" href="{{ route('register.get') }}">Create Account</a>
        </h4>
    </div>
</body>
</html>

