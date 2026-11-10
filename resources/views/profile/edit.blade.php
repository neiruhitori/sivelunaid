<x-app-layout>
    <x-slot name="header">
        <div class="d-flex align-items-center justify-content-between flex-wrap">
            <div>
                <h1 class="m-0 text-dark">Profil Saya</h1>
                <small class="text-muted">Kelola informasi akun dan keamanan.</small>
            </div>
            <ol class="breadcrumb float-sm-right mb-0 bg-transparent">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Profil</li>
            </ol>
        </div>
    </x-slot>

    <div class="row">
        <div class="col-lg-8">
            <div class="card card-primary card-outline">
                <div class="card-header"><h3 class="card-title"><i class="fas fa-user-edit mr-2"></i>Informasi Profil</h3></div>
                <div class="card-body">@include('profile.partials.update-profile-information-form')</div>
            </div>
            <div class="card card-info card-outline">
                <div class="card-header"><h3 class="card-title"><i class="fas fa-lock mr-2"></i>Ubah Password</h3></div>
                <div class="card-body">@include('profile.partials.update-password-form')</div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card card-danger card-outline">
                <div class="card-header"><h3 class="card-title"><i class="fas fa-user-times mr-2"></i>Hapus Akun</h3></div>
                <div class="card-body">@include('profile.partials.delete-user-form')</div>
            </div>
        </div>
    </div>
</x-app-layout>
