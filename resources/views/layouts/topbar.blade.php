<header class="admin-topbar bg-white border-bottom d-flex align-items-center gap-2 px-3 px-lg-4">
    <button class="btn btn-ghost d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar" aria-label="{{ __('Toggle navigation') }}">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M3 6h18M3 12h18M3 18h18"/></svg>
    </button>

    <div class="ms-auto d-flex align-items-center gap-1">
        <div class="dropdown">
            <button class="btn btn-ghost dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                🌐 {{ config('app.supported_locales')[app()->getLocale()] ?? app()->getLocale() }}
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                @foreach (config('app.supported_locales') as $code => $label)
                    <li>
                        <a class="dropdown-item {{ app()->getLocale() === $code ? 'active' : '' }}" href="{{ route('locale.switch', $code) }}">{{ $label }}</a>
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="dropdown">
            <button class="btn btn-ghost dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                {{ Auth::user()->name }}
                <span class="badge {{ Auth::user()->isAdmin() ? 'text-bg-primary' : 'text-bg-secondary' }}">{{ Auth::user()->role->label() }}</span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <a class="dropdown-item" href="{{ route('profile.edit') }}">{{ __('Profile') }}</a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item">{{ __('Log Out') }}</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</header>
