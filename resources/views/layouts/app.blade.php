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
        
        <!-- Icons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
        
        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
        
        <!-- Custom Theme Styles -->
        <style>
            :root {
                --primary-color: #8b5cf6;
                --primary-dark: #7c3aed;
                --primary-light: #a78bfa;
                --primary-lighter: #e9d5ff;
            }
            
            .btn-primary {
                background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
                border: none;
                box-shadow: 0 4px 6px rgba(139, 92, 246, 0.3);
                transition: all 0.3s ease;
            }
            
            .btn-primary:hover {
                background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary-color) 100%);
                box-shadow: 0 6px 12px rgba(139, 92, 246, 0.4);
                transform: translateY(-2px);
            }
            
            .card {
                border-radius: 12px;
                transition: all 0.3s ease;
                border: 1px solid rgba(139, 92, 246, 0.1);
            }
            
            .card:hover {
                box-shadow: 0 8px 24px rgba(139, 92, 246, 0.15);
            }
            
            .form-control:focus, .form-select:focus {
                border-color: var(--primary-color);
                box-shadow: 0 0 0 0.2rem rgba(139, 92, 246, 0.25);
            }
            
            .table thead th {
                background: linear-gradient(135deg, #f8f9fa 0%, #f3f4f6 100%);
                color: #4b5563;
                font-weight: 600;
                border-bottom: 2px solid var(--primary-lighter);
            }
            
            .table tbody tr {
                transition: all 0.2s ease;
            }
            
            .table tbody tr:hover {
                background-color: rgba(139, 92, 246, 0.05);
                transform: scale(1.01);
            }
            
            .badge {
                padding: 0.5em 0.75em;
                font-weight: 500;
                border-radius: 6px;
            }
            
            .bg-primary {
                background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%) !important;
            }
            
            .text-primary {
                color: var(--primary-color) !important;
            }
            
            .page-link {
                color: var(--primary-color);
            }
            
            .page-item.active .page-link {
                background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
                border-color: var(--primary-color);
            }
            
            .page-link:hover {
                color: var(--primary-dark);
                background-color: var(--primary-lighter);
            }
        </style>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
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
