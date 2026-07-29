<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="h4 mb-0">{{ __('Customers') }}</h1>
            @can('create', \App\Models\Customer::class)
                <a href="{{ route('customers.create') }}" class="btn btn-primary">{{ __('New Customer') }}</a>
            @endcan
        </div>
    </x-slot>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="GET" action="{{ route('customers.index') }}" class="row g-2 mb-3">
                <div class="col-12 col-md-4">
                    <input type="search" name="search" value="{{ request('search') }}" class="form-control" placeholder="{{ __('Search by name, email or phone') }}">
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
                            <th>{{ __('Email') }}</th>
                            <th>{{ __('Phone') }}</th>
                            <th class="text-end">{{ __('Order Count') }}</th>
                            <th class="text-end">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($customers as $customer)
                            <tr>
                                <td>{{ $customer->name }}</td>
                                <td class="text-secondary">{{ $customer->email }}</td>
                                <td class="text-secondary">{{ $customer->phone }}</td>
                                <td class="text-end">{{ $customer->orders_count }}</td>
                                <td class="text-end">
                                    @can('update', $customer)
                                        <a href="{{ route('customers.edit', $customer) }}" class="btn btn-sm btn-outline-primary">{{ __('Edit') }}</a>
                                    @endcan
                                    @can('delete', $customer)
                                        <form method="POST" action="{{ route('customers.destroy', $customer) }}" class="d-inline" onsubmit="return confirm('{{ __('Are you sure you want to delete this customer?') }}')">
                                            @csrf
                                            @method('delete')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">{{ __('Delete') }}</button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-secondary py-4">{{ __('No customers found.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $customers->links() }}
        </div>
    </div>
</x-app-layout>
