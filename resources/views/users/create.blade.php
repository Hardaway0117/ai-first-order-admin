<x-app-layout>
    <x-slot name="header">
        <h1 class="h4 mb-0">{{ __('New User') }}</h1>
    </x-slot>

    <div class="card border-0 shadow-sm" style="max-width: 40rem;">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('users.store') }}">
                @include('users._form')
            </form>
        </div>
    </div>
</x-app-layout>
