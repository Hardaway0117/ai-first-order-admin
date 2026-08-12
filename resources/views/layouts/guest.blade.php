<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'AI-First Order Admin') }}</title>

        <link rel="icon" href='data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64"><rect width="64" height="64" rx="14" fill="%236d28d9"/><text x="32" y="43" font-family="Arial,sans-serif" font-size="28" font-weight="bold" fill="%23fff" text-anchor="middle">AI</text></svg>'>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body>
        <div class="row g-0 min-vh-100">
            <div class="col-lg-6 auth-hero position-relative d-none d-lg-flex flex-column align-items-center justify-content-center overflow-hidden p-5">
                <div class="aurora aurora-1"></div>
                <div class="aurora aurora-2"></div>
                <div class="aurora aurora-3"></div>

                @include('partials.big-cat')

                <h1 class="text-white fs-3 fw-semibold mt-4 position-relative">{{ config('app.name', 'AI-First Order Admin') }}</h1>
                <p class="text-white-50 position-relative mb-0 text-center">{{ __('A SaaS order management admin built with an AI-first workflow.') }}</p>
            </div>

            <div class="col-lg-6 d-flex flex-column align-items-center justify-content-center bg-white py-5">
                <div class="d-lg-none mb-3">
                    @include('partials.big-cat', ['small' => true])
                </div>

                <div class="w-100 px-4" style="max-width: 26rem;">
                    {{ $slot }}
                </div>

                <div class="mt-4 small">
                    @foreach (config('app.supported_locales') as $code => $label)
                        <a href="{{ route('locale.switch', $code) }}" class="text-decoration-none mx-1 {{ app()->getLocale() === $code ? 'fw-bold' : 'text-secondary' }}">{{ $label }}</a>
                    @endforeach
                </div>
            </div>
        </div>
    </body>
</html>
