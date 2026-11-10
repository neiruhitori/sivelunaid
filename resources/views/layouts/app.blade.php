<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            @include('layouts.navigation')

            <div x-data="{ sidebarOpen: false, isDesktop: window.innerWidth >= 1024, sidebarCollapsed: false }"
                @resize.window="isDesktop = window.innerWidth >= 1024; if (isDesktop) sidebarOpen = false"
                class="min-h-[calc(100vh-4rem)] lg:flex">
                <button type="button" @click="sidebarOpen = !sidebarOpen"
                    class="m-4 inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm lg:hidden dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200"
                    aria-controls="admin-sidebar" :aria-expanded="sidebarOpen.toString()">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                    Menu Admin
                </button>

                <aside id="admin-sidebar" x-cloak x-show="sidebarOpen || isDesktop"
                    :style="isDesktop ? (sidebarCollapsed ? 'width: 5rem' : 'width: 16rem') : ''"
                    class="w-full border-b border-gray-200 bg-slate-900 text-slate-200 transition-[width] duration-200 lg:min-h-[calc(100vh-4rem)] lg:w-64 lg:shrink-0 lg:border-b-0 lg:border-r lg:border-slate-800 dark:bg-slate-900">
                    <div class="flex min-h-16 items-center justify-between px-4 pt-4">
                        <p x-show="!sidebarCollapsed" class="px-3 text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Menu Utama</p>
                        <button type="button" @click="sidebarCollapsed = !sidebarCollapsed"
                            class="hidden rounded-lg p-2 text-slate-400 transition hover:bg-slate-800 hover:text-white lg:inline-flex"
                            :aria-label="sidebarCollapsed ? 'Buka sidebar' : 'Tutup sidebar'"
                            :title="sidebarCollapsed ? 'Buka sidebar' : 'Tutup sidebar'">
                            <svg class="h-5 w-5 transition-transform" :class="sidebarCollapsed ? 'rotate-180' : ''" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                        </button>
                    </div>
                    <div class="px-3 pb-6 pt-3">
                        <nav class="space-y-1.5" aria-label="Menu admin">
                            @php
                                $adminMenu = [
                                    ['label' => 'Dashboard', 'route' => 'dashboard', 'active' => 'dashboard'],
                                    ['label' => 'Category', 'route' => 'admin.categories.index', 'active' => 'admin.categories.*'],
                                    ['label' => 'Product', 'route' => 'admin.products.index', 'active' => 'admin.products.*'],
                                    ['label' => 'Product Group', 'route' => 'admin.product-groups.index', 'active' => 'admin.product-groups.*'],
                                    ['label' => 'Product Variant', 'route' => 'admin.product-variants.index', 'active' => 'admin.product-variants.*'],
                                    ['label' => 'Produk Field', 'route' => 'admin.product-fields.index', 'active' => 'admin.product-fields.*'],
                                ];
                            @endphp
                            @foreach ($adminMenu as $item)
                                <a href="{{ route($item['route']) }}" :title="sidebarCollapsed ? @js($item['label']) : ''"
                                    @if (request()->routeIs($item['active'])) aria-current="page" @endif
                                    class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition {{ request()->routeIs($item['active']) ? 'bg-indigo-500 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
                                    :class="sidebarCollapsed ? 'lg:justify-center lg:px-2' : ''">
                                    <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                                        @if ($item['label'] === 'Dashboard')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3.75 3.75h6.5v6.5h-6.5zM13.75 3.75h6.5v6.5h-6.5zM3.75 13.75h6.5v6.5h-6.5zM13.75 13.75h6.5v6.5h-6.5z" />
                                        @elseif ($item['label'] === 'Category')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 5h7v6H4zM13 5h7v6h-7zM4 13h7v6H4zM13 13h7v6h-7z" />
                                        @elseif ($item['label'] === 'Product')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m12 3 8 4.5v9L12 21l-8-4.5v-9L12 3zM4.4 7.7 12 12l7.6-4.3M12 12v9" />
                                        @elseif ($item['label'] === 'Product Group')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 5h6v6H4zM14 5h6v6h-6zM9 14h6v6H9zM7 11v2h10v-2" />
                                        @elseif ($item['label'] === 'Product Variant')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m12 3 8 4.5v9L12 21l-8-4.5v-9L12 3zM8.5 9.5h7M8.5 13h7M8.5 16.5h3" />
                                        @else
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 4h10a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2zM9 8h6M9 12h6M9 16h3" />
                                        @endif
                                    </svg>
                                    <span x-show="!sidebarCollapsed" class="truncate">{{ $item['label'] }}</span>
                                </a>
                            @endforeach
                        </nav>
                    </div>
                </aside>

                <div class="min-w-0 flex-1">
                    @isset($header)
                        <header class="bg-white shadow-sm dark:bg-gray-800">
                            <div class="px-4 py-5 sm:px-6 lg:px-8">
                                {{ $header }}
                            </div>
                        </header>
                    @endisset

                    <main class="px-4 py-6 sm:px-6 lg:px-8">
                        {{ $slot }}
                    </main>
                </div>
            </div>
        </div>
    </body>
</html>
