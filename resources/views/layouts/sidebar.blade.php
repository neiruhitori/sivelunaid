<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="{{ route('dashboard') }}" class="brand-link">
        <span class="brand-image img-circle elevation-2 d-flex align-items-center justify-content-center bg-primary text-white" style="width: 33px; height: 33px; opacity: 1"><i class="fas fa-store"></i></span>
        <span class="brand-text font-weight-light">VelunaID</span>
    </a>

    <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image"><span class="img-circle elevation-2 bg-secondary d-inline-flex align-items-center justify-content-center text-white" style="width: 34px; height: 34px"><i class="fas fa-user"></i></span></div>
            <div class="info"><span class="d-block text-white">{{ Auth::user()->name }}</span></div>
        </div>

        @php
            $adminMenu = [
                ['label' => 'Dashboard', 'route' => 'dashboard', 'active' => 'dashboard', 'icon' => 'fas fa-tachometer-alt'],
                ['label' => 'Pesanan', 'route' => 'admin.orders.index', 'active' => 'admin.orders.*', 'icon' => 'fas fa-receipt'],
                ['label' => 'Category', 'route' => 'admin.categories.index', 'active' => 'admin.categories.*', 'icon' => 'fas fa-th-large'],
                ['label' => 'Product', 'route' => 'admin.products.index', 'active' => 'admin.products.*', 'icon' => 'fas fa-box'],
                ['label' => 'Product Group', 'route' => 'admin.product-groups.index', 'active' => 'admin.product-groups.*', 'icon' => 'fas fa-layer-group'],
                ['label' => 'Product Variant', 'route' => 'admin.product-variants.index', 'active' => 'admin.product-variants.*', 'icon' => 'fas fa-boxes'],
                ['label' => 'Produk Field', 'route' => 'admin.product-fields.index', 'active' => 'admin.product-fields.*', 'icon' => 'fas fa-list-alt'],
            ];
        @endphp
        <nav class="mt-2" aria-label="Menu admin">
            <ul class="nav nav-pills nav-sidebar flex-column" role="menu" data-accordion="false">
                <li class="nav-header">MENU UTAMA</li>
                @foreach ($adminMenu as $item)
                    <li class="nav-item">
                        <a href="{{ route($item['route']) }}" class="nav-link {{ request()->routeIs($item['active']) ? 'active' : '' }}">
                            <i class="nav-icon {{ $item['icon'] }}"></i>
                            <p>{{ $item['label'] }}</p>
                        </a>
                    </li>
                @endforeach
            </ul>
        </nav>
    </div>
</aside>
