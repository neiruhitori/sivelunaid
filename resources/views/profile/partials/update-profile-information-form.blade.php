<form id="send-verification" method="post" action="{{ route('verification.send') }}">@csrf</form>
<form method="post" action="{{ route('profile.update') }}">
    @csrf
    @method('patch')
    <div class="form-group">
        <label for="name">Nama</label>
        <input id="name" name="name" type="text" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="form-group">
        <label for="email">Email</label>
        <input id="email" name="email" type="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required autocomplete="username">
        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-circle mr-1"></i> Email kamu belum diverifikasi.
            <button form="send-verification" class="btn btn-link btn-sm p-0 ml-1">Kirim ulang tautan verifikasi</button>
            @if (session('status') === 'verification-link-sent')<div class="mt-2 text-success">Tautan verifikasi baru sudah dikirim.</div>@endif
        </div>
    @endif

    <div class="d-flex align-items-center">
        <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i>Simpan Profil</button>
        @if (session('status') === 'profile-updated')<span class="ml-3 text-success"><i class="fas fa-check-circle mr-1"></i>Perubahan tersimpan.</span>@endif
    </div>
</form>
