@extends('welcome.layout')

@section('head')

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="Pilih paket {{ $productGroup->name }} di Veluna Store.">
        <title>{{ $productGroup->name }} - Veluna Store</title>
        <link rel="icon" type="image/png" href="{{ asset('Welcome/image/iconvelunabulat.png') }}">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&display=swap"
            rel="stylesheet">
        <link rel="stylesheet" href="{{ asset('Welcome/style.css') }}">
        <link rel="stylesheet" href="{{ asset('Welcome/roblox.css') }}">
    </head>
@endsection

@section('content')
    <main class="roblox-page product-order-page" data-product-order data-product-name="{{ $productGroup->product->name }}"
        data-group-name="{{ $productGroup->name }}">
        <div class="page-width">
            <nav class="breadcrumbs" aria-label="Breadcrumb">
                <a href="{{ route('welcome.index') }}">Beranda</a><span aria-hidden="true">/</span>
                <a href="{{ route('welcome.index') }}#produk">Produk</a><span aria-hidden="true">/</span>
                <span aria-current="page">{{ $productGroup->name }}</span>
            </nav>

            <section class="roblox-banner" aria-label="{{ $productGroup->name }}">
                <div class="roblox-banner-copy">
                    <p class="eyebrow">{{ $productGroup->product->category->name ?? 'PRODUK DIGITAL' }}</p>
                    <h1>{{ $productGroup->name }}</h1>
                    @if ($productGroup->banner_description)
                        <p>{{ $productGroup->banner_description }}</p>
                    @endif
                </div>
                <div class="roblox-banner-art">
                    <img class="roblox-symbol game-logo"
                        src="{{ $productGroup->product->image ? asset('storage/' . $productGroup->product->image) : asset('Welcome/image/iconvelunabulat.png') }}"
                        alt="{{ $productGroup->product->name }}">
                </div>
                <span class="banner-orbit orbit-one" aria-hidden="true"></span>
                <span class="banner-orbit orbit-two" aria-hidden="true"></span>
            </section>

            <section class="game-overview" aria-labelledby="game-title">
                <div class="game-cover"><img class="roblox-symbol game-logo"
                        src="{{ $productGroup->product->image ? asset('storage/' . $productGroup->product->image) : asset('Welcome/image/iconvelunabulat.png') }}"
                        alt=""></div>
                <div class="game-overview-copy">
                    <p class="eyebrow">{{ $productGroup->product->category->name ?? 'PRODUK' }}</p>
                    <h2 id="game-title">{{ $productGroup->product->name }} · {{ $productGroup->name }}</h2>
                    <p class="publisher">{{ $productGroup->product->description ?: 'Pilih paket yang tersedia.' }}</p>
                    <div class="service-points"><span><b aria-hidden="true">✓</b> Pilihan harga jelas</span><span><b
                                aria-hidden="true">♡</b> Mudah</span><span><b aria-hidden="true">✦</b> Cepat</span></div>
                </div>
            </section>

            <div class="purchase-layout">
                <section class="purchase-card" aria-labelledby="variant-heading">
                    <div class="step-heading"><span>1</span>
                        <h2 id="variant-heading">Pilih paket {{ $productGroup->name }}</h2>
                    </div>
                    <div class="nominal-content">
                        <p class="nominal-note">Pilih paket dan harga sesuai keinginan Anda.</p>
                        @if ($productGroup->variants->isNotEmpty())
                            <div class="nominal-grid" role="group" aria-label="Pilihan paket {{ $productGroup->name }}">
                                @foreach ($productGroup->variants as $variant)
                                    @php($price = $variant->discount_price ?? $variant->price)
                                    <button class="nominal-option" type="button" data-order-variant
                                        data-variant-id="{{ $variant->id }}" data-variant-name="{{ $variant->name }}"
                                        data-price="{{ $price }}" aria-pressed="false">
                                        <span class="robux-icon"
                                            aria-hidden="true">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($variant->name, 0, 1)) }}</span>
                                        <span class="nominal-details"><strong>{{ $variant->name }}</strong>
                                            @if ($variant->description)
                                                <small>{{ $variant->description }}</small>
                                            @endif
                                        </span>
                                        <b class="nominal-price">Rp{{ number_format((float) $price, 0, ',', '.') }}
                                            @if ($variant->discount_price !== null)
                                                <small
                                                    class="original-price">Rp{{ number_format((float) $variant->price, 0, ',', '.') }}</small>
                                            @endif
                                        </b>
                                    </button>
                                @endforeach
                            </div>
                        @else
                            <p class="catalog-empty">Belum ada variant aktif untuk pilihan ini.</p>
                        @endif

                        @if ($productGroup->fields->isNotEmpty())
                            <div class="step-heading product-fields-heading"><span>2</span>
                                <h2>Data yang diperlukan</h2>
                            </div>
                            <div class="account-fields-row product-fields-row">
                                @foreach ($productGroup->fields as $field)
                                    @if ($field->type === 'backup_code')
                                        <div class="account-field backup-code-field">
                                            <label>{{ $field->name }}</label>
                                            @for ($codeIndex = 1; $codeIndex <= 3; $codeIndex++)
                                                <input id="product-field-{{ $field->id }}-{{ $codeIndex }}" type="text"
                                                    name="{{ $field->field_key }}_{{ $codeIndex }}" data-order-field
                                                    data-field-label="{{ $field->name }} {{ $codeIndex }}"
                                                    placeholder="{{ $field->placeholder ?: 'Masukkan ' . $field->name . ' ' . $codeIndex }}"
                                                    autocomplete="off">
                                            @endfor
                                            <small>Opsional. Isi hingga tiga kode backup.</small>
                                        </div>
                                    @else
                                        @php($inputType = in_array($field->type, ['text', 'number', 'email', 'password'], true) ? $field->type : 'text')
                                        <div class="account-field">
                                            <label for="product-field-{{ $field->id }}">{{ $field->name }}@if ($field->is_required)
                                                    <span aria-hidden="true"> *</span>
                                                @endif
                                            </label>
                                            <input id="product-field-{{ $field->id }}" type="{{ $inputType }}"
                                                name="{{ $field->field_key }}" data-order-field
                                                data-field-label="{{ $field->name }}"
                                                placeholder="{{ $field->placeholder ?: $field->name }}"
                                                @if ($field->is_required) required @endif
                                                @if ($inputType === 'number') inputmode="decimal" @endif autocomplete="off">
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @endif

                        <fieldset class="payment-field product-payment-field">
                            <legend>3. Pilih pembayaran</legend>
                            <label><input type="radio" name="payment-method" value="QRIS" data-order-payment>
                                QRIS</label>
                            <label><input type="radio" name="payment-method" value="Transfer Bank" data-order-payment>
                                Transfer Bank</label>
                            <label><input type="radio" name="payment-method" value="E-Wallet" data-order-payment>
                                E-Wallet</label>
                        </fieldset>
                    </div>
                </section>

                <aside class="purchase-aside">
                    <section class="support-card"><span class="support-icon" aria-hidden="true">♡</span>
                        <div>
                            <h2>Butuh bantuan?</h2>
                            <p>Tim Veluna siap membantu.</p><a href="https://wa.me/6738386518" target="_blank"
                                rel="noopener noreferrer">Hubungi kami <span aria-hidden="true">→</span></a>
                        </div>
                    </section>
                    <section class="order-card" aria-labelledby="order-heading">
                        <h2 id="order-heading">Ringkasan pesanan</h2>
                        <div class="order-empty" data-order-empty><span class="empty-icon" aria-hidden="true">◇</span>
                            <p>Pilih paket dan lengkapi data pesanan untuk melihat ringkasan.</p>
                        </div>
                        <div class="order-details" data-order-details hidden>
                            <div><span>Produk</span><strong>{{ $productGroup->product->name }}</strong></div>
                            <div><span>Pilihan</span><strong>{{ $productGroup->name }}</strong></div>
                            <div><span>Paket</span><strong data-summary-variant>—</strong></div>
                            <div data-summary-fields></div>
                            <div><span>Pembayaran</span><strong data-summary-payment>—</strong></div>
                            <div class="order-total"><span>Total</span><strong data-summary-price>—</strong></div>
                        </div>
                        <button class="checkout-button" type="button" data-order-checkout disabled>Buat pesanan via
                            WhatsApp</button>
                        <p class="checkout-message" data-order-message role="status" aria-live="polite"></p>
                        <p class="demo-note">Detail pembayaran dilanjutkan melalui WhatsApp.</p>
                    </section>
                </aside>
            </div>

      <section class="description-card" aria-labelledby="about-product-title">
                <p class="eyebrow">TENTANG PRODUK</p>
                <h2 id="about-product-title">{{ $productGroup->product->name }}</h2>
        @if ($aboutItems->isNotEmpty())
          <ul class="product-about-list">
            @foreach ($aboutItems as $item)
              <li>{{ $item }}</li>
            @endforeach
          </ul>
                @endif
            </section>
        </div>
    </main>

    <dialog class="order-confirm-dialog" aria-labelledby="order-confirm-title" data-order-confirm>
        <div class="order-confirm-content">
            <p class="eyebrow">PERIKSA KEMBALI</p>
            <h2 id="order-confirm-title">Konfirmasi pesanan</h2>
            <p class="confirm-intro">Pastikan pilihan dan data pesanan sudah benar. WhatsApp akan dibuka untuk konfirmasi.
            </p>
            <dl class="confirm-summary" data-order-confirm-summary></dl>
            <div class="confirm-actions"><button class="confirm-cancel" type="button"
                    data-order-cancel>Kembali</button><button class="confirm-submit" type="button"
                    data-order-submit>Konfirmasi &amp; buka WhatsApp</button></div>
        </div>
    </dialog>
@endsection

@section('scripts')
    <script src="{{ asset('Welcome/site-loader.js') }}"></script>
    <script src="{{ asset('Welcome/music-player.js') }}"></script>
    <script src="{{ asset('Welcome/order-guide.js') }}"></script>
    <script src="{{ asset('Welcome/product-order.js') }}"></script>
@endsection
