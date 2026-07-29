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
        <div class="d-flex">
            <aside class="admin-sidebar d-none d-lg-flex flex-column flex-shrink-0">
                @include('layouts.sidebar-content')
            </aside>

            <div class="offcanvas offcanvas-start admin-sidebar d-flex flex-column" tabindex="-1" id="mobileSidebar">
                <div class="text-end pt-2 pe-2">
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="{{ __('Close') }}"></button>
                </div>
                @include('layouts.sidebar-content')
            </div>

            <div class="admin-main flex-grow-1 min-vh-100 d-flex flex-column" style="min-width: 0;">
                @include('layouts.topbar')

                @isset($header)
                    <header class="bg-white border-bottom">
                        <div class="px-4 py-3">
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                <main class="admin-content flex-grow-1 px-4 py-4">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('Close') }}"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('Close') }}"></button>
                        </div>
                    @endif

                    {{ $slot }}
                </main>
            </div>
        </div>

        @include('partials.cat')
    </body>
</html>
