<x-app-layout>
    <x-slot name="header">
        <h1 class="h4 mb-0">{{ __('Edit Product') }}</h1>
    </x-slot>

    <div class="card border-0 shadow-sm" style="max-width: 40rem;">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('products.update', $product) }}">
                @method('patch')
                @include('products._form')
            </form>
        </div>
    </div>
</x-app-layout>
