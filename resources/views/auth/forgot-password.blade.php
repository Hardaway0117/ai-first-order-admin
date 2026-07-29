<x-guest-layout>
    <h1 class="h5 mb-3 text-center">{{ __('Forgot Password') }}</h1>

    <p class="text-secondary small">{{ __('Enter your email and we will send you a password reset link.') }}</p>

    <x-auth-session-status class="mb-3" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="mb-3">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <div class="d-flex justify-content-between align-items-center">
            <a class="small text-decoration-none" href="{{ route('login') }}">{{ __('Back to login') }}</a>

            <x-primary-button>{{ __('Send Password Reset Link') }}</x-primary-button>
        </div>
    </form>
</x-guest-layout>
