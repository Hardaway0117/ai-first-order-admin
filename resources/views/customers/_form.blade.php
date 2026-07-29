@csrf

<div class="mb-3">
    <x-input-label for="name" :value="__('Name')" />
    <x-text-input id="name" name="name" type="text" :value="old('name', $customer->name ?? '')" required autofocus />
    <x-input-error :messages="$errors->get('name')" />
</div>

<div class="mb-3">
    <x-input-label for="email" :value="__('Email')" />
    <x-text-input id="email" name="email" type="email" :value="old('email', $customer->email ?? '')" required />
    <x-input-error :messages="$errors->get('email')" />
</div>

<div class="mb-3">
    <x-input-label for="phone" :value="__('Phone')" />
    <x-text-input id="phone" name="phone" type="text" :value="old('phone', $customer->phone ?? '')" />
    <x-input-error :messages="$errors->get('phone')" />
</div>

<div class="mb-4">
    <x-input-label for="address" :value="__('Address')" />
    <x-text-input id="address" name="address" type="text" :value="old('address', $customer->address ?? '')" />
    <x-input-error :messages="$errors->get('address')" />
</div>

<div class="d-flex justify-content-between">
    <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary">{{ __('Back') }}</a>
    <x-primary-button>{{ __('Save') }}</x-primary-button>
</div>
