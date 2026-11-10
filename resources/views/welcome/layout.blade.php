<!DOCTYPE html>
<html lang="id">
@yield('head')
<body data-welcome-assets="{{ asset('Welcome/') }}/"@yield('body-attributes')>
@php($isHome = request()->routeIs('welcome.index'))
@include('welcome.partials.header', ['isHome' => $isHome])
@yield('content')
@include('welcome.partials.footer')
@yield('scripts')
</body>
</html>
