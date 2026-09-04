
    <!-- SIDEBAR KANAN: Standings (Desktop Only & Sticky ketika di-scroll) -->
    <aside class="hidden lg:block lg:col-span-3 lg:sticky lg:top-8 h-fit space-y-6">
        <!-- LIVE CHAT CARD -->
        <a id="chat-card-desktop" href="<?= base_url('chat'); ?>" class="glass-card hidden group overflow-hidden hover:bg-white/[0.02] transition-all duration-300">
            <div class="p-4">
                <div class="flex items-center justify-between mb-2.5">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-red-500/10 border border-red-500/20 flex items-center justify-center flex-shrink-0">
                            <i data-lucide="message-circle" class="w-4 h-4 text-red-400"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-[9px] font-syne uppercase tracking-widest text-red-400 font-bold">Live Chat</p>
                            <p id="chat-card-session-desktop" class="text-[10px] text-slate-500 font-medium truncate">...</p>
                        </div>
                    </div>
                    <span class="text-[9px] font-semibold px-2.5 py-1 rounded-full border text-red-400 border-red-500/20 bg-red-500/5 group-hover:bg-red-500/10 transition-colors flex-shrink-0">
                        Masuk <i data-lucide="arrow-right" class="w-3 h-3 inline-block -mt-0.5"></i>
                    </span>
                </div>
                <p id="chat-card-event-desktop" class="text-[10px] text-slate-500 leading-relaxed truncate">...</p>
            </div>
        </a>

        <div class="glass-card p-4 border border-white/[0.05] rounded-xl bg-slate-950/40 backdrop-blur-md">

        <!-- Container Standings -->
<div class="bg-slate-900 p-4 rounded-xl max-w-sm">
    <!-- Header -->
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-2">
            <span class="w-1 h-3 bg-red-500 rounded-full animate-pulse"></span>
            <h3 class="font-syne text-xs font-bold uppercase tracking-widest text-slate-200">Championship Standings</h3>
        </div>
        <a href="#" class="text-slate-500 hover:text-red-400 transition-colors p-1 rounded-md hover:bg-white/[0.03]">
            <i data-lucide="chevron-right" class="w-4 h-4"></i>
        </a>
    </div>

    <!-- Standings List (Diyenis secara dinamis lewat JS) -->
    <div id="driver-standings-list" class="space-y-1.5">
        <!-- Efek Loading sebelum data muncul -->
        <div class="text-center py-4 text-xs text-slate-500 animate-pulse">
            Loading Data...
        </div>
    </div>
</div>

</div>
    </aside>
</div>


<script>
    // Helper untuk mapping warna border berdasarkan ID tim dari API
    const getTeamColorClass = (constructorId) => {
        const colors = {
            'red_bull': 'border-blue-500',
            'mclaren': 'border-orange-500',
            'ferrari': 'border-red-600',
            'mercedes': 'border-teal-400',
            'aston_martin': 'border-emerald-700',
            'alpine': 'border-blue-400',
            'haas': 'border-slate-400',
            'rb': 'border-blue-600', // Visa Cash App RB
            'sauber': 'border-lime-500', // Kick Sauber
            'williams': 'border-sky-500'
        };
        return colors[constructorId] || 'border-slate-600'; // fallback color
    };

    // Fungsi fetch data dari Open F1 / Ergast API clone
    async function fetchF1Standings() {
        const container = document.getElementById('driver-standings-list');
        
        try {
            // Menggunakan API publik Ergast (saat ini di-maintain oleh komunitas secara free)
            const response = await fetch('https://api.jolpi.ca/ergast/f1/current/driverStandings.json');
            const data = await response.json();
            
            // Ambil list driver standings
            const standings = data.MRData.StandingsTable.StandingsLists[0].DriverStandings;
            
            // Potong hanya ambil Top 10
            const top10 = standings.slice(0, 5);
            
            // Reset container loading
            container.innerHTML = '';
            
            // Render HTML untuk tiap driver
            top10.forEach(item => {
                const position = item.position;
                const points = item.points;
                const driver = item.Driver;
                const constructor = item.Constructors[0]; // Ambil tim saat ini
                
                // Format nama: Inisial depan + Nama belakang (misal: M. Verstappen)
                const shortName = `${driver.givenName.charAt(0)}. ${driver.familyName}`;
                const nationality = driver.nationality.substring(0, 3).toUpperCase();
                const borderClass = getTeamColorClass(constructor.constructorId);

                const rowHtml = `
                    <div class="group flex items-center justify-between bg-white/[0.02] hover:bg-white/[0.04] border-l-2 ${borderClass} p-2.5 rounded-r-md transition-all duration-200">
                        <div class="flex items-center gap-3">
                            <span class="font-syne text-xs font-black text-slate-400 group-hover:text-white transition-colors w-4">${escapeHtml(position)}</span>
                            <div>
                                <div class="flex items-center gap-1.5">
                                    <p class="text-xs font-bold text-slate-200 group-hover:text-white transition-colors">${escapeHtml(shortName)}</p>
                                    <span class="text-[8px] text-slate-600 font-mono">${escapeHtml(nationality)}</span>
                                </div>
                                <p class="text-[9px] text-slate-500 font-medium tracking-wider uppercase mt-0.5">${escapeHtml(constructor.name)}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="font-mono text-xs font-bold text-slate-200 tracking-tight">${escapeHtml(points)} <span class="text-[9px] text-slate-500 font-normal">PTS</span></span>
                        </div>
                    </div>
                `;
                
                container.innerHTML += rowHtml;
            });
            
        } catch (error) {
            console.error('Gagal mengambil data F1:', error);
            container.innerHTML = `<div class="text-center py-4 text-xs text-red-400">Failed to load standings.</div>`;
        }
    }

    // Jalankan fungsi saat halaman load
    document.addEventListener('DOMContentLoaded', fetchF1Standings);
</script>