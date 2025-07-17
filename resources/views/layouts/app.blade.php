<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <style>
            .custom-footer {
    background-color: #000;
    color: #fff;
    padding: 24px 16px;
    font-size: 14px;
}

.footer-container {
    max-width: 1200px;
    margin: auto;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 12px;
    text-align: center;
}

.footer-instagram {
    display: flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
    color: #fff;
    transition: opacity 0.3s ease;
}

.footer-instagram:hover {
    opacity: 0.8;
}

.footer-icon {
    width: 24px;
    height: 24px;
}

/* Responsif: Horizontal layout di layar besar */
@media (min-width: 640px) {
    .footer-container {
        flex-direction: row;
        justify-content: space-between;
        text-align: left;
    }

    @media (max-width: 640px) {
    .flash-message-container {
        width: 90% !important;
        left: 5% !important;
        padding: 1rem !important;
    }

    .flash-message-container .text-sm {
        font-size: 0.875rem;
    }

    .flash-message-container svg {
        width: 24px;
        height: 24px;
    }

    .flash-message-container button {
        top: 0.25rem !important;
        right: 0.5rem !important;
        font-size: 1rem;
    }
}
}

        </style>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @auth
    <script>
        window.Laravel = {
            userIsStaffOrAdmin: {{ auth()->user()->hasRole('admin') || auth()->user()->hasRole('staff') ? 'true' : 'false' }}
        };
    </script>
@endauth

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900 flex flex-col justify-between">
            <div>
                @include('layouts.navigation')

                @if (session('success') || session('error'))
<div
    x-data="{ show: true }"
    x-show="show"
    x-init="setTimeout(() => show = false, 5000)"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-2"
    x-transition:enter-end="opacity-100 translate-y-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="flash-message-container fixed top-4 left-1/2 transform -translate-x-1/2 z-50 max-w-lg w-full px-4"
>
    <div class="relative flex flex-col items-center text-center p-4 rounded-lg shadow-md border w-full"
        style="
            background-color: {{ session('success') ? '#dcfce7' : '#fee2e2' }} !important;
            color: {{ session('success') ? '#065f46' : '#991b1b' }} !important;
            border-color: {{ session('success') ? '#bbf7d0' : '#fecaca' }} !important;
            --tw-shadow-color: rgba(0, 0, 0, 0.1);
            box-shadow: 0 4px 6px -1px var(--tw-shadow-color), 0 2px 4px -2px var(--tw-shadow-color) !important;
        "
        x-bind:style="(window.matchMedia('(prefers-color-scheme: dark)').matches) ? 
        '{{ session('success') ? 'background-color:#064e3b !important;color:#bbf7d0 !important;border-color:#065f46 !important' 
        : 'background-color:#7f1d1d !important;color:#fecaca !important;border-color:#991b1b !important' }}' : ''"
    >
        <!-- Ikon dan Pesan -->
        <div class="flex flex-col items-center justify-center space-y-2 w-full">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                @if (session('success'))
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                @else
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                @endif
            </svg>
            <p class="text-sm font-medium">
                {{ session('success') ?? session('error') }}
            </p>
        </div>

       <!-- Tombol Close -->
<button 
    @click="show = false" 
    class="text-lg font-bold px-2 focus:outline-none"
    style="position: absolute !important; top: 0.5rem !important; right: 0.75rem !important;"
>
    &times;
</button>
    </div>
</div>
@endif

                <!-- Page Heading -->
                @isset($header)
                    <header class="bg-white dark:bg-gray-800 shadow">
                        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                <!-- Page Content -->
                <main class="mb-10">
                    {{ $slot }}
                </main>
            </div>

<footer class="custom-footer">
    <div class="footer-container">
        <!-- Copyright -->
        <div class="footer-copy">
            &copy; {{ date('Y') }} Jaringan Pintar Nusantara. All rights reserved.
        </div>

        <!-- Instagram -->
        <a href="https://instagram.com/jp_nusantara" target="_blank" class="footer-instagram">
            <img src="{{ asset('images/icons8-instagram-logo-94.png') }}" alt="Instagram" class="footer-icon">
            <span>@jp_nusantara</span>
        </a>
    </div>
</footer>




        </div>


    </body>
</html>
