<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'VelunaID Admin'))</title>
    <link rel="stylesheet" href="{{ asset('AdminLTE-3.2.0/plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('AdminLTE-3.2.0/dist/css/adminlte.min.css') }}">
    <link rel="stylesheet" href="{{ asset('AdminLTE-3.2.0/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('AdminLTE-3.2.0/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
    <style>
        .select2-container { width: 100% !important; }
        .relation-group-row td { background: #eef3fb !important; border-top: 2px solid #cbd8eb; color: #34445d; }
    </style>
    @stack('styles')
    <style>
        .modal .modal-content {
            min-height: 0;
            max-height: calc(100vh - 1rem);
            max-height: calc(100dvh - 1rem);
        }
        .modal .modal-content > form {
            display: flex;
            flex: 1 1 auto;
            flex-direction: column;
            min-height: 0;
            overflow: hidden;
        }
        .modal .modal-body,
        .modal .modal-content > form > .modal-body {
            flex: 1 1 auto;
            min-height: 0;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
            overscroll-behavior: contain;
        }
        .modal .modal-header,
        .modal .modal-footer,
        .modal .modal-content > form > .modal-header,
        .modal .modal-content > form > .modal-footer {
            flex: 0 0 auto;
        }
        @media (max-width: 767.98px) {
            .modal .modal-dialog { margin: .5rem; }
            .modal .modal-content { max-height: calc(100vh - 1rem); max-height: calc(100dvh - 1rem); }
            .modal .modal-footer { padding-bottom: calc(.75rem + env(safe-area-inset-bottom, 0px)); }
        }
    </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
    @include('layouts.navigation')
    @include('layouts.sidebar')

    <div class="content-wrapper">
        @isset($header)
            <section class="content-header">
                <div class="container-fluid">{{ $header }}</div>
            </section>
        @endisset
        <section class="content">
            <div class="container-fluid">{{ $slot }}</div>
        </section>
    </div>

    <footer class="main-footer text-sm">
        <strong>{{ config('app.name', 'VelunaID') }}</strong>
        <div class="float-right d-none d-sm-inline-block">Admin Panel</div>
    </footer>
</div>
<script src="{{ asset('AdminLTE-3.2.0/plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('AdminLTE-3.2.0/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('AdminLTE-3.2.0/dist/js/adminlte.min.js') }}"></script>
<script src="{{ asset('AdminLTE-3.2.0/plugins/select2/js/select2.full.min.js') }}"></script>
@stack('scripts')
<script>
    $(function () {
        $('form select').each(function () {
            if ($(this).hasClass('select2-hidden-accessible')) return;

            const modal = $(this).closest('.modal');
            $(this).select2({
                theme: 'bootstrap4',
                width: '100%',
                dropdownParent: modal.length ? modal : $(document.body),
                minimumResultsForSearch: 0,
            });
        });
    });
</script>
</body>
</html>
