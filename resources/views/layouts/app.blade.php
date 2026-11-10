<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'VelunaID Admin'))</title>
    <link rel="stylesheet" href="{{ asset('AdminLTE-3.2.0/plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('AdminLTE-3.2.0/dist/css/adminlte.min.css') }}">
    @stack('styles')
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
@stack('scripts')
</body>
</html>
