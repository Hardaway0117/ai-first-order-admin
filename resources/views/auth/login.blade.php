<x-guest-layout>
    <h1 class="h5 mb-3 text-center">登入</h1>

    <x-auth-session-status class="mb-3" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="mb-3">
            <x-input-label for="email" value="電子郵件" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <div class="mb-3">
            <x-input-label for="password" value="密碼" />
            <x-text-input id="password" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" />
        </div>

        <div class="form-check mb-3">
            <input id="remember_me" type="checkbox" class="form-check-input" name="remember">
            <label class="form-check-label" for="remember_me">記住我</label>
        </div>

        <div class="d-flex justify-content-between align-items-center">
            @if (Route::has('password.request'))
                <a class="small text-decoration-none" href="{{ route('password.request') }}">忘記密碼？</a>
            @endif

            <x-primary-button>登入</x-primary-button>
        </div>

        @if (Route::has('register'))
            <p class="small text-secondary text-center mt-4 mb-0">
                還沒有帳號？<a class="text-decoration-none" href="{{ route('register') }}">立即註冊</a>
            </p>
        @endif
    </form>
</x-guest-layout>
