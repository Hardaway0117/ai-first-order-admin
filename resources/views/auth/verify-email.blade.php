<x-guest-layout>
    <h1 class="h5 mb-3 text-center">驗證電子郵件</h1>

    <p class="text-secondary small">感謝註冊！開始使用前，請點擊我們寄送給你的驗證連結。沒有收到信件嗎？我們可以再寄一次。</p>

    @if (session('status') == 'verification-link-sent')
        <div class="alert alert-success">新的驗證連結已寄送至你註冊時填寫的電子郵件。</div>
    @endif

    <div class="d-flex justify-content-between align-items-center mt-4">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <x-primary-button>重新寄送驗證信</x-primary-button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="btn btn-link text-decoration-none">登出</button>
        </form>
    </div>
</x-guest-layout>
