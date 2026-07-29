<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'AI-First Order Admin') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body>
        <div class="min-vh-100 d-flex flex-column justify-content-center align-items-center bg-light py-4 px-3">
            <a href="/" class="text-decoration-none mb-4">
                <span class="fs-4 fw-semibold text-dark">{{ config('app.name', 'AI-First Order Admin') }}</span>
            </a>

            <div class="card shadow-sm w-100" style="max-width: 26rem;">
                <div class="card-body p-4">
                    {{ $slot }}
                </div>
            </div>

            <div class="mt-3 small">
                @foreach (config('app.supported_locales') as $code => $label)
                    <a href="{{ route('locale.switch', $code) }}" class="text-decoration-none mx-1 {{ app()->getLocale() === $code ? 'fw-bold' : 'text-secondary' }}">{{ $label }}</a>
                @endforeach
            </div>
        </div>

        @include('partials.cat')
    </body>
</html>
