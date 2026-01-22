<x-guest-layout>
    <div x-data="{ 
        openModal: false,
        init() {
            // Cek jika ada session flash 'registration-success' dari Controller
            @if (session('status') === 'registration-success')
                setTimeout(() => { this.openModal = true; }, 300);
            @endif
        }
    }">
        
        <div class="text-center mb-8">
            <h2 class="text-xl font-bold text-gray-800 dark:text-white">Selamat Datang Kembali!</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Masuk untuk mengelola sistem monitoring.</p>
        </div>

        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                        </svg>
                    </div>
                    <input id="email" class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg leading-5 bg-white dark:bg-gray-900 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 sm:text-sm transition duration-150 ease-in-out" 
                           type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="nama@email.com" />
                </div>
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div>
                <div class="flex justify-between items-center mb-1">
                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Password</label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-xs font-medium text-green-600 hover:text-green-500 dark:text-green-400 dark:hover:text-green-300 transition">
                            Lupa password?
                        </a>
                    @endif
                </div>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <input id="password" class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg leading-5 bg-white dark:bg-gray-900 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 sm:text-sm transition duration-150 ease-in-out" 
                           type="password" name="password" required autocomplete="current-password" placeholder="••••••••" />
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div class="flex items-center">
                <input id="remember_me" type="checkbox" name="remember" class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded cursor-pointer bg-gray-100 dark:bg-gray-800 dark:border-gray-700">
                <label for="remember_me" class="ml-2 block text-sm text-gray-600 dark:text-gray-400 cursor-pointer select-none">
                    Ingat saya di perangkat ini
                </label>
            </div>

            <button type="submit" class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-semibold text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all active:scale-95">
                Masuk Sekarang
            </button>

            <div class="relative">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-200 dark:border-gray-700"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-2 bg-white dark:bg-gray-900 text-gray-500">atau</span>
                </div>
            </div>

            <div class="text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Belum memiliki akun? 
                    <a href="{{ route('register') }}" class="font-semibold text-green-600 hover:text-green-500 dark:text-green-400 dark:hover:text-green-300 transition">
                        Daftar Gratis
                    </a>
                </p>
            </div>
        </form>

        <div x-show="openModal" style="display: none;" 
             class="fixed inset-0 z-50 flex items-center justify-center p-4"
             aria-labelledby="modal-title" role="dialog" aria-modal="true">
            
            <div x-show="openModal" 
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm transition-opacity"></div>

            <div x-show="openModal"
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-gray-800 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-md border border-green-100 dark:border-gray-700">
                
                <div class="bg-white dark:bg-gray-800 px-4 pb-4 pt-5 sm:p-6 sm:pb-4 text-center">
                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-green-100 dark:bg-green-900/30 mb-4">
                        <svg class="h-10 w-10 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold leading-6 text-gray-900 dark:text-white" id="modal-title">Registrasi Berhasil!</h3>
                    <div class="mt-2">
                        <p class="text-sm text-gray-500 dark:text-gray-300">
                            Akun Anda telah berhasil dibuat. Silakan masukkan email dan password Anda untuk masuk ke dashboard.
                        </p>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700/50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                    <button type="button" @click="openModal = false" class="inline-flex w-full justify-center rounded-lg bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500 sm:w-auto transition-colors">
                        Siap, Login Sekarang
                    </button>
                </div>
            </div>
        </div>
        </div>
</x-guest-layout>