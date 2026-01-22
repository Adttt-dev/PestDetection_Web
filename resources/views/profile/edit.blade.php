<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Pengaturan Akun') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ activeTab: 'profile' }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <div class="space-y-6">
                    
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-green-100 dark:border-gray-700 p-6 text-center relative overflow-hidden">
                        <div class="absolute top-0 left-0 w-full h-24 bg-gradient-to-r from-green-400 to-emerald-600"></div>
                        
                        <div class="relative z-10 -mt-10">
                            <div class="mx-auto w-24 h-24 rounded-full bg-white dark:bg-gray-900 border-4 border-white dark:border-gray-800 flex items-center justify-center shadow-md">
                                <span class="text-3xl font-bold text-green-600 dark:text-green-400">
                                    {{ substr($user->name, 0, 1) }}
                                </span>
                            </div>
                            
                            <h3 class="mt-4 text-xl font-bold text-gray-900 dark:text-white">{{ $user->name }}</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
                            
                            <div class="mt-4 flex justify-center gap-2">
                                <span class="px-3 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 text-xs font-bold rounded-full border border-green-200 dark:border-green-800">
                                    Petani Admin
                                </span>
                                <span class="px-3 py-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-xs font-bold rounded-full">
                                    Aktif
                                </span>
                            </div>
                        </div>
                    </div>

                    <nav class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-green-100 dark:border-gray-700 overflow-hidden">
                        <button @click="activeTab = 'profile'" 
                                class="w-full flex items-center gap-3 px-6 py-4 text-left transition-all border-l-4"
                                :class="activeTab === 'profile' 
                                    ? 'bg-green-50 dark:bg-green-900/20 border-green-500 text-green-700 dark:text-green-400 font-bold' 
                                    : 'border-transparent text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700'">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            Informasi Profil
                        </button>
                        
                        <div class="border-t border-gray-100 dark:border-gray-700"></div>

                        <button @click="activeTab = 'password'" 
                                class="w-full flex items-center gap-3 px-6 py-4 text-left transition-all border-l-4"
                                :class="activeTab === 'password' 
                                    ? 'bg-green-50 dark:bg-green-900/20 border-green-500 text-green-700 dark:text-green-400 font-bold' 
                                    : 'border-transparent text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700'">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            Keamanan & Password
                        </button>

                        <div class="border-t border-gray-100 dark:border-gray-700"></div>

                        <button @click="activeTab = 'delete'" 
                                class="w-full flex items-center gap-3 px-6 py-4 text-left transition-all border-l-4"
                                :class="activeTab === 'delete' 
                                    ? 'bg-red-50 dark:bg-red-900/20 border-red-500 text-red-700 dark:text-red-400 font-bold' 
                                    : 'border-transparent text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700'">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            Hapus Akun
                        </button>
                    </nav>
                </div>

                <div class="lg:col-span-2">
                    
                    <div x-show="activeTab === 'profile'" 
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 translate-x-4"
                         x-transition:enter-end="opacity-100 translate-x-0"
                         class="bg-white dark:bg-gray-800 p-6 sm:p-8 rounded-2xl shadow-lg border border-green-100 dark:border-gray-700">
                        <div class="max-w-xl">
                            @include('profile.partials.update-profile-information-form')
                        </div>
                    </div>

                    <div x-show="activeTab === 'password'" style="display: none;"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 translate-x-4"
                         x-transition:enter-end="opacity-100 translate-x-0"
                         class="bg-white dark:bg-gray-800 p-6 sm:p-8 rounded-2xl shadow-lg border border-green-100 dark:border-gray-700">
                        <div class="max-w-xl">
                            @include('profile.partials.update-password-form')
                        </div>
                    </div>

                    <div x-show="activeTab === 'delete'" style="display: none;"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 translate-x-4"
                         x-transition:enter-end="opacity-100 translate-x-0"
                         class="bg-white dark:bg-gray-800 p-6 sm:p-8 rounded-2xl shadow-lg border border-red-100 dark:border-red-900/50">
                        <div class="max-w-xl">
                            @include('profile.partials.delete-user-form')
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Notifikasi Profile Updated
            @if (session('status') === 'profile-updated')
                setTimeout(() => {
                    window.showAlert('success', 'Berhasil Disimpan', 'Data profil Anda telah berhasil diperbarui.');
                }, 300);
            @endif

            // Notifikasi Password Updated
            @if (session('status') === 'password-updated')
                setTimeout(() => {
                    window.showAlert('success', 'Password Berubah', 'Kata sandi Anda telah berhasil diganti.');
                }, 300);
            @endif
        });
    </script>
</x-app-layout>