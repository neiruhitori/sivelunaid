@extends('welcome.layout')

@section('head')

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="Veluna Store — temukan produk pilihan dan penawaran spesial.">
        <title>Veluna Store — Belanja Pilihanmu</title>
        <link rel="icon" type="image/png" href="{{ asset('Welcome/image/iconvelunabulat.png') }}">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&display=swap"
            rel="stylesheet">
        <link rel="stylesheet" href="{{ asset('Welcome/style.css') }}">
    </head>
@endsection

@section('content')
    <main>
        <section class="hero page-width" aria-label="Penawaran unggulan" aria-roledescription="carousel" data-carousel>
            <div class="hero-track" data-carousel-track aria-live="off">
                <article class="hero-slide is-active" aria-roledescription="slide" aria-label="1 dari 3">
                    <div class="hero-copy">
                        <p class="eyebrow">SELAMAT DATANG DI VELUNA</p>
                        <h1>Temukan yang kamu suka,<br><span>dengan cara yang mudah.</span></h1>
                        <p class="hero-description">Pilihan produk terbaik, harga bersahabat, dan pengalaman belanja yang
                            nyaman
                            untukmu.</p>
                        <a class="button button-light" href="#produk">Jelajahi Produk <span aria-hidden="true">→</span></a>
                    </div>
                    <div class="hero-art" aria-label="Banner Veluna Store" role="img">
                        <span class="hero-badge">PILIHAN<br><strong>SPESIAL</strong></span>
                    </div>
                </article>
                <article class="hero-slide" aria-roledescription="slide" aria-label="2 dari 3" aria-hidden="true" inert>
                    <div class="hero-copy">
                        <p class="eyebrow">KOLEKSI TERBARU</p>
                        <h1>Awali harimu dengan<br><span>inspirasi yang baru.</span></h1>
                        <p class="hero-description">Jelajahi koleksi terbaru Veluna dan temukan pilihan yang paling cocok
                            untukmu.
                        </p>
                        <a class="button button-light" href="#produk">Lihat Koleksi <span aria-hidden="true">→</span></a>
                    </div>
                    <div class="hero-art" aria-label="Banner Veluna Store" role="img">
                        <span class="hero-badge">KOLEKSI<br><strong>BARU</strong></span>
                    </div>
                </article>
                <article class="hero-slide" aria-roledescription="slide" aria-label="3 dari 3" aria-hidden="true" inert>
                    <div class="hero-copy">
                        <p class="eyebrow">PENAWARAN KHUSUS</p>
                        <h1>Hal istimewa untuk<br><span>momen spesialmu.</span></h1>
                        <p class="hero-description">Temukan produk pilihan dan penawaran menarik yang sudah disiapkan
                            untukmu.</p>
                        <a class="button button-light" href="#produk">Temukan Pilihan <span aria-hidden="true">→</span></a>
                    </div>
                    <div class="hero-art" aria-label="Banner Veluna Store" role="img">
                        <span class="hero-badge">PILIHAN<br><strong>VELUNA</strong></span>
                    </div>
                </article>
            </div>
            <button class="carousel-arrow carousel-previous" type="button" aria-label="Banner sebelumnya"
                data-carousel-previous>‹</button>
            <button class="carousel-arrow carousel-next" type="button" aria-label="Banner berikutnya"
                data-carousel-next>›</button>
            <div class="hero-dots" role="group" aria-label="Pilih banner">
                <button class="selected" type="button" aria-label="Tampilkan banner 1" aria-current="true"
                    data-carousel-dot="0"></button>
                <button type="button" aria-label="Tampilkan banner 2" data-carousel-dot="1"></button>
                <button type="button" aria-label="Tampilkan banner 3" data-carousel-dot="2"></button>
            </div>
        </section>

        <section class="benefits page-width" aria-label="Keunggulan Veluna Store">
            <div><span class="benefit-icon">✦</span>
                <p><strong>Aman</strong><small>Keamanan dan kenyamanan prioritas Utama kami</small></p>
            </div>
            <div><span class="benefit-icon">◇</span>
                <p><strong>Terpercaya</strong><small>Sudah dipercaya oleh ratusan pelanggan</small></p>
            </div>
            <div><span class="benefit-icon">♡</span>
                <p><strong>Layanan Terbaik</strong><small>Kami siap membantu yang anda butuhkan</small></p>
            </div>
        </section>

        <section class="product-section page-width" id="produk">
            <div class="section-heading">
                <div>
                    <p class="eyebrow">PILIHAN UNTUKMU</p>
                    <h2>Jelajahi Produk</h2>
                    <p class="section-description">Pilih kategori dan temukan produk yang kamu cari.</p>
                </div>
            </div>

            <div class="category-pills" id="kategori" role="tablist" aria-label="Kategori produk">
                @foreach ($categories as $index => $category)
                    <button class="pill category-pill {{ $index === 0 ? 'active' : '' }}"
                        id="tab-category-{{ $category->id }}" type="button" role="tab"
                        aria-selected="{{ $index === 0 ? 'true' : 'false' }}"
                        aria-controls="panel-category-{{ $category->id }}" data-product-tab="category-{{ $category->id }}"
                        tabindex="{{ $index === 0 ? '0' : '-1' }}"
                        style="--category-image: {{ $category->image ? 'url(' . json_encode(asset('storage/' . $category->image)) . ')' : 'none' }}">
                        <span class="pill-name">{{ $category->name }}</span>
                        <span class="pill-count">{{ $category->products->count() }} produk</span>
                    </button>
                @endforeach
            </div>
            <p class="search-status" data-search-status role="status" aria-live="polite" hidden></p>

            @forelse ($categories as $index => $category)
                <div class="product-panel" id="panel-category-{{ $category->id }}" role="tabpanel"
                    aria-labelledby="tab-category-{{ $category->id }}" data-product-panel="category-{{ $category->id }}"
                    @if ($index > 0) hidden @endif>
                    <h3 class="search-panel-heading" hidden>{{ $category->name }}</h3>
                    <div
                        class="product-grid catalog-product-grid {{ $category->products->count() <= 2 ? 'game-grid' : '' }}">
                        @forelse ($category->products as $product)
                            @php
                                $productSlug = \Illuminate\Support\Str::slug($product->slug ?: $product->name);
                                $fallbackProductUrl = match ($productSlug) {
                                    'roblox' => route('welcome.roblox'),
                                    'mobile-legends' => route('welcome.mobile-legends'),
                                    default => 'https://wa.me/6738386518?text=' .
                                        rawurlencode('Halo Veluna, saya tertarik dengan produk ' . $product->name),
                                };
                                $productUrl = $product->groups->count() === 1
                                    ? route('welcome.product-group', $product->groups->first())
                                    : ($product->groups->count() > 1 ? '#' : $fallbackProductUrl);
                                $fallbackImage = match ($productSlug) {
                                    'roblox' => asset('Welcome/image/iconroblox.png'),
                                    'mobile-legends' => asset('Welcome/image/iconml.png'),
                                    default => null,
                                };
                            @endphp
                            @php
                                $catalogImage = $product->image ? asset('storage/' . $product->image) : $fallbackImage;
                            @endphp
                            <article class="product-card">
                                <a class="product-image catalog-product-image" href="{{ $productUrl }}"
                                    @if ($productSlug === 'roblox' && $product->groups->isEmpty()) data-roblox-methods @endif
                                    @if ($product->groups->count() > 1) data-product-groups="{{ $product->groups->map(fn ($group) => ['name' => $group->name, 'url' => route('welcome.product-group', $group)])->values()->toJson() }}" data-product-name="{{ $product->name }}" @endif
                                    aria-label="Lihat {{ $product->name }}">
                                    @if ($catalogImage)
                                        <span class="catalog-product-backdrop"
                                            style="--product-image: url('{{ $catalogImage }}')"
                                            aria-hidden="true"></span>
                                        <img class="catalog-product-logo" src="{{ $catalogImage }}"
                                            alt="{{ $product->name }}">
                                    @else
                                        <span class="catalog-product-mark"
                                            aria-hidden="true">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($product->name, 0, 1)) }}</span>
                                    @endif
                                    <b>{{ $category->name }}</b>
                                </a>
                                <div class="product-info">
                                    <p class="product-category">{{ $category->name }}</p>
                                    <h3>{{ $product->name }}</h3>
                                    <p class="product-detail">{{ $product->description ?: 'Produk pilihan Veluna Store' }}
                                    </p>
                                    <a class="product-cta" href="{{ $productUrl }}"
                                        @if ($productSlug === 'roblox' && $product->groups->isEmpty()) data-roblox-methods @endif
                                        @if ($product->groups->count() > 1) data-product-groups="{{ $product->groups->map(fn ($group) => ['name' => $group->name, 'url' => route('welcome.product-group', $group)])->values()->toJson() }}" data-product-name="{{ $product->name }}" @endif>
                                        {{ $product->groups->isNotEmpty() || in_array($productSlug, ['roblox', 'mobile-legends'], true) ? 'Lihat produk' : 'Tanya produk' }}
                                        <span aria-hidden="true">&rarr;</span>
                                    </a>
                                </div>
                            </article>
                        @empty
                            <p class="catalog-empty">Belum ada produk aktif di kategori ini.</p>
                        @endforelse
                    </div>
                </div>
            @empty
                <p class="catalog-empty">Kategori produk akan segera hadir.</p>
            @endforelse
        </section>

        <section class="promo page-width">
            <div class="promo-art" role="img" aria-label="Ilustrasi Veluna Store"><img
                    src="{{ asset('Welcome/image/iconvelunabulat.png') }}" alt="">
            </div>
            <div class="promo-copy">
                <p class="eyebrow">KENALI VELUNA</p>
                <h2>Teman belanja digitalmu, sejak Februari 2026.</h2>
                <p>Berawal dari gift in-game Roblox, kini VélunaID hadir dengan pilihan produk digital yang mudah, aman, dan
                    ramah di kantong.</p>
                <button class="button button-dark" type="button" data-veluna-about-open>Tentang Veluna <span
                        aria-hidden="true">→</span></button>
            </div>
        </section>

        <section class="article-section page-width" id="artikel">
            <!-- <div class="section-heading">
            <div>
              <p class="eyebrow">CERITA & INSPIRASI</p>
              <h2>Artikel terbaru</h2>
              <p class="section-description">Temukan cerita, tips, dan inspirasi dari Veluna.</p>
            </div>
            <a class="text-link" href="#">Lihat semua artikel <span aria-hidden="true">→</span></a>
          </div>
          <div class="article-grid">
            <a class="article-card" href="#"><span class="article-image"><span>Area gambar artikel</span></span><span class="article-content"><small>INSPIRASI · 5 MENIT BACA</small><strong>Temukan inspirasi baru bersama Veluna</strong><span>Baca selengkapnya →</span></span></a>
            <a class="article-card" href="#"><span class="article-image article-image-two"><span>Area gambar artikel</span></span><span class="article-content"><small>PANDUAN · 3 MENIT BACA</small><strong>Tips memilih produk yang tepat untukmu</strong><span>Baca selengkapnya →</span></span></a>
            <a class="article-card" href="#"><span class="article-image article-image-three"><span>Area gambar artikel</span></span><span class="article-content"><small>VELUNA · 4 MENIT BACA</small><strong>Cerita di balik koleksi pilihan kami</strong><span>Baca selengkapnya →</span></span></a>
          </div> -->
        </section>
    </main>

    <dialog class="roblox-method-dialog" aria-labelledby="roblox-method-title" data-roblox-method-dialog>
        <div class="method-dialog-content">
            <button class="method-dialog-close" type="button" aria-label="Tutup pilihan metode"
                data-method-dialog-close>×</button>
            <p class="eyebrow">PILIH JENIS LAYANAN</p>
            <h2 id="roblox-method-title">Top up Roblox</h2>
            <p class="method-dialog-intro">Pilih metode kategori Roblox untuk melihat paket yang tersedia.</p>
            <div class="roblox-method-options">
                <a class="roblox-method-option" href="{{ route('welcome.roblox') }}">
                    <span class="method-option-icon" aria-hidden="true">V</span>
                    <span><strong>Robux Visend</strong><small>Proses kirim Robux hanya menggunakan username.</small></span>
                    <span class="method-option-arrow" aria-hidden="true">→</span>
                </a>
                <a class="roblox-method-option vilog-option" href="{{ route('welcome.roblox-vilog') }}">
                    <span class="method-option-icon" aria-hidden="true">V</span>
                    <span><strong>Robux Vilog</strong><small>Proses kirim Robux menggunakan login akun.</small></span>
                    <span class="method-option-arrow" aria-hidden="true">→</span>
                </a>
                <a class="roblox-method-option gig-option" href="{{ route('welcome.roblox-gig') }}">
                    <span class="method-option-icon" aria-hidden="true">G</span>
                    <span><strong>GIG <small>(Gift in Game)</small></strong><small>Halaman sedang disiapkan.</small></span>
                    <span class="method-option-arrow" aria-hidden="true">→</span>
                </a>
            </div>
            <p class="method-security-note"><span aria-hidden="true">♧</span> Data dijamin keamanan dan kerahasiaan nya.
            </p>
        </div>
    </dialog>

    <dialog class="roblox-method-dialog product-groups-dialog" aria-labelledby="product-groups-title" data-product-groups-dialog>
        <div class="method-dialog-content">
            <button class="method-dialog-close" type="button" aria-label="Tutup pilihan" data-product-groups-close>&times;</button>
            <p class="eyebrow">PILIHAN PRODUK</p>
            <h2 id="product-groups-title" data-product-groups-title>Pilih pilihan yang tersedia</h2>
            <p class="method-dialog-intro">Pilih salah satu kelompok produk untuk melanjutkan.</p>
            <div class="roblox-method-options" data-product-groups-options></div>
        </div>
    </dialog>

    <dialog class="about-dialog" aria-labelledby="about-dialog-title" data-veluna-about-dialog>
        <button class="method-dialog-close" type="button" aria-label="Tutup cerita VélunaID"
            data-veluna-about-close>×</button>
        <div class="about-dialog-content" data-about-page="story">
            <div class="about-dialog-heading">
                <img src="{{ asset('Welcome/image/iconvelunabulat.png') }}" alt="">
                <p class="eyebrow">CERITA KAMI · SEJAK FEBRUARI 2026</p>
            </div>
            <h2 id="about-dialog-title">Berawal dari satu gift, tumbuh jadi teman digitalmu.</h2>
            <p class="about-dialog-story">VélunaID berdiri pada Februari 2026 dari keinginan sederhana: menyediakan
                kebutuhan
                digital yang mudah, aman, dan ramah di kantong. Perjalanan kami dimulai dari gift in-game Roblox, lalu
                berkembang ke penjualan Robux. Kini, VélunaID terus bertumbuh dengan menghadirkan beragam produk digital,
                termasuk aplikasi premium.</p>
            <div class="about-values">
                <div><span aria-hidden="true">✦</span><strong>Aman & terpercaya</strong><small>Belanja dengan lebih
                        tenang.</small></div>
                <div><span aria-hidden="true">♡</span><strong>Harga bersahabat</strong><small>Produk berkualitas, tetap
                        affordable.</small></div>
                <div><span aria-hidden="true">→</span><strong>Pelayanan friendly</strong><small>Cepat, ramah, dan nggak
                        ribet.</small></div>
            </div>
            <p class="about-dialog-note">Visi kami adalah menjadi toko digital yang aman, affordable, dan terpercaya.
                Terima
                kasih sudah menjadi bagian dari perjalanan VélunaID.</p>
            <button class="button button-dark about-dialog-done" type="button" data-about-next>Senang berkenalan! <span
                    aria-hidden="true">♡</span></button>
        </div>
        <section class="about-welcome-page" aria-labelledby="about-welcome-title" data-about-page="welcome" hidden>
            <div class="welcome-art" aria-hidden="true">
                <span class="welcome-spark welcome-spark-one">✦</span>
                <span class="welcome-spark welcome-spark-two">♡</span>
                <img src="{{ asset('Welcome/image/iconvelunabulat.png') }}" alt="">
            </div>
            <p class="eyebrow">SENANG BISA KENAL KAMU</p>
            <h2 id="about-welcome-title" tabindex="-1">Hai, teman Veluna! ✨</h2>
            <p class="welcome-message" aria-live="polite" data-welcome-message>Senang kamu mampir dan mengenal cerita
                kami.
                Semoga kamu menemukan hal digital yang kamu cari di sini.</p>
            <div class="welcome-progress" role="group" aria-label="Langkah sambutan">
                <span class="is-current" data-welcome-step="0"></span>
                <span data-welcome-step="1"></span>
                <span data-welcome-step="2"></span>
            </div>
            <div class="welcome-actions">
                <button class="button button-dark" type="button" data-welcome-next>Lanjutkan cerita <span
                        aria-hidden="true">→</span></button>
                <button class="welcome-back" type="button" data-about-back>← Kembali ke cerita Véluna</button>
            </div>
        </section>
    </dialog>

    <dialog class="update-group-dialog" aria-labelledby="update-group-title" aria-describedby="update-group-description"
        data-update-group-dialog>
        <button class="method-dialog-close" type="button" aria-label="Tutup pengumuman"
            data-update-group-close>×</button>
        <div class="update-group-content">
            <p class="eyebrow">KABAR VELUNA STORE</p>
            <h2 id="update-group-title">Veluna Store Updates!</h2>
            <p id="update-group-description">Dapatkan informasi lebih awal seputar promo, ketersediaan produk, dan
                aktivitas terbaru Veluna Store.</p>
            <p class="update-group-benefits-intro">Bergabunglah dengan grup WhatsApp kami untuk mendapatkan:</p>
            <ul class="update-group-benefits">
                <li>Informasi stok dan produk ready.</li>
                <li>Pengumuman toko buka dan tutup.</li>
                <li>Update terbaru seputar Veluna Store.</li>
            </ul>
            <a class="button button-dark update-group-join"
                href="https://chat.whatsapp.com/EIoorF55lkwD4i8cpP6UGC?s=cl&amp;p=a&amp;iam=2" target="_blank"
                rel="noopener noreferrer">
                Gabung Grup WhatsApp <span aria-hidden="true">→</span>
            </a>
            <a class="update-group-testimonials" href="https://t.me/velunaidd" target="_blank"
                rel="noopener noreferrer">
                Lihat Testimoni di Telegram <span aria-hidden="true">→</span>
            </a>
            <button class="update-group-dismiss" type="button" data-update-group-close>Nanti saja</button>
        </div>
    </dialog>
@endsection

@section('scripts')
    <script src="{{ asset('Welcome/site-loader.js') }}"></script>
    <script src="{{ asset('Welcome/music-player.js') }}"></script>
    <script src="{{ asset('Welcome/order-guide.js') }}"></script>
    <script src="{{ asset('Welcome/script.js') }}"></script>
@endsection
