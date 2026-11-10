<p class="text-muted">Menghapus akun akan menghapus data akun secara permanen.</p>
<button type="button" class="btn btn-outline-danger" data-toggle="modal" data-target="#delete-account-modal">
    <i class="fas fa-trash-alt mr-1"></i>Hapus Akun
</button>

<div class="modal fade" id="delete-account-modal" tabindex="-1" role="dialog" aria-labelledby="delete-account-title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form method="post" action="{{ route('profile.destroy') }}">
                @csrf
                @method('delete')
                <div class="modal-header">
                    <h5 class="modal-title" id="delete-account-title">Hapus akun?</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Tutup"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <p>Masukkan password untuk mengonfirmasi penghapusan akun. Tindakan ini tidak dapat dibatalkan.</p>
                    <div class="form-group mb-0">
                        <label for="delete-account-password">Password</label>
                        <input id="delete-account-password" name="password" type="password" class="form-control @if ($errors->userDeletion->has('password')) is-invalid @endif" required>
                        @if ($errors->userDeletion->has('password'))<div class="invalid-feedback">{{ $errors->userDeletion->first('password') }}</div>@endif
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger"><i class="fas fa-trash-alt mr-1"></i>Hapus Akun</button>
                </div>
            </form>
        </div>
    </div>
</div>

@if ($errors->userDeletion->isNotEmpty())
    @push('scripts')
        <script>$(function () { $('#delete-account-modal').modal('show'); });</script>
    @endpush
@endif
