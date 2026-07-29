<section>
    <header class="mb-3">
        <h2 class="h5 mb-1 text-danger">{{ __('Delete Account') }}</h2>
        <p class="text-secondary small mb-0">{{ __('Once your account is deleted, all of its data will be permanently removed. Please back up anything you want to keep.') }}</p>
    </header>

    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#confirmUserDeletionModal">{{ __('Delete Account') }}</button>

    <div class="modal fade" id="confirmUserDeletionModal" tabindex="-1" aria-labelledby="confirmUserDeletionLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form method="post" action="{{ route('profile.destroy') }}" class="modal-content">
                @csrf
                @method('delete')

                <div class="modal-header">
                    <h3 class="modal-title h5" id="confirmUserDeletionLabel">{{ __('Are you sure you want to delete your account?') }}</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
                </div>

                <div class="modal-body">
                    <p class="text-secondary small">{{ __('Please enter your password to confirm you would like to permanently delete your account.') }}</p>

                    <x-input-label for="delete_password" :value="__('Password')" class="visually-hidden" />
                    <x-text-input id="delete_password" name="password" type="password" :placeholder="__('Password')" />
                    <x-input-error :messages="$errors->userDeletion->get('password')" />
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-danger">{{ __('Confirm Deletion') }}</button>
                </div>
            </form>
        </div>
    </div>

    @if ($errors->userDeletion->isNotEmpty())
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                new window.bootstrap.Modal(document.getElementById('confirmUserDeletionModal')).show();
            });
        </script>
    @endif
</section>
