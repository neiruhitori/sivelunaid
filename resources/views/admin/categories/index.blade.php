<x-app-layout>
    <x-slot name="header">
        <div class="d-flex align-items-center justify-content-between flex-wrap">
            <div>
                <h1 class="m-0 text-dark">Category</h1>
                <small class="text-muted">Kelola kategori produk di katalog VelunaID.</small>
            </div>
            <ol class="breadcrumb float-sm-right mb-0 bg-transparent">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Category</li>
            </ol>
        </div>
    </x-slot>

    <div class="card card-primary card-outline">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-th-large mr-2"></i>Daftar Category</h3>
            <div class="card-tools">
                <button type="button" id="btn-add-category" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus mr-1"></i> Tambah Category
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="categories-table" class="table table-bordered table-hover table-striped w-100">
                    <thead>
                        <tr>
                            <th>No.</th><th>Gambar</th><th>Nama Category</th><th>Slug</th>
                            <th>Urutan</th><th>Status</th><th>Dibuat</th><th style="min-width: 130px">Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="category-modal" tabindex="-1" role="dialog" aria-labelledby="modal-title" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form id="category-form" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="remove_image" id="remove_image" value="0">
                    <input type="hidden" name="sort_order_custom" id="sort_order_custom" value="0">
                    <div class="modal-header">
                        <div>
                            <h5 class="modal-title" id="modal-title">Tambah Category</h5>
                            <small class="text-muted">Lengkapi informasi kategori produk.</small>
                        </div>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Tutup"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div id="form-errors" class="alert alert-danger d-none" role="alert"></div>
                        <div class="form-group">
                            <label for="name">Nama Category <span class="text-danger">*</span></label>
                            <input class="form-control" type="text" id="name" name="name" required maxlength="100" placeholder="Contoh: Games">
                        </div>
                        <div class="form-group">
                            <label for="description">Deskripsi <small class="text-muted">(opsional)</small></label>
                            <textarea class="form-control" id="description" name="description" rows="3" placeholder="Deskripsi singkat kategori"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="image">Gambar Category <small class="text-muted">(opsional)</small></label>
                            <div id="current-image-wrapper" class="d-none align-items-center mb-2">
                                <img id="current-image" src="" alt="Gambar kategori saat ini" class="img-thumbnail mr-3" style="width:72px;height:72px;object-fit:cover">
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
                            <div class="form-group col-sm-6">
                                <label for="sort_order">Urutan Tampilan</label>
                                <input class="form-control" type="number" id="sort_order" name="sort_order" value="0" min="0" step="1" required>
                                <small id="sort-order-help" class="form-text text-muted">Urutan dimulai dari 0.</small>
                            </div>
                            <div class="form-group col-sm-6">
                                <label for="is_active">Status Category</label>
                                <select class="custom-select" id="is_active" name="is_active" required>
                                    <option value="1">Aktif</option><option value="0">Nonaktif</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" id="btn-cancel-modal" class="btn btn-default" data-dismiss="modal">Batal</button>
                        <button type="submit" id="btn-save-category" class="btn btn-primary"><i class="fas fa-save mr-1"></i><span>Simpan Category</span></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('styles')
        <link rel="stylesheet" href="{{ asset('AdminLTE-3.2.0/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
        <link rel="stylesheet" href="{{ asset('AdminLTE-3.2.0/plugins/sweetalert2/sweetalert2.min.css') }}">
        <link rel="stylesheet" href="{{ asset('AdminLTE-3.2.0/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}">
        <style>
            #categories-table td { vertical-align: middle; }
            #categories-table td:nth-child(3) { font-weight: 600; }
            .dataTables_wrapper .dataTables_filter input { margin-left: .5rem; }
            .category-image-soft { position:relative; width:56px; height:56px; display:inline-flex; align-items:flex-end; justify-content:flex-end; padding:5px; overflow:hidden; border:1px solid rgba(111,66,193,.14); border-radius:13px; background-color:#f3eff8; background-position:center; background-size:cover; box-shadow:0 3px 10px rgba(45,30,60,.12); color:#fff; cursor:zoom-in; transition:transform .18s ease,box-shadow .18s ease; }
            .category-image-soft:hover { transform:translateY(-2px); box-shadow:0 6px 15px rgba(45,30,60,.2); }
            .category-image-soft.is-empty { align-items:center; justify-content:center; border-style:dashed; background:linear-gradient(145deg,#f7f4fa,#ece6f2); color:#9b8ba9; cursor:default; box-shadow:none; }
            .category-image-soft-icon { width:21px; height:21px; display:grid; place-items:center; border:1px solid rgba(255,255,255,.5); border-radius:7px; background:rgba(44,32,55,.42); font-size:10px; }
            .swal-category-image { border-radius:12px; }
        </style>
    @endpush

    @push('scripts')
        <script src="{{ asset('AdminLTE-3.2.0/plugins/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('AdminLTE-3.2.0/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('AdminLTE-3.2.0/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
        <script>
            $(function () {
                const table = $('#categories-table').DataTable({
                    processing: true, serverSide: true, autoWidth: false,
                    ajax: "{{ route('admin.categories.data') }}",
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                        { data: 'image_preview', name: 'image', orderable: false, searchable: false },
                        { data: 'name', name: 'name' }, { data: 'slug', name: 'slug' },
                        { data: 'sort_order', name: 'sort_order' },
                        { data: 'status', name: 'is_active', searchable: false },
                        { data: 'created_at', name: 'created_at' },
                        { data: 'actions', name: 'actions', orderable: false, searchable: false }
                    ],
                    order: [[4, 'asc']],
                    language: {
                        processing: 'Memproses...', search: 'Cari:', lengthMenu: 'Tampilkan _MENU_ data',
                        info: 'Menampilkan _START_–_END_ dari _TOTAL_ data', infoEmpty: 'Belum ada data category',
                        infoFiltered: '(difilter dari _MAX_ total data)', zeroRecords: 'Category tidak ditemukan',
                        emptyTable: 'Belum ada category. Silakan tambahkan category pertama.',
                        paginate: { first: 'Pertama', last: 'Terakhir', next: 'Berikutnya', previous: 'Sebelumnya' }
                    }
                });

                const modal = $('#category-modal');
                const form = $('#category-form');
                const errors = $('#form-errors');
                const saveButton = $('#btn-save-category');
                const saveText = saveButton.find('span');
                const storeUrl = "{{ route('admin.categories.store') }}";
                const updateUrl = "{{ route('admin.categories.update', ':id') }}";
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
                    $('#remove_image').val('0');
                    $('#sort_order_custom').val('0');
                    $('#sort_order').val(0).removeAttr('max');
                    $('#sort-order-help').text('Urutan dimulai dari 0.');
                    $('#is_active').val('1').trigger('change');
                    $('#current-image-wrapper').addClass('d-none').removeClass('d-flex');
                    $('#image-remove-notice').addClass('d-none');
                    $('#current-image').attr('src', '');
                    clearPreview();
                    form.find('input[name="_method"]').remove();
                }
                function openAdd() {
                    editing = false; resetForm();
                    $('#sort_order').attr('max', {{ max($categoryCount, $nextSortOrder) }}).val({{ $nextSortOrder }});
                    $('#sort-order-help').text('Nomor berikutnya dari database: {{ $nextSortOrder }}. Ubah nomor untuk menyisipkan kategori.');
                    form.attr('action', storeUrl);
                    $('#modal-title').text('Tambah Category');
                    saveText.text('Simpan Category');
                    modal.modal('show');
                }
                $('#btn-add-category').on('click', openAdd);
                $('#sort_order').on('input', function () {
                    $('#sort_order_custom').val('1');
                });
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

                $('#categories-table').on('click', '[data-action="edit"]', function () {
                    const row = table.row($(this).closest('tr')).data();
                    if (!row) return;
                    editing = true; resetForm();
                    form.attr('action', updateUrl.replace(':id', row.id));
                    form.append('<input type="hidden" name="_method" value="PUT">');
                    $('#modal-title').text('Edit Category');
                    saveText.text('Simpan Perubahan');
                    $('#name').val(row.name);
                    $('#description').val(row.description || '');
                    $('#sort_order').val(row.sort_order);
                    $('#sort_order').attr('max', Math.max(0, {{ $categoryCount }} - 1));
                    $('#sort_order_custom').val('0');
                    $('#sort-order-help').text('Pilih posisi 0–' + Math.max(0, {{ $categoryCount }} - 1) + '. Kategori lain akan menyesuaikan urutannya.');
                    $('#is_active').val(row.is_active ? '1' : '0').trigger('change');
                    if (row.image) {
                        $('#current-image').attr('src', "{{ asset('storage') }}/" + row.image);
                        $('#current-image-wrapper').removeClass('d-none').addClass('d-flex');
                    }
                    modal.modal('show');
                });

                $('#categories-table').on('click', '[data-category-image-preview]', function () {
                    const imageUrl = $(this).data('image-url');
                    const imageName = $(this).data('image-name');
                    Swal.fire({
                        title: imageName,
                        imageUrl: imageUrl,
                        imageAlt: imageName,
                        imageWidth: 560,
                        showConfirmButton: false,
                        customClass: { image: 'swal-category-image' }
                    });
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
                $('#btn-cancel-image').on('click', function () {
                    $('#image').val('');
                    clearPreview();
                });

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
                        saveText.text(editing ? 'Simpan Perubahan' : 'Simpan Category');
                    }
                });

                $('#categories-table').on('click', '[data-action="delete"]', async function () {
                    const id = $(this).data('id');
                    const result = await Swal.fire({ title: 'Hapus category?', text: 'Category yang dihapus tidak dapat dikembalikan.', icon: 'warning', showCancelButton: true, confirmButtonText: 'Ya, hapus', cancelButtonText: 'Batal', confirmButtonColor: '#dc3545' });
                    if (!result.isConfirmed) return;
                    try {
                        const response = await fetch(updateUrl.replace(':id', id), {
                            method: 'POST', headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
                            body: JSON.stringify({ _method: 'DELETE' })
                        });
                        const payload = await response.json();
                        if (!response.ok) throw new Error(payload.message || 'Gagal menghapus category.');
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
