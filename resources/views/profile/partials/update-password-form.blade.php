<p class="text-muted">Gunakan password yang kuat untuk menjaga keamanan akun.</p>
<form method="post" action="{{ route('password.update') }}">
    @csrf
    @method('put')
    <div class="form-group">
        <label for="update_password_current_password">Password Saat Ini</label>
        <input id="update_password_current_password" name="current_password" type="password" class="form-control @if ($errors->updatePassword->has('current_password')) is-invalid @endif" autocomplete="current-password">
        @if ($errors->updatePassword->has('current_password'))<div class="invalid-feedback">{{ $errors->updatePassword->first('current_password') }}</div>@endif
    </div>
    <div class="form-group">
        <label for="update_password_password">Password Baru</label>
        <input id="update_password_password" name="password" type="password" class="form-control @if ($errors->updatePassword->has('password')) is-invalid @endif" autocomplete="new-password">
        @if ($errors->updatePassword->has('password'))<div class="invalid-feedback">{{ $errors->updatePassword->first('password') }}</div>@endif
    </div>
    <div class="form-group">
        <label for="update_password_password_confirmation">Konfirmasi Password Baru</label>
        <input id="update_password_password_confirmation" name="password_confirmation" type="password" class="form-control" autocomplete="new-password">
    </div>
    <div class="d-flex align-items-center">
        <button type="submit" class="btn btn-primary"><i class="fas fa-key mr-1"></i>Perbarui Password</button>
        @if (session('status') === 'password-updated')<span class="ml-3 text-success"><i class="fas fa-check-circle mr-1"></i>Password diperbarui.</span>@endif
    </div>
</form>
