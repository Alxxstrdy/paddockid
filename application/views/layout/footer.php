    <!-- LOADING OVERLAY -->
    <div id="post-loading-overlay" class="loading-overlay hidden">
        <div class="loading-overlay__backdrop"></div>
        <div class="loading-overlay__content">
            <div class="spinner"></div>
            <span class="loading-overlay__text">Memposting...</span>
        </div>
    </div>

    <!-- LOGIN PROMPT MODAL -->
    <div id="login-modal" class="modal-backdrop hidden" style="z-index:600;">
        <div class="modal text-center" onclick="event.stopPropagation();">
            <div style="width:48px;height:48px;margin:0 auto 16px;border-radius:50%;background:var(--color-primary-bg);display:flex;align-items:center;justify-content:center;">
                <i data-lucide="lock" style="width:24px;height:24px;" class="c-primary"></i>
            </div>
            <h3 class="text-heading text-sm" style="margin-bottom:8px;">Login Diperlukan</h3>
            <p class="text-small" style="margin-bottom:24px;line-height:1.6;">Silakan masuk atau daftar akun terlebih dahulu untuk mengakses fitur ini.</p>
            <div class="flex-row gap-3">
                <button onclick="hideLoginModal()" class="btn btn-secondary flex-1">Kembali</button>
                <a href="<?= base_url('auth'); ?>" class="btn btn-primary flex-1">Masuk / Daftar</a>
            </div>
        </div>
    </div>

    <!-- REPORT MODAL -->
    <div id="report-modal" class="modal-backdrop hidden animate-fade-in">
        <div class="modal" onclick="event.stopPropagation();">
            <div class="modal-header">
                <h3 class="modal-title" id="report-modal-title">Laporkan</h3>
                <button onclick="closeReportModal()" class="modal-close">
                    <i data-lucide="x" style="width:16px;height:16px;"></i>
                </button>
            </div>
            <input type="hidden" id="report-target-type" value="">
            <input type="hidden" id="report-target-id" value="">
            <textarea id="report-reason" rows="4" placeholder="Jelaskan alasan laporan kamu..." class="textarea" required></textarea>
            <div class="modal-footer">
                <button onclick="closeReportModal()" class="btn btn-secondary btn-sm">Batal</button>
                <button onclick="submitReport()" class="btn btn-primary btn-sm">Kirim Laporan</button>
            </div>
        </div>
    </div>

    <!-- MOBILE BOTTOM NAVIGATION -->
    <div class="bottom-nav show-mobile">
        <div class="bottom-nav__inner">
            <a href="<?= base_url('home'); ?>" class="bottom-nav__item nav-bottom" data-nav="home">
                <i data-lucide="layout-grid" style="width:20px;height:20px;"></i>
                <span>Feed</span>
            </a>
            <a href="<?= base_url('race-hub'); ?>" class="bottom-nav__item nav-bottom" data-nav="race">
                <i data-lucide="calendar" style="width:20px;height:20px;"></i>
                <span>Race Hub</span>
            </a>
            <a href="<?= base_url('search'); ?>" class="bottom-nav__item nav-bottom" data-nav="search">
                <i data-lucide="search" style="width:20px;height:20px;"></i>
                <span>Search</span>
            </a>
            <a href="<?= base_url('chat'); ?>" class="bottom-nav__item nav-bottom" data-nav="chat">
                <i data-lucide="message-circle" style="width:20px;height:20px;"></i>
                <span>Chat</span>
            </a>
        </div>
    </div>

    <!-- COOKIE CONSENT BANNER -->
    <div id="cookie-consent-banner" class="cookie-banner hidden">
        <div class="cookie-banner__inner">
            <div class="flex-1">
                <h4 class="text-xs font-bold uppercase flex-row gap-2" style="margin-bottom:4px;letter-spacing:0.04em;">
                    <i data-lucide="cookie" style="width:16px;height:16px;" class="c-primary"></i> Izin Cookie
                </h4>
                <p class="text-caption" style="line-height:1.6;">
                    Kami menggunakan cookie untuk memastikan PaddockID berfungsi, menyimpan preferensi kamu, dan (jika disetujui) menampilkan iklan yang relevan. Kamu bisa mengubah pilihan kapan saja di halaman Pengaturan.
                </p>
            </div>
            <div class="flex-row gap-2 flex-shrink-0">
                <button onclick="consentCookie('essential_only')" class="btn btn-secondary btn-sm">Hanya Penting</button>
                <button onclick="consentCookie('accept_all')" class="btn btn-primary btn-sm">Terima Semua</button>
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
    list.innerHTML = '<div class="empty-state p-6"><span class="empty-state__text">Memuat...</span></div>';

    fetch('<?= base_url("notifications/get_notifications"); ?>')
        .then(r => r.json())
        .then(data => {
            if (!data.length) {
                list.innerHTML = '<div class="empty-state"><span class="empty-state__text">Belum ada notifikasi</span></div>';
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
                item.className = 'dropdown-item' + (n.is_read == '0' ? ' dropdown-item--unread' : '');
                item.innerHTML = `
                    <div class="relative flex-shrink-0" style="width:32px;height:32px;margin-top:2px;" data-user-id="${escapeHtml(n.actor_id)}">
                        <img src="${escapeHtml(n.actor_avatar)}" alt="" style="width:32px;height:32px;border-radius:50%;object-fit:cover;"
                             onerror="this.src='<?= assets_url('default.jpg'); ?>'">
                        ${n.actor_is_online ? '<div class="online-indicator"></div>' : ''}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs leading-relaxed">
                            <strong class="font-semibold text-truncate">${escapeHtml(n.actor_username)}</strong>
                            ${escapeHtml(n.message)}
                        </p>
                        <span class="text-caption mt-1 block">${escapeHtml(n.created_at)}</span>
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
            list.innerHTML = '<div class="empty-state p-6"><span class="empty-state__text">Gagal memuat notifikasi</span></div>';
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
            a.classList.remove('dropdown-item--unread');
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

document.addEventListener('DOMContentLoaded', function () {
    if (typeof lucide !== 'undefined') lucide.createIcons();
    showCookieConsentBanner();
});

if (IS_LOGGED_IN) {
    setInterval(updateNotifBadge, 30000);
}

if (IS_LOGGED_IN) {
    function pingHeartbeat() {
        fetch('<?= base_url("home/ping"); ?>', { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: getCsrfField() }).catch(() => {});
    }
    pingHeartbeat();
    setInterval(pingHeartbeat, 30000);
}

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

            const currentPath = window.location.pathname;
            const navLinks = document.querySelectorAll('.nav-bottom, .nav-sidebar');
            
            navLinks.forEach(link => {
                const href = link.getAttribute('href');
                if (!href || href === '#') return;
                
                const linkPath = new URL(href, window.location.origin).pathname;
                
                link.classList.remove('is-active');

                if (currentPath === linkPath) {
                    link.classList.add('is-active');
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
                    showToast(data.message, 'success');
                } else {
                    alert(data.message);
                }
            })
            .catch(err => {
                console.error('Error:', err);
                alert('Terjadi kesalahan. Silakan coba lagi.');
            });
        }

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
                showToast('Alasan laporan harus diisi.', 'error');
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
                showToast(data.message || (data.status === 'success' ? 'Laporan berhasil dikirim.' : 'Gagal mengirim laporan.'), data.status === 'success' ? 'success' : 'error');
            })
            .catch(err => {
                closeReportModal();
                showToast('Terjadi kesalahan. Silakan coba lagi.', 'error');
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
                        btn.className = 'follow-btn btn-follow btn-follow--active';
                    } else {
                        btn.textContent = 'Follow';
                        btn.className = 'follow-btn btn-follow btn-follow--inactive';
                    }
                } else {
                    showToast(data.message || 'Terjadi kesalahan', 'error');
                }
            })
            .catch(err => {
                showToast('Terjadi kesalahan jaringan', 'error');
                console.error('Gagal follow/unfollow:', err);
            });
        }

        function showToast(message, type) {
            const toast = document.createElement('div');
            toast.className = `toast toast--${type || 'success'}`;
            toast.textContent = message;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 3000);
        }

        lucide.createIcons();
    </script>
</body>
</html>
