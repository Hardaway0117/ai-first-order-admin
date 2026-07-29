<x-guest-layout>
    <h1 class="h5 mb-3 text-center">確認密碼</h1>

    <p class="text-secondary small">這是安全性較高的操作，請再次輸入密碼以繼續。</p>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <div class="mb-3">
            <x-input-label for="password" value="密碼" />
            <x-text-input id="password" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" />
        </div>

        <div class="text-end">
            <x-primary-button>確認</x-primary-button>
        </div>
    </form>
</x-guest-layout>
