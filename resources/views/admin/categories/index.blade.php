<x-app-layout>
    <div
        class="-mx-4 -my-6 min-h-[calc(100vh-4rem)] bg-slate-50 px-4 py-7 text-slate-800 sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">

        <div class="mx-auto max-w-[1500px] space-y-7">

            {{-- Breadcrumb --}}
            <div class="flex items-center gap-2 text-xs font-medium text-slate-400">
                <a href="{{ route('dashboard') }}" class="transition hover:text-indigo-600">Dashboard</a>
                <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M7.2 4.2a.75.75 0 0 1 1.06 0l4.5 4.5a.75.75 0 0 1 0 1.06l-4.5 4.5A.75.75 0 1 1 7.2 13.2L11.17 9.23 7.2 5.26a.75.75 0 0 1 0-1.06Z"
                        clip-rule="evenodd" />
                </svg>
                <span class="font-semibold text-indigo-600">Kategori</span>
            </div>

            {{-- Hero --}}
            <section
                class="relative isolate overflow-hidden rounded-3xl bg-gradient-to-br from-indigo-600 via-violet-600 to-purple-700 px-6 py-7 shadow-xl shadow-indigo-200/50 sm:px-9 sm:py-9">
                <div
                    class="pointer-events-none absolute -right-16 -top-28 h-72 w-72 rounded-full border-[35px] border-white/10">
                </div>
                <div
                    class="pointer-events-none absolute -bottom-32 right-48 h-64 w-64 rounded-full bg-white/5 blur-2xl">
                </div>
                <div class="pointer-events-none absolute right-10 top-10 hidden opacity-20 sm:block">
                    <svg class="h-32 w-32" viewBox="0 0 120 120" fill="none">
                        <rect x="13" y="13" width="38" height="38" rx="10" stroke="white"
                            stroke-width="3" />
                        <rect x="69" y="13" width="38" height="38" rx="10" stroke="white"
                            stroke-width="3" />
                        <rect x="13" y="69" width="38" height="38" rx="10" stroke="white"
                            stroke-width="3" />
                        <rect x="69" y="69" width="38" height="38" rx="10" stroke="white"
                            stroke-width="3" />
                    </svg>
                </div>

                <div class="relative flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
                    <div class="max-w-2xl">
                        <div
                            class="mb-3 inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-3 py-1.5 text-xs font-semibold text-white/90 backdrop-blur">
                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-300"></span>
                            MANAJEMEN PRODUK
                        </div>
                        <h1 class="text-2xl font-bold tracking-tight text-white sm:text-3xl">
                            Kategori Produk
                        </h1>
                        <p class="mt-2 max-w-xl text-sm leading-relaxed text-indigo-100 sm:text-base">
                            Atur dan kelompokkan produk VelunaID agar katalog lebih terstruktur dan mudah dikelola.
                        </p>
                    </div>

                    <button type="button" id="btn-add-category"
                        class="inline-flex min-h-12 shrink-0 items-center justify-center gap-2.5 rounded-xl bg-white px-5 py-3 text-sm font-bold text-indigo-700 shadow-lg shadow-indigo-950/10 transition duration-200 hover:-translate-y-0.5 hover:bg-indigo-50 hover:shadow-xl focus:outline-none focus:ring-4 focus:ring-white/30">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2"
                                d="M12 5v14m7-7H5" />
                        </svg>
                        Tambah Kategori
                    </button>
                </div>
            </section>

            {{-- Table heading --}}
            <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h2 class="text-lg font-bold tracking-tight text-slate-800">Daftar Kategori</h2>
                    <p class="mt-1 text-sm text-slate-500">Kelola seluruh kategori yang tersedia di katalog VelunaID.
                    </p>
                </div>
                <div
                    class="inline-flex w-fit items-center gap-2 rounded-full border border-emerald-100 bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700">
                    <span class="relative flex h-2 w-2">
                        <span
                            class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-50"></span>
                        <span class="relative inline-flex h-2 w-2 rounded-full bg-emerald-500"></span>
                    </span>
                    Data tersinkronisasi
                </div>
            </div>

            {{-- Table card --}}
            <section
                class="overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-sm shadow-slate-200/50">
                <div
                    class="flex flex-col gap-3 border-b border-slate-100 px-5 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-7">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                    d="M4 5h7v6H4zM13 5h7v6h-7zM4 13h7v6H4zM13 13h7v6h-7z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-800">Semua Kategori</h3>
                            <p class="mt-0.5 text-xs text-slate-400">Informasi dan pengaturan kategori produk</p>
                        </div>
                    </div>
                    <span class="text-xs text-slate-400">Gunakan pencarian untuk menemukan kategori</span>
                </div>

                <div class="p-4 sm:p-6">
                    <div class="overflow-x-auto">
                        <table id="categories-table" class="w-full text-left text-sm">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Gambar</th>
                                    <th>Nama Kategori</th>
                                    <th>Slug</th>
                                    <th>Urutan</th>
                                    <th>Status</th>
                                    <th>Dibuat</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </section>

            <p class="px-1 text-center text-xs text-slate-400">
                VelunaID Admin Panel <span class="mx-1">·</span> Category Management
            </p>
        </div>
    </div>

    {{-- Modal --}}
    <div id="category-modal"
        class="fixed inset-0 z-[100] hidden items-end justify-center bg-slate-950/60 p-0 opacity-0 backdrop-blur-sm transition-opacity duration-200 sm:items-center sm:p-5">

        <div id="category-modal-panel"
            class="flex max-h-[94dvh] w-full translate-y-5 flex-col overflow-hidden rounded-t-3xl bg-white shadow-2xl transition-transform duration-200 sm:max-h-[90vh] sm:max-w-xl sm:translate-y-3 sm:rounded-3xl">

            {{-- Modal header --}}
            <div
                class="relative shrink-0 overflow-hidden bg-gradient-to-r from-indigo-600 to-violet-600 px-5 py-5 sm:px-7">
                <div
                    class="pointer-events-none absolute -right-5 -top-12 h-32 w-32 rounded-full border-[18px] border-white/10">
                </div>
                <div class="relative flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div
                            class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border border-white/20 bg-white/15 text-white shadow-sm">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                    d="M4 5h7v6H4zM13 5h7v6h-7zM4 13h7v6H4zM13 13h7v6h-7z" />
                            </svg>
                        </div>
                        <div>
                            <h3 id="modal-title" class="text-base font-bold text-white">Tambah Kategori</h3>
                            <p class="mt-1 text-xs text-indigo-100">Lengkapi informasi kategori produk</p>
                        </div>
                    </div>
                    <button type="button" id="btn-close-modal"
                        class="rounded-xl p-2 text-white/80 transition hover:bg-white/15 hover:text-white"
                        aria-label="Tutup modal">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m6 6 12 12M18 6 6 18" />
                        </svg>
                    </button>
                </div>
            </div>

            <form id="category-form" method="POST" enctype="multipart/form-data"
                class="flex min-h-0 flex-1 flex-col">
                @csrf

                <input type="hidden" name="remove_image" id="remove_image" value="0">

                <div class="min-h-0 flex-1 space-y-5 overflow-y-auto px-5 py-6 sm:px-7">
                    <div id="form-errors"
                        class="hidden rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    </div>

                    <div>
                        <label for="name" class="mb-2 block text-sm font-semibold text-slate-700">
                            Nama Kategori <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" id="name" name="name"
                            placeholder="Contoh: Games atau Aplikasi Premium" required
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 hover:border-slate-300 focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-100">
                    </div>

                    <div>
                        <label for="description" class="mb-2 block text-sm font-semibold text-slate-700">
                            Deskripsi <span class="ml-1 text-xs font-normal text-slate-400">Opsional</span>
                        </label>
                        <textarea id="description" name="description" rows="3" placeholder="Tuliskan deskripsi singkat kategori..."
                            class="w-full resize-y rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 hover:border-slate-300 focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-100"></textarea>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">
                            Gambar Kategori <span class="ml-1 text-xs font-normal text-slate-400">Opsional</span>
                        </label>

                        <div
                            class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-4 transition hover:border-indigo-300 hover:bg-indigo-50/30">
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-center">

                                <div id="current-image-wrapper"
                                    class="hidden items-center gap-3 rounded-xl border border-slate-200 bg-white p-2.5 sm:flex-row">

                                    <img id="current-image" src="" alt="Gambar kategori saat ini"
                                        class="h-14 w-14 shrink-0 rounded-lg object-cover">

                                    <div class="min-w-0 flex-1">
                                        <p class="text-xs font-semibold text-slate-700">
                                            Gambar saat ini
                                        </p>
                                        <p class="mt-1 text-xs text-slate-400">
                                            Gambar yang tersimpan pada kategori.
                                        </p>

                                        <button type="button" id="btn-remove-current-image"
                                            class="mt-2 inline-flex items-center gap-1.5 text-xs font-semibold text-rose-600 transition hover:text-rose-700">
                                            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 6h18M8 6V4h8v2m-9 0 1 14h8l1-14M10 11v5m4-5v5" />
                                            </svg>
                                            Hapus gambar
                                        </button>
                                    </div>
                                </div>

                                <div class="min-w-0 flex-1">
                                    <input type="file" id="image" name="image"
                                        accept="image/png,image/jpeg,image/webp"
                                        class="block w-full cursor-pointer text-xs text-slate-500 file:mr-3 file:cursor-pointer file:rounded-lg file:border-0 file:bg-indigo-100 file:px-3 file:py-2.5 file:text-xs file:font-bold file:text-indigo-700 hover:file:bg-indigo-200">
                                    <p class="mt-2 text-xs leading-relaxed text-slate-400">
                                        JPG, JPEG, PNG, atau WEBP. Maksimal 2 MB. Kosongkan jika tidak ingin menggunakan
                                        gambar baru.
                                    </p>
                                </div>
                            </div>
                            <div id="new-image-preview-wrapper"
                                class="mt-3 hidden items-center gap-3 rounded-xl border border-indigo-100 bg-white p-2">
                                <img id="new-image-preview" src="" alt="Preview gambar baru"
                                    class="h-10 w-10 shrink-0 rounded-lg object-cover"
                                    style="width: 40px; height: 40px; max-width: 40px;">
                                <div class="min-w-0">
                                    <p class="text-xs font-semibold text-slate-700">Pratinjau gambar baru</p>
                                    <p id="new-image-name" class="mt-0.5 truncate text-xs text-slate-400"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label for="sort_order" class="mb-2 block text-sm font-semibold text-slate-700">Urutan
                                Tampilan</label>
                            <input type="number" id="sort_order" name="sort_order" value="0" min="0"
                                required
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-800 outline-none transition hover:border-slate-300 focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-100">
                        </div>
                        <div>
                            <label for="is_active" class="mb-2 block text-sm font-semibold text-slate-700">Status
                                Kategori</label>
                            <select id="is_active" name="is_active" required
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-800 outline-none transition hover:border-slate-300 focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-100">
                                <option value="1">Aktif</option>
                                <option value="0">Nonaktif</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Modal footer --}}
                <div
                    class="flex shrink-0 flex-col-reverse gap-2 border-t border-slate-100 bg-white px-5 py-4 sm:flex-row sm:justify-end sm:px-7">
                    <button type="button" id="btn-cancel-modal"
                        class="inline-flex min-h-11 items-center justify-center rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50 hover:text-slate-800">
                        Batal
                    </button>
                    <button type="submit" id="btn-save-category"
                        class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-indigo-600 px-6 py-2.5 text-sm font-bold text-white shadow-lg shadow-indigo-200 transition hover:-translate-y-0.5 hover:bg-indigo-700 hover:shadow-indigo-300 focus:outline-none focus:ring-4 focus:ring-indigo-100 disabled:cursor-not-allowed disabled:opacity-60 disabled:hover:translate-y-0">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m5 12 4 4L19 6" />
                        </svg>
                        <span>Simpan Kategori</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- DataTables & SweetAlert --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.4/css/dataTables.dataTables.min.css">

    <style>
        #categories-table {
            width: 100% !important;
            min-width: 850px;
            border-collapse: separate !important;
            border-spacing: 0;
        }

        #categories-table thead th {
            padding: 13px 15px;
            border-bottom: 1px solid #e9edf5;
            background: #f8f9fd;
            color: #64748b;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
            white-space: nowrap;
        }

        #categories-table tbody td {
            padding: 13px 15px;
            border-bottom: 1px solid #f1f4f8;
            color: #64748b;
            font-size: 13px;
            vertical-align: middle;
        }

        #categories-table tbody tr {
            transition: background-color .15s ease;
        }

        #categories-table tbody tr:hover {
            background: #fafaff;
        }

        #categories-table tbody td:nth-child(3) {
            color: #1e293b;
            font-weight: 650;
        }

        #categories-table_wrapper .dt-layout-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            margin: 0 0 18px;
        }

        #categories-table_wrapper .dt-layout-row:last-child {
            margin: 18px 0 0;
        }

        #categories-table_wrapper .dt-search,
        #categories-table_wrapper .dt-length,
        #categories-table_wrapper .dt-info {
            color: #94a3b8;
            font-size: 12px;
        }

        #categories-table_wrapper .dt-search input,
        #categories-table_wrapper .dt-length select {
            min-height: 38px;
            margin-left: 7px;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            background: #fff;
            padding: 7px 11px;
            color: #334155;
            outline: none;
            transition: .2s;
        }

        #categories-table_wrapper .dt-search input:focus,
        #categories-table_wrapper .dt-length select:focus {
            border-color: #818cf8;
            box-shadow: 0 0 0 3px #eef2ff;
        }

        #categories-table_wrapper .dt-paging {
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
        }

        #categories-table_wrapper .dt-paging-button {
            min-width: 34px;
            min-height: 34px;
            border: 0 !important;
            border-radius: 9px !important;
            color: #64748b !important;
            font-size: 12px;
        }

        #categories-table_wrapper .dt-paging-button:hover {
            background: #eef2ff !important;
            color: #4f46e5 !important;
        }

        #categories-table_wrapper .dt-paging-button.current {
            border: 0 !important;
            background: #4f46e5 !important;
            color: #fff !important;
            box-shadow: 0 3px 8px rgb(79 70 229 / 20%);
        }

        #categories-table_wrapper .dt-paging-button.disabled {
            opacity: .4;
        }

        #categories-table_wrapper .dt-processing {
            color: #4f46e5;
            background: rgb(255 255 255 / 95%);
        }

        @media (max-width: 640px) {
            #categories-table_wrapper .dt-layout-row {
                align-items: stretch;
                flex-direction: column;
            }

            #categories-table_wrapper .dt-search,
            #categories-table_wrapper .dt-length {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 8px;
                text-align: left;
            }

            #categories-table_wrapper .dt-search input {
                width: min(65%, 260px);
                margin-left: 0;
            }

            #categories-table_wrapper .dt-paging {
                justify-content: flex-start;
                overflow-x: auto;
            }
        }

        body.overflow-hidden {
            overflow: hidden;
        }

        #category-modal.is-open {
            opacity: 1;
        }

        #category-modal.is-open #category-modal-panel {
            transform: translateY(0);
        }
    </style>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            const table = $('#categories-table').DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                scrollX: true,
                ajax: "{{ route('admin.categories.data') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'image_preview',
                        name: 'image',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'slug',
                        name: 'slug'
                    },
                    {
                        data: 'sort_order',
                        name: 'sort_order'
                    },
                    {
                        data: 'status',
                        name: 'is_active',
                        searchable: false
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    }
                ],
                order: [
                    [4, 'asc']
                ],
                language: {
                    processing: 'Memproses...',
                    search: 'Cari:',
                    lengthMenu: 'Tampilkan _MENU_ data',
                    info: 'Menampilkan _START_–_END_ dari _TOTAL_ data',
                    infoEmpty: 'Belum ada data kategori',
                    infoFiltered: '(difilter dari _MAX_ total data)',
                    zeroRecords: 'Kategori tidak ditemukan',
                    emptyTable: 'Belum ada kategori. Silakan tambahkan kategori pertama.',
                    paginate: {
                        first: 'Pertama',
                        last: 'Terakhir',
                        next: 'Berikutnya',
                        previous: 'Sebelumnya'
                    }
                }
            });

            const modal = $('#category-modal');
            const modalPanel = $('#category-modal-panel');
            const form = $('#category-form');
            const modalTitle = $('#modal-title');
            const formErrors = $('#form-errors');
            const saveButton = $('#btn-save-category');
            const saveButtonText = saveButton.find('span');

            const storeUrl = "{{ route('admin.categories.store') }}";
            const updateUrl = "{{ route('admin.categories.update', ':id') }}";
            const csrfToken = $('meta[name="csrf-token"]').attr('content');

            let isEditMode = false;
            let previewObjectUrl = null;

            function clearNewImagePreview() {
                if (previewObjectUrl) {
                    URL.revokeObjectURL(previewObjectUrl);
                    previewObjectUrl = null;
                }

                $('#new-image-preview-wrapper').addClass('hidden').removeClass('flex');
                $('#new-image-preview').attr('src', '');
                $('#new-image-name').text('');
            }

            function openModal() {
                modal.removeClass('hidden').addClass('flex');
                document.body.classList.add('overflow-hidden');

                requestAnimationFrame(() => {
                    modal.addClass('is-open');
                });

                setTimeout(() => $('#name').trigger('focus'), 150);
            }

            function closeModal() {
                modal.removeClass('is-open');

                setTimeout(() => {
                    modal.addClass('hidden').removeClass('flex');
                }, 200);

                document.body.classList.remove('overflow-hidden');
                form[0].reset();
                formErrors.addClass('hidden').empty();
                $('#current-image-wrapper').addClass('hidden');
                $('#current-image').attr('src', '');
                clearNewImagePreview();

                saveButton.prop('disabled', false);
                saveButtonText.text(isEditMode ? 'Simpan Perubahan' : 'Simpan Kategori');
            }

            function resetAddModal() {
                isEditMode = false;
                form[0].reset();
                form.attr('action', storeUrl);
                form.find('input[name="_method"]').remove();

                modalTitle.text('Tambah Kategori');
                saveButtonText.text('Simpan Kategori');

                $('#remove_image').val('0');

                $('#sort_order').val(0);
                $('#is_active').val('1');

                formErrors.addClass('hidden').empty();
                $('#current-image-wrapper').addClass('hidden');
                clearNewImagePreview();

                openModal();
            }

            $('#btn-add-category').on('click', resetAddModal);
            $('#btn-close-modal, #btn-cancel-modal').on('click', closeModal);

            modal.on('click', function(event) {
                if (event.target === this) closeModal();
            });

            // Preview gambar yang baru dipilih
            $('#image').on('change', function() {
                clearNewImagePreview();

                const file = this.files[0];
                if (!file) return;

                if (!file.type.startsWith('image/')) return;

                previewObjectUrl = URL.createObjectURL(file);
                $('#new-image-preview').attr('src', previewObjectUrl);
                $('#new-image-name').text(file.name);
                $('#new-image-preview-wrapper').removeClass('hidden').addClass('flex');
            });

            // Edit kategori
            $('#categories-table').on('click', '[data-action="edit"]', function() {
                const row = table.row($(this).closest('tr')).data();
                if (!row) return;

                isEditMode = true;
                form[0].reset();
                $('#remove_image').val('0');
                form.attr('action', updateUrl.replace(':id', row.id));
                form.find('input[name="_method"]').remove();
                form.append('<input type="hidden" name="_method" value="PUT">');

                modalTitle.text('Edit Kategori');
                saveButtonText.text('Simpan Perubahan');

                $('#name').val(row.name);
                $('#description').val(row.description ?? '');
                $('#sort_order').val(row.sort_order);
                $('#is_active').val(row.is_active ? '1' : '0');

                if (row.image) {
                    $('#current-image')
                        .attr('src', "{{ asset('storage') }}/" + row.image);
                    $('#current-image-wrapper').removeClass('hidden');
                } else {
                    $('#current-image-wrapper').addClass('hidden');
                }

                formErrors.addClass('hidden').empty();
                clearNewImagePreview();
                openModal();
            });

            // Hapus gambar kategori yang sedang tersimpan
            $('#btn-remove-current-image').on('click', function() {
                $('#remove_image').val('1');

                $('#current-image-wrapper').addClass('hidden');

                Swal.fire({
                    icon: 'info',
                    title: 'Gambar ditandai untuk dihapus',
                    text: 'Gambar akan dihapus setelah kamu menyimpan perubahan.',
                    timer: 1800,
                    showConfirmButton: false
                });
            });

            // Simpan kategori
            form.on('submit', async function(event) {
                event.preventDefault();

                const formData = new FormData(this);
                const actionUrl = form.attr('action');

                saveButton.prop('disabled', true);
                saveButtonText.text('Menyimpan...');
                formErrors.addClass('hidden').empty();

                try {
                    const response = await fetch(actionUrl, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: formData
                    });

                    const result = await response.json();

                    if (!response.ok) {
                        if (response.status === 422 && result.errors) {
                            const messages = Object.values(result.errors).flat();
                            formErrors.html(
                                '<ul class="list-disc space-y-1 pl-5">' +
                                messages.map(message =>
                                    '<li>' + $('<div>').text(message).html() + '</li>'
                                ).join('') +
                                '</ul>'
                            ).removeClass('hidden');

                            return;
                        }

                        throw new Error(result.message || 'Terjadi kesalahan.');
                    }

                    closeModal();
                    table.ajax.reload(null, false);

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: result.message,
                        timer: 1800,
                        showConfirmButton: false,
                        customClass: {
                            popup: 'rounded-2xl'
                        }
                    });

                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal menyimpan',
                        text: error.message,
                        confirmButtonColor: '#4f46e5'
                    });
                } finally {
                    saveButton.prop('disabled', false);
                    saveButtonText.text(isEditMode ? 'Simpan Perubahan' : 'Simpan Kategori');
                }
            });

            // Hapus kategori
            $('#categories-table').on('click', '[data-action="delete"]', async function() {
                const id = $(this).data('id');
                const url = updateUrl.replace(':id', id);

                const confirmation = await Swal.fire({
                    title: 'Hapus kategori?',
                    text: 'Kategori yang dihapus tidak dapat dikembalikan.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#e11d48',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Ya, hapus',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    customClass: {
                        popup: 'rounded-2xl'
                    }
                });

                if (!confirmation.isConfirmed) return;

                try {
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            _method: 'DELETE'
                        })
                    });

                    const result = await response.json();

                    if (!response.ok) {
                        throw new Error(result.message || 'Gagal menghapus kategori.');
                    }

                    table.ajax.reload(null, false);

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: result.message,
                        timer: 1800,
                        showConfirmButton: false,
                        customClass: {
                            popup: 'rounded-2xl'
                        }
                    });

                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Tidak dapat menghapus',
                        text: error.message,
                        confirmButtonColor: '#4f46e5'
                    });
                }
            });

            // Tutup modal dengan tombol Escape
            $(document).on('keydown', function(event) {
                if (event.key === 'Escape' && !modal.hasClass('hidden')) {
                    closeModal();
                }
            });
        });
    </script>
</x-app-layout>
