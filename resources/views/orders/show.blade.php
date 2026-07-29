<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="h4 mb-0">{{ __('Order Details') }} <span class="font-monospace fs-6 text-secondary">{{ $order->order_number }}</span></h1>
            <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">{{ __('Back') }}</a>
        </div>
    </x-slot>

    <div class="row g-4">
        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <h2 class="h6 text-secondary mb-3">{{ __('Customer') }}</h2>
                    <p class="mb-1 fw-semibold">{{ $order->customer->name }}</p>
                    <p class="mb-1 text-secondary small">{{ $order->customer->email }}</p>
                    <p class="mb-3 text-secondary small">{{ $order->customer->phone }}</p>

                    <h2 class="h6 text-secondary mb-2">{{ __('Status') }}</h2>
                    <p class="mb-3"><span class="badge {{ $order->status->badgeClass() }}">{{ $order->status->label() }}</span></p>

                    @php
                        $nextStatuses = collect(\App\Enums\OrderStatus::cases())->filter(fn ($s) => $order->status->canTransitionTo($s));
                    @endphp

                    @if ($nextStatuses->isNotEmpty())
                        <form method="POST" action="{{ route('orders.update-status', $order) }}" class="d-flex gap-2">
                            @csrf
                            @method('patch')
                            <select name="status" class="form-select form-select-sm">
                                @foreach ($nextStatuses as $status)
                                    <option value="{{ $status->value }}">{{ $status->label() }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-sm btn-primary text-nowrap">{{ __('Update Status') }}</button>
                        </form>
                    @endif

                    @if ($order->notes)
                        <h2 class="h6 text-secondary mt-3 mb-1">{{ __('Notes') }}</h2>
                        <p class="mb-0 small">{{ $order->notes }}</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h2 class="h6 text-secondary mb-3">{{ __('Items') }}</h2>
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>{{ __('Product') }}</th>
                                    <th class="text-end">{{ __('Unit Price') }}</th>
                                    <th class="text-end">{{ __('Quantity') }}</th>
                                    <th class="text-end">{{ __('Subtotal') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order->items as $item)
                                    <tr>
                                        <td>{{ $item->product_name }}</td>
                                        <td class="text-end">${{ number_format($item->unit_price, 2) }}</td>
                                        <td class="text-end">{{ $item->quantity }}</td>
                                        <td class="text-end">${{ number_format($item->subtotal, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3" class="text-end">{{ __('Total') }}</th>
                                    <th class="text-end">${{ number_format($order->total, 2) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
