<!-- LIVE TIMING STRIP -->
<div class="live-strip">
    <div class="live-strip__bar">
        <span id="event-name" class="live-strip__name">Loading...</span>
        <span class="live-strip__sep" aria-hidden="true"></span>
        <span class="live-strip__location">
            <i data-lucide="map-pin" style="width:12px;height:12px;" class="c-primary"></i>
            <span id="event-location">Loading...</span>
        </span>
        <span class="live-strip__spacer" aria-hidden="true"></span>
        <span id="session" class="live-strip__session">...</span>
        <span class="live-strip__sep" aria-hidden="true"></span>
        <span id="timer" class="timer-display">-</span>
    </div>
</div>

<!-- CONTAINER UTAMA -->
<div class="page-wrapper">
    <div class="page-grid">
    
    <!-- SIDEBAR KIRI -->
    <aside class="sidebar-left">
        <nav class="nav-menu">
            <a href="<?= base_url('home'); ?>" class="nav-sidebar">
                <span class="nav-sidebar__icon"><i data-lucide="layout-grid" style="width:16px;height:16px;"></i></span>
                <span>Feed</span>
            </a>
            <a href="<?= base_url('race-hub'); ?>" class="nav-sidebar">
                <span class="nav-sidebar__icon"><i data-lucide="calendar" style="width:16px;height:16px;"></i></span>
                <span>Race Hub</span>
            </a>
            <a href="<?= base_url('chat'); ?>" class="nav-sidebar">
                <span class="nav-sidebar__icon"><i data-lucide="message-circle" style="width:16px;height:16px;"></i></span>
                <span>Chat</span>
            </a>
            <a href="<?= base_url('dm'); ?>" class="nav-sidebar">
                <span class="nav-sidebar__icon"><i data-lucide="mail" style="width:16px;height:16px;"></i></span>
                <span>Pesan</span>
            </a>
            <a href="<?= base_url('borders'); ?>" class="nav-sidebar">
                <span class="nav-sidebar__icon"><i data-lucide="sparkles" style="width:16px;height:16px;"></i></span>
                <span>Borders</span>
            </a>
            <?php if ($this->session->userdata('user_logged_in') && !empty($this->session->userdata('user_logged_in')['role']) && $this->session->userdata('user_logged_in')['role'] === 'admin'): ?>
            <div class="nav-menu__divider"></div>
            <a href="<?= base_url('admin'); ?>" class="nav-sidebar nav-sidebar--accent">
                <span class="nav-sidebar__icon"><i data-lucide="shield" style="width:16px;height:16px;"></i></span>
                <span class="font-semibold">Admin Panel</span>
            </a>
            <?php endif; ?>
        </nav>
    </aside>

    <!-- KONTEN TENGAH -->
    <main class="main-content space-y-4">

        <!-- LIVE CHAT CARD (Mobile) -->
        <a id="chat-card-mobile" href="<?= base_url('chat'); ?>" class="show-mobile chat-card relative" style="display:none;">
            <div class="p-3-5 relative z-10 flex-row justify-between">
                <div class="flex-row gap-2-5 min-w-0">
                    <div style="width:28px;height:28px;border-radius:var(--radius-md);background:var(--color-primary-bg);border:1px solid var(--color-primary-border);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i data-lucide="message-circle" style="width:14px;height:14px;" class="c-primary"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-micro c-primary font-bold">Live Chat</p>
                        <p id="chat-card-session-mobile" class="text-caption text-truncate">...</p>
                    </div>
                </div>
                <span class="btn-xs c-primary" style="border:1px solid var(--color-primary-border);background:var(--color-primary-bg);border-radius:var(--radius-md);flex-shrink:0;">
                    Masuk <i data-lucide="arrow-right" style="width:12px;height:12px;" class="inline-block" ></i>
                </span>
            </div>
        </a>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const timerEl = document.getElementById("timer");
    const eventEl = document.getElementById("event-name");
    const locationEl = document.getElementById("event-location");
    const sessionEl = document.getElementById("session");

    const pingDot = document.getElementById("ping-dot");
    const solidDot = document.getElementById("solid-dot");
    const statusBadge = document.getElementById("status-badge");

    const desktopChatCard = document.getElementById("chat-card-desktop");
    const mobileChatCard = document.getElementById("chat-card-mobile");
    const desktopChatSession = document.getElementById("chat-card-session-desktop");
    const desktopChatEvent = document.getElementById("chat-card-event-desktop");
    const mobileChatSession = document.getElementById("chat-card-session-mobile");

    if (!timerEl) return;

    let countDownDate = null;
    let currentStatus = "";
    let countdownFinished = false;
    let x = null;

    function renderLiveStatus() {
        const dotStyles = {
            "YELLOW FLAG": { dot: "var(--color-yellow)", badgeClass: "flag-badge--yellow" },
            "RED FLAG":    { dot: "var(--color-danger)",    badgeClass: "flag-badge--red" },
            "VSC":         { dot: "var(--color-warning)",  badgeClass: "flag-badge--vsc" },
            "SC":          { dot: "var(--color-orange)",    badgeClass: "flag-badge--sc" },
            "FINISHED":    { dot: "var(--text-subtle)",     badgeClass: "flag-badge--finished" },
            "LIVE":        { dot: "var(--color-success)",   badgeClass: "flag-badge--live" },
        };
        const badgeLabels = {
            "YELLOW FLAG": "Yellow Flag", "RED FLAG": "Red Flag", "VSC": "VSC",
            "SC": "Safety Car", "FINISHED": "Finished", "LIVE": "Live Timing",
        };
        const statusText = {
            "YELLOW FLAG": "YELLOW FLAG", "RED FLAG": "RED FLAG", "VSC": "VSC",
            "SC": "SAFETY CAR", "FINISHED": "FINISHED", default: "LIVE",
        };

        const info = dotStyles[currentStatus] || dotStyles["LIVE"];
        const badgeText = badgeLabels[currentStatus] || "Live Timing";
        const shouldPing = !["FINISHED"].includes(currentStatus);

        if (pingDot) {
            pingDot.style.background = info.dot;
            pingDot.style.animation = shouldPing ? "ping 1.5s cubic-bezier(0, 0, 0.2, 1) infinite" : "none";
        }
        if (solidDot) solidDot.style.background = info.dot;
        if (statusBadge) {
            statusBadge.className = "flag-badge " + info.badgeClass;
            statusBadge.innerText = badgeText;
        }

        timerEl.innerHTML = statusText[currentStatus] || statusText.default;
        timerEl.className = "timer-display" + (currentStatus !== "FINISHED" && currentStatus !== "" ? " animate-pulse" : "");
    }

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
            let display;

            if (distance > 0) {
                if (distance >= 86400000) {
                    const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    display = days + "d " + String(hours).padStart(2, '0') + "h";
                } else if (distance >= 3600000) {
                    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    display = String(hours).padStart(2, '0') + "h " + String(minutes).padStart(2, '0') + "m";
                } else {
                    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((distance % (1000 * 60)) / 1000);
                    display = String(minutes).padStart(2, '0') + "m " + String(seconds).padStart(2, '0') + "s";
                }
                timerEl.innerHTML = display;
                timerEl.className = "timer-display";
            } else {
                clearInterval(x);
                x = null;
                countdownFinished = true;
                renderLiveStatus();
            }
        }, 1000);
    }

    function checkStatusFromDatabase() {
        fetch('<?= base_url("home/get_live_status"); ?>')
            .then(response => response.json())
            .then(data => {
                if (!data) return;

                if (data.event_name) eventEl.innerText = data.event_name;
                if (data.location) locationEl.innerText = data.location;
                if (data.session) sessionEl.innerText = data.session;

                if (data.chat_slug) {
                    const chatUrl = '<?= base_url("chat/room/") ?>' + data.chat_slug;
                    if (desktopChatCard) { desktopChatCard.href = chatUrl; desktopChatCard.classList.remove('hidden'); }
                    if (mobileChatCard) { mobileChatCard.href = chatUrl; mobileChatCard.style.display = ''; }
                    if (desktopChatSession) desktopChatSession.innerText = (data.session || '...') + ' Chat';
                    if (desktopChatEvent) desktopChatEvent.innerText = (data.event_name || '') + ' — ' + (data.location || '');
                    if (mobileChatSession) mobileChatSession.innerText = (data.event_name || '') + ' — ' + (data.session || '...');
                    if (typeof lucide !== 'undefined') lucide.createIcons();
                } else {
                    if (desktopChatCard) desktopChatCard.classList.add('hidden');
                    if (mobileChatCard) mobileChatCard.style.display = 'none';
                }

                if (data.target_date) countDownDate = new Date(data.target_date).getTime();

                const newStatus = data.status ? data.status.trim().toUpperCase() : "";

                if (currentStatus !== newStatus) {
                    currentStatus = newStatus;

                    if (currentStatus !== "") {
                        if (x !== null) { clearInterval(x); x = null; }
                        countdownFinished = true;
                        renderLiveStatus();
                    } else {
                        countdownFinished = false;
                        startCountdown();
                    }
                } else if (currentStatus === "" && !countdownFinished) {
                    startCountdown();
                }
            })
            .catch(err => console.error("Gagal memperbarui status dari DB:", err));
    }

    checkStatusFromDatabase();
    setInterval(checkStatusFromDatabase, 15000);
});
</script>
