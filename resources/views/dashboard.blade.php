<x-app-layout>
    <x-slot name="header">
        <div class="d-flex flex-wrap align-items-center justify-content-between">
            <div>
                <h1 class="m-0 text-dark">Dashboard</h1>
                <small class="text-muted">Ringkasan pengelolaan katalog VelunaID</small>
            </div>
            <ol class="breadcrumb float-sm-right mb-0 bg-transparent">
                <li class="breadcrumb-item active">Dashboard</li>
            </ol>
        </div>
    </x-slot>

    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('status') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Tutup"><span aria-hidden="true">&times;</span></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner"><h3>{{ $categoryCount }}</h3><p>Category</p></div>
                <div class="icon"><i class="fas fa-th-large"></i></div>
                <a href="{{ route('admin.categories.index') }}" class="small-box-footer">Kelola kategori <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner"><h3>{{ $productCount }}</h3><p>Product</p></div>
                <div class="icon"><i class="fas fa-box"></i></div>
                <a href="{{ route('admin.products.index') }}" class="small-box-footer">Kelola produk <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner"><h3>{{ $productGroupCount }}</h3><p>Product Group</p></div>
                <div class="icon"><i class="fas fa-layer-group"></i></div>
                <a href="{{ route('admin.product-groups.index') }}" class="small-box-footer">Kelola grup <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner"><h3>{{ $productVariantCount }}</h3><p>Product Variant</p></div>
                <div class="icon"><i class="fas fa-boxes"></i></div>
                <a href="{{ route('admin.product-variants.index') }}" class="small-box-footer">Kelola varian <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-receipt mr-2"></i>Pesanan</h3>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-5 mb-3 mb-md-0">
                            <div class="order-summary-metric">
                                <span class="order-summary-icon bg-light text-primary"><i class="fas fa-receipt"></i></span>
                                <div><span class="text-muted">Total pesanan</span><strong>{{ number_format($orderCount) }}</strong></div>
                            </div>
                        </div>
                        <div class="col-md-5 mb-3 mb-md-0">
                            <div class="order-summary-metric">
                                <span class="order-summary-icon bg-warning text-white"><i class="fas fa-hourglass-half"></i></span>
                                <div><span class="text-muted">Perlu diproses <small>(Menunggu Pembayaran)</small></span><strong>{{ number_format($pendingOrderCount) }}</strong></div>
                            </div>
                        </div>
                        <div class="col-md-2 text-md-right">
                            <button type="button" class="btn btn-primary btn-block" data-toggle="modal" data-target="#pending-orders-modal">Kelola Pesanan</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="pending-orders-modal" tabindex="-1" role="dialog" aria-labelledby="pending-orders-title" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title" id="pending-orders-title">Pesanan Menunggu Pembayaran</h5>
                        <small class="text-muted"><span data-pending-total>{{ number_format($pendingOrderCount) }}</span> pesanan perlu diperiksa</small>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Tutup"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-sm mb-0">
                            <thead class="thead-light"><tr><th>Nomor / Tanggal</th><th>Produk</th><th>Total</th><th>Pembayaran</th><th>Perbarui Status</th><th>Aksi</th></tr></thead>
                            <tbody data-pending-rows><tr><td colspan="6" class="text-center text-muted py-4">Memuat pesanan...</td></tr></tbody>
                        </table>
                    </div>
                    <div class="pending-orders-pagination d-flex align-items-center justify-content-between mt-3">
                        <small class="text-muted" data-pending-page-info></small>
                        <div>
                            <button type="button" class="btn btn-sm btn-outline-secondary mr-1" data-pending-prev disabled>Sebelumnya</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" data-pending-next disabled>Berikutnya</button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer"><a href="{{ route('admin.orders.index') }}" class="btn btn-outline-primary mr-auto">Buka semua pesanan</a><button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card {{ $storefrontOpen ? 'card-success' : 'card-danger' }}">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-store mr-2"></i>Status Website</h3>
                    <div class="card-tools">
                        <span class="badge {{ $storefrontOpen ? 'badge-success' : 'badge-danger' }}">
                            {{ $storefrontOpen ? 'OPEN' : 'CLOSED' }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md">
                            <strong>{{ $storefrontOpen ? 'Website sedang dibuka.' : 'Website sedang ditutup.' }}</strong>
                            <div class="text-muted">{{ $storefrontOpen ? 'Pengunjung dapat membuka semua halaman Welcome.' : 'Semua halaman Welcome dikunci untuk pengunjung.' }}</div>
                        </div>
                        <div class="col-md-auto mt-3 mt-md-0 text-md-right">
                            <form method="POST" action="{{ route('admin.storefront.status') }}" class="m-0">
                                @csrf
                                <input type="hidden" name="is_open" value="{{ $storefrontOpen ? 0 : 1 }}">
                                <button type="submit" class="btn btn-lg {{ $storefrontOpen ? 'btn-danger' : 'btn-success' }}">
                                    <i class="fas {{ $storefrontOpen ? 'fa-lock' : 'fa-lock-open' }} mr-1"></i>
                                    {{ $storefrontOpen ? 'Close Website' : 'Open Website' }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .small-box { min-height: 134px; }
            .order-summary-metric { display: flex; align-items: center; gap: 14px; }
            .order-summary-icon { display: grid; place-items: center; flex: 0 0 48px; width: 48px; height: 48px; border-radius: 12px; font-size: 18px; }
            .order-summary-metric span:not(.order-summary-icon) { display: block; font-size: 13px; }
            .order-summary-metric strong { display: block; margin-top: 2px; color: #343a40; font-size: 24px; line-height: 1.1; }
            #pending-orders-modal .modal-dialog { width: calc(100% - 1rem); max-width: 1140px; max-height: calc(100vh - 1rem); margin: .5rem auto; }
            #pending-orders-modal .modal-content { max-height: calc(100vh - 1rem); }
            #pending-orders-modal .modal-body { min-height: 220px; overflow-y: auto; }
            #pending-orders-modal .table-responsive { max-height: 62vh; overflow: auto; }
            #pending-orders-modal table { min-width: 950px; table-layout: fixed; }
            #pending-orders-modal th:nth-child(1) { width: 18%; }
            #pending-orders-modal th:nth-child(2) { width: 26%; }
            #pending-orders-modal th:nth-child(3) { width: 12%; }
            #pending-orders-modal th:nth-child(4) { width: 16%; }
            #pending-orders-modal th:nth-child(5) { width: 16%; }
            #pending-orders-modal th:nth-child(6) { width: 12%; }
            #pending-orders-modal td { vertical-align: middle; }
            #pending-orders-modal td { overflow-wrap: anywhere; }
            #pending-orders-modal .pending-customer-details > td { padding: 8px 14px; }
            #pending-orders-modal .pending-customer-details th { padding-left: 0; font-weight: 600; }
            #pending-orders-modal .pending-status-select { min-width: 130px; }
            #pending-orders-modal .pending-orders-pagination { position: sticky; bottom: -1rem; padding: 10px 0 0; background: #fff; }
            @media (max-width: 575.98px) {
                #pending-orders-modal .modal-header, #pending-orders-modal .modal-footer { padding: .75rem; }
                #pending-orders-modal .modal-footer { flex-wrap: wrap; }
                #pending-orders-modal .modal-footer .mr-auto { margin-right: 0 !important; width: 100%; }
                #pending-orders-modal .pending-status-select { min-width: 125px; }
                #pending-orders-modal .pending-orders-pagination { align-items: flex-start !important; flex-direction: column; gap: 8px; }
            }
            @media (max-width: 575.98px) { .order-summary-metric span:not(.order-summary-icon) { font-size: 12px; } }
        </style>
    @endpush

    @push('scripts')
        <script>
            $(function () {
                const modal = $('#pending-orders-modal');
                const rows = modal.find('[data-pending-rows]');
                let currentPage = 1;

                const formatMoney = (amount) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(Number(amount));
                const setEmpty = (message) => rows.empty().append($('<tr>').append($('<td colspan="6" class="text-center text-muted py-4">').text(message)));

                const loadPendingOrders = (page = 1) => {
                    currentPage = page;
                    setEmpty('Memuat pesanan...');
                    $.get("{{ route('admin.orders.pending') }}", { page }).done((response) => {
                        rows.empty();
                        $('[data-pending-total]').text(new Intl.NumberFormat('id-ID').format(response.total));
                        $('[data-pending-page-info]').text(response.total ? `Halaman ${response.current_page} dari ${response.last_page}` : 'Tidak ada pesanan yang perlu diperiksa.');
                        $('[data-pending-prev]').prop('disabled', response.current_page <= 1);
                        $('[data-pending-next]').prop('disabled', response.current_page >= response.last_page);

                        if (!response.orders.length) {
                            setEmpty('Tidak ada pesanan berstatus Menunggu Pembayaran.');
                            return;
                        }

                        response.orders.forEach((order) => {
                            const productDetail = [order.group_name, order.option_name, order.variant_name].filter(Boolean).join(' · ');
                            const customerToggle = $('<button type="button" class="btn btn-link btn-sm p-0 mt-1">Lihat data pembeli</button>').attr('data-toggle-customer-data', order.id);
                            const identity = $('<div>').append($('<strong>').text(order.order_number)).append($('<small class="d-block text-muted">').text(new Date(order.created_at).toLocaleString('id-ID')), customerToggle);
                            const product = $('<div>').append($('<strong>').text(order.product_name)).append($('<small class="d-block text-muted">').text(productDetail));
                            const select = $('<select class="form-control form-control-sm">')
                                .append($('<option>').val('completed').text('Selesai'))
                                .append($('<option>').val('cancelled').text('Dibatalkan'));
                            const save = $('<button type="button" class="btn btn-sm btn-primary">Simpan</button>').attr('data-save-pending', order.id);
                            select.addClass('pending-status-select');
                            const orderRow = $('<tr>').append(
                                $('<td>').append(identity),
                                $('<td>').append(product),
                                $('<td>').text(formatMoney(order.total)),
                                $('<td>').text(order.payment_method),
                                $('<td>').append(select),
                                $('<td>').append(save),
                            );
                            const customerFields = $('<table class="table table-sm table-borderless mb-0">');
                            (order.customer_data || []).forEach((field) => {
                                customerFields.append($('<tr>').append(
                                    $('<th scope="row" class="text-muted" style="width: 30%">').text(field.label),
                                    $('<td>').text(field.value),
                                ));
                            });
                            if (!order.customer_data?.length) {
                                customerFields.append($('<tr>').append($('<td class="text-muted">').text('Tidak ada data tambahan dari pembeli.')));
                            }
                            const customerDetails = $('<tr class="pending-customer-details">').attr('data-customer-row', order.id).hide()
                                .append($('<td colspan="6" class="bg-light">').append($('<div class="px-2 py-1">').append($('<strong class="d-block mb-1">').text('Data pembeli'), customerFields)));
                            rows.append(orderRow, customerDetails);
                        });
                    }).fail(() => setEmpty('Pesanan gagal dimuat. Coba lagi.'));
                };

                modal.on('show.bs.modal', () => loadPendingOrders(1));
                modal.on('click', '[data-pending-prev]', () => loadPendingOrders(Math.max(1, currentPage - 1)));
                modal.on('click', '[data-pending-next]', () => loadPendingOrders(currentPage + 1));
                modal.on('click', '[data-toggle-customer-data]', function () {
                    const detailRow = modal.find(`[data-customer-row="${$(this).attr('data-toggle-customer-data')}"]`);
                    const isOpen = detailRow.is(':visible');
                    detailRow.toggle(!isOpen);
                    $(this).text(isOpen ? 'Lihat data pembeli' : 'Tutup data pembeli');
                });
                modal.on('click', '[data-save-pending]', function () {
                    const button = $(this);
                    const orderId = button.attr('data-save-pending');
                    const status = button.closest('tr').find('.pending-status-select').val();
                    const label = status === 'completed' ? 'Selesai' : 'Dibatalkan';
                    if (!window.confirm(`Ubah status pesanan ini menjadi ${label}?`)) return;

                    button.prop('disabled', true).text('Menyimpan...');
                    $.ajax({
                        url: "{{ route('admin.orders.status', ':id') }}".replace(':id', orderId),
                        method: 'POST',
                        data: { _token: $('meta[name="csrf-token"]').attr('content'), _method: 'PATCH', status },
                        headers: { Accept: 'application/json' },
                    }).done(() => {
                        loadPendingOrders(currentPage);
                        const counter = $('.order-summary-metric').eq(1).find('strong');
                        const pendingCount = Number(counter.text().replace(/[^0-9]/g, ''));
                        counter.text(new Intl.NumberFormat('id-ID').format(Math.max(0, pendingCount - 1)));
                    }).fail((xhr) => {
                        button.prop('disabled', false).text('Simpan');
                        window.alert(xhr.responseJSON?.message || 'Status pesanan gagal diperbarui.');
                    });
                });
            });
        </script>
    @endpush
</x-app-layout>
