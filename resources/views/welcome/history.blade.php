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

        <form class="order-history-search" method="GET" action="{{ route('welcome.orders.history') }}" role="search" data-order-history-search>
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
    <script src="{{ asset('Welcome/site-loader.js') }}"></script>
    <script src="{{ asset('Welcome/music-player.js') }}"></script>
    <script src="{{ asset('Welcome/order-guide.js') }}"></script>
    <script>
        (() => {
            const form = document.querySelector('[data-order-history-search]');
            const input = form?.querySelector('input[name="q"]');
            const results = document.querySelector('[data-order-history-results]');
            if (!form || !input || !results) return;

            let timer;
            let controller;
            let requestId = 0;

            const loadResults = async (url, updateAddress = true) => {
                controller?.abort();
                controller = new AbortController();
                const currentRequest = ++requestId;
                results.setAttribute('aria-busy', 'true');
                results.classList.add('is-loading');

                try {
                    const response = await fetch(url, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                        signal: controller.signal,
                    });
                    if (!response.ok) throw new Error('Pencarian gagal dimuat.');
                    const data = await response.json();
                    if (currentRequest !== requestId) return;
                    results.innerHTML = data.html;
                    if (updateAddress) window.history.replaceState({}, '', url);
                } catch (error) {
                    if (error.name !== 'AbortError') form.submit();
                } finally {
                    if (currentRequest === requestId) {
                        results.setAttribute('aria-busy', 'false');
                        results.classList.remove('is-loading');
                    }
                }
            };

            const makeUrl = () => {
                const url = new URL(form.action, window.location.href);
                const query = input.value.trim();
                if (query) url.searchParams.set('q', query);
                else url.searchParams.delete('q');
                url.searchParams.delete('page');
                return url.toString();
            };

            input.addEventListener('input', () => {
                window.clearTimeout(timer);
                timer = window.setTimeout(() => loadResults(makeUrl()), 300);
            });

            form.addEventListener('submit', (event) => {
                event.preventDefault();
                window.clearTimeout(timer);
                loadResults(makeUrl());
            });

            results.addEventListener('click', (event) => {
                const link = event.target.closest('.pagination a');
                if (!link) return;
                event.preventDefault();
                loadResults(link.href);
            });

            window.addEventListener('popstate', () => {
                const url = new URL(window.location.href);
                input.value = url.searchParams.get('q') || '';
                loadResults(url.toString(), false);
            });
        })();
    </script>
@endsection
