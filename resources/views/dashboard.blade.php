<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col">
            <h2 class="font-bold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Monitoring Lahan Pintar
            </h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">Panel Kontrol & Statistik Pertanian</p>
        </div>
    </x-slot>

    <div x-data="{
            system: { esp32_online: false, mqtt_connected: false, database: 'loading', esp32_system_enabled: false, esp32_camera_sleep_mode: true },
            stats: { total_detections: '-', today_detections: '-', most_detected_pest: '-', most_detected_count: 0 },
            latest: { hasData: false, pests: [], confidence: 0 },
            history: [], // Data untuk galeri foto terbaru
            loading: true,
            
            // State untuk Tombol & Modal
            processingCapture: false,
            processingToggle: false,
            modalOpen: false,
            selectedItem: null,
            
            async init() {
                await this.fetchStatus();
                this.fetchStats();
                await this.fetchLatest(); 
                this.fetchHistory();
                this.loading = false;
                
                // Auto refresh setiap 5 detik
                setInterval(() => { this.fetchStatus(); this.fetchStats(); }, 5000);
            },

            // --- API CALLS ---
            async fetchStatus() {
                try {
                    const res = await fetch('https://pestdetectionapi-production.up.railway.app/ping');
                    if(res.ok) {
                        const data = await res.json();
                        this.system = { ...this.system, ...data };
                        this.system.esp32_system_enabled = Boolean(this.system.esp32_system_enabled);
                        this.system.esp32_camera_sleep_mode = Boolean(this.system.esp32_camera_sleep_mode);
                    }
                } catch(e) {}
            },

            async fetchStats() {
                try {
                    const res = await fetch('https://pestdetectionapi-production.up.railway.app/api/stats');
                    if(res.ok) this.stats = await res.json();
                } catch(e) {}
            },

            async fetchLatest() {
                try {
                    const res = await fetch('https://pestdetectionapi-production.up.railway.app/data');
                    if(res.ok) {
                        const data = await res.json();
                        if (data.newDetection) {
                            this.latest = {
                                hasData: true,
                                pests: data.pestNames,
                                confidence: data.confidence,
                                time: data.detectionTime
                            };
                        }
                    }
                } catch(e) {}
            },

            async fetchHistory() {
                try {
                    // Ambil 6 data agar pas dengan layout lebar (Grid 3 kolom x 2 baris)
                    const res = await fetch('https://pestdetectionapi-production.up.railway.app/api/history?limit=6');
                    if(res.ok) this.history = await res.json();
                } catch(e) {}
            },

            // --- ACTIONS ---
            async toggleSwitch() {
                if (this.processingToggle) return;

                const targetState = !this.system.esp32_system_enabled;
                const title = targetState ? 'Aktifkan Sistem?' : 'Matikan Sistem?';
                const message = targetState 
                    ? 'Kamera akan mulai mendeteksi hama secara otomatis.' 
                    : 'Kamera akan berhenti mendeteksi hama. Anda masih bisa mengambil foto manual.';
                
                const confirmed = await window.showConfirm(title, message);
                
                if(!confirmed) {
                    this.system.esp32_system_enabled = !this.system.esp32_system_enabled;
                    this.$nextTick(() => { this.system.esp32_system_enabled = !targetState; });
                    return; 
                }

                this.processingToggle = true;
                this.system.esp32_system_enabled = targetState;

                try {
                    await fetch('https://pestdetectionapi-production.up.railway.app/api/system/control', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/json'},
                        body: JSON.stringify({ active: targetState })
                    });
                    await this.fetchStatus();
                    window.showAlert('success', 'Berhasil', targetState ? 'Sistem diaktifkan.' : 'Sistem dinonaktifkan.');
                } catch(e) { 
                    window.showAlert('error', 'Gagal', 'Koneksi bermasalah.');
                    this.system.esp32_system_enabled = !targetState;
                } finally {
                    this.processingToggle = false;
                }
            },

            async triggerCapture() {
                this.processingCapture = true;
                try {
                    const res = await fetch('https://pestdetectionapi-production.up.railway.app/api/trigger-capture', { method: 'POST' });
                    const data = await res.json();
                    if (data.success) {
                        window.showAlert('success', 'Terkirim', 'Meminta kamera mengambil gambar...');
                        setTimeout(() => { this.fetchLatest(); this.fetchHistory(); }, 3000);
                    } else {
                        window.showAlert('error', 'Gagal', data.error);
                    }
                } catch(e) { window.showAlert('error', 'Error', 'Koneksi putus.'); } 
                finally { this.processingCapture = false; }
            },

            // --- MODAL LOGIC (Sama seperti History) ---
            openModal(item) {
                this.selectedItem = item;
                this.modalOpen = true;
            },

            async deleteItem(id) {
                const confirmed = await window.showConfirm('Hapus Foto?', 'Data ini akan dihapus permanen.');
                if (!confirmed) return;
                
                try {
                    const res = await fetch(`https://pestdetectionapi-production.up.railway.app/api/delete/${id}`, { method: 'DELETE' });
                    const data = await res.json();
                    if (data.success) {
                        // Hapus dari list dashboard
                        this.history = this.history.filter(i => i.id !== id);
                        if(this.selectedItem && this.selectedItem.id === id) this.modalOpen = false;
                        window.showAlert('success', 'Terhapus', 'Foto berhasil dihapus.');
                    } else { 
                        window.showAlert('error', 'Gagal', data.error);
                    }
                } catch (e) { 
                    window.showAlert('error', 'Error', 'Gagal menghubungi server.');
                }
            },

            // --- HELPER TIME ---
            formatDate(dateString) {
                if (!dateString) return '-';
                const date = new Date(dateString);
                const now = new Date();
                const diff = now - date;
                const seconds = Math.floor(diff / 1000);
                const minutes = Math.floor(seconds / 60);
                const hours = Math.floor(minutes / 60);
                const days = Math.floor(hours / 24);

                if (days > 7) {
                    const year = date.getFullYear();
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    const day = String(date.getDate()).padStart(2, '0');
                    return `${year}-${month}-${day}`;
                }
                if (days > 0) return `${days} hari lalu`;
                if (hours > 0) return `${hours} jam lalu`;
                if (minutes > 0) return `${minutes} menit lalu`;
                return 'Baru saja';
            }

         }" class="space-y-6">

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-green-100 dark:border-gray-700 p-6 sm:p-8">
            <div class="flex flex-col md:flex-row items-center justify-between gap-8">
                <div class="flex items-center gap-6 w-full md:w-auto">
                    <button @click="toggleSwitch()" 
                            :disabled="processingToggle"
                            class="relative inline-flex h-14 w-28 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus-visible:ring-2 focus-visible:ring-green-600 focus-visible:ring-offset-2"
                            :class="system.esp32_system_enabled ? 'bg-green-600' : 'bg-gray-200 dark:bg-gray-700'">
                        <span class="sr-only">Use setting</span>
                        <span class="pointer-events-none inline-block h-12 w-12 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out mt-0.5 ml-0.5"
                              :class="system.esp32_system_enabled ? 'translate-x-14' : 'translate-x-0'">
                            <span class="absolute inset-0 flex items-center justify-center transition-opacity" 
                                  :class="system.esp32_system_enabled ? 'opacity-0 ease-out duration-100' : 'opacity-100 ease-in duration-200'" aria-hidden="true">
                                <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </span>
                            <span class="absolute inset-0 flex items-center justify-center transition-opacity" 
                                  :class="system.esp32_system_enabled ? 'opacity-100 ease-in duration-200' : 'opacity-0 ease-out duration-100'" aria-hidden="true">
                                <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            </span>
                        </span>
                    </button>
                    <div>
                        <h3 class="text-lg font-bold" 
                            :class="system.esp32_system_enabled ? 'text-green-600 dark:text-green-400' : 'text-gray-500 dark:text-gray-400'"
                            x-text="processingToggle ? 'Memproses...' : (system.esp32_system_enabled ? 'Sistem AKTIF' : 'Sistem MATI')">
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            <span x-show="system.esp32_system_enabled">Monitoring berjalan otomatis.</span>
                            <span x-show="!system.esp32_system_enabled">Monitoring dinonaktifkan.</span>
                        </p>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row items-center gap-4 w-full md:w-auto justify-end">
                    <button @click="triggerCapture()" :disabled="processingCapture"
                            class="w-full sm:w-auto flex items-center justify-center gap-2 px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-xl font-semibold shadow-lg shadow-green-200 dark:shadow-none transition-all active:scale-95 disabled:opacity-70 disabled:cursor-not-allowed">
                        <svg x-show="!processingCapture" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        <svg x-show="processingCapture" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <span>Ambil Foto</span>
                    </button>

                    <div class="flex flex-row md:flex-col gap-2">
                        <div class="flex items-center gap-2 px-3 py-1 rounded-lg border text-sm font-medium transition-colors"
                             :class="!system.esp32_online 
                                ? 'bg-red-50 border-red-200 text-red-700 dark:bg-red-900/20 dark:border-red-800 dark:text-red-400' 
                                : (system.esp32_camera_sleep_mode 
                                    ? 'bg-yellow-50 border-yellow-200 text-yellow-700 dark:bg-yellow-900/20 dark:border-yellow-800 dark:text-yellow-400'
                                    : 'bg-green-50 border-green-200 text-green-700 dark:bg-green-900/20 dark:border-green-800 dark:text-green-400')">
                            <span class="relative flex h-2.5 w-2.5">
                                <span x-show="system.esp32_online && !system.esp32_camera_sleep_mode" class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2.5 w-2.5" :class="!system.esp32_online ? 'bg-red-500' : (system.esp32_camera_sleep_mode ? 'bg-yellow-500' : 'bg-green-500')"></span>
                            </span>
                            <span x-text="!system.esp32_online ? 'Kamera Offline' : (system.esp32_camera_sleep_mode ? 'Kamera Sleep' : 'Kamera Aktif')"></span>
                        </div>
                        <div class="flex items-center gap-2 px-3 py-1 bg-teal-50 dark:bg-teal-900/20 rounded-lg border border-teal-100 dark:border-teal-800">
                            <span class="w-2 h-2 rounded-full" :class="system.mqtt_connected ? 'bg-teal-500' : 'bg-red-500'"></span>
                            <span class="text-xs font-medium text-teal-700 dark:text-teal-300" x-text="system.mqtt_connected ? 'Server OK' : 'Server Putus'"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl p-6 text-white shadow-lg shadow-green-500/20">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-green-100 text-sm font-medium mb-1">Total Deteksi</p>
                        <h3 class="text-3xl font-bold" x-text="stats.total_detections"></h3>
                    </div>
                    <div class="p-2 bg-white/20 rounded-lg"><svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 002 2h2a2 2 0 002-2z"></path></svg></div>
                </div>
                <p class="text-xs text-green-100 mt-4">Sepanjang waktu</p>
            </div>
            <div class="bg-gradient-to-br from-teal-500 to-teal-600 rounded-2xl p-6 text-white shadow-lg shadow-teal-500/20">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-teal-100 text-sm font-medium mb-1">Hari Ini</p>
                        <h3 class="text-3xl font-bold" x-text="stats.today_detections"></h3>
                    </div>
                    <div class="p-2 bg-white/20 rounded-lg"><svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg></div>
                </div>
                <p class="text-xs text-teal-100 mt-4">Pukul 00:00 - Sekarang</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg border border-green-100 dark:border-gray-700">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-gray-500 dark:text-gray-400 text-sm font-medium mb-1">Paling Sering Muncul</p>
                        <h3 class="text-xl font-bold text-gray-800 dark:text-white truncate" x-text="stats.most_detected_pest"></h3>
                        <p class="text-sm font-semibold text-green-600 dark:text-green-400 mt-1" x-text="stats.most_detected_count + ' kali'"></p>
                    </div>
                    <div class="p-2 bg-green-50 dark:bg-green-900/30 rounded-lg"><svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg></div>
                </div>
                <p class="text-xs text-gray-400 mt-4">Berdasarkan data statistik</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <div class="lg:col-span-1 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-green-100 dark:border-gray-700 p-6 h-full">
                <h3 class="font-bold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"></path></svg>
                    Informasi Perangkat
                </h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center py-3 border-b dark:border-gray-700">
                        <span class="text-gray-500 dark:text-gray-400 text-sm">Mode Kamera</span>
                        <span class="font-medium text-gray-800 dark:text-white" x-text="system.esp32_camera_sleep_mode ? 'Hemat Daya (Sleep)' : 'Siaga (Active)'"></span>
                    </div>
                    <div class="flex justify-between items-center py-3 border-b dark:border-gray-700">
                        <span class="text-gray-500 dark:text-gray-400 text-sm">Komunikasi Terakhir</span>
                        <span class="font-mono text-gray-800 dark:text-white bg-green-50 dark:bg-gray-700 px-2 py-1 rounded text-xs" x-text="system.esp32_last_seen_seconds_ago ? system.esp32_last_seen_seconds_ago + ' detik yang lalu' : '-'"></span>
                    </div>
                    <div class="flex justify-between items-center py-3">
                        <span class="text-gray-500 dark:text-gray-400 text-sm">Zona Waktu</span>
                        <span class="font-medium text-gray-800 dark:text-white" x-text="system.timezone || 'Asia/Jakarta'"></span>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-green-100 dark:border-gray-700 p-6 flex flex-col">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-bold text-gray-800 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        Foto Terbaru
                    </h3>
                    <a href="{{ route('history') }}" class="text-sm text-green-600 dark:text-green-400 hover:underline font-medium">Lihat Semua Galeri &rarr;</a>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 flex-1">
                    <template x-for="item in history" :key="item.id">
                        <div class="relative rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-900 border border-green-100 dark:border-gray-700 aspect-video group cursor-pointer hover:shadow-lg transition-all"
                             @click="openModal(item)">
                            <img :src="'data:image/jpeg;base64,' + item.image" loading="lazy" class="w-full h-full object-cover transition duration-300 group-hover:scale-110">
                            
                            <div class="absolute inset-0 bg-black/20 group-hover:bg-black/0 transition-colors"></div>
                            
                            <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/90 to-transparent p-3 pt-6">
                                <div class="flex justify-between items-end">
                                    <div>
                                        <p class="text-[11px] text-white font-bold truncate" x-text="item.pestNames.join(', ')"></p>
                                        <p class="text-[10px] text-gray-300" x-text="formatDate(item.timestamp)"></p>
                                    </div>
                                    <span class="text-[10px] bg-green-600 text-white px-1.5 py-0.5 rounded font-bold" x-text="item.confidence + '%'"></span>
                                </div>
                            </div>
                        </div>
                    </template>
                    <div x-show="history.length === 0" class="col-span-full flex flex-col items-center justify-center text-gray-400 text-sm py-12">
                        <svg class="w-10 h-10 mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        Belum ada foto yang masuk.
                    </div>
                </div>
            </div>
        </div>

        <div x-show="modalOpen" style="display: none;" 
             class="fixed inset-0 z-50 flex items-center justify-center p-4"
             x-transition.opacity>
            <div class="absolute inset-0 bg-gray-900/80 backdrop-blur-sm" @click="modalOpen = false"></div>
            <div class="relative bg-white dark:bg-gray-800 rounded-3xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden flex flex-col" x-transition.scale>
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
                                    Hapus
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

    </div>
</x-app-layout>