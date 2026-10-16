@if(session()->has('user_id'))
    @php
        $user = null;

        if (session()->has('user_name')) {
            $user = (object) [
                'id' => session('user_id'),
                'name' => session('user_name'),
                'email' => session('user_email') ?? '',
                'created_at' => now(),
            ];
        } else {
            $user = \App\Models\User::find(session('user_id'));
        }
    @endphp
    @if($user)
        <div class="user-banner">
            <div class="user-banner__content">
                <span class="user-banner__welcome">Welcome, {{ $user->name }}</span>
                <div class="user-banner__actions">
                    <a href="{{ route('account') }}" class="user-banner__link">My Account</a>
                    <form method="POST" action="{{ route('logout') }}" class="user-banner__logout-form">
                        @csrf
                        <button type="submit" class="user-banner__logout-btn">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endif
