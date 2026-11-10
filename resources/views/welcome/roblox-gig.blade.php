@extends('welcome.layout')

@section('head')
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Informasi layanan Roblox Gift in Game di Veluna Store.">
  <title>Roblox GIG - Veluna Store</title>
  <link rel="icon" type="image/png" href="{{ asset('Welcome/image/iconvelunabulat.png') }}">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('Welcome/style.css') }}">
  <link rel="stylesheet" href="{{ asset('Welcome/roblox.css') }}">
</head>
@endsection

@section('content')
  <main class="gig-page page-width">
    <nav class="breadcrumbs" aria-label="Breadcrumb">
      <a href="{{ route('welcome.index') }}">Beranda</a><span aria-hidden="true">/</span>
      <a href="{{ route('welcome.roblox') }}">Roblox</a><span aria-hidden="true">/</span>
      <span aria-current="page">GIG</span>
    </nav>
    <nav class="method-switch gig-method-switch" aria-label="Metode top up Roblox">
      <a href="{{ route('welcome.roblox') }}">Robux Visend</a>
      <a href="{{ route('welcome.roblox-vilog') }}">Robux Vilog</a>
      <a class="active" href="{{ route('welcome.roblox-gig') }}" aria-current="page">GIG <span>(Gift in Game)</span></a>
    </nav>
    <section class="gig-placeholder">
      <img class="gig-icon game-logo" src="{{ asset('Welcome/image/iconroblox.png') }}" alt="Ikon Roblox">
      <p class="eyebrow">ROBLOX · GIFT IN GAME</p>
      <h1>Halaman GIG sedang disiapkan</h1>
      <p>Informasi paket dan cara pemesanan Gift in Game akan ditambahkan di sini.</p>
      <a class="button button-dark" href="{{ route('welcome.index') }}#produk">Kembali ke produk <span aria-hidden="true">→</span></a>
    </section>
  </main>
@endsection

@section('scripts')
<script src="{{ asset('Welcome/site-loader.js') }}"></script>
  <script src="{{ asset('Welcome/music-player.js') }}"></script>
  <script src="{{ asset('Welcome/order-guide.js') }}"></script>
@endsection
