<x-app-layout>
    <x-slot name="header">
        <div class="d-flex flex-wrap align-items-center justify-content-between">
            <div>
                <h1 class="m-0 text-dark">Dashboard</h1>
                <small class="text-muted">Ringkasan pengelolaan katalog VelunaID</small>
            </div>
            <ol class="breadcrumb float-sm-right mb-0 bg-transparent">
                <li class="breadcrumb-item active">Dashboard</li>
            </ol>
        </div>
    </x-slot>

    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner"><h3>{{ $categoryCount }}</h3><p>Category</p></div>
                <div class="icon"><i class="fas fa-th-large"></i></div>
                <a href="{{ route('admin.categories.index') }}" class="small-box-footer">Kelola kategori <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner"><h3>{{ $productCount }}</h3><p>Product</p></div>
                <div class="icon"><i class="fas fa-box"></i></div>
                <a href="{{ route('admin.products.index') }}" class="small-box-footer">Kelola produk <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner"><h3>{{ $productGroupCount }}</h3><p>Product Group</p></div>
                <div class="icon"><i class="fas fa-layer-group"></i></div>
                <a href="{{ route('admin.product-groups.index') }}" class="small-box-footer">Kelola grup <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner"><h3>{{ $productVariantCount }}</h3><p>Product Variant</p></div>
                <div class="icon"><i class="fas fa-boxes"></i></div>
                <a href="{{ route('admin.product-variants.index') }}" class="small-box-footer">Kelola varian <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><h3 class="card-title"><i class="fas fa-compass mr-2 text-primary"></i>Mulai Kelola Katalog</h3></div>
                <div class="card-body">
                    <p class="text-muted">Gunakan menu di samping untuk mengatur kategori, produk, grup produk, varian, dan field produk.</p>
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-primary"><i class="fas fa-th-large mr-1"></i> Buka Category</a>
                    <a href="{{ route('admin.product-fields.index') }}" class="btn btn-outline-secondary ml-2"><i class="fas fa-list-alt mr-1"></i> Produk Field</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="info-box">
                <span class="info-box-icon bg-primary"><i class="fas fa-list-alt"></i></span>
                <div class="info-box-content"><span class="info-box-text">Produk Field</span><span class="info-box-number">Atur atribut produk</span><a href="{{ route('admin.product-fields.index') }}">Buka pengaturan <i class="fas fa-arrow-right fa-sm"></i></a></div>
            </div>
        </div>
    </div>
</x-app-layout>
