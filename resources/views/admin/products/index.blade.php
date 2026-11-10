<x-app-layout>
    <x-slot name="header">
        <div class="d-flex align-items-center justify-content-between flex-wrap">
            <div>
                <h1 class="m-0 text-dark">Product</h1>
                <small class="text-muted">Kelola produk dan hubungkan ke kategori katalog.</small>
            </div>
            <ol class="breadcrumb float-sm-right mb-0 bg-transparent">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Product</li>
            </ol>
        </div>
    </x-slot>

    @if ($categories->isEmpty())
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle mr-1"></i>
            Tambahkan kategori terlebih dahulu sebelum membuat produk.
            <a href="{{ route('admin.categories.index') }}" class="alert-link ml-1">Buka Category</a>
        </div>
    @endif

    <div class="card card-primary card-outline">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-box mr-2"></i>Daftar Product</h3>
            <div class="card-tools">
                <button type="button" id="btn-add-product" class="btn btn-primary btn-sm" @disabled($categories->isEmpty())>
                    <i class="fas fa-plus mr-1"></i> Tambah Product
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="products-table" class="table table-bordered table-hover table-striped w-100">
                    <thead>
                        <tr>
                            <th>No.</th><th>Kategori</th><th>Gambar</th><th>Nama Product</th><th>Slug</th>
                            <th>Unggulan</th><th>Status</th><th>Urutan</th><th>Dibuat</th><th style="min-width:130px">Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="product-modal" tabindex="-1" role="dialog" aria-labelledby="modal-title" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <form id="product-form" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="remove_image" id="remove_image" value="0">
                    <input type="hidden" name="sort_order_custom" id="sort_order_custom" value="0">
                    <div class="modal-header">
                        <div>
                            <h5 class="modal-title" id="modal-title">Tambah Product</h5>
                            <small class="text-muted">Slug produk dibuat otomatis dari nama.</small>
                        </div>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Tutup"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div id="form-errors" class="alert alert-danger d-none" role="alert"></div>
                        <div class="form-group">
                            <label for="category_id">Kategori <span class="text-danger">*</span></label>
                            <select class="custom-select" id="category_id" name="category_id" required>
                                <option value="">Pilih kategori</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="name">Nama Produk <span class="text-danger">*</span></label>
                            <input class="form-control" type="text" id="name" name="name" required maxlength="150" placeholder="Masukkan nama produk">
                        </div>
                        <div class="form-group">
                            <label for="description">Deskripsi Produk <small class="text-muted">(opsional)</small></label>
                            <textarea class="form-control" id="description" name="description" rows="3" placeholder="Deskripsi singkat produk"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="image">Gambar Produk <small class="text-muted">(opsional)</small></label>
                            <div id="current-image-wrapper" class="d-none align-items-center mb-2">
                                <img id="current-image" src="" alt="Gambar produk saat ini" class="img-thumbnail mr-3" style="width:72px;height:72px;object-fit:cover">
                                <div><small class="d-block text-muted">Gambar saat ini</small><button type="button" id="btn-remove-current-image" class="btn btn-link btn-sm text-danger p-0">Hapus gambar</button></div>
                            </div>
                            <div id="image-remove-notice" class="d-none mb-2">
                                <span class="text-warning"><i class="fas fa-exclamation-circle mr-1"></i>Gambar akan dihapus saat disimpan.</span>
                                <button type="button" id="btn-restore-current-image" class="btn btn-link btn-sm">Batalkan</button>
                            </div>
                            <div id="new-image-preview-wrapper" class="d-none align-items-center mb-2">
                                <img id="new-image-preview" src="" alt="Pratinjau gambar" class="img-thumbnail mr-2" style="width:48px;height:48px;object-fit:cover">
                                <small id="new-image-name" class="text-muted text-truncate mr-2"></small>
                                <button type="button" id="btn-cancel-image" class="btn btn-outline-secondary btn-xs ml-auto" title="Batalkan pilihan gambar"><i class="fas fa-times mr-1"></i>Batalkan gambar</button>
                            </div>
                            <div class="custom-file">
                                <input class="custom-file-input" type="file" id="image" name="image" accept="image/png,image/jpeg,image/webp">
                                <label class="custom-file-label" for="image">Pilih gambar</label>
                            </div>
                            <small class="form-text text-muted">JPG, JPEG, PNG, atau WEBP. Maksimal 2 MB.</small>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label for="is_featured">Produk Unggulan</label>
                                <select class="custom-select" id="is_featured" name="is_featured" required>
                                    <option value="0">Tidak</option><option value="1">Ya</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="is_active">Status Produk</label>
                                <select class="custom-select" id="is_active" name="is_active" required>
                                    <option value="1">Aktif</option><option value="0">Nonaktif</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="sort_order">Urutan Tampilan</label>
                                <input class="form-control" type="number" id="sort_order" name="sort_order" value="0" min="0" step="1" required>
                                <small id="sort-order-help" class="form-text text-muted">Urutan dimulai dari 0.</small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                        <button type="submit" id="btn-save-product" class="btn btn-primary"><i class="fas fa-save mr-1"></i><span>Simpan Product</span></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('styles')
        <link rel="stylesheet" href="{{ asset('AdminLTE-3.2.0/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
        <link rel="stylesheet" href="{{ asset('AdminLTE-3.2.0/plugins/sweetalert2/sweetalert2.min.css') }}">
        <link rel="stylesheet" href="{{ asset('AdminLTE-3.2.0/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}">
        <style>#products-table td { vertical-align: middle; } #products-table td:nth-child(4) { font-weight: 600; }</style>
    @endpush

    @push('scripts')
        <script src="{{ asset('AdminLTE-3.2.0/plugins/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('AdminLTE-3.2.0/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('AdminLTE-3.2.0/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
        <script>
            $(function () {
                const table = $('#products-table').DataTable({
                    processing: true, serverSide: true, autoWidth: false,
                    ajax: "{{ route('admin.products.data') }}",
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                        { data: 'category_name', name: 'categories.name' },
                        { data: 'image_preview', name: 'image', orderable: false, searchable: false },
                        { data: 'name', name: 'products.name' },
                        { data: 'slug', name: 'products.slug' },
                        { data: 'featured_status', name: 'products.is_featured', searchable: false },
                        { data: 'status', name: 'products.is_active', searchable: false },
                        { data: 'sort_order', name: 'products.sort_order' },
                        { data: 'created_at', name: 'products.created_at' },
                        { data: 'actions', name: 'actions', orderable: false, searchable: false }
                    ],
                    order: [[7, 'asc']],
                    language: {
                        processing: 'Memproses...', search: 'Cari:', lengthMenu: 'Tampilkan _MENU_ data',
                        info: 'Menampilkan _START_–_END_ dari _TOTAL_ data', infoEmpty: 'Belum ada data produk',
                        infoFiltered: '(difilter dari _MAX_ total data)', zeroRecords: 'Produk tidak ditemukan',
                        emptyTable: 'Belum ada produk. Silakan tambahkan produk pertama.',
                        paginate: { first: 'Pertama', last: 'Terakhir', next: 'Berikutnya', previous: 'Sebelumnya' }
                    }
                });

                const modal = $('#product-modal');
                const form = $('#product-form');
                const errors = $('#form-errors');
                const saveButton = $('#btn-save-product');
                const saveText = saveButton.find('span');
                const storeUrl = "{{ route('admin.products.store') }}";
                const updateUrl = "{{ route('admin.products.update', ':id') }}";
                const csrf = $('meta[name="csrf-token"]').attr('content');
                let editing = false;
                let previewUrl = null;

                function clearPreview() {
                    if (previewUrl) URL.revokeObjectURL(previewUrl);
                    previewUrl = null;
                    $('#new-image-preview-wrapper').addClass('d-none').removeClass('d-flex');
                    $('#new-image-preview').attr('src', '');
                    $('#new-image-name').text('');
                    $('#image').next('.custom-file-label').text('Pilih gambar');
                }
                function resetForm() {
                    form[0].reset();
                    errors.addClass('d-none').empty();
                    $('#remove_image, #sort_order_custom').val('0');
                    $('#sort_order').val(0).removeAttr('max');
                    $('#sort-order-help').text('Urutan dimulai dari 0.');
                    $('#category_id').val('').trigger('change');
                    $('#is_featured').val('0').trigger('change');
                    $('#is_active').val('1').trigger('change');
                    $('#current-image-wrapper').addClass('d-none').removeClass('d-flex');
                    $('#image-remove-notice').addClass('d-none');
                    $('#current-image').attr('src', '');
                    clearPreview();
                    form.find('input[name="_method"]').remove();
                }
                function openAdd() {
                    editing = false; resetForm();
                    $('#sort_order').attr('max', {{ max($productCount, $nextSortOrder) }}).val({{ $nextSortOrder }});
                    $('#sort-order-help').text('Nomor berikutnya dari database: {{ $nextSortOrder }}. Ubah nomor untuk menyisipkan produk.');
                    form.attr('action', storeUrl);
                    $('#modal-title').text('Tambah Product');
                    saveText.text('Simpan Product');
                    modal.modal('show');
                }
                $('#btn-add-product').on('click', openAdd);
                $('#sort_order').on('input', function () { $('#sort_order_custom').val('1'); });
                modal.on('hidden.bs.modal', resetForm);

                $('#image').on('change', function () {
                    const file = this.files[0];
                    clearPreview();
                    if (!file) return;
                    $(this).next('.custom-file-label').text(file.name);
                    if (!file.type.startsWith('image/')) return;
                    previewUrl = URL.createObjectURL(file);
                    $('#new-image-preview').attr('src', previewUrl);
                    $('#new-image-name').text(file.name);
                    $('#new-image-preview-wrapper').removeClass('d-none').addClass('d-flex');
                });

                $('#products-table').on('click', '[data-action="edit"]', function () {
                    const row = table.row($(this).closest('tr')).data();
                    if (!row) return;
                    editing = true; resetForm();
                    form.attr('action', updateUrl.replace(':id', row.id));
                    form.append('<input type="hidden" name="_method" value="PUT">');
                    $('#modal-title').text('Edit Product');
                    saveText.text('Simpan Perubahan');
                    $('#category_id').val(row.category_id).trigger('change');
                    $('#name').val(row.name);
                    $('#description').val(row.description || '');
                    $('#is_featured').val(row.is_featured ? '1' : '0').trigger('change');
                    $('#is_active').val(row.is_active ? '1' : '0').trigger('change');
                    $('#sort_order').val(row.sort_order).attr('max', Math.max(0, {{ $productCount }} - 1));
                    $('#sort_order_custom').val('0');
                    $('#sort-order-help').text('Pilih posisi 0–' + Math.max(0, {{ $productCount }} - 1) + '. Produk lain akan menyesuaikan urutannya.');
                    if (row.image) {
                        $('#current-image').attr('src', "{{ asset('storage') }}/" + row.image);
                        $('#current-image-wrapper').removeClass('d-none').addClass('d-flex');
                    }
                    modal.modal('show');
                });

                $('#btn-remove-current-image').on('click', function () {
                    $('#remove_image').val('1');
                    $('#current-image-wrapper').addClass('d-none').removeClass('d-flex');
                    $('#image-remove-notice').removeClass('d-none');
                    Swal.fire({ icon: 'info', title: 'Gambar ditandai untuk dihapus', text: 'Gambar akan dihapus setelah perubahan disimpan.', timer: 1800, showConfirmButton: false });
                });
                $('#btn-restore-current-image').on('click', function () {
                    $('#remove_image').val('0');
                    $('#current-image-wrapper').removeClass('d-none').addClass('d-flex');
                    $('#image-remove-notice').addClass('d-none');
                });
                $('#btn-cancel-image').on('click', function () { $('#image').val(''); clearPreview(); });

                form.on('submit', async function (event) {
                    event.preventDefault();
                    saveButton.prop('disabled', true);
                    saveText.text('Menyimpan...');
                    errors.addClass('d-none').empty();
                    try {
                        const response = await fetch(form.attr('action'), {
                            method: 'POST', headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf }, body: new FormData(this)
                        });
                        const result = await response.json();
                        if (!response.ok) {
                            if (response.status === 422 && result.errors) {
                                errors.html(Object.values(result.errors).flat().map(message => $('<div>').text(message).html()).join('<br>')).removeClass('d-none');
                                return;
                            }
                            throw new Error(result.message || 'Terjadi kesalahan.');
                        }
                        modal.modal('hide'); table.ajax.reload(null, false);
                        Swal.fire({ icon: 'success', title: 'Berhasil', text: result.message, timer: 1800, showConfirmButton: false });
                    } catch (error) {
                        Swal.fire({ icon: 'error', title: 'Gagal menyimpan', text: error.message });
                    } finally {
                        saveButton.prop('disabled', false);
                        saveText.text(editing ? 'Simpan Perubahan' : 'Simpan Product');
                    }
                });

                $('#products-table').on('click', '[data-action="delete"]', async function () {
                    const id = $(this).data('id');
                    const result = await Swal.fire({
                        title: 'Hapus produk?',
                        text: 'Grup dan varian yang terhubung dengan produk ini juga akan terhapus.',
                        icon: 'warning', showCancelButton: true, confirmButtonText: 'Ya, hapus',
                        cancelButtonText: 'Batal', confirmButtonColor: '#dc3545'
                    });
                    if (!result.isConfirmed) return;
                    try {
                        const response = await fetch(updateUrl.replace(':id', id), {
                            method: 'POST', headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
                            body: JSON.stringify({ _method: 'DELETE' })
                        });
                        const payload = await response.json();
                        if (!response.ok) throw new Error(payload.message || 'Gagal menghapus produk.');
                        table.ajax.reload(null, false);
                        Swal.fire({ icon: 'success', title: 'Berhasil', text: payload.message, timer: 1800, showConfirmButton: false });
                    } catch (error) {
                        Swal.fire({ icon: 'error', title: 'Tidak dapat menghapus', text: error.message });
                    }
                });
            });
        </script>
    @endpush
</x-app-layout>
