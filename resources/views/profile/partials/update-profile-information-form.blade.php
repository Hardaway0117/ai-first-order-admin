<section>
    <header class="mb-3">
        <h2 class="h5 mb-1">基本資料</h2>
        <p class="text-secondary small mb-0">更新帳號的姓名與電子郵件。</p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}">
        @csrf
        @method('patch')

        <div class="mb-3">
            <x-input-label for="name" value="姓名" />
            <x-text-input id="name" name="name" type="text" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" />
        </div>

        <div class="mb-3">
            <x-input-label for="email" value="電子郵件" />
            <x-text-input id="email" name="email" type="email" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="small mt-2">
                    你的電子郵件尚未驗證。
                    <button form="send-verification" class="btn btn-link btn-sm p-0 align-baseline">重新寄送驗證信</button>
                </div>

                @if (session('status') === 'verification-link-sent')
                    <div class="text-success small mt-2">新的驗證連結已寄送至你的電子郵件。</div>
                @endif
            @endif
        </div>

        <div class="d-flex align-items-center gap-3">
            <x-primary-button>儲存</x-primary-button>

            @if (session('status') === 'profile-updated')
                <span class="text-success small">已儲存。</span>
            @endif
        </div>
    </form>
</section>
