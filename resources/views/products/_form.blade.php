@csrf

<div class="mb-3">
    <x-input-label for="name" :value="__('Name')" />
    <x-text-input id="name" name="name" type="text" :value="old('name', $product->name ?? '')" required autofocus />
    <x-input-error :messages="$errors->get('name')" />
</div>

<div class="mb-3">
    <x-input-label for="sku" value="SKU" />
    <x-text-input id="sku" name="sku" type="text" :value="old('sku', $product->sku ?? '')" required />
    <x-input-error :messages="$errors->get('sku')" />
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <x-input-label for="price" :value="__('Price')" />
        <x-text-input id="price" name="price" type="number" step="0.01" min="0" :value="old('price', $product->price ?? '')" required />
        <x-input-error :messages="$errors->get('price')" />
    </div>

    <div class="col-md-6 mb-3">
        <x-input-label for="stock" :value="__('Stock')" />
        <x-text-input id="stock" name="stock" type="number" min="0" :value="old('stock', $product->stock ?? 0)" required />
        <x-input-error :messages="$errors->get('stock')" />
    </div>
</div>

<div class="form-check form-switch mb-4">
    <input type="hidden" name="is_active" value="0">
    <input id="is_active" name="is_active" type="checkbox" value="1" class="form-check-input" role="switch"
        @checked(old('is_active', $product->is_active ?? true))>
    <label class="form-check-label" for="is_active">{{ __('Active') }}</label>
</div>

<div class="d-flex justify-content-between">
    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">{{ __('Back') }}</a>
    <x-primary-button>{{ __('Save') }}</x-primary-button>
</div>
