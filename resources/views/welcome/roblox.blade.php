@extends('welcome.layout')

@section('head')
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Pilih nominal Robux Visend di Veluna Store.">
  <title>Robux Visend - Veluna Store</title>
  <link rel="icon" type="image/png" href="{{ asset('Welcome/image/iconvelunabulat.png') }}">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('Welcome/style.css') }}">
  <link rel="stylesheet" href="{{ asset('Welcome/roblox.css') }}">
</head>
@endsection

@section('body-attributes') data-order-method="Visend" @endsection

@section('content')
  <main class="roblox-page">
    <div class="page-width">
      <nav class="breadcrumbs" aria-label="Breadcrumb">
        <a href="{{ route('welcome.index') }}">Beranda</a><span aria-hidden="true">/</span>
        <a href="{{ route('welcome.index') }}#produk">Game</a><span aria-hidden="true">/</span>
        <span aria-current="page">Visend</span>
      </nav>

      <section class="roblox-banner" aria-label="Roblox voucher">
        <div class="roblox-banner-copy">
          <p class="eyebrow">TOP UP ROBLOX</p>
          <h1>VISEND</h1>
          <p>Pilih paket Robux Visend yang sesuai. Membutuhkan Username dan Display Name Roblox. Tanpa menggunakan kata sandi atau kode OTP.</p>
        </div>
        <div class="roblox-banner-art">
          <img class="roblox-symbol game-logo" src="{{ asset('Welcome/image/iconroblox.png') }}" alt="Ikon Roblox">
        </div>
        <span class="banner-orbit orbit-one" aria-hidden="true"></span>
        <span class="banner-orbit orbit-two" aria-hidden="true"></span>
      </section>

      <section class="game-overview" aria-labelledby="game-title">
        <div class="game-cover">
          <img class="roblox-symbol game-logo" src="{{ asset('Welcome/image/iconroblox.png') }}" alt="Ikon Roblox">
        </div>
        <div class="game-overview-copy">
          <p class="eyebrow">METODE ROBUX</p>
          <h2 id="game-title">Robux Visend</h2>
          <p class="publisher">Roblox · Pilihan nominal sekali beli</p>
          <div class="service-points">
            <span><b aria-hidden="true">✓</b> Tanpa kata sandi</span>
            <span><b aria-hidden="true">♡</b> Mudah</span>
            <span><b aria-hidden="true">✦</b> Cepat</span>
          </div>
        </div>
      </section>

      <nav class="method-switch" aria-label="Metode top up Roblox">
        <a class="active" href="{{ route('welcome.roblox') }}" aria-current="page">Robux Visend</a>
        <a href="{{ route('welcome.roblox-vilog') }}">Robux Vilog</a>
        <a href="{{ route('welcome.roblox-gig') }}">GIG <span>(Gift in Game)</span></a>
      </nav>

      <div class="purchase-layout">
        <section class="purchase-card" aria-labelledby="nominal-heading">
          <div class="step-heading">
            <span>1</span>
            <h2 id="nominal-heading">1. Pilih nominal Robux Visend</h2>
          </div>
          <div class="nominal-content">
            <p class="nominal-note">Pilih paket dan harga berikut sesuaikan keinginan Anda. Pastikan username Roblox yang dimasukkan benar dan sesuai dengan akun.</p>
            <div class="nominal-grid" role="group" aria-label="Pilihan nominal Robux">
              <button class="nominal-option" type="button" data-nominal="10" data-price="1600" aria-pressed="false">
                <span class="robux-icon" aria-hidden="true">R</span><span class="nominal-details"><strong>10 Robux</strong><small>Visend</small></span><b class="nominal-price">Rp 1.600</b>
              </button>
              <button class="nominal-option" type="button" data-nominal="20" data-price="3200" aria-pressed="false">
                <span class="robux-icon" aria-hidden="true">R</span><span class="nominal-details"><strong>20 Robux</strong><small>Visend</small></span><b class="nominal-price">Rp 3.200</b>
              </button>
              <button class="nominal-option" type="button" data-nominal="30" data-price="4800" aria-pressed="false">
                <span class="robux-icon" aria-hidden="true">R</span><span class="nominal-details"><strong>30 Robux</strong><small>Visend</small></span><b class="nominal-price">Rp 4.800</b>
              </button>
              <button class="nominal-option" type="button" data-nominal="40" data-price="6400" aria-pressed="false">
                <span class="robux-icon" aria-hidden="true">R</span><span class="nominal-details"><strong>40 Robux</strong><small>Visend</small></span><b class="nominal-price">Rp 6.400</b>
              </button>
              <button class="nominal-option" type="button" data-nominal="50" data-price="8000" aria-pressed="false">
                <span class="robux-icon" aria-hidden="true">R</span><span class="nominal-details"><strong>50 Robux</strong><small>Visend</small></span><b class="nominal-price">Rp 8.000</b>
              </button>
              <button class="nominal-option" type="button" data-nominal="60" data-price="9600" aria-pressed="false">
                <span class="robux-icon" aria-hidden="true">R</span><span class="nominal-details"><strong>60 Robux</strong><small>Visend</small></span><b class="nominal-price">Rp 9.600</b>
              </button>
              <button class="nominal-option" type="button" data-nominal="70" data-price="11200" aria-pressed="false">
                <span class="robux-icon" aria-hidden="true">R</span><span class="nominal-details"><strong>70 Robux</strong><small>Visend</small></span><b class="nominal-price">Rp 11.200</b>
              </button>
              <button class="nominal-option" type="button" data-nominal="80" data-price="12800" aria-pressed="false">
                <span class="robux-icon" aria-hidden="true">R</span><span class="nominal-details"><strong>80 Robux</strong><small>Visend</small></span><b class="nominal-price">Rp 12.800</b>
              </button>
              <button class="nominal-option" type="button" data-nominal="90" data-price="14400" aria-pressed="false">
                <span class="robux-icon" aria-hidden="true">R</span><span class="nominal-details"><strong>90 Robux</strong><small>Visend</small></span><b class="nominal-price">Rp 14.400</b>
              </button>
              <button class="nominal-option" type="button" data-nominal="100" data-price="16000" aria-pressed="false">
                <span class="robux-icon" aria-hidden="true">R</span><span class="nominal-details"><strong>100 Robux</strong><small>Visend</small></span><b class="nominal-price">Rp 16.000</b>
              </button>
              <button class="nominal-option" type="button" data-nominal="200" data-price="32000" aria-pressed="false">
                <span class="robux-icon" aria-hidden="true">R</span><span class="nominal-details"><strong>200 Robux</strong><small>Visend</small></span><b class="nominal-price">Rp 32.000</b>
              </button>
              <button class="nominal-option" type="button" data-nominal="300" data-price="48000" aria-pressed="false">
                <span class="robux-icon" aria-hidden="true">R</span><span class="nominal-details"><strong>300 Robux</strong><small>Visend</small></span><b class="nominal-price">Rp 48.000</b>
              </button>
              <button class="nominal-option" type="button" data-nominal="400" data-price="64000" aria-pressed="false">
                <span class="robux-icon" aria-hidden="true">R</span><span class="nominal-details"><strong>400 Robux</strong><small>Visend</small></span><b class="nominal-price">Rp 64.000</b>
              </button>
              <button class="nominal-option" type="button" data-nominal="500" data-price="80000" aria-pressed="false">
                <span class="robux-icon" aria-hidden="true">R</span><span class="nominal-details"><strong>500 Robux</strong><small>Visend</small></span><b class="nominal-price">Rp 80.000</b>
              </button>
              <button class="nominal-option" type="button" data-nominal="600" data-price="96000" aria-pressed="false">
                <span class="robux-icon" aria-hidden="true">R</span><span class="nominal-details"><strong>600 Robux</strong><small>Visend</small></span><b class="nominal-price">Rp 96.000</b>
              </button>
              <button class="nominal-option" type="button" data-nominal="700" data-price="112000" aria-pressed="false">
                <span class="robux-icon" aria-hidden="true">R</span><span class="nominal-details"><strong>700 Robux</strong><small>Visend</small></span><b class="nominal-price">Rp 112.000</b>
              </button>
              <button class="nominal-option" type="button" data-nominal="800" data-price="128000" aria-pressed="false">
                <span class="robux-icon" aria-hidden="true">R</span><span class="nominal-details"><strong>800 Robux</strong><small>Visend</small></span><b class="nominal-price">Rp 128.000</b>
              </button>
              <button class="nominal-option" type="button" data-nominal="900" data-price="144000" aria-pressed="false">
                <span class="robux-icon" aria-hidden="true">R</span><span class="nominal-details"><strong>900 Robux</strong><small>Visend</small></span><b class="nominal-price">Rp 144.000</b>
              </button>
              <button class="nominal-option" type="button" data-nominal="1000" data-price="160000" aria-pressed="false">
                <span class="robux-icon" aria-hidden="true">R</span><span class="nominal-details"><strong>1.000 Robux</strong><small>Visend</small></span><b class="nominal-price">Rp 160.000</b>
              </button>
            </div>
            <div class="account-fields-row">
              <div class="account-field">
                <label for="roblox-username">Username Roblox</label>
                <input id="roblox-username" type="text" autocomplete="off" placeholder="Masukkan nama pengguna Roblox">
                <small>Masukkan username Roblox; tanda @ ditambahkan otomatis.</small>
              </div>
              <div class="account-field">
                <label for="roblox-displayname">Displayname Roblox</label>
                <input id="roblox-displayname" type="text" autocomplete="off" placeholder="Masukkan nama tampilan Roblox">
                <small>Masukkan Nama Tampilan.</small>
              </div>
            </div>
            <fieldset class="payment-field">
              <legend>2. Pilih pembayaran</legend>
              <label><input type="radio" name="payment-method" value="QRIS" data-payment-option> QRIS</label>
              <label><input type="radio" name="payment-method" value="Transfer Bank" data-payment-option> Transfer Bank</label>
              <label><input type="radio" name="payment-method" value="E-Wallet" data-payment-option> E-Wallet</label>
            </fieldset>
            <section class="payment-detail" data-payment-panel="QRIS" hidden>
              <h3>Pembayaran QRIS</h3>
              <figure class="qris-image"><img src="{{ asset('Welcome/image/qris.jpeg') }}" alt="Kode QRIS untuk pembayaran Veluna"><figcaption>Pindai kode QRIS ini untuk membayar. Setelah itu, konfirmasi pembayaran melalui WhatsApp.</figcaption></figure>
            </section>
            <section class="payment-detail" data-payment-panel="Transfer Bank" hidden>
              <h3>Transfer ke SeaBank</h3>
              <div class="provider-options">
                <label><input type="radio" name="bank-provider" value="SeaBank" data-provider-option><span><strong>SeaBank</strong><small>No. rekening: 901318468036</small></span></label>
              </div>
              <p class="payment-next-step">Setelah memilih SeaBank, lanjutkan konfirmasi dan pembayaran melalui WhatsApp.</p>
            </section>
            <section class="payment-detail" data-payment-panel="E-Wallet" hidden>
              <h3>Pilih e-wallet tujuan</h3>
              <div class="provider-options wallet-options">
                <label><input type="radio" name="wallet-provider" value="DANA" data-provider-option><span><strong>DANA</strong><small>+62 852-3649-6518</small></span></label>
                <label><input type="radio" name="wallet-provider" value="GoPay" data-provider-option><span><strong>GoPay</strong><small>+62 852-3649-6518</small></span></label>
                <label><input type="radio" name="wallet-provider" value="SPay" data-provider-option><span><strong>SPay</strong><small>+62 852-3649-6518</small></span></label>
              </div>
              <p class="payment-next-step">Setelah memilih e-wallet, lanjutkan konfirmasi dan pembayaran melalui WhatsApp.</p>
            </section>
          </div>
        </section>

        <aside class="purchase-aside">
          <section class="rating-card" aria-label="Informasi layanan">
            <p class="aside-label">Informasi Visend</p>
            <div class="visend-info"><span aria-hidden="true">✓</span><strong>Privasi akun diutamakan dan akun wajib berusia 18 tahun atau lebih</strong></div>
            <p class="rating-caption">Veluna tidak meminta kata sandi maupun kode OTP.</p>
          </section>

          <section class="support-card">
            <span class="support-icon" aria-hidden="true">♡</span>
            <div><h2>Butuh bantuan?</h2><p>Tim Veluna siap membantu.</p><a href="https://wa.me/6738386518" target="_blank" rel="noopener noreferrer">Hubungi kami <span aria-hidden="true">→</span></a></div>
          </section>

          <section class="order-card" aria-labelledby="order-heading">
            <h2 id="order-heading">Ringkasan pesanan</h2>
            <div class="order-empty" data-order-empty>
              <span class="empty-icon" aria-hidden="true">◇</span>
              <p>Pilih nominal Robux untuk melihat ringkasan.</p>
            </div>
            <div class="order-details" data-order-details hidden>
              <div><span>Metode</span><strong>Visend</strong></div>
              <div><span>Nominal</span><strong data-selected-nominal>—</strong></div>
              <div><span>Username</span><strong data-selected-username>—</strong></div>
              <div><span>Displayname</span><strong data-selected-displayname>—</strong></div>
              <div><span>Pembayaran</span><strong data-selected-payment>—</strong></div>
              <div class="order-total"><span>Total</span><strong data-selected-price>—</strong></div>
            </div>
            <button class="checkout-button" type="button" data-checkout disabled>Buat pesanan via WhatsApp</button>
            <p class="checkout-message" data-checkout-message role="status" aria-live="polite"></p>
            <p class="demo-note">Detail pembayaran dilanjutkan melalui WhatsApp.</p>
          </section>
        </aside>
      </div>

      <section class="description-card">
        <p class="eyebrow">TENTANG PRODUK</p>
        <h2>Robux Visend</h2>
        <p>1. WAJIB menyalakan V2L (verifikasi 2 langkah) bagi yg membeli lebih dari 500 robux, agar limit penerima robux naik.<br>
          2. Akun baru atau kurang dari 2 bulan hanya bisa menerima robux 500r/hari dan 1000r/bulan. WAJIB mengaktifkan V2L dari awal biar limitnya naik.<br>
          3. WAJIB menautkan akun roblox parents yg sudah TERVERIFIKASI 18+ bagi penerima yg robloxnya terverifikasi kurang dari 18 tahun.<br>
          </p>
      </section>
    </div>
  </main>

  <dialog class="order-confirm-dialog" aria-labelledby="order-confirm-title" data-order-confirm-dialog>
    <div class="order-confirm-content">
      <p class="eyebrow">PERIKSA KEMBALI</p>
      <h2 id="order-confirm-title">Konfirmasi pesanan</h2>
      <p class="confirm-intro">Pastikan detail pesanan sudah benar. WhatsApp akan dibuka dengan pilihan pembayaran yang sudah disiapkan.</p>
      <dl class="confirm-summary" data-confirm-summary></dl>
      <p class="confirm-security">Pembayaran dilanjutkan melalui WhatsApp. Pastikan data kamu benar.</p>
      <div class="confirm-actions">
        <button class="confirm-cancel" type="button" data-confirm-cancel>Kembali</button>
        <button class="confirm-submit" type="button" data-confirm-submit>Konfirmasi & buka WhatsApp</button>
      </div>
    </div>
  </dialog>
@endsection

@section('scripts')
<script src="{{ asset('Welcome/site-loader.js') }}"></script>
  <script src="{{ asset('Welcome/music-player.js') }}"></script>
  <script src="{{ asset('Welcome/order-guide.js') }}"></script>
  <script src="{{ asset('Welcome/roblox.js') }}"></script>
@endsection
