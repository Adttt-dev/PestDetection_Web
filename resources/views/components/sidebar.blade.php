<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
       class="fixed inset-y-0 left-0 z-50 w-64 h-screen bg-white dark:bg-gray-800 border-r border-green-100 dark:border-gray-700 transition-transform duration-300 ease-in-out lg:translate-x-0 flex flex-col shadow-xl lg:shadow-none">
    
    <div class="h-16 flex items-center justify-center border-b border-green-100 dark:border-gray-700 shrink-0 bg-white dark:bg-gray-800">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2 font-bold text-xl text-green-700 dark:text-green-400">
            <span class="text-3xl">🌱</span>
            <span class="tracking-tight">PestDetection</span>
        </a>
    </div>

    <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
        <p class="px-4 text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Menu Utama</p>
        
        <a href="{{ route('dashboard') }}" 
           class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 font-bold shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:bg-green-50 dark:hover:bg-gray-700 hover:text-green-600 dark:hover:text-green-300' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
            Dashboard
        </a>

        <a href="{{ route('history') }}" 
           class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('history') ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 font-bold shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:bg-green-50 dark:hover:bg-gray-700 hover:text-green-600 dark:hover:text-green-300' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            Riwayat
        </a>
        
        <p class="px-4 text-xs font-bold text-gray-400 uppercase tracking-wider mt-6 mb-2">Pengaturan</p>
        
        <a href="{{ route('profile.edit') }}" 
           class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('profile.edit') ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 font-bold shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:bg-green-50 dark:hover:bg-gray-700 hover:text-green-600 dark:hover:text-green-300' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
            Profile Saya
        </a>
    </nav>

    <div class="p-4 border-t border-green-100 dark:border-gray-700 shrink-0 bg-white dark:bg-gray-800">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="flex w-full items-center gap-3 px-4 py-2 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-xl transition-colors font-medium">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                Logout
            </button>
        </form>
    </div>
</aside>