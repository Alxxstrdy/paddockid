<!-- CONTAINER UTAMA -->
<div class="page-wrapper">
    <div class="page-grid">
    
    <!-- SIDEBAR KIRI -->
    <aside class="sidebar-left">
        <div class="card p-3 space-y-1">
            <p class="text-label px-4 mb-2">Menu</p>
            <a href="<?= base_url('home'); ?>" class="nav-sidebar">
                <i data-lucide="layout-grid" style="width:16px;height:16px;"></i> <span>Feed</span>
            </a>
            <a href="<?= base_url('race-hub'); ?>" class="nav-sidebar">
                <i data-lucide="calendar" style="width:16px;height:16px;"></i> <span>Race Hub</span>
            </a>
            <a href="<?= base_url('chat'); ?>" class="nav-sidebar">
                <i data-lucide="message-circle" style="width:16px;height:16px;"></i> <span>Chat</span>
            </a>
            <a href="<?= base_url('borders'); ?>" class="nav-sidebar">
                <i data-lucide="sparkles" style="width:16px;height:16px;"></i> <span>Borders</span>
            </a>
            <?php if ($this->session->userdata('user_logged_in') && !empty($this->session->userdata('user_logged_in')['role']) && $this->session->userdata('user_logged_in')['role'] === 'admin'): ?>
            <a href="<?= base_url('admin'); ?>" class="nav-sidebar c-primary" style="border-top:1px solid var(--border-subtle);margin-top:8px;padding-top:16px;">
                <i data-lucide="shield" style="width:16px;height:16px;"></i> <span class="font-semibold">Admin Panel</span>
            </a>
            <?php endif; ?>
        </div>

        <!-- Countdown Desktop -->
        <div class="card card--countdown p-5 mt-4">
            <div class="flex-row justify-between mb-4 relative z-10">
                <div id="status-indicator-desktop" class="flex-row gap-2">
                    <span class="ping-dot" style="height:8px;width:8px;">
                        <span id="ping-dot-desktop" class="ping-dot__ping" style="background-color:var(--color-success);"></span>
                        <span id="solid-dot-desktop" class="ping-dot__solid" style="background-color:var(--color-success);"></span>
                    </span>
                    <span id="status-badge-desktop" class="flag-badge flag-badge--live">
                        Live Timing
                    </span>
                </div>
                <div class="p-1-5" style="background:var(--bg-surface-subtle);border:1px solid var(--border-default);border-radius:var(--radius-md);color:var(--text-subtle);">
                    <i data-lucide="timer" style="width:14px;height:14px;" class="animate-spin"></i>
                </div>
            </div>

            <div class="space-y-1 relative z-10">
                <span id="event-label-desktop" class="text-micro">Next Grand Prix</span>
                <h3 id="event-name-desktop" class="text-heading" style="font-size:1.25rem;">
                    Loading...
                </h3>
                <p class="text-small flex-row gap-1-5">
                    <i data-lucide="map-pin" style="width:12px;height:12px;" class="c-primary"></i> 
                    <span id="event-location-desktop">Loading...</span>
                </p>
            </div>

            <div class="mt-5" style="padding-top:16px;border-top:1px dashed var(--border-default);display:flex;align-items:center;justify-content:space-between;position:relative;z-index:10;">
                <div class="flex-col">
                    <span class="text-micro">Next Session</span>
                    <span id="session-desktop" class="session-label mt-0-5">
                        ...
                    </span>
                </div>
                <div class="flex-col items-end">
                    <span id="timer-label-desktop" class="text-micro mb-1">Time Remaining</span>
                    <span id="timer-desktop" class="timer-display">
                        -
                    </span>
                </div>
            </div>
        </div>
    </aside>

    <!-- KONTEN TENGAH -->
    <main class="main-content space-y-4">
        <?php if (isset($show_category) && $show_category === true): ?>
        <?php $this->load->view('layout/category_tags'); ?>
        <?php endif; ?>
        
        <!-- LIVE COUNTDOWN BANNER (MOBILE) -->
        <div class="show-mobile card card--countdown p-4">
            <div class="flex-row justify-between" style="padding-bottom:12px;border-bottom:1px dashed var(--border-default);position:relative;z-index:10;">
                <div class="flex-row gap-2">
                    <span id="ping-dot-mobile" class="ping-dot" style="height:8px;width:8px;">
                        <span class="ping-dot__ping" style="background-color:var(--color-success);"></span>
                        <span class="ping-dot__solid" style="background-color:var(--color-success);height:6px;width:6px;"></span>
                    </span>
                    <h3 id="event-name-mobile" class="text-heading" style="font-size:0.75rem;">
                        Loading...
                    </h3>
                </div>
                <span id="event-location-mobile" class="text-micro">Loading...</span>
            </div>

            <div class="flex-row justify-between" style="padding-top:12px;position:relative;z-index:10;">
                <div class="flex-row gap-1-5">
                    <span class="text-micro" style="text-transform:uppercase;">Session:</span>
                    <span id="session-mobile" class="session-label">
                        ...
                    </span>
                </div>
                <span id="timer-mobile" class="timer-display timer-display--sm">
                    ...
                </span>
            </div>
        </div>

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
                <span class="btn-xs c-primary" style="border:1px solid var(--color-primary-border);background:var(--color-primary-bg);border-radius:var(--radius-pill);flex-shrink:0;">
                    Masuk <i data-lucide="arrow-right" style="width:12px;height:12px;" class="inline-block" ></i>
                </span>
            </div>
        </a>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const desktopTimer = document.getElementById("timer-desktop");
    const mobileTimer = document.getElementById("timer-mobile");

    const desktopLabel = document.getElementById("timer-label-desktop");
    const desktopEventLabel = document.getElementById("event-label-desktop");
    
    const desktopEvent = document.getElementById("event-name-desktop");
    const desktopLocation = document.getElementById("event-location-desktop");
    const mobileLocation = document.getElementById("event-location-mobile");
    const mobileEvent = document.getElementById("event-name-mobile");
    const desktopSession = document.getElementById("session-desktop");
    const mobileSession = document.getElementById("session-mobile");
    const desktopChatCard = document.getElementById("chat-card-desktop");
    const mobileChatCard = document.getElementById("chat-card-mobile");
    const desktopChatSession = document.getElementById("chat-card-session-desktop");
    const desktopChatEvent = document.getElementById("chat-card-event-desktop");
    const mobileChatSession = document.getElementById("chat-card-session-mobile");

    const desktopPingDot = document.getElementById("ping-dot-desktop");
    const desktopSolidDot = document.getElementById("solid-dot-desktop");
    const desktopStatusBadge = document.getElementById("status-badge-desktop");
    const mobilePingDot = document.getElementById("ping-dot-mobile");
    
    
    if (!desktopTimer) return;

    let countDownDate = null;
    let currentStatus = "";
    let countdownFinished = false;
    let x = null; 

    function renderLiveStatus() {
        let liveText = "";

        if (desktopLabel) desktopLabel.innerText = "Session Info";
        if (desktopEventLabel) desktopEventLabel.innerText = "CURRENT GRAND PRIX";

        const dotStyles = {
            "YELLOW FLAG": { dot: "var(--color-yellow)", badgeClass: "flag-badge flag-badge--yellow" },
            "RED FLAG":    { dot: "var(--color-danger)",    badgeClass: "flag-badge flag-badge--red" },
            "VSC":         { dot: "var(--color-warning)",  badgeClass: "flag-badge flag-badge--vsc" },
            "SC":          { dot: "var(--color-orange)",    badgeClass: "flag-badge flag-badge--sc" },
            "FINISHED":    { dot: "var(--text-subtle)",     badgeClass: "flag-badge flag-badge--finished" },
            "LIVE":        { dot: "var(--color-success)",   badgeClass: "flag-badge flag-badge--live" },
        };
        const badgeLabels = {
            "YELLOW FLAG": "Yellow Flag", "RED FLAG": "Red Flag", "VSC": "VSC",
            "SC": "Safety Car", "FINISHED": "Finished", "LIVE": "Live Timing",
        };

        const info = dotStyles[currentStatus] || dotStyles["LIVE"];
        const badgeText = badgeLabels[currentStatus] || "Live Timing";
        const shouldPing = !["FINISHED"].includes(currentStatus);

        if (desktopPingDot) {
            desktopPingDot.style.background = info.dot;
            if (shouldPing) {
                desktopPingDot.style.animation = "ping 1.5s cubic-bezier(0, 0, 0.2, 1) infinite";
                desktopPingDot.style.opacity = "0.75";
            } else {
                desktopPingDot.style.animation = "none";
                desktopPingDot.style.opacity = "0.75";
            }
        }
        if (desktopSolidDot) {
            desktopSolidDot.style.background = info.dot;
        }
        if (desktopStatusBadge) {
            desktopStatusBadge.className = info.badgeClass;
            desktopStatusBadge.innerText = badgeText;
        }
        if (mobilePingDot) {
            const pingSpan = mobilePingDot.querySelector('.ping-dot__ping');
            if (pingSpan) pingSpan.style.background = info.dot;
        }

        const flagTimerClasses = {
            "YELLOW FLAG": "timer-display flag-badge--yellow",
            "RED FLAG":    "timer-display flag-badge--red animate-pulse",
            "VSC":         "timer-display flag-badge--vsc animate-pulse",
            "SC":          "timer-display flag-badge--sc animate-pulse",
            "FINISHED":    "timer-display flag-badge--finished",
            default:       "timer-display flag-badge--live animate-pulse",
        };

        switch (currentStatus) {
            case "YELLOW FLAG":
                liveText = "YELLOW FLAG";
                break;
            case "RED FLAG":
                liveText = "RED FLAG";
                break;
            case "VSC":
                liveText = "VSC";
                break;
            case "SC":
                liveText = "SAFETY CAR";
                break;
            case "FINISHED":
                liveText = "FINISHED";
                break;
            default:
                liveText = "LIVE";
                break;
        }

        desktopTimer.innerHTML = liveText;
        desktopTimer.className = flagTimerClasses[currentStatus] || flagTimerClasses.default;

        if (mobileTimer) {
            mobileTimer.innerHTML = liveText;
            mobileTimer.className = "timer-display timer-display--sm";
            if (currentStatus !== "FINISHED" && currentStatus !== "") {
                mobileTimer.classList.add("animate-pulse");
            }
        }
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

            if (distance > 0) {
                if (desktopLabel) desktopLabel.innerText = "Time Remaining";
                if (desktopEventLabel) desktopEventLabel.innerText = "NEXT GRAND PRIX";
                let finalTemplate = "";                

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
                else { 
                    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((distance % (1000 * 60)) / 1000);
                    const minutesStr = String(minutes).padStart(2, '0') + "m ";
                    const secondsStr = String(seconds).padStart(2, '0') + "s";
                    finalTemplate = minutesStr + ' ' + secondsStr;
                }

                desktopTimer.innerHTML = finalTemplate;
                desktopTimer.className = "timer-display"; 

                if (mobileTimer) {
                    mobileTimer.innerHTML = finalTemplate;
                    mobileTimer.className = "timer-display timer-display--sm";
                }
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
                if (data) {
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
                    
                    if (data.target_date) {
                        countDownDate = new Date(data.target_date).getTime();
                    }

                    const newStatus = data.status ? data.status.trim().toUpperCase() : "";
                    
                    if (currentStatus !== newStatus) {
                        currentStatus = newStatus;
                        
                        if (currentStatus !== "") {
                            if (x !== null) {
                                clearInterval(x);
                                x = null;
                            }
                            countdownFinished = true;
                            renderLiveStatus();
                        } else {
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

    checkStatusFromDatabase();
    setInterval(checkStatusFromDatabase, 15000);
});
</script>
