<!-- CONTAINER UTAMA -->
<div class="max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-12 py-4 lg:py-8 grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8">
    
    <!-- SIDEBAR KIRI: Desktop Only (Sticky ketika di-scroll) -->
    <aside class="hidden lg:block lg:col-span-3 lg:sticky lg:top-8 h-fit space-y-6">
        <div class="glass-card p-3.5 space-y-1">
            <p class="text-[10px] font-syne uppercase tracking-widest text-slate-500 px-4 mb-2">Menu</p>
            <a href="<?= base_url('home'); ?>" class="nav-sidebar flex items-center gap-3 px-4 py-3 rounded-full font-medium transition-all">
                <i data-lucide="layout-grid" class="w-4 h-4"></i> <span class="text-xs">Feed</span>
            </a>
            <a href="<?= base_url('race-hub'); ?>" class="nav-sidebar flex items-center gap-3 px-4 py-3 rounded-full font-medium transition-all">
                <i data-lucide="calendar" class="w-4 h-4"></i> <span class="text-xs">Race Hub</span>
            </a>
            <a href="#" class="flex items-center gap-3 px-4 py-3 text-slate-400 hover:text-white hover:bg-white/[0.02] rounded-full transition-all">
                <i data-lucide="sparkles" class="w-4 h-4"></i> <span class="text-xs">Border Shop</span>
            </a>
            <a href="#" class="flex items-center gap-3 px-4 py-3 text-slate-400 hover:text-white hover:bg-white/[0.02] rounded-full transition-all">
                <i data-lucide="gamepad-2" class="w-4 h-4"></i> <span class="text-xs">Games</span>
            </a>
        </div>

<!-- Countdown versi Desktop (Redesigned) -->
<div class="glass-card p-5 relative overflow-hidden group rounded-[24px] border border-white/[0.05] bg-gradient-to-b from-slate-900/80 to-slate-950/90 shadow-2xl">
    <div class="absolute inset-0 opacity-[0.02] bg-[linear-gradient(to_right,#ffffff_1px,transparent_1px),linear-gradient(to_bottom,#ffffff_1px,transparent_1px)] bg-[size:16px_16px] pointer-events-none"></div>
    <div class="absolute -right-10 -bottom-10 w-32 h-32 bg-gradient-to-br from-red-500/10 to-transparent blur-2xl pointer-events-none rounded-full"></div>
    
    <div class="flex items-center justify-between mb-4 relative z-10">
        <div class="flex items-center gap-2">
            <span class="relative flex h-2 w-2">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
            </span>
            <span class="text-[10px] font-syne uppercase tracking-widest text-emerald-400 font-bold bg-emerald-500/5 border border-emerald-500/10 px-2.5 py-1 rounded-full backdrop-blur-sm">
                Live Timing
            </span>
        </div>
        <div class="p-1.5 bg-white/[0.02] border border-white/[0.05] rounded-lg text-slate-400 group-hover:text-red-400 transition-colors">
            <i data-lucide="timer" class="w-3.5 h-3.5 animate-spin-slow"></i>
        </div>
    </div>

    <div class="space-y-1 relative z-10">
        <span id="event-label-desktop" class="text-[10px] font-mono tracking-wider text-slate-500 uppercase">Next Grand Prix</span>
        <h3 id="event-name-desktop" class="font-syne text-xl tracking-tight uppercase font-extrabold bg-gradient-to-r from-white via-slate-200 to-slate-400 bg-clip-text text-transparent">
            Loading...
        </h3>
        <p class="text-xs text-slate-400 font-medium flex items-center gap-1.5">
            <i data-lucide="map-pin" class="w-3 h-3 text-red-500"></i> 
            
            <span id="event-location-desktop">Loading...</span>
        </p>
    </div>

    <div class="mt-5 pt-4 border-t border-dashed border-white/[0.06] flex items-center justify-between relative z-10">
        <div class="flex flex-col">
            <span class="text-[9px] font-mono text-slate-500 uppercase tracking-wider">Next Session</span>
            <span id="session-desktop" class="font-syne text-xs font-bold text-slate-200 tracking-wide bg-white/[0.04] border border-white/[0.05] px-2 py-0.5 rounded-md mt-0.5">
                ...
            </span>
        </div>
            
        <div class="flex flex-col items-end">
            <span id="timer-label-desktop" class="text-[9px] font-mono text-slate-500 uppercase tracking-wider mb-1">Time Remaining</span>
            
            <span id="timer-desktop" class="font-mono text-sm font-bold text-orange-400 bg-gradient-to-r from-orange-500/10 to-amber-500/5 border border-orange-500/20 px-3 py-1 rounded-xl shadow-[0_0_15px_rgba(249,115,22,0.05)] tracking-wide">
                -
            </span>
        </div>
    </div>
</div>
    </aside>

    <!-- KONTEN TENGAH (UTAMA DI HP & DESKTOP) -->
    <main class="col-span-1 lg:col-span-6 space-y-4 sm:space-y-6">
        <?php if (isset($show_category) && $show_category === true): ?>
        <?php $this->load->view('layout/category_tags'); ?>
        <?php endif; ?>
        
<!-- LIVE COUNTDOWN BANNER (MOBILE ONLY - Redesigned) -->
<div class="block lg:hidden glass-card overflow-hidden rounded-2xl border border-white/[0.05] bg-gradient-to-b from-slate-900/90 to-slate-950/95 shadow-xl relative p-4">
    <div class="absolute inset-0 opacity-[0.02] bg-[linear-gradient(to_right,#ffffff_1px,transparent_1px),linear-gradient(to_bottom,#ffffff_1px,transparent_1px)] bg-[size:12px_12px] pointer-events-none"></div>
    <div class="absolute -right-6 -bottom-6 w-20 h-20 bg-gradient-to-br from-red-500/10 to-transparent blur-xl pointer-events-none rounded-full"></div>
    
    <div class="flex items-center justify-between pb-3 border-b border-dashed border-white/[0.06] relative z-10">
        <div class="flex items-center gap-2">
            <span class="relative flex h-2 w-2">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-emerald-500"></span>
            </span>
            <h3 id="event-name-mobile" class="font-syne text-xs tracking-tight uppercase font-extrabold bg-gradient-to-r from-white via-slate-200 to-slate-400 bg-clip-text text-transparent">
                Loading...
            </h3>
        </div>
        <span id="event-location-mobile" class="text-[9px] font-mono text-slate-500 uppercase tracking-wider">Loading...</span>
    </div>

    <div class="flex items-center justify-between pt-3 relative z-10">
        <div class="flex items-center gap-1.5">
            <span class="text-[9px] font-mono text-slate-500 uppercase">Session:</span>
            <span id="session-mobile" class="font-syne text-[10px] font-bold text-slate-200 tracking-wide bg-white/[0.04] border border-white/[0.05] px-2 py-0.5 rounded">
                ...
            </span>
        </div>
        
        <span id="timer-mobile" class="font-mono text-xs font-bold text-orange-400 bg-gradient-to-r from-orange-500/10 to-amber-500/5 border border-orange-500/20 px-2.5 py-1 rounded-lg tracking-wide shadow-[0_0_15px_rgba(249,115,22,0.03)]">
            ...
        </span>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const desktopTimer = document.getElementById("timer-desktop");
    const mobileTimer = document.getElementById("timer-mobile");

    const desktopLabel = document.getElementById("timer-label-desktop");
    const desktopEventLabel = document.getElementById("event-label-desktop");
    
    // Element info event dan session
    const desktopEvent = document.getElementById("event-name-desktop");
    const desktopLocation = document.getElementById("event-location-desktop");
    const mobileLocation = document.getElementById("event-location-mobile");
    const mobileEvent = document.getElementById("event-name-mobile");
    const desktopSession = document.getElementById("session-desktop");
    const mobileSession = document.getElementById("session-mobile");
    
    
    if (!desktopTimer) return;

    let countDownDate = null;
    let currentStatus = "";
    let countdownFinished = false;
    let x = null; 

    // ============================================================
    // FUNGSI KHUSUS RENDER STATUS BENDERA / LIVE SESSION
    // ============================================================
    function renderLiveStatus() {
        let liveText = "";
        let desktopClass = "";
        let mobileClass = "";

        if (desktopLabel) desktopLabel.innerText = "Session Info";
        if (desktopEventLabel) desktopEventLabel.innerText = "CURRENT GRAND PRIX";

        // Menggunakan text-xs (12px) agar pas dengan ukuran box timer aslimu
        switch (currentStatus) {
            case "YELLOW FLAG":
                liveText = "YELLOW FLAG";
                desktopClass = "font-mono text-xs font-bold text-yellow-400 bg-yellow-500/10 border border-yellow-500/20 px-3 py-1 rounded-xl shadow-[0_0_15px_rgba(234,179,8,0.05)] tracking-wide";
                mobileClass = "text-xs font-mono font-bold text-yellow-400 bg-yellow-500/10 border border-yellow-500/20 px-2.5 py-1 rounded-lg";
                break;
            case "RED FLAG":
                liveText = "RED FLAG";
                desktopClass = "font-mono text-xs font-bold text-red-400 bg-red-500/10 border border-red-500/20 px-3 py-1 rounded-xl shadow-[0_0_15px_rgba(239,68,68,0.05)] tracking-wide animate-pulse";
                mobileClass = "text-xs font-mono font-bold text-red-400 bg-red-500/10 border border-red-500/20 px-2.5 py-1 rounded-lg animate-pulse";
                break;
            case "VSC":
                liveText = "VSC";
                desktopClass = "font-mono text-xs font-bold text-amber-500 bg-amber-500/10 border border-amber-500/20 px-3 py-1 rounded-xl shadow-[0_0_15px_rgba(245,158,11,0.05)] tracking-wide animate-pulse";
                mobileClass = "text-xs font-mono font-bold text-amber-500 bg-amber-500/10 border border-amber-500/20 px-2.5 py-1 rounded-lg animate-pulse";
                break;
            case "SC":
                liveText = "SAFETY CAR";
                desktopClass = "font-mono text-xs font-bold text-orange-400 bg-orange-500/10 border border-orange-500/20 px-3 py-1 rounded-xl shadow-[0_0_15px_rgba(249,115,22,0.05)] tracking-wide animate-pulse";
                mobileClass = "text-xs font-mono font-bold text-orange-400 bg-orange-500/10 border border-orange-500/20 px-2.5 py-1 rounded-lg animate-pulse";
                break;
            case "FINISHED":
                liveText = "FINISHED";
                desktopClass = "font-mono text-xs font-bold text-slate-300 bg-white/[0.04] border border-white/[0.08] px-3 py-1 rounded-xl tracking-wide";
                mobileClass = "text-xs font-mono font-bold text-slate-300 bg-white/[0.04] border border-white/[0.08] px-2.5 py-1 rounded-lg";
                break;
            default:
                liveText = "LIVE";
                desktopClass = "font-mono text-xs font-bold text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 px-3 py-1 rounded-xl shadow-[0_0_15px_rgba(16,185,129,0.05)] tracking-wide animate-pulse";
                mobileClass = "text-xs font-mono font-bold text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 px-2.5 py-1 rounded-lg";
                break;
        }

        desktopTimer.innerHTML = liveText;
        desktopTimer.className = desktopClass;

        if (mobileTimer) {
            mobileTimer.innerHTML = liveText;
            mobileTimer.className = mobileClass;
        }
    }

    // ============================================================
    // FUNGSI UTAMA ENGINE COUNTDOWN MUNDUR
    // ============================================================
    function startCountdown() {
        if (x !== null) return; 

        x = setInterval(function() {
            if (countdownFinished || !countDownDate) {
                clearInterval(x);
                x = null;
                return;
            }

            const now = new Date().getTime();
            const distance = countDownDate - now;            

            if (distance > 0) {
                if (desktopLabel) desktopLabel.innerText = "Time Remaining";
                if (desktopEventLabel) desktopEventLabel.innerText = "NEXT GRAND PRIX";
                let finalTemplate = "";                

                // JIKA SISA WAKTU MASIH 1 JAM ATAU LEBIH
                if (distance >= 86400000) { 
                    const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));

                    const dayStr = days > 0 ? days + "d " : "";
                    const hoursStr = String(hours).padStart(2, '0') + "h";
                    
                    finalTemplate = dayStr + ' ' + hoursStr;

                } else if(distance >= 3600000){
                    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));

                    const hoursStr = String(hours).padStart(2, '0') + "h";
                    const minutesStr = String(minutes).padStart(2, '0') + "m ";
                    
                    finalTemplate = hoursStr + ' ' + minutesStr;
                }
                // JIKA SISA WAKTU SUDAH KURANG DARI 1 JAM (MASUK MODE INTENS!)
                else { 
                    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                    const minutesStr = String(minutes).padStart(2, '0') + "m ";
                    const secondsStr = String(seconds).padStart(2, '0') + "s";
                    
                    finalTemplate = minutesStr + ' ' + secondsStr;
                }

                desktopTimer.innerHTML = finalTemplate;
                // Mengembalikan warna bawaan orange countdown original milikmu
                desktopTimer.className = "font-mono text-sm font-bold text-orange-400 bg-gradient-to-r from-orange-500/10 to-amber-500/5 border border-orange-500/20 px-3 py-1 rounded-xl shadow-[0_0_15px_rgba(249,115,22,0.05)] tracking-wide"; 

                if (mobileTimer) {
                    mobileTimer.innerHTML = finalTemplate;
                    mobileTimer.className = "font-mono text-xs font-bold text-orange-400 bg-gradient-to-r from-orange-500/10 to-amber-500/5 border border-orange-500/20 px-2.5 py-1 rounded-lg tracking-wide shadow-[0_0_15px_rgba(249,115,22,0.03)]";
                }
            } else {
                clearInterval(x);
                x = null;
                countdownFinished = true; 
                renderLiveStatus();
            }
        }, 1000);
    }

    // ============================================================
    // FUNGSI POLLING AJAX: Sinkronisasi Data dengan Database
    // ============================================================
    function checkStatusFromDatabase() {
        fetch('<?= base_url("home/get_live_status"); ?>')
            .then(response => response.json())
            .then(data => {
                if (data) {
                    // Update Informasi Teks Event & Sesi secara Dinamis
                    if (data.event_name) {
                        if (desktopEvent) desktopEvent.innerText = data.event_name;
                        if (mobileEvent) mobileEvent.innerText = data.event_name;
                    }
                    if (data.session) {
                        if (desktopSession) desktopSession.innerText = data.session;
                        if (mobileSession) mobileSession.innerText = data.session;
                    }
                    if (data.location) {
                        if (desktopLocation) desktopLocation.innerText = data.location;
                        if (mobileLocation) mobileLocation.innerText = data.location;
                    }
                    
                    // Update Target Date Countdown
                    if (data.target_date) {
                        countDownDate = new Date(data.target_date).getTime();
                    }

                    const newStatus = data.status ? data.status.trim().toUpperCase() : "";
                    
                    if (currentStatus !== newStatus) {
                        currentStatus = newStatus;
                        
                        if (currentStatus !== "") {
                            // Jika ada bendera aktif, hentikan countdown, set status selesai dan render UI bendera
                            if (x !== null) {
                                clearInterval(x);
                                x = null;
                            }
                            countdownFinished = true;
                            renderLiveStatus();
                        } else {
                            // Jika status kembali kosong (""), jalankan hitung mundur kembali
                            countdownFinished = false;
                            startCountdown();
                        }
                    } else if (currentStatus === "" && !countdownFinished) {
                        startCountdown();
                    }
                }
            })
            .catch(err => console.error("Gagal memperbarui status dari DB:", err));
    }

    // Pertama kali dimuat, panggil AJAX segera untuk mengisi layout
    checkStatusFromDatabase();

    // Background checker berkala setiap 15 detik
    setInterval(checkStatusFromDatabase, 15000);
});
</script>