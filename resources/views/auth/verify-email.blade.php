<x-guest-layout>
    <h1 class="h5 mb-3 text-center">{{ __('Verify Email') }}</h1>

    <p class="text-secondary small">{{ __("Thanks for signing up! Before getting started, please click the verification link we just sent to your email. Didn't receive it? We can send another.") }}</p>

    @if (session('status') == 'verification-link-sent')
        <div class="alert alert-success">{{ __('A new verification link has been sent to the email address you provided during registration.') }}</div>
    @endif

    <div class="d-flex justify-content-between align-items-center mt-4">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <x-primary-button>{{ __('Resend Verification Email') }}</x-primary-button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="btn btn-link text-decoration-none">{{ __('Log Out') }}</button>
        </form>
    </div>
</x-guest-layout>
