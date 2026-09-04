    <!-- LOADING OVERLAY -->
    <div id="post-loading-overlay" class="fixed inset-0 z-[9999] hidden">
        <div class="absolute inset-0 bg-black/70 backdrop-blur-sm"></div>
        <div class="absolute inset-0 flex flex-col items-center justify-center gap-3">
            <div class="w-10 h-10 border-2 border-red-500 border-t-transparent rounded-full animate-spin"></div>
            <span class="text-sm font-semibold text-slate-300 tracking-wide">Memposting...</span>
        </div>
    </div>

    <!-- LOGIN PROMPT MODAL -->
    <div id="login-modal" class="fixed inset-0 z-[999] hidden">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="hideLoginModal()"></div>
        <div class="absolute inset-0 flex items-center justify-center p-4">
            <div class="glass-card rounded-2xl w-full max-w-sm border border-white/[0.06] shadow-2xl p-6 text-center">
                <div class="w-12 h-12 mx-auto mb-4 rounded-full bg-red-500/10 flex items-center justify-center">
                    <i data-lucide="lock" class="w-6 h-6 text-red-500"></i>
                </div>
                <h3 class="font-syne text-sm uppercase tracking-tight text-white mb-2">Login Diperlukan</h3>
                <p class="text-xs text-slate-400 leading-relaxed mb-6">Silakan masuk atau daftar akun terlebih dahulu untuk mengakses fitur ini.</p>
                <div class="flex gap-3">
                    <button onclick="hideLoginModal()" class="flex-1 px-4 py-2.5 text-xs font-semibold text-slate-300 bg-white/[0.05] hover:bg-white/[0.08] rounded-xl transition-colors border border-white/[0.06]">
                        Kembali
                    </button>
                    <a href="<?= base_url('auth'); ?>" class="flex-1 px-4 py-2.5 text-xs font-semibold text-white bg-red-600 hover:bg-red-500 rounded-xl transition-colors shadow-lg shadow-red-600/10">
                        Masuk / Daftar
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- REPORT MODAL -->
    <div id="report-modal" class="hidden fixed inset-0 z-[999] bg-black/80 backdrop-blur-sm flex items-center justify-center p-4 select-none animate-fade-in">
        <div class="w-full max-w-md glass-card p-6 space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-bold text-slate-200" id="report-modal-title">Laporkan</h3>
                <button onclick="closeReportModal()" class="text-slate-500 hover:text-slate-300 p-1 rounded-md hover:bg-white/[0.05] transition-colors">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
            <input type="hidden" id="report-target-type" value="">
            <input type="hidden" id="report-target-id" value="">
            <textarea id="report-reason" rows="4" placeholder="Jelaskan alasan laporan kamu..." class="w-full bg-slate-800 text-xs sm:text-sm text-slate-200 placeholder-slate-500 border border-white/[0.06] rounded-lg px-3 py-2.5 focus:outline-none focus:border-red-500/50 resize-none transition-colors" required></textarea>
            <div class="flex gap-2 justify-end">
                <button onclick="closeReportModal()" class="text-xs px-4 py-2 rounded-lg bg-white/[0.05] text-slate-300 hover:bg-white/[0.08] transition-colors font-medium">Batal</button>
                <button onclick="submitReport()" class="text-xs px-4 py-2 rounded-lg bg-red-600 hover:bg-red-500 text-white font-semibold transition-colors shadow-lg shadow-red-600/10">Kirim Laporan</button>
            </div>
        </div>
    </div>

    <!-- MOBILE BOTTOM NAVIGATION (Hanya muncul di Layar HP) -->
    <div class="block lg:hidden fixed bottom-0 left-0 right-0 z-50 bg-[#05070c]/80 backdrop-blur-xl border-t border-white/[0.04] px-6 py-3">
        <div class="flex items-center justify-between text-slate-400">
            <a href="<?= base_url('home'); ?>" class="flex flex-col items-center justify-center gap-1 nav-bottom" data-nav="home">
                <i data-lucide="layout-grid" class="w-5 h-5"></i>
                <span class="text-[9px] font-medium">Feed</span>
            </a>
            <a href="<?= base_url('race-hub'); ?>" class="flex flex-col items-center justify-center gap-1 hover:text-white transition-colors nav-bottom" data-nav="race">
                <i data-lucide="calendar" class="w-5 h-5"></i>
                <span class="text-[9px] font-medium">Race Hub</span>
            </a>
            <a href="<?= base_url('search'); ?>" class="flex flex-col items-center justify-center gap-1 hover:text-white transition-colors nav-bottom" data-nav="search">
                <i data-lucide="search" class="w-5 h-5"></i>
                <span class="text-[9px] font-medium">Search</span>
            </a>
            <a href="<?= base_url('chat'); ?>" class="flex flex-col items-center justify-center gap-1 hover:text-white transition-colors nav-bottom" data-nav="chat">
                <i data-lucide="message-circle" class="w-5 h-5"></i>
                <span class="text-[9px] font-medium">Chat</span>
            </a>
        </div>
    </div>

    <!-- COOKIE CONSENT BANNER -->
    <div id="cookie-consent-banner" class="fixed bottom-0 inset-x-0 z-[9998] hidden">
        <div class="glass-card mx-3 sm:mx-auto max-w-3xl rounded-2xl border border-white/[0.08] shadow-2xl p-5 mb-4 flex flex-col sm:flex-row items-start sm:items-center gap-4">
            <div class="flex-1">
                <h4 class="text-xs font-bold text-white uppercase tracking-wide mb-1 flex items-center gap-2">
                    <i data-lucide="cookie" class="w-4 h-4 text-red-500"></i> Izin Cookie
                </h4>
                <p class="text-[11px] text-slate-400 leading-relaxed">
                    Kami menggunakan cookie untuk memastikan PaddockID berfungsi, menyimpan preferensi kamu, dan (jika disetujui) menampilkan iklan yang relevan. Kamu bisa mengubah pilihan kapan saja di halaman Pengaturan.
                </p>
            </div>
            <div class="flex gap-2 flex-shrink-0">
                <button onclick="consentCookie('essential_only')" class="px-4 py-2 text-[11px] font-semibold text-slate-300 bg-white/[0.05] hover:bg-white/[0.1] rounded-lg transition-colors border border-white/[0.06]">
                    Hanya Penting
                </button>
                <button onclick="consentCookie('accept_all')" class="px-4 py-2 text-[11px] font-semibold text-white bg-red-600 hover:bg-red-500 rounded-lg transition-colors shadow-lg shadow-red-600/10">
                    Terima Semua
                </button>
            </div>
        </div>
    </div>

    <script>
        <?php $footer_user_data = $this->session->userdata('user_logged_in'); ?>
const IS_LOGGED_IN = <?= $footer_user_data ? 'true' : 'false'; ?>;
const CURRENT_USERNAME = <?= json_encode($footer_user_data ? $footer_user_data['username'] : '', JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
const CURRENT_USER_ID = <?= $footer_user_data ? $footer_user_data['user_id'] : 0; ?>;

// === NOTIFICATION SYSTEM ===
let notificationDropdownOpen = false;

function toggleNotificationDropdown() {
    const dd = document.getElementById('notification-dropdown');
    notificationDropdownOpen = !notificationDropdownOpen;
    if (notificationDropdownOpen) {
        dd.classList.remove('hidden');
        loadNotifications();
    } else {
        dd.classList.add('hidden');
    }
}

function loadNotifications() {
    const list = document.getElementById('notification-list');
    list.innerHTML = '<div class="px-4 py-8 text-center text-slate-500 text-xs">Memuat...</div>';

    fetch('<?= base_url("notifications/get_notifications"); ?>')
        .then(r => r.json())
        .then(data => {
            if (!data.length) {
                list.innerHTML = '<div class="px-4 py-12 text-center text-slate-500 text-xs">Belum ada notifikasi</div>';
                return;
            }
            list.innerHTML = '';
            data.forEach(n => {
                let link = '#';
                if (n.type === 'follow') {
                    link = '<?= base_url("user/"); ?>' + encodeURIComponent(n.actor_username);
                } else if (n.type === 'like' || n.type === 'comment') {
                    link = '<?= base_url("post/"); ?>' + encodeURIComponent(n.post_author_username || n.actor_username) + '/' + n.id_post;
                } else if (n.type === 'reply') {
                    link = '<?= base_url("post/"); ?>' + encodeURIComponent(n.post_author_username || n.actor_username) + '/' + n.id_post;
                }

                const item = document.createElement('a');
                item.href = link;
                item.className = 'flex items-start gap-3 px-4 py-3 hover:bg-white/[0.02] transition-colors ' + (n.is_read == '0' ? 'bg-white/[0.02] border-l-2 border-red-500' : '');
                item.innerHTML = `
                    <div class="relative w-8 h-8 flex-shrink-0 mt-0.5" data-user-id="${escapeHtml(n.actor_id)}">
                        <img src="${escapeHtml(n.actor_avatar)}" alt="" class="w-8 h-8 rounded-full object-cover"
                             onerror="this.src='<?= assets_url('default.jpg'); ?>'">
                        ${n.actor_is_online ? '<div class="online-indicator"></div>' : ''}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs text-slate-200 leading-relaxed">
                            <strong class="font-semibold text-white">${escapeHtml(n.actor_username)}</strong>
                            ${escapeHtml(n.message)}
                        </p>
                        <span class="text-[10px] text-slate-500 mt-1 block">${escapeHtml(n.created_at)}</span>
                    </div>
                `;
                if (n.is_read == '0') {
                    item.addEventListener('click', function(e) {
                        markNotificationRead(n.id_notification);
                    });
                }
                list.appendChild(item);
            });
        })
        .catch(() => {
            list.innerHTML = '<div class="px-4 py-8 text-center text-slate-500 text-xs">Gagal memuat notifikasi</div>';
        });
}

function getCsrfField() {
    const name = document.querySelector('meta[name="csrf-token-name"]').content;
    const hash = document.querySelector('meta[name="csrf-token-hash"]').content;
    return name + '=' + encodeURIComponent(hash);
}

function markNotificationRead(id) {
    fetch('<?= base_url("notifications/mark_read"); ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: getCsrfField() + '&id_notification=' + id
    }).then(() => updateNotifBadge());
}

function markAllNotificationsRead() {
    fetch('<?= base_url("notifications/mark_read"); ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: getCsrfField()
    }).then(() => {
        document.querySelectorAll('#notification-list a').forEach(a => {
            a.classList.remove('bg-white/[0.02]', 'border-l-2', 'border-red-500');
        });
        updateNotifBadge();
    });
}

function updateNotifBadge() {
    fetch('<?= base_url("notifications/get_unread_count"); ?>')
        .then(r => r.json())
        .then(data => {
            const badge = document.getElementById('notif-badge');
            if (data.count > 0) {
                badge.textContent = data.count > 9 ? '9+' : data.count;
                badge.classList.remove('hidden');
            } else {
                badge.classList.add('hidden');
            }
        });
}

function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}

// === COOKIE CONSENT & PREFERENSI ===
const COOKIE_CONSENT_SET = <?= has_pref_cookie('consent') ? 'true' : 'false'; ?>;

function showCookieConsentBanner() {
    if (COOKIE_CONSENT_SET) return;
    const banner = document.getElementById('cookie-consent-banner');
    if (banner) banner.classList.remove('hidden');
}

function hideCookieConsentBanner() {
    const banner = document.getElementById('cookie-consent-banner');
    if (banner) banner.classList.add('hidden');
}

function consentCookie(action) {
    fetch('<?= base_url("consent/save"); ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: getCsrfField() + '&action=' + encodeURIComponent(action)
    })
    .then(r => r.json())
    .then(() => {
        hideCookieConsentBanner();
        if (action === 'accept_all') {
            loadAdsAfterConsent();
        }
    })
    .catch(() => hideCookieConsentBanner());
}

function setPreference(key, value) {
    fetch('<?= base_url("consent/set_preference"); ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: getCsrfField() + '&key=' + encodeURIComponent(key) + '&value=' + encodeURIComponent(value)
    })
    .then(r => r.json())
    .catch(() => {});
}

function loadAdsAfterConsent() {
    if (Array.from(document.scripts).some(s => s.src.includes('adsbygoogle.js'))) return;
    <?php $this->config->load('ads'); ?>
    const pubId = '<?= htmlspecialchars($this->config->item('adsense_pub_id') ?? '', ENT_QUOTES); ?>';
    if (!pubId) return;
    const s = document.createElement('script');
    s.async = true;
    s.src = 'https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=' + encodeURIComponent(pubId);
    s.crossOrigin = 'anonymous';
    document.head.appendChild(s);
}

// Panggil saat halaman siap
document.addEventListener('DOMContentLoaded', function () {
    if (typeof lucide !== 'undefined') lucide.createIcons();
    showCookieConsentBanner();
});

// Polling: cek notifikasi baru setiap 30 detik
if (IS_LOGGED_IN) {
    setInterval(updateNotifBadge, 30000);
}

// Heartbeat: perbarui last_activity setiap 30 detik
if (IS_LOGGED_IN) {
    function pingHeartbeat() {
        fetch('<?= base_url("home/ping"); ?>', { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: getCsrfField() }).catch(() => {});
    }
    pingHeartbeat();
    setInterval(pingHeartbeat, 30000);
}

// Real-time online status polling setiap 15 detik
function updateOnlineIndicators() {
    const elements = document.querySelectorAll('[data-user-id]');
    const userIds = [];
    elements.forEach(el => {
        const id = el.getAttribute('data-user-id');
        if (id && id !== '0' && !userIds.includes(id)) userIds.push(id);
    });
    if (userIds.length === 0) return;

    fetch('<?= base_url("home/get_online_status"); ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: getCsrfField() + '&' + userIds.map(id => 'user_ids[]=' + encodeURIComponent(id)).join('&')
    })
    .then(r => r.json())
    .then(data => {
        if (!data.statuses) return;
        elements.forEach(el => {
            const id = el.getAttribute('data-user-id');
            const online = data.statuses[id];
            let indicator = el.querySelector('.online-indicator');
            if (online) {
                if (!indicator) {
                    indicator = document.createElement('div');
                    indicator.className = 'online-indicator';
                    el.appendChild(indicator);
                }
            } else {
                if (indicator) indicator.remove();
            }
        });
    })
    .catch(() => {});
}

if (IS_LOGGED_IN) {
    setInterval(updateOnlineIndicators, 15000);
}

// Tutup dropdown saat klik di luar
document.addEventListener('click', function(e) {
    const wrapper = document.getElementById('notification-bell-wrapper');
    if (notificationDropdownOpen && wrapper && !wrapper.contains(e.target)) {
        toggleNotificationDropdown();
    }
});

        function showLoginModal() {
            document.getElementById('login-modal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function hideLoginModal() {
            document.getElementById('login-modal').classList.add('hidden');
            document.body.style.overflow = '';
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Prevent post detail click for guests
            document.addEventListener('click', function(e) {
                const cardLink = e.target.closest('a[href*="/post/"]');
                if (cardLink && !IS_LOGGED_IN) {
                    e.preventDefault();
                    showLoginModal();
                }
            });

            document.addEventListener('click', function(e) {
    const userLink = e.target.closest('a[href*="/user/"]');
    if (userLink && IS_LOGGED_IN) {
        const href = userLink.getAttribute('href');
        const username = href.split('/user/').pop().replace(/\/$/, '');
        if (username === CURRENT_USERNAME) {
            e.preventDefault();
            window.location.href = '<?= base_url("profile"); ?>';
        }
    }
});

            // Active navigation highlighting
            const currentPath = window.location.pathname;
            const navLinks = document.querySelectorAll('.nav-bottom, .nav-sidebar');
            
            navLinks.forEach(link => {
                const href = link.getAttribute('href');
                if (!href || href === '#') return;
                
                const linkPath = new URL(href, window.location.origin).pathname;
                
                // Remove existing active state
                link.classList.remove('text-white', 'text-red-500');
                const icon = link.querySelector('[data-lucide]');
                if (icon) icon.classList.remove('text-red-500');

                if (currentPath === linkPath) {
                    link.classList.add('text-white');
                    if (icon) icon.classList.add('text-red-500');
                    
                    // For sidebar items, also add bg highlight
                    if (link.classList.contains('nav-sidebar')) {
                        link.classList.add('bg-white/[0.04]');
                        link.querySelector('span')?.classList.remove('text-slate-400');
                        link.querySelector('span')?.classList.add('text-white');
                    }

                    // For bottom nav
                    if (link.classList.contains('nav-bottom')) {
                        const span = link.querySelector('span');
                        if (span) {
                            span.classList.remove('text-slate-400');
                            span.classList.add('text-white');
                        }
                    }
                }
            });
        });

        function deletePost(id_post) {
            if (!confirm('Apakah kamu yakin ingin menghapus postingan ini?')) return;

            fetch('<?= base_url("post/delete_post"); ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: getCsrfField() + '&id_post=' + id_post
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    const article = document.querySelector(`article[data-post-id="${id_post}"]`);
                    if (article) {
                        article.style.transition = 'all 0.3s';
                        article.style.opacity = '0';
                        article.style.transform = 'scale(0.95)';
                        setTimeout(() => article.remove(), 300);
                    }
                    const toast = document.createElement('div');
                    toast.className = 'fixed bottom-20 left-1/2 -translate-x-1/2 z-[9999] bg-emerald-600 text-white text-xs font-semibold px-4 py-2.5 rounded-xl shadow-lg';
                    toast.textContent = data.message;
                    document.body.appendChild(toast);
                    setTimeout(() => toast.remove(), 3000);
                } else {
                    alert(data.message);
                }
            })
            .catch(err => {
                console.error('Error:', err);
                alert('Terjadi kesalahan. Silakan coba lagi.');
            });
        }

        // Escape string untuk digunakan di onclick handler (single-quoted JS string)
        function escapeJsString(str) {
            return String(str)
                .replace(/\\/g, '\\\\')
                .replace(/'/g, "\\'")
                .replace(/"/g, '\\"')
                .replace(/&/g, '\\x26')
                .replace(/</g, '\\x3c')
                .replace(/>/g, '\\x3e')
                .replace(/\r\n/g, '\\n')
                .replace(/\r/g, '\\n')
                .replace(/\n/g, '\\n');
        }

        // Report Post / Comment
        function openReportPost(postId) {
            document.getElementById('report-target-type').value = 'post';
            document.getElementById('report-target-id').value = postId;
            document.getElementById('report-modal-title').textContent = 'Laporkan Postingan';
            document.getElementById('report-reason').value = '';
            document.getElementById('report-modal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function openReportComment(commentId) {
            document.getElementById('report-target-type').value = 'comment';
            document.getElementById('report-target-id').value = commentId;
            document.getElementById('report-modal-title').textContent = 'Laporkan Komentar';
            document.getElementById('report-reason').value = '';
            document.getElementById('report-modal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeReportModal() {
            document.getElementById('report-modal').classList.add('hidden');
            document.body.style.overflow = '';
        }

        function submitReport() {
            const type = document.getElementById('report-target-type').value;
            const id = document.getElementById('report-target-id').value;
            const reason = document.getElementById('report-reason').value.trim();

            if (!reason) {
                showToast('Alasan laporan harus diisi.', 'red');
                return;
            }

            if (!IS_LOGGED_IN) {
                closeReportModal();
                showLoginModal();
                return;
            }

            const formData = new FormData();
            formData.append(document.querySelector('meta[name="csrf-token-name"]').content, document.querySelector('meta[name="csrf-token-hash"]').content);
            let url;
            if (type === 'post') {
                formData.append('id_post', id);
                formData.append('reason', reason);
                url = '<?= base_url("post/report"); ?>';
            } else {
                formData.append('id_comment', id);
                formData.append('reason', reason);
                url = '<?= base_url("post/report_comment"); ?>';
            }

            fetch(url, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                closeReportModal();
                showToast(data.message || (data.status === 'success' ? 'Laporan berhasil dikirim.' : 'Gagal mengirim laporan.'), data.status === 'success' ? 'emerald' : 'red');
            })
            .catch(err => {
                closeReportModal();
                showToast('Terjadi kesalahan. Silakan coba lagi.', 'red');
                console.error('Error:', err);
            });
        }

        function toggleFollowUser(userId, btn) {
            if (!IS_LOGGED_IN) {
                showLoginModal();
                return;
            }

            fetch('<?= base_url("user/toggle_follow"); ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: getCsrfField() + '&user_id=' + userId
            })
            .then(r => r.json())
            .then(data => {
                if (data.status === 'success') {
                    if (data.action === 'followed') {
                        btn.textContent = 'Following';
                        btn.className = 'follow-btn flex-shrink-0 text-xs font-semibold px-4 py-1.5 rounded-full transition-all border bg-white/[0.05] text-slate-300 border-white/[0.08] hover:border-red-500/30 hover:text-red-400';
                    } else {
                        btn.textContent = 'Follow';
                        btn.className = 'follow-btn flex-shrink-0 text-xs font-semibold px-4 py-1.5 rounded-full transition-all border bg-red-600 text-white border-red-600 hover:bg-red-500';
                    }
                } else {
                    showToast(data.message || 'Terjadi kesalahan', 'red');
                }
            })
            .catch(err => {
                showToast('Terjadi kesalahan jaringan', 'red');
                console.error('Gagal follow/unfollow:', err);
            });
        }

        function showToast(message, color) {
            const toast = document.createElement('div');
            const bgColor = color === 'red' ? 'bg-red-600' : 'bg-emerald-600';
            toast.className = `fixed bottom-20 left-1/2 -translate-x-1/2 z-[9999] ${bgColor} text-white text-xs font-semibold px-4 py-2.5 rounded-xl shadow-lg`;
            toast.textContent = message;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 3000);
        }

        // Lucide Icons
        lucide.createIcons();
    </script>
</body>
</html>
