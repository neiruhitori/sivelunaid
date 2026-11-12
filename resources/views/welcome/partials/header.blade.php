  <div class="site-loader" data-site-loader role="status" aria-label="Memuat Veluna Store">
    <div class="site-loader-content">
      <span class="site-loader-spinner"><img data-site-loader-icon alt=""></span>
      <p class="site-loader-text">Menyiapkan pengalaman Veluna...</p>
    </div>
  </div>
  <header class="site-header">
    <div class="header-top page-width">
      <a class="brand" href="{{ route('welcome.index') }}" aria-label="Veluna Store beranda"><img class="brand-mark" src="{{ asset('Welcome/image/iconveluna.png') }}" alt="">VELUNA<span>STORE</span></a>
      <form class="search" role="search" action="{{ route('welcome.index') }}" method="get">
        <label class="sr-only" for="site-search">Cari produk</label>
        <input id="site-search" type="search" name="q" placeholder="Cari produk favoritmu..." autocomplete="off">
        <button type="submit" aria-label="Cari">⌕</button>
      </form>
      <div class="header-actions">
        <a class="whatsapp-link" href="https://wa.me/6738386518" target="_blank" rel="noopener noreferrer">Hubungi via WhatsApp</a>
      </div>
    </div>
    <nav class="main-nav page-width" aria-label="Navigasi utama">
      <a @if($isHome) class="active" @endif href="{{ route('welcome.index') }}">Beranda</a>
      <a @if(request()->routeIs('welcome.product-group')) class="active" @endif href="{{ route('welcome.index') }}#produk">Produk</a>
      <a href="{{ route('welcome.index') }}#tentang">Tentang Kami</a>
      <a @if(request()->routeIs('welcome.orders.history')) class="active" @endif href="{{ route('welcome.orders.history') }}">Riwayat Pesanan</a>
      <a href="#cara-order" data-order-guide>Cara Order</a>
    </nav>
  </header>
