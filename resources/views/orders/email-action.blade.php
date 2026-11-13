<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Konfirmasi Status Pesanan - Veluna Store</title>
</head>
<body style="margin:0;padding:24px 12px;background:#f4f6f9;font-family:Arial,Helvetica,sans-serif;color:#344255;">
    @php
        $targetLabel = $targetStatus === 'completed' ? 'Selesai' : 'Dibatalkan';
        $currentLabel = \App\Models\Order::STATUSES[$order->status] ?? $order->status;
    @endphp
    <main style="max-width:520px;margin:8vh auto;padding:30px;background:#fff;border:1px solid #e3e8ef;border-radius:12px;box-shadow:0 12px 32px rgba(52,79,107,.08);">
        <p style="margin:0 0 8px;color:#bd6584;font-size:12px;font-weight:bold;letter-spacing:1px;">VELUNA STORE</p>
        @if ($updated)
            <h1 style="margin:0 0 10px;color:#365f8d;font-size:23px;">Status pesanan diperbarui</h1>
            <p style="margin:0;color:#68788a;line-height:1.7;">Pesanan <strong>{{ $order->order_number }}</strong> sekarang berstatus <strong>{{ $currentLabel }}</strong>.</p>
        @elseif ($alreadyProcessed)
            <h1 style="margin:0 0 10px;color:#365f8d;font-size:23px;">Pesanan sudah diproses</h1>
            <p style="margin:0;color:#68788a;line-height:1.7;">Status pesanan <strong>{{ $order->order_number }}</strong> sudah <strong>{{ $currentLabel }}</strong>, jadi tidak diubah.</p>
        @else
            <h1 style="margin:0 0 10px;color:#365f8d;font-size:23px;">Konfirmasi tindakan pesanan</h1>
            <p style="margin:0 0 18px;color:#68788a;line-height:1.7;">Anda akan mengubah status pesanan <strong>{{ $order->order_number }}</strong> menjadi <strong>{{ $targetLabel }}</strong>.</p>
            <div style="margin:0 0 22px;padding:14px;border-radius:8px;background:#f8fafc;line-height:1.8;font-size:14px;">
                <div><span style="color:#78889a;">Produk:</span> {{ $order->product_name }}</div>
                <div><span style="color:#78889a;">Pilihan:</span> {{ $order->group_name }}</div>
                <div><span style="color:#78889a;">Rincian:</span> {{ $order->variant_name }}</div>
                <div><span style="color:#78889a;">Total:</span> Rp{{ number_format((float) $order->total, 0, ',', '.') }}</div>
            </div>
            <form method="POST" action="{{ request()->fullUrl() }}">
                @csrf
                <button type="submit" style="width:100%;min-height:46px;border:0;border-radius:7px;background:{{ $targetStatus === 'completed' ? '#28a745' : '#dc6b7e' }};color:#fff;font-size:14px;font-weight:bold;cursor:pointer;">Konfirmasi {{ $targetLabel }}</button>
            </form>
            <p style="margin:14px 0 0;color:#98a2ad;font-size:11px;line-height:1.5;">Tindakan ini hanya dapat dilakukan selama pesanan masih berstatus Menunggu Pembayaran.</p>
        @endif
    </main>
</body>
</html>
