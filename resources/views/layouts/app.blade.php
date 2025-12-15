<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        @php
            $primaryColor = optional($appSettings)->primary_color ?? 'var(--primary-color)';
            $secondaryColor = optional($appSettings)->secondary_color ?? 'var(--primary-dark)';
            $logoPath = optional($appSettings)->logo ? asset('storage/' . $appSettings->logo) : null;
            $faviconPath = optional($appSettings)->favicon ? asset('storage/' . $appSettings->favicon) : asset('favicon.ico');
        @endphp

        <link rel="icon" href="{{ $faviconPath }}">

        <title>{{ config('app.name', 'Signature ERP') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        
        <!-- Icons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
        
        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
        
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- Custom Theme Variables (placed after compiled CSS for override) -->
        <style>
            :root {
                --primary-color: {{ $primaryColor }};
                --primary-dark: {{ $secondaryColor }};
                --primary-light: {{ $primaryColor }};
                --primary-lighter: {{ $secondaryColor }};
            }
            
            /* Sidebar Navigation Styles - Inline to ensure override */
            nav a.sidebar-link,
            nav button.sidebar-link,
            aside a.sidebar-link,
            aside button.sidebar-link {
                color: #374151 !important;
                transition: all 0.2s ease !important;
            }
            
            nav a.sidebar-link:hover,
            nav button.sidebar-link:hover,
            aside a.sidebar-link:hover,
            aside button.sidebar-link:hover {
                background-color: color-mix(in srgb, var(--primary-color) 10%, white) !important;
                color: var(--primary-color) !important;
            }
            
            nav a.sidebar-active,
            nav button.sidebar-active,
            aside a.sidebar-active,
            aside button.sidebar-active,
            nav a.sidebar-link.sidebar-active,
            nav button.sidebar-link.sidebar-active,
            aside a.sidebar-link.sidebar-active,
            aside button.sidebar-link.sidebar-active {
                background-color: color-mix(in srgb, var(--primary-color) 10%, white) !important;
                color: var(--primary-color) !important;
                font-weight: 500 !important;
            }
            
            nav a.sidebar-link.text-gray-700:hover,
            nav button.sidebar-link.text-gray-700:hover,
            nav a.sidebar-active.text-gray-700,
            nav button.sidebar-active.text-gray-700,
            aside a.sidebar-link.text-gray-700:hover,
            aside button.sidebar-link.text-gray-700:hover,
            aside a.sidebar-active.text-gray-700,
            aside button.sidebar-active.text-gray-700 {
                color: var(--primary-color) !important;
            }
            
            nav a.sidebar-link.text-gray-600:hover,
            nav button.sidebar-link.text-gray-600:hover,
            nav a.sidebar-active.text-gray-600,
            nav button.sidebar-active.text-gray-600,
            aside a.sidebar-link.text-gray-600:hover,
            aside button.sidebar-link.text-gray-600:hover,
            aside a.sidebar-active.text-gray-600,
            aside button.sidebar-active.text-gray-600 {
                color: var(--primary-color) !important;
            }
            
            /* Button Styles - Override Bootstrap */
            .btn-primary,
            .btn-primary.btn-sm {
                background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%) !important;
                border: none !important;
                border-color: var(--primary-color) !important;
                color: white !important;
                border-radius: 8px !important;
                box-shadow: 0 4px 6px color-mix(in srgb, var(--primary-color) 30%, transparent) !important;
            }
            
            .btn-primary:hover,
            .btn-primary:focus,
            .btn-primary:active,
            .btn-primary.active,
            .btn-primary.btn-sm:hover,
            .btn-primary.btn-sm:focus {
                background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary-color) 100%) !important;
                border-color: var(--primary-dark) !important;
                color: white !important;
                box-shadow: 0 6px 12px color-mix(in srgb, var(--primary-color) 40%, transparent) !important;
                transform: translateY(-2px) !important;
            }
            
            .btn-outline-primary,
            .btn-outline-primary.btn-sm,
            button.btn-outline-primary,
            a.btn-outline-primary {
                color: var(--primary-color) !important;
                border-color: var(--primary-color) !important;
                background-color: transparent !important;
                border-width: 1px !important;
                border-radius: 6px !important;
                transition: all 0.2s ease !important;
            }
            
            .btn-outline-primary:hover,
            .btn-outline-primary:focus,
            .btn-outline-primary:active,
            .btn-outline-primary.active,
            .btn-outline-primary.btn-sm:hover,
            .btn-outline-primary.btn-sm:focus,
            button.btn-outline-primary:hover,
            button.btn-outline-primary:focus,
            a.btn-outline-primary:hover,
            a.btn-outline-primary:focus {
                background-color: var(--primary-color) !important;
                border-color: var(--primary-color) !important;
                color: white !important;
            }
            
            .btn-outline-primary i,
            .btn-outline-primary.btn-sm i {
                color: inherit !important;
            }
        </style>
        
        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    </head>
    <body class="font-sans antialiased bg-gray-50">
        <div class="flex h-screen overflow-hidden" x-data="{ sidebarOpen: false }">
            <!-- Mobile Sidebar Overlay -->
            <!-- Overlay -->
            <div x-show="sidebarOpen" 
                 @click="sidebarOpen = false"
                 x-cloak
                 class="lg:hidden fixed inset-0 bg-gray-900 bg-opacity-50 z-40 transition-opacity"></div>
            
            <!-- Mobile Sidebar -->
            <aside x-show="sidebarOpen"
                   x-cloak
                   x-transition:enter="transition ease-out duration-300"
                   x-transition:enter-start="-translate-x-full"
                   x-transition:enter-end="translate-x-0"
                   x-transition:leave="transition ease-in duration-300"
                   x-transition:leave-start="translate-x-0"
                   x-transition:leave-end="-translate-x-full"
                   class="lg:hidden fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-gray-200 flex flex-col">
                @include('layouts.sidebar-content')
            </aside>

            <!-- Desktop Sidebar -->
            <aside class="hidden lg:flex w-64 bg-white border-r border-gray-200 flex-col h-screen">
                @include('layouts.sidebar-content')
            </aside>

            <!-- Main Content Area -->
            <div class="flex-1 flex flex-col overflow-hidden">
                <!-- Header -->
                @include('layouts.header')

                <!-- Page Content -->
                <main class="flex-1 overflow-y-auto bg-gray-50 p-4 lg:p-6">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>

