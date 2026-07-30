<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="h4 mb-0">{{ __('Users') }}</h1>
            <a href="{{ route('users.create') }}" class="btn btn-primary">{{ __('New User') }}</a>
        </div>
    </x-slot>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="GET" action="{{ route('users.index') }}" class="row g-2 mb-3">
                <div class="col-12 col-md-4">
                    <input type="search" name="search" value="{{ request('search') }}" class="form-control" placeholder="{{ __('Search by name or email') }}">
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
                            <th>{{ __('Role') }}</th>
                            <th>{{ __('Created At') }}</th>
                            <th class="text-end">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td>{{ $user->name }} @if (auth()->user()->is($user))<span class="badge text-bg-light">{{ __('You') }}</span>@endif</td>
                                <td class="text-secondary">{{ $user->email }}</td>
                                <td>
                                    <span class="badge {{ $user->isAdmin() ? 'text-bg-primary' : 'text-bg-secondary' }}">{{ $user->role->label() }}</span>
                                </td>
                                <td class="text-secondary">{{ $user->created_at->format('Y-m-d') }}</td>
                                <td class="text-end">
                                    <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-outline-primary">{{ __('Edit') }}</a>
                                    @can('delete', $user)
                                        <form method="POST" action="{{ route('users.destroy', $user) }}" class="d-inline" onsubmit="return confirm('{{ __('Are you sure you want to delete this user?') }}')">
                                            @csrf
                                            @method('delete')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">{{ __('Delete') }}</button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $users->links() }}
        </div>
    </div>
</x-app-layout>
