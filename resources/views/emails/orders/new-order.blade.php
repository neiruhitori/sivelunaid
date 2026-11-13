<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pesanan baru {{ $order->order_number }}</title>
</head>
<body style="margin:0;padding:24px 12px;background:#f4f6f9;font-family:Arial,Helvetica,sans-serif;color:#344255;">
    <div style="max-width:680px;margin:0 auto;padding:28px;background:#fff;border:1px solid #e3e8ef;border-radius:12px;">
        <p style="margin:0 0 8px;color:#bd6584;font-size:12px;font-weight:bold;letter-spacing:1px;">VELUNA STORE</p>
        <h1 style="margin:0 0 8px;color:#365f8d;font-size:24px;">Pesanan baru masuk</h1>
        <p style="margin:0 0 22px;color:#68788a;line-height:1.6;">Ada pesanan yang perlu ditindaklanjuti. Periksa rincian di bawah, lalu perbarui statusnya.</p>

        <table role="presentation" style="width:100%;border-collapse:collapse;margin-bottom:22px;">
            <tr><td style="padding:8px 0;color:#78889a;width:150px;">Nomor pesanan</td><td style="padding:8px 0;font-weight:bold;">{{ $order->order_number }}</td></tr>
            <tr><td style="padding:8px 0;color:#78889a;">Tanggal</td><td style="padding:8px 0;">{{ $order->created_at?->format('d M Y, H:i') }}</td></tr>
            <tr><td style="padding:8px 0;color:#78889a;">Produk</td><td style="padding:8px 0;">{{ $order->product_name }} - {{ $order->group_name }}</td></tr>
            <tr><td style="padding:8px 0;color:#78889a;">Pembayaran</td><td style="padding:8px 0;">{{ $order->payment_method }}</td></tr>
            <tr><td style="padding:8px 0;color:#78889a;">Status</td><td style="padding:8px 0;">Menunggu Pembayaran</td></tr>
        </table>

        <h2 style="margin:0 0 10px;color:#405d7a;font-size:16px;">Rincian pesanan</h2>
        <table role="presentation" style="width:100%;border-collapse:collapse;margin-bottom:22px;">
            <thead><tr>
                <th align="left" style="padding:9px 7px;border-bottom:1px solid #e5eaf0;color:#78889a;font-size:12px;">Paket</th>
                <th align="center" style="padding:9px 7px;border-bottom:1px solid #e5eaf0;color:#78889a;font-size:12px;">Jumlah</th>
                <th align="right" style="padding:9px 7px;border-bottom:1px solid #e5eaf0;color:#78889a;font-size:12px;">Subtotal</th>
            </tr></thead>
            <tbody>
                @foreach ($items as $item)
                    <tr>
                        <td style="padding:10px 7px;border-bottom:1px solid #eef1f5;">{{ !empty($item['option_name']) ? $item['option_name'] . ' - ' : '' }}{{ $item['name'] }}</td>
                        <td align="center" style="padding:10px 7px;border-bottom:1px solid #eef1f5;">{{ $item['quantity'] }}</td>
                        <td align="right" style="padding:10px 7px;border-bottom:1px solid #eef1f5;">Rp{{ number_format((float) $item['subtotal'], 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot><tr>
                <td colspan="2" align="right" style="padding:12px 7px;font-weight:bold;">Total</td>
                <td align="right" style="padding:12px 7px;color:#bd6584;font-weight:bold;">Rp{{ number_format((float) $order->total, 0, ',', '.') }}</td>
            </tr></tfoot>
        </table>

        <h2 style="margin:0 0 10px;color:#405d7a;font-size:16px;">Data pembeli</h2>
        @if (count($customerFields))
            <table role="presentation" style="width:100%;border-collapse:collapse;margin-bottom:24px;">
                @foreach ($customerFields as $field)
                    <tr>
                        <td style="padding:8px 7px;border-bottom:1px solid #eef1f5;color:#78889a;width:40%;">{{ $field['label'] }}</td>
                        <td style="padding:8px 7px;border-bottom:1px solid #eef1f5;overflow-wrap:anywhere;">{{ $field['value'] }}</td>
                    </tr>
                @endforeach
            </table>
        @else
            <p style="margin:0 0 24px;color:#78889a;">Tidak ada data pembeli yang perlu ditampilkan.</p>
        @endif

        <p style="margin:0 0 12px;color:#68788a;font-size:13px;">Pilih tindakan untuk membuka halaman konfirmasi:</p>
        <div style="margin-bottom:12px;">
            <a href="{{ $completedUrl }}" style="display:inline-block;padding:12px 18px;border-radius:6px;background:#28a745;color:#fff;text-decoration:none;font-size:13px;font-weight:bold;">Tandai Selesai</a>
            <a href="{{ $cancelledUrl }}" style="display:inline-block;margin-left:7px;padding:12px 18px;border-radius:6px;background:#dc6b7e;color:#fff;text-decoration:none;font-size:13px;font-weight:bold;">Batalkan Pesanan</a>
        </div>
        <p style="margin:12px 0 0;color:#98a2ad;font-size:11px;line-height:1.5;">Tautan tindakan berlaku selama 14 hari. Status baru berubah setelah Anda mengonfirmasi tindakan pada halaman yang terbuka.</p>
    </div>
</body>
</html>
