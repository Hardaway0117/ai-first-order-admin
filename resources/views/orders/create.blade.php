<x-app-layout>
    <x-slot name="header">
        <h1 class="h4 mb-0">{{ __('New Order') }}</h1>
    </x-slot>

    <div class="card border-0 shadow-sm" style="max-width: 52rem;">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('orders.store') }}">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <x-input-label for="customer_id" :value="__('Customer')" />
                        <select id="customer_id" name="customer_id" class="form-select" required>
                            <option value="">{{ __('Select a customer') }}</option>
                            @foreach ($customers as $customer)
                                <option value="{{ $customer->id }}" @selected(old('customer_id') == $customer->id)>{{ $customer->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('customer_id')" />
                    </div>

                    <div class="col-md-6 mb-3">
                        <x-input-label for="notes" :value="__('Notes')" />
                        <x-text-input id="notes" name="notes" type="text" :value="old('notes')" />
                        <x-input-error :messages="$errors->get('notes')" />
                    </div>
                </div>

                <h2 class="h6 mt-2 mb-2">{{ __('Items') }}</h2>
                <x-input-error :messages="$errors->get('items')" class="mb-2" />

                <div class="table-responsive">
                    <table class="table align-middle" id="items-table">
                        <thead>
                            <tr>
                                <th style="min-width: 16rem;">{{ __('Product') }}</th>
                                <th class="text-end" style="width: 8rem;">{{ __('Quantity') }}</th>
                                <th class="text-end" style="width: 9rem;">{{ __('Subtotal') }}</th>
                                <th style="width: 5rem;"></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                            <tr>
                                <th colspan="2" class="text-end">{{ __('Total') }}</th>
                                <th class="text-end" id="order-total">$0.00</th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <button type="button" class="btn btn-outline-primary btn-sm mb-4" id="add-item">＋ {{ __('Add Item') }}</button>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">{{ __('Back') }}</a>
                    <x-primary-button>{{ __('Save') }}</x-primary-button>
                </div>
            </form>
        </div>
    </div>

    {{-- 動態品項列：純前端試算，實際單價與總額以後端為準 --}}
    <script>
        const PRODUCTS = @json($products);
        const STOCK_LABEL = @json(__('Stock'));

        const tbody = document.querySelector('#items-table tbody');
        const totalCell = document.getElementById('order-total');
        let rowIndex = 0;

        function productOptions() {
            return ['<option value="">{{ __('Select a product') }}</option>']
                .concat(PRODUCTS.map(p => `<option value="${p.id}" data-price="${p.price}">${p.name}（$${p.price}／${STOCK_LABEL} ${p.stock}）</option>`))
                .join('');
        }

        function recalc() {
            let total = 0;
            tbody.querySelectorAll('tr').forEach(tr => {
                const price = parseFloat(tr.querySelector('select').selectedOptions[0]?.dataset.price || 0);
                const qty = parseInt(tr.querySelector('input[type=number]').value || 0, 10);
                const sub = price * qty;
                tr.querySelector('.subtotal').textContent = '$' + sub.toFixed(2);
                total += sub;
            });
            totalCell.textContent = '$' + total.toFixed(2);
        }

        function addRow() {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td><select name="items[${rowIndex}][product_id]" class="form-select" required>${productOptions()}</select></td>
                <td><input type="number" name="items[${rowIndex}][quantity]" class="form-control text-end" value="1" min="1" max="999" required></td>
                <td class="text-end subtotal">$0.00</td>
                <td class="text-end"><button type="button" class="btn btn-sm btn-outline-danger remove-row">{{ __('Remove') }}</button></td>`;
            tbody.appendChild(tr);
            rowIndex++;
        }

        tbody.addEventListener('change', recalc);
        tbody.addEventListener('input', recalc);
        tbody.addEventListener('click', e => {
            if (e.target.classList.contains('remove-row')) {
                e.target.closest('tr').remove();
                recalc();
            }
        });
        document.getElementById('add-item').addEventListener('click', addRow);

        addRow();
    </script>
</x-app-layout>
