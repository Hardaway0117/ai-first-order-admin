<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="h4 mb-0">{{ __('Products') }}</h1>
            <a href="{{ route('products.create') }}" class="btn btn-primary">{{ __('New Product') }}</a>
        </div>
    </x-slot>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="GET" action="{{ route('products.index') }}" class="row g-2 mb-3">
                <div class="col-12 col-md-4">
                    <input type="search" name="search" value="{{ request('search') }}" class="form-control" placeholder="{{ __('Search by name or SKU') }}">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-outline-secondary">{{ __('Search') }}</button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>{{ __('Name') }}</th>
                            <th>SKU</th>
                            <th class="text-end">{{ __('Price') }}</th>
                            <th class="text-end">{{ __('Stock') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th class="text-end">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($products as $product)
                            <tr>
                                <td>{{ $product->name }}</td>
                                <td class="text-secondary">{{ $product->sku }}</td>
                                <td class="text-end">${{ number_format($product->price, 2) }}</td>
                                <td class="text-end">{{ $product->stock }}</td>
                                <td>
                                    <span class="badge {{ $product->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">
                                        {{ $product->is_active ? __('Active') : __('Inactive') }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-outline-primary">{{ __('Edit') }}</a>
                                    <form method="POST" action="{{ route('products.destroy', $product) }}" class="d-inline" onsubmit="return confirm('{{ __('Are you sure you want to delete this product?') }}')">
                                        @csrf
                                        @method('delete')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">{{ __('Delete') }}</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-secondary py-4">{{ __('No products found.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $products->links() }}
        </div>
    </div>
</x-app-layout>
