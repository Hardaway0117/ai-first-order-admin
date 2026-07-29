<x-guest-layout>
    <h1 class="h5 mb-3 text-center">忘記密碼</h1>

    <p class="text-secondary small">忘記密碼了嗎？請填寫你的電子郵件，我們會寄送重設密碼連結給你。</p>

    <x-auth-session-status class="mb-3" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="mb-3">
            <x-input-label for="email" value="電子郵件" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <div class="d-flex justify-content-between align-items-center">
            <a class="small text-decoration-none" href="{{ route('login') }}">返回登入</a>

            <x-primary-button>寄送重設連結</x-primary-button>
        </div>
    </form>
</x-guest-layout>
