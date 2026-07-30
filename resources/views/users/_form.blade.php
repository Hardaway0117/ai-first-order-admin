@csrf

<div class="mb-3">
    <x-input-label for="name" :value="__('Name')" />
    <x-text-input id="name" name="name" type="text" :value="old('name', $user->name ?? '')" required autofocus />
    <x-input-error :messages="$errors->get('name')" />
</div>

<div class="mb-3">
    <x-input-label for="email" :value="__('Email')" />
    <x-text-input id="email" name="email" type="email" :value="old('email', $user->email ?? '')" required />
    <x-input-error :messages="$errors->get('email')" />
</div>

<div class="mb-3">
    <x-input-label for="role" :value="__('Role')" />
    <select id="role" name="role" class="form-select" required>
        @foreach (\App\Enums\UserRole::cases() as $role)
            <option value="{{ $role->value }}" @selected(old('role', isset($user) ? $user->role->value : 'staff') === $role->value)>{{ $role->label() }}</option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('role')" />
</div>

<div class="mb-3">
    <x-input-label for="password" :value="__('Password')" />
    <x-text-input id="password" name="password" type="password" autocomplete="new-password" :required="! isset($user)" />
    @isset($user)
        <div class="form-text">{{ __('Leave blank to keep the current password.') }}</div>
    @endisset
    <x-input-error :messages="$errors->get('password')" />
</div>

<div class="mb-4">
    <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
    <x-text-input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" :required="! isset($user)" />
</div>

<div class="d-flex justify-content-between">
    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">{{ __('Back') }}</a>
    <x-primary-button>{{ __('Save') }}</x-primary-button>
</div>
