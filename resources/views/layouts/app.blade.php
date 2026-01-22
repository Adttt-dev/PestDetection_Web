<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'AgriPest Control') }}</title>

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
<body class="font-sans antialiased h-full bg-green-50/50 dark:bg-gray-900 text-gray-900 dark:text-gray-100"
      x-data="{ 
          sidebarOpen: false,
          darkMode: localStorage.getItem('theme') === 'dark',
          toggleTheme() {
              this.darkMode = !this.darkMode;
              if (this.darkMode) {
                  document.documentElement.classList.add('dark');
                  localStorage.setItem('theme', 'dark');
              } else {
                  document.documentElement.classList.remove('dark');
                  localStorage.setItem('theme', 'light');
              }
          }
      }">

    <div x-show="sidebarOpen" 
         @click="sidebarOpen = false" 
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-gray-900/50 z-40 lg:hidden">
    </div>

    <x-sidebar />

    <div class="flex flex-col min-h-screen lg:pl-64 transition-all duration-300">
        
        <header class="sticky top-0 z-30 h-16 bg-white/80 dark:bg-gray-800/80 backdrop-blur-md border-b border-green-100 dark:border-gray-700 flex items-center justify-between px-4 sm:px-6 lg:px-8">
            
            <button @click="sidebarOpen = true" class="lg:hidden text-gray-500 hover:text-green-600 dark:text-gray-400">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
            </button>

            <div class="hidden lg:block font-semibold text-xl text-gray-800 dark:text-white">
                {{ $header ?? 'Dashboard' }}
            </div>

            <div class="flex items-center gap-4 ml-auto lg:ml-0">
                
                <button @click="toggleTheme()" class="p-2 rounded-full text-gray-500 hover:bg-green-50 dark:text-gray-400 dark:hover:bg-gray-700 transition-colors focus:outline-none">
                    <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    <svg x-show="darkMode" style="display: none;" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                </button>

                <div class="relative" 
                     x-data="{ dropdownOpen: false }"
                     @mouseenter="dropdownOpen = true" 
                     @mouseleave="dropdownOpen = false">
                    
                    <button @click="dropdownOpen = !dropdownOpen" 
                            class="flex items-center gap-3 focus:outline-none group py-2">
                        
                        <div class="hidden sm:flex flex-col items-end cursor-pointer">
                            <span class="text-sm font-bold text-gray-800 dark:text-white group-hover:text-green-600 transition-colors">
                                {{ Auth::user()->name }}
                            </span>
                        </div>

                        <div class="h-9 w-9 rounded-full bg-green-100 dark:bg-green-900 flex items-center justify-center text-green-600 dark:text-green-300 border border-green-200 dark:border-green-700 transition-transform group-hover:scale-105">
                            <span class="text-sm font-bold">{{ substr(Auth::user()->name, 0, 1) }}</span>
                        </div>
                    </button>

                    <div x-show="dropdownOpen"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                         x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                         class="absolute right-0 mt-0 w-48 bg-white dark:bg-gray-800 rounded-xl shadow-xl py-1 border border-green-100 dark:border-gray-700 z-50">
                        
                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-green-50 dark:hover:bg-green-900/20 hover:text-green-700 dark:hover:text-green-400 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            Profile Saya
                        </a>

                        <div class="border-t border-green-100 dark:border-gray-700 my-1"></div>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <a href="{{ route('logout') }}"
                               onclick="event.preventDefault(); this.closest('form').submit();"
                               class="flex items-center gap-2 px-4 py-2.5 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                Keluar
                            </a>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <main class="flex-1 p-4 sm:p-6 lg:p-8">
            {{ $slot }}
        </main>
    </div>

    <div x-data="{ 
            open: false, 
            type: 'success', 
            title: '', 
            message: '', 
            resolvePromise: null, 
            
            show(type, title, message, isConfirm = false) {
                this.type = type;
                this.title = title;
                this.message = message;
                this.open = true;
                
                if (isConfirm) {
                    return new Promise((resolve) => {
                        this.resolvePromise = resolve;
                    });
                }
            },
            
            confirmAction() {
                this.open = false;
                if (this.resolvePromise) this.resolvePromise(true);
            },
            
            cancelAction() {
                this.open = false;
                if (this.resolvePromise) this.resolvePromise(false);
            }
         }"
         @notify.window="show($event.detail.type, $event.detail.title, $event.detail.message)"
         @confirm.window="$event.detail.callback(show($event.detail.type, $event.detail.title, $event.detail.message, true))"
         x-show="open" 
         style="display: none;"
         class="relative z-[100]" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        
        <div x-show="open" 
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm transition-opacity"></div>

        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div x-show="open"
                     x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-gray-800 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-100 dark:border-gray-700">
                    
                    <div class="bg-white dark:bg-gray-800 px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full sm:mx-0 sm:h-10 sm:w-10 transition-colors"
                                 :class="{
                                    'bg-green-100 text-green-600 dark:bg-green-900/30 dark:text-green-400': type === 'success',
                                    'bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400': type === 'error',
                                    'bg-yellow-100 text-yellow-600 dark:bg-yellow-900/30 dark:text-yellow-400': type === 'confirm'
                                 }">
                                <svg x-show="type === 'success'" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                                <svg x-show="type === 'error'" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" /></svg>
                                <svg x-show="type === 'confirm'" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z" /></svg>
                            </div>
                            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                                <h3 class="text-lg font-semibold leading-6 text-gray-900 dark:text-white" x-text="title"></h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500 dark:text-gray-300" x-text="message"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700/50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 gap-2">
                        <button type="button" x-show="type === 'confirm'" @click="confirmAction()" class="inline-flex w-full justify-center rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500 sm:ml-3 sm:w-auto transition-colors">
                            Ya, Lanjutkan
                        </button>
                        <button type="button" x-show="type !== 'confirm'" @click="cancelAction()" class="inline-flex w-full justify-center rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500 sm:ml-3 sm:w-auto transition-colors">
                            Oke, Mengerti
                        </button>
                        <button type="button" x-show="type === 'confirm'" @click="cancelAction()" class="mt-3 inline-flex w-full justify-center rounded-lg bg-white dark:bg-gray-800 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-gray-200 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 sm:mt-0 sm:w-auto transition-colors">
                            Batal
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.showAlert = (type, title, message) => {
            window.dispatchEvent(new CustomEvent('notify', {
                detail: { type, title, message }
            }));
        };

        window.showConfirm = (title, message) => {
            return new Promise((resolve) => {
                window.dispatchEvent(new CustomEvent('confirm', {
                    detail: { 
                        type: 'confirm', 
                        title, 
                        message,
                        callback: (promise) => { promise.then(resolve); }
                    }
                }));
            });
        };
    </script>
</body>
</html>