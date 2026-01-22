<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Pest Control') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>
<body class="font-sans text-gray-900 antialiased">
    
    <div class="min-h-screen flex flex-col justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-green-50 to-white dark:from-gray-950 dark:to-gray-900 p-4 transition-colors duration-300">
        
        <div class="mb-8 text-center">
            <a href="/" class="flex flex-col items-center gap-2 group">
                <div class="w-16 h-16 bg-green-600 dark:bg-green-700 rounded-2xl flex items-center justify-center text-3xl shadow-lg shadow-green-500/30 dark:shadow-green-900/50 transform group-hover:scale-110 transition-transform duration-300 border border-green-400 dark:border-green-600">
                    🌱
                </div>
                <h1 class="mt-4 text-2xl font-bold text-gray-800 dark:text-green-50 tracking-tight">
                    AgriPest Control
                </h1>
                <p class="text-sm text-green-700 dark:text-green-400 font-medium">Smart Farming System</p>
            </a>
        </div>

        <div class="w-full sm:max-w-md bg-white dark:bg-gray-900 shadow-2xl shadow-green-100/50 dark:shadow-none overflow-hidden sm:rounded-2xl border border-green-100 dark:border-gray-800 relative">
            <div class="h-1.5 w-full bg-gradient-to-r from-green-400 via-emerald-500 to-teal-600"></div>
            
            <div class="px-6 py-8 sm:p-8">
                {{ $slot }}
            </div>
        </div>

        <div class="mt-8 text-center text-xs text-gray-400 dark:text-gray-600">
            &copy; {{ date('Y') }} WormTeam. All rights reserved.
        </div>
    </div>
</body>
</html>