<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-100 leading-tight">
                    🗂️ Arsip Deteksi
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Database hasil tangkapan kamera</p>
            </div>
            <div class="px-4 py-1 bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-300 rounded-full text-xs font-bold border border-green-200 dark:border-green-800">
                Total Data: <span x-data x-text="$store.history.totalItems">0</span>
            </div>
        </div>
    </x-slot>

    <div class="py-8" x-data="historyPage()">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-green-100 dark:border-gray-700 p-4 mb-8 sticky top-20 z-20 backdrop-blur-md bg-opacity-95 dark:bg-opacity-95">
                <div class="flex flex-col sm:flex-row gap-4 justify-between items-center">
                    
                    <div class="relative w-full sm:max-w-md group">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400 group-focus-within:text-green-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" 
                               x-model="search" 
                               @input="currentPage = 1" 
                               placeholder="Cari hama (contoh: Tikus)..." 
                               class="block w-full pl-10 pr-4 py-3 border-gray-200 dark:border-gray-600 rounded-xl leading-5 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:outline-none focus:bg-white dark:focus:bg-gray-800 focus:ring-2 focus:ring-green-500/50 focus:border-green-500 transition duration-150 ease-in-out sm:text-sm">
                    </div>
                    
                    <button @click="fetchHistory()" 
                            class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 border border-transparent text-sm font-semibold rounded-xl text-green-700 bg-green-50 hover:bg-green-100 dark:bg-green-900/30 dark:text-green-300 dark:hover:bg-green-900/50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all active:scale-95">
                        <span x-show="loading" class="animate-spin mr-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        </span>
                        <span x-show="!loading" class="mr-2">↻</span>
                        Muat Ulang
                    </button>
                </div>
            </div>

            <div x-show="loading" class="flex flex-col items-center justify-center py-20">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-600 mb-4"></div>
                <p class="text-gray-500 dark:text-gray-400 animate-pulse">Menyiapkan data...</p>
            </div>

            <div x-show="!loading && paginatedItems.length === 0" class="flex flex-col items-center justify-center py-20 bg-white dark:bg-gray-800 rounded-2xl border border-dashed border-gray-300 dark:border-gray-700">
                <div class="w-20 h-20 bg-green-50 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-10 h-10 text-green-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <p class="text-gray-500 dark:text-gray-400 text-lg font-medium">Data kosong.</p>
            </div>

            <div x-show="!loading && paginatedItems.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-8">
                <template x-for="item in paginatedItems" :key="item.id">
                    <div class="group bg-white dark:bg-gray-800 rounded-2xl shadow-sm hover:shadow-xl border border-green-100 dark:border-gray-700 transition-all duration-300 hover:-translate-y-1 overflow-hidden flex flex-col">
                        
                        <div class="relative aspect-[4/3] bg-gray-100 dark:bg-gray-900 overflow-hidden cursor-pointer" @click="openModal(item)">
                            <img :src="'data:image/jpeg;base64,' + item.image" 
                                 loading="lazy" 
                                 class="w-full h-full object-cover transition duration-700 group-hover:scale-110">
                            
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            
                            <div class="absolute top-3 right-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-white/90 dark:bg-black/70 text-gray-800 dark:text-white backdrop-blur-sm shadow-sm">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-1.5"></span>
                                    <span x-text="Math.round(item.confidence) + '%'"></span>
                                </span>
                            </div>
                        </div>

                        <div class="p-5 flex-1 flex flex-col">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <h3 class="font-bold text-lg text-gray-800 dark:text-gray-100 leading-tight group-hover:text-green-600 dark:group-hover:text-green-400 transition-colors" x-text="item.pestNames.join(', ') || 'Tidak Dikenali'"></h3>
                                    
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        <span x-text="formatDate(item.timestamp)"></span>
                                    </p>
                                </div>
                            </div>

                            <div class="mt-auto pt-4 flex gap-3">
                                <button @click="openModal(item)" class="flex-1 inline-flex justify-center items-center px-4 py-2 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-300 text-sm font-semibold rounded-xl hover:bg-green-100 dark:hover:bg-green-900/40 transition-colors">
                                    Detail
                                </button>
                                <button @click="deleteItem(item.id)" class="inline-flex justify-center items-center p-2 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 rounded-xl hover:bg-red-100 dark:hover:bg-red-900/40 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <div x-show="!loading && totalPages > 1" class="flex flex-col sm:flex-row items-center justify-between gap-4 border-t border-gray-200 dark:border-gray-700 pt-6">
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    Menampilkan <span class="font-bold text-gray-800 dark:text-white" x-text="startItem"></span> 
                    sampai <span class="font-bold text-gray-800 dark:text-white" x-text="endItem"></span> 
                    dari <span class="font-bold text-gray-800 dark:text-white" x-text="filteredAllItems.length"></span> hasil
                </div>
                <div class="flex items-center gap-2">
                    <button @click="prevPage" 
                            :disabled="currentPage === 1"
                            class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                        &laquo; Sebelumnya
                    </button>
                    <div class="hidden sm:flex gap-1">
                        <template x-for="page in pagesArray">
                            <button @click="currentPage = page" 
                                    class="w-8 h-8 flex items-center justify-center rounded-lg text-sm font-medium transition-colors"
                                    :class="currentPage === page 
                                        ? 'bg-green-600 text-white shadow-md shadow-green-500/30' 
                                        : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700'"
                                    x-text="page">
                            </button>
                        </template>
                    </div>
                    <button @click="nextPage" 
                            :disabled="currentPage === totalPages"
                            class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                        Selanjutnya &raquo;
                    </button>
                </div>
            </div>
        </div>

        <div x-show="modalOpen" style="display: none;" 
             class="fixed inset-0 z-50 flex items-center justify-center p-4"
             x-transition.opacity>
            
            <div class="absolute inset-0 bg-gray-900/80 backdrop-blur-sm" @click="modalOpen = false"></div>

            <div class="relative bg-white dark:bg-gray-800 rounded-3xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden flex flex-col"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-90 translate-y-4"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="opacity-0 scale-90 translate-y-4">
                 
                 <template x-if="selectedItem">
                    <div class="flex flex-col h-full">
                        <div class="relative bg-gray-100 dark:bg-black flex-1 flex items-center justify-center min-h-[300px] max-h-[60vh] p-4">
                             <button @click="modalOpen = false" class="absolute top-4 right-4 z-10 p-2 bg-black/50 hover:bg-black/70 text-white rounded-full backdrop-blur-md transition-all">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                            <img :src="'data:image/jpeg;base64,' + selectedItem.image" class="max-w-full max-h-full object-contain rounded-lg shadow-lg">
                        </div>
                        
                        <div class="p-6 sm:p-8 bg-white dark:bg-gray-800 border-t border-green-100 dark:border-gray-700">
                            <div class="flex flex-col sm:flex-row justify-between items-start gap-4">
                                <div>
                                    <p class="text-sm font-bold text-green-600 dark:text-green-400 uppercase tracking-wide">Hasil Deteksi</p>
                                    <h3 class="text-3xl font-bold text-gray-900 dark:text-white mt-1" x-text="selectedItem.pestNames.join(', ')"></h3>
                                </div>
                                <div class="flex items-center gap-2 px-4 py-2 bg-green-50 dark:bg-green-900/30 rounded-xl border border-green-200 dark:border-green-800">
                                    <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span class="font-bold text-green-700 dark:text-green-300" x-text="selectedItem.confidence + '% Akurat'"></span>
                                </div>
                            </div>

                            <div class="mt-6 p-4 bg-gray-50 dark:bg-gray-700/30 rounded-2xl">
                                <div class="flex items-center gap-3">
                                    <div class="p-2 bg-white dark:bg-gray-700 rounded-lg shadow-sm">
                                        <svg class="w-6 h-6 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">Waktu Pengambilan</p>
                                        <p class="font-semibold text-gray-800 dark:text-gray-200" x-text="selectedItem.timestamp"></p>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-8 flex justify-end gap-3">
                                <button @click="modalOpen = false" class="px-6 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                    Tutup
                                </button>
                                <button @click="deleteItem(selectedItem.id); modalOpen = false;" class="px-6 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white font-medium shadow-lg shadow-red-500/30 transition transform active:scale-95">
                                    Hapus Permanen
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('history', {
                totalItems: 0
            });
        });

        function historyPage() {
            return {
                items: [],
                search: '',
                loading: true,
                modalOpen: false,
                selectedItem: null,
                currentPage: 1,
                itemsPerPage: 12,

                init() { this.fetchHistory(); },

                // --- HELPER FUNCTION: FORMAT WAKTU ---
                // Sesuai request: Jika < 7 hari (relatif), Jika > 7 hari (Tanggal YYYY-MM-DD)
                formatDate(dateString) {
                    if (!dateString) return '-';
                    
                    const date = new Date(dateString);
                    const now = new Date();
                    
                    // Hitung selisih dalam milidetik
                    const diff = now - date;
                    
                    // Konversi ke satuan waktu
                    const seconds = Math.floor(diff / 1000);
                    const minutes = Math.floor(seconds / 60);
                    const hours = Math.floor(minutes / 60);
                    const days = Math.floor(hours / 24);

                    // LOGIKA TAMPILAN
                    // Jika lebih dari 7 hari (1 minggu), tampilkan Tanggal saja
                    if (days > 7) {
                        const year = date.getFullYear();
                        const month = String(date.getMonth() + 1).padStart(2, '0');
                        const day = String(date.getDate()).padStart(2, '0');
                        return `${year}-${month}-${day}`;
                    }

                    // Jika kurang dari 7 hari, tampilkan format relatif
                    if (days > 0) return `${days} hari lalu`;
                    if (hours > 0) return `${hours} jam lalu`;
                    if (minutes > 0) return `${minutes} menit lalu`;
                    
                    return 'Baru saja';
                },

                get filteredAllItems() {
                    if (this.search === '') return this.items;
                    return this.items.filter(item => {
                        const pests = item.pestNames ? item.pestNames.join(' ').toLowerCase() : '';
                        return pests.includes(this.search.toLowerCase());
                    });
                },
                get paginatedItems() {
                    const start = (this.currentPage - 1) * this.itemsPerPage;
                    const end = start + this.itemsPerPage;
                    return this.filteredAllItems.slice(start, end);
                },
                get totalPages() { return Math.ceil(this.filteredAllItems.length / this.itemsPerPage); },
                get startItem() { return (this.currentPage - 1) * this.itemsPerPage + 1; },
                get endItem() { return Math.min(this.currentPage * this.itemsPerPage, this.filteredAllItems.length); },
                get pagesArray() {
                    let pages = [];
                    for(let i = 1; i <= this.totalPages; i++) {
                        if (i === 1 || i === this.totalPages || (i >= this.currentPage - 1 && i <= this.currentPage + 1)) {
                            pages.push(i);
                        }
                    }
                    return pages; 
                },
                nextPage() { if (this.currentPage < this.totalPages) this.currentPage++; },
                prevPage() { if (this.currentPage > 1) this.currentPage--; },

                async fetchHistory() {
                    this.loading = true;
                    try {
                        const res = await fetch('https://pestdetectionapi-production.up.railway.app/api/history?limit=100');
                        if (res.ok) {
                            this.items = await res.json();
                            Alpine.store('history').totalItems = this.items.length;
                            this.currentPage = 1;
                        }
                    } catch (e) { 
                        window.showAlert('error', 'Gagal', 'Tidak dapat mengambil data history.');
                    } finally { 
                        this.loading = false; 
                    }
                },

                openModal(item) {
                    this.selectedItem = item;
                    this.modalOpen = true;
                },

                async deleteItem(id) {
                    const confirmed = await window.showConfirm('Hapus Permanen?', 'Data yang dihapus tidak bisa dikembalikan.');
                    if (!confirmed) return;
                    
                    try {
                        const res = await fetch(`https://pestdetectionapi-production.up.railway.app/api/delete/${id}`, { method: 'DELETE' });
                        const data = await res.json();
                        if (data.success) {
                            this.items = this.items.filter(i => i.id !== id);
                            Alpine.store('history').totalItems = this.items.length;
                            if (this.paginatedItems.length === 0 && this.currentPage > 1) this.currentPage--;
                            if(this.selectedItem && this.selectedItem.id === id) this.modalOpen = false;
                            window.showAlert('success', 'Terhapus', 'Data berhasil dihapus.');
                        } else { 
                            window.showAlert('error', 'Gagal', data.error);
                        }
                    } catch (e) { 
                        window.showAlert('error', 'Error', 'Terjadi kesalahan koneksi.');
                    }
                }
            }
        }
    </script>
</x-app-layout>