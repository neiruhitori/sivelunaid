@if ($orders->isEmpty())
    <div class="order-history-empty">
        <span aria-hidden="true">&#9825;</span>
        <h2>{{ $search !== '' ? 'Pesanan tidak ditemukan' : 'Belum ada pesanan selesai' }}</h2>
        <p>{{ $search !== '' ? 'Coba gunakan kata kunci lain.' : 'Pesanan akan tampil di sini setelah admin menyelesaikannya.' }}</p>
        <a class="button button-dark" href="{{ route('welcome.index') }}#produk">Lihat produk</a>
    </div>
@else
    <p class="order-history-count">Menampilkan {{ $orders->firstItem() }}-{{ $orders->lastItem() }} dari {{ $orders->total() }} pesanan{{ $search !== '' ? ' yang cocok' : '' }}</p>
    <div class="order-history-list">
        @foreach ($orders as $order)
            <article class="order-history-card">
                <div class="order-history-card-top">
                    <span class="order-history-number">{{ $order->order_number }}</span>
                    <span class="order-history-status">{{ \App\Models\Order::STATUSES[$order->status] ?? 'Selesai' }}</span>
                </div>
                <div class="order-history-card-content">
                    <div>
                        <h2>{{ $order->product_name }}</h2>
                        <p>{{ $order->group_name }}@if($order->option_name) &middot; {{ $order->option_name }}@endif</p>
                        @if (!empty($order->items))
                            <ul class="order-history-items">
                                @foreach ($order->items as $item)
                                    <li>{{ $item['option_name'] ? $item['option_name'] . ' · ' : '' }}{{ $item['name'] }} × {{ $item['quantity'] }}</li>
                                @endforeach
                            </ul>
                        @else
                            <p>{{ $order->variant_name }}</p>
                        @endif
                    </div>
                    <strong>Rp{{ number_format((float) $order->total, 0, ',', '.') }}</strong>
                </div>
                <time datetime="{{ $order->completed_at?->toIso8601String() }}">Selesai {{ $order->completed_at?->format('d M Y, H:i') }}</time>
                @if (!empty($order->public_customer_data))
                    <div class="order-history-customer">
                        <strong>Data pembeli</strong>
                        <div class="order-history-customer-fields">
                            @foreach ($order->public_customer_data as $field)
                                <div><span>{{ $field['label'] }}</span><b>{{ $field['value'] }}</b></div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </article>
        @endforeach
    </div>
    <div class="order-history-pagination">{{ $orders->links('pagination::bootstrap-4') }}</div>
@endif
