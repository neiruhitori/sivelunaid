<x-app-layout>
    <x-slot name="header">
        <div class="d-flex align-items-center justify-content-between flex-wrap">
            <div>
                <h1 class="m-0 text-dark">Pesanan</h1>
                <small class="text-muted">Periksa pesanan yang masuk melalui WhatsApp dan perbarui statusnya.</small>
            </div>
            <ol class="breadcrumb float-sm-right mb-0 bg-transparent">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Pesanan</li>
            </ol>
        </div>
    </x-slot>

    <div class="card card-primary card-outline">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-receipt mr-2"></i>Daftar Pesanan</h3>
        </div>
        <div class="card-body">
            <div class="alert alert-light border small">
                Pesanan baru berstatus <strong>Menunggu Pembayaran</strong>. Ubah menjadi <strong>Selesai</strong> setelah
                pesanan dipenuhi; hanya pesanan selesai yang tampil di riwayat publik.
            </div>
            <div class="table-responsive">
                <table id="orders-table" class="table table-bordered table-hover table-striped w-100">
                    <thead><tr>
                        <th>No.</th><th>Nomor Pesanan</th><th>Tanggal</th><th>Produk</th><th>Pilihan</th>
                        <th>Subpilihan</th><th>Paket</th><th>Total</th><th>Pembayaran</th><th>Status</th><th>Aksi</th>
                    </tr></thead>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="order-detail-modal" tabindex="-1" role="dialog" aria-labelledby="order-detail-title" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <div><h5 class="modal-title" id="order-detail-title">Detail Pesanan</h5><small class="text-muted" id="order-detail-number"></small></div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Tutup"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <dl class="row mb-0" id="order-detail-list"></dl>
                    <hr>
                    <h6>Data dari pembeli</h6>
                    <div class="table-responsive"><table class="table table-sm table-bordered mb-0"><tbody id="order-customer-data"></tbody></table></div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button></div>
            </div>
        </div>
    </div>

    @push('styles')
        <link rel="stylesheet" href="{{ asset('AdminLTE-3.2.0/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
        <link rel="stylesheet" href="{{ asset('AdminLTE-3.2.0/plugins/sweetalert2/sweetalert2.min.css') }}">
        <link rel="stylesheet" href="{{ asset('AdminLTE-3.2.0/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}">
        <style>
            #orders-table td { vertical-align: middle; }
            #orders-table .order-status-select { min-width: 160px; }
            #order-detail-list dt { color: #6c757d; font-weight: 600; }
            #order-detail-list dd { overflow-wrap: anywhere; }
        </style>
    @endpush

    @push('scripts')
        <script src="{{ asset('AdminLTE-3.2.0/plugins/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('AdminLTE-3.2.0/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('AdminLTE-3.2.0/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
        <script>
            $(function () {
                const table = $('#orders-table').DataTable({
                    processing: true,
                    serverSide: true,
                    autoWidth: false,
                    ajax: "{{ route('admin.orders.data') }}",
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                        { data: 'order_number', name: 'order_number' },
                        { data: 'created_at', name: 'created_at' },
                        { data: 'product_name', name: 'product_name' },
                        { data: 'group_name', name: 'group_name' },
                        { data: 'option_name', name: 'option_name', defaultContent: '-' },
                        { data: 'variant_name', name: 'variant_name' },
                        { data: 'total', name: 'total', searchable: false },
                        { data: 'payment_method', name: 'payment_method' },
                        { data: 'status_control', name: 'status', orderable: false, searchable: false },
                        { data: 'actions', name: 'actions', orderable: false, searchable: false },
                    ],
                    order: [[2, 'desc']],
                    language: {
                        processing: 'Memuat...', search: 'Cari:', lengthMenu: 'Tampilkan _MENU_ pesanan',
                        info: 'Menampilkan _START_–_END_ dari _TOTAL_ pesanan', infoEmpty: 'Belum ada pesanan',
                        zeroRecords: 'Pesanan tidak ditemukan', emptyTable: 'Belum ada pesanan masuk',
                        paginate: { first: 'Pertama', last: 'Terakhir', next: 'Berikutnya', previous: 'Sebelumnya' },
                    },
                });

                $('#orders-table').on('change', '[data-order-status]', function () {
                    const select = $(this);
                    const oldStatus = select.attr('data-order-status');
                    const newStatus = select.val();
                    const order = table.row(select.closest('tr')).data();
                    const labels = @json($statuses);
                    if (!order || newStatus === oldStatus) return;

                    Swal.fire({
                        icon: newStatus === 'completed' ? 'question' : 'warning',
                        title: 'Ubah status pesanan?',
                        text: `${order.order_number} akan diubah menjadi ${labels[newStatus]}.`,
                        showCancelButton: true,
                        confirmButtonText: 'Ya, ubah status',
                        cancelButtonText: 'Batal',
                    }).then((result) => {
                        if (!result.isConfirmed) {
                            select.val(oldStatus);
                            return;
                        }

                        $.ajax({
                            url: "{{ route('admin.orders.status', ':id') }}".replace(':id', order.id),
                            method: 'POST',
                            data: { _token: $('meta[name="csrf-token"]').attr('content'), _method: 'PATCH', status: newStatus },
                            headers: { Accept: 'application/json' },
                        }).done((response) => {
                            table.ajax.reload(null, false);
                            Swal.fire({ icon: 'success', title: 'Berhasil', text: response.message, timer: 1500, showConfirmButton: false });
                        }).fail((xhr) => {
                            select.val(oldStatus);
                            Swal.fire({ icon: 'error', title: 'Gagal', text: xhr.responseJSON?.message || 'Status pesanan gagal diperbarui.' });
                        });
                    });
                });

                $('#orders-table').on('click', '[data-order-detail]', function () {
                    const id = $(this).attr('data-order-detail');
                    $.get("{{ route('admin.orders.show', ':id') }}".replace(':id', id)).done((order) => {
                        $('#order-detail-number').text(order.order_number);
                        const rows = [
                            ['Tanggal', new Date(order.created_at).toLocaleString('id-ID')],
                            ['Produk', order.product_name], ['Pilihan', order.group_name],
                            ['Subpilihan', order.option_name || '-'], ['Paket', order.variant_name],
                            ['Total', new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(Number(order.total))],
                            ['Pembayaran', order.payment_method], ['Status', labelsForStatus(order.status)],
                        ];
                        const details = $('#order-detail-list').empty();
                        rows.forEach(([label, value]) => {
                            details.append($('<dt class="col-sm-4">').text(label));
                            details.append($('<dd class="col-sm-8">').text(value));
                        });
                        const dataRows = $('#order-customer-data').empty();
                        (order.customer_data || []).forEach((field) => {
                            const row = $('<tr>');
                            row.append($('<th scope="row" class="w-50">').text(field.label));
                            row.append($('<td>').text(field.value));
                            dataRows.append(row);
                        });
                        if (!order.customer_data?.length) dataRows.append($('<tr>').append($('<td colspan="2" class="text-muted">').text('Tidak ada data tambahan.')));
                        $('#order-detail-modal').modal('show');
                    }).fail(() => Swal.fire({ icon: 'error', title: 'Gagal', text: 'Detail pesanan gagal dimuat.' }));
                });

                function labelsForStatus(status) {
                    return @json($statuses)[status] || status;
                }
            });
        </script>
    @endpush
</x-app-layout>
