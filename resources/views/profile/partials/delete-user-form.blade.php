<section>
    <header class="mb-3">
        <h2 class="h5 mb-1 text-danger">刪除帳號</h2>
        <p class="text-secondary small mb-0">帳號刪除後，所有資料將永久移除且無法復原，請先備份需要保留的資料。</p>
    </header>

    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#confirmUserDeletionModal">刪除帳號</button>

    <div class="modal fade" id="confirmUserDeletionModal" tabindex="-1" aria-labelledby="confirmUserDeletionLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form method="post" action="{{ route('profile.destroy') }}" class="modal-content">
                @csrf
                @method('delete')

                <div class="modal-header">
                    <h3 class="modal-title h5" id="confirmUserDeletionLabel">確定要刪除帳號嗎？</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="關閉"></button>
                </div>

                <div class="modal-body">
                    <p class="text-secondary small">請輸入密碼，以確認永久刪除帳號。</p>

                    <x-input-label for="delete_password" value="密碼" class="visually-hidden" />
                    <x-text-input id="delete_password" name="password" type="password" placeholder="密碼" />
                    <x-input-error :messages="$errors->userDeletion->get('password')" />
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">取消</button>
                    <button type="submit" class="btn btn-danger">確認刪除</button>
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
