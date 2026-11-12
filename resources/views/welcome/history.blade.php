@extends('welcome.layout')

@section('head')
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="Lihat pesanan Veluna Store yang sudah selesai.">
        <title>Riwayat Pesanan - Veluna Store</title>
        <link rel="icon" type="image/png" href="{{ asset('Welcome/image/iconvelunabulat.png') }}">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="{{ asset('Welcome/style.css') }}">
    </head>
@endsection

@section('content')
    <main class="order-history-page page-width">
        <nav class="breadcrumbs" aria-label="Breadcrumb">
            <a href="{{ route('welcome.index') }}">Beranda</a><span aria-hidden="true">/</span>
            <span aria-current="page">Riwayat Pesanan</span>
        </nav>
        <header class="order-history-heading">
            <p class="eyebrow">VELUNA STORE</p>
            <h1>Riwayat Pesanan</h1>
            <p>Riwayat pesanan yang sudah selesai. Informasi pribadi pembeli tidak ditampilkan.</p>
        </header>

        <form class="order-history-search" method="GET" action="{{ route('welcome.orders.history') }}" role="search" data-order-history-search data-endpoint="{{ route('welcome.orders.history') }}">
            <label class="sr-only" for="order-history-query">Cari pesanan</label>
            <input id="order-history-query" type="search" name="q" value="{{ $search }}" placeholder="Cari produk atau nomor pesanan...">
            <button class="button button-dark" type="submit">Cari</button>
            @if ($search !== '')
                <a class="order-history-clear" href="{{ route('welcome.orders.history') }}">Hapus pencarian</a>
            @endif
        </form>

        <div data-order-history-results aria-live="polite" aria-busy="false">
            @include('welcome.partials.order-history-results', ['orders' => $orders, 'search' => $search])
        </div>
    </main>
@endsection

@section('scripts')
    <script src="{{ asset('Welcome/js/site-loader.js') }}"></script>
    <script src="{{ asset('Welcome/js/music-player.js') }}"></script>
    <script src="{{ asset('Welcome/js/order-guide.js') }}"></script>
    <script src="{{ asset('Welcome/js/order-history.js') }}"></script>
@endsection
