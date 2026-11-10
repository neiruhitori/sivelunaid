<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button" aria-label="Toggle sidebar">
                <i class="fas fa-bars"></i>
            </a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="{{ route('dashboard') }}" class="nav-link">VelunaID Admin</a>
        </li>
    </ul>
    <ul class="navbar-nav ml-auto align-items-center">
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#" role="button">
                <i class="far fa-user mr-1"></i>{{ Auth::user()->name }} <i class="fas fa-angle-down ml-1"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <a href="{{ route('profile.edit') }}" class="dropdown-item"><i class="fas fa-user-cog mr-2"></i>Profil</a>
                <div class="dropdown-divider"></div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="dropdown-item"><i class="fas fa-sign-out-alt mr-2"></i>Keluar</button>
                </form>
            </div>
        </li>
    </ul>
</nav>
