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

    <!-- SHARE MODAL -->
    <div id="share-modal" class="modal-backdrop hidden animate-fade-in" onclick="closeShareModal()">
        <div class="modal" onclick="event.stopPropagation();">
            <div class="modal-header">
                <h3 class="modal-title">Bagikan Postingan</h3>
                <button onclick="closeShareModal()" class="modal-close" aria-label="Tutup">
                    <i data-lucide="x" style="width:16px;height:16px;"></i>
                </button>
            </div>

            <div class="modal-body">
                <div class="share-section">
                    <p class="share-section__title">
                        <i data-lucide="mail" style="width:13px;height:13px;"></i>
                        Kirim ke DM
                    </p>
                    <input
                        type="text"
                        id="share-dm-search"
                        class="share-dm-search"
                        placeholder="Cari pengguna..."
                        autocomplete="off"
                        spellcheck="false"
                    >
                    <div id="share-dm-list" class="share-dm-list">
                        <div class="share-dm-loading">Memuat kontak...</div>
                    </div>
                    <div id="share-dm-empty" class="share-dm-empty hidden">Pengguna tidak ditemukan.</div>
                </div>

                <div class="share-section">
                    <p class="share-section__title">
                        <i data-lucide="globe" style="width:13px;height:13px;"></i>
                        Bagikan ke luar
                    </p>
                    <div class="share-externals">
                        <button type="button" class="btn btn-secondary btn-sm share-external-btn" onclick="shareExternal('wa')">
                            <img class="share-external-icon" data-share-icon="whatsapp" alt="" aria-hidden="true"> WhatsApp
                        </button>
                        <button type="button" class="btn btn-secondary btn-sm share-external-btn" onclick="shareExternal('x')">
                            <img class="share-external-icon" data-share-icon="x" alt="" aria-hidden="true"> X
                        </button>
                        <button type="button" class="btn btn-secondary btn-sm share-external-btn" onclick="shareExternal('telegram')">
                            <img class="share-external-icon" data-share-icon="telegram" alt="" aria-hidden="true"> Telegram
                        </button>
                        <button type="button" class="btn btn-secondary btn-sm share-external-btn" onclick="shareExternal('facebook')">
                            <img class="share-external-icon" data-share-icon="facebook" alt="" aria-hidden="true"> Facebook
                        </button>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button id="share-copy-btn" onclick="copyShareLink()" class="btn btn-secondary btn-sm flex-1">
                    <i data-lucide="link" style="width:13px;height:13px;"></i> Salin Link
                </button>
                <a id="share-open-link" href="#" target="_blank" rel="noopener" class="btn btn-primary btn-sm flex-1" style="text-decoration:none;">
                    Buka Postingan
                </a>
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
            <a href="<?= base_url('dm'); ?>" class="bottom-nav__item nav-bottom" data-nav="dm">
                <div class="relative">
                    <i data-lucide="mail" style="width:20px;height:20px;"></i>
                    <span class="dm-badge absolute badge-count hidden">0</span>
                </div>
                <span>Pesan</span>
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

// === GLOBAL FETCH WRAPPER: tandai semua request sebagai AJAX (mencegah race session login) ===
(function () {
    const _fetch = window.fetch;
    window.fetch = function (input, init) {
        let sameOrigin = true;
        if (typeof input === 'string') {
            if (/^https?:\/\//i.test(input) && !input.startsWith(window.location.origin)) {
                sameOrigin = false;
            }
        } else if (input && input.url) {
            if (/^https?:\/\//i.test(input.url) && !input.url.startsWith(window.location.origin)) {
                sameOrigin = false;
            }
        }
        if (sameOrigin) {
            init = init || {};
            const headers = new Headers(init.headers || {});
            if (!headers.has('X-Requested-With')) headers.set('X-Requested-With', 'XMLHttpRequest');
            init.headers = headers;
        }
        return _fetch.call(this, input, init);
    };
})();

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
                } else if (n.type === 'dm') {
                    link = '<?= base_url("dm/conversation/"); ?>' + (n.id_dm || '');
                } else if (n.type === 'admin') {
                    link = (n.gift_type || n.gift_coins) ? '<?= base_url("borders"); ?>' : '<?= base_url("home"); ?>';
                }

                const item = document.createElement('a');
                item.href = link;
                item.className = 'dropdown-item' + (n.is_read == '0' ? ' dropdown-item--unread' : '');
                item.innerHTML = `
                    <div class="relative flex-shrink-0" style="width:32px;height:32px;margin-top:2px;" data-user-id="${escapeHtml(n.actor_id)}">
                        <img src="${escapeHtml(n.actor_avatar)}" alt="" style="width:32px;height:32px;border-radius:50%;object-fit:cover;"
                             onerror="this.src='<?= assets_url('default.jpg'); ?>'">
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

function updateDmBadge() {
    if (!IS_LOGGED_IN) return;
    fetch('<?= base_url("dm/get_unread_count"); ?>')
        .then(r => r.json())
        .then(data => {
            const count = (data && data.count) ? data.count : 0;
            document.querySelectorAll('#dm-badge, .bottom-nav .dm-badge').forEach(badge => {
                if (count > 0) {
                    badge.textContent = count > 9 ? '9+' : count;
                    badge.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                }
            });
        })
        .catch(() => {});
}

function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}

function escapeAttr(str) {
    return String(str == null ? '' : str)
        .replace(/&/g, '&amp;')
        .replace(/"/g, '&quot;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;');
}

// Ubah @mention & #hashtag di teks yang SUDAH di-escape menjadi link klik.
// Entitas HTML (&...;) dibiarkan utuh, @user -> /user/..., #topik -> /search?q=...
const BASE_URL_JS = <?= json_encode(base_url(), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
function linkifyContent(escapedText) {
    return String(escapedText).replace(/(&(?:#\d+|#x[\da-f]+|[a-z]+);)|@([A-Za-z0-9_]+)|#([A-Za-z0-9_]+)/gi, function(match, entity, mention, hashtag) {
        if (entity) return entity;
        if (mention) {
            return '<a href="' + BASE_URL_JS + 'user/' + encodeURIComponent(mention) + '" class="post-mention">@' + mention + '</a>';
        }
        if (hashtag) {
            return '<a href="' + BASE_URL_JS + 'search?q=' + encodeURIComponent(hashtag) + '" class="post-hashtag">#' + hashtag + '</a>';
        }
        return match;
    });
}

// === SHARE POSTINGAN (ke luar & ke DM) ===
let __sharePost = null;
let __shareDmUsers = null;
let __shareSearchTimer = null;
let __shareSearchSeq = 0;

function openShareModal(payload) {
    __sharePost = payload || null;
    if (!__sharePost) return;

    const openLink = document.getElementById('share-open-link');
    if (openLink) openLink.href = getSharePostUrl();

    const searchEl = document.getElementById('share-dm-search');
    if (searchEl) searchEl.value = '';

    bindShareDmSearch();

    const modal = document.getElementById('share-modal');
    if (!modal) return;
    document.getElementById('share-dm-list').innerHTML = '<div class="share-dm-loading">Memuat kontak...</div>';
    const dmEmpty = document.getElementById('share-dm-empty');
    if (dmEmpty) dmEmpty.classList.add('hidden');
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    if (typeof lucide !== 'undefined') lucide.createIcons();
    loadShareExternalIcons();

    loadDmRecipients();
}

const SHARE_ICON_CDN = 'https://cdn.simpleicons.org/';

function loadShareExternalIcons() {
    const icons = document.querySelectorAll('[data-share-icon]');
    icons.forEach((img) => {
        const slug = img.getAttribute('data-share-icon');
        if (!slug || img.dataset.shareIconLoaded === '1') return;
        img.dataset.shareIconLoaded = '1';
        img.addEventListener('error', () => { img.style.visibility = 'hidden'; }, { once: true });
        img.src = SHARE_ICON_CDN + slug;
    });
}

function bindShareDmSearch() {
    const searchEl = document.getElementById('share-dm-search');
    if (!searchEl || searchEl.getAttribute('data-bound') === '1') return;
    searchEl.setAttribute('data-bound', '1');
    let lastQuery = '';
    searchEl.addEventListener('input', function() {
        const q = (this.value || '').trim();
        if (q === lastQuery) return;
        lastQuery = q;
        clearTimeout(__shareSearchTimer);
        __shareSearchTimer = setTimeout(function() {
            if (q) {
                searchShareUsers(q);
            } else {
                renderDmRecipients('');
            }
        }, 250);
    });
}

function searchShareUsers(q) {
    const list = document.getElementById('share-dm-list');
    if (!list) return;
    const seq = ++__shareSearchSeq;
    list.innerHTML = '<div class="share-dm-loading">Mencari pengguna...</div>';
    fetch(BASE_URL_JS + 'dm/share_search?q=' + encodeURIComponent(q))
        .then(function(r) { return r.json(); })
        .then(function(users) {
            if (seq !== __shareSearchSeq) return;
            renderDmRecipients(q, Array.isArray(users) ? users : []);
        })
        .catch(function() {
            if (seq !== __shareSearchSeq) return;
            list.innerHTML = '<div class="share-dm-loading c-danger">Gagal mencari pengguna.</div>';
        });
}

function closeShareModal() {
    const modal = document.getElementById('share-modal');
    if (!modal) return;
    modal.classList.add('hidden');
    document.body.style.overflow = '';
    __sharePost = null;
}

function getSharePostUrl() {
    if (!__sharePost) return '#';
    return BASE_URL_JS + 'post/' + encodeURIComponent(__sharePost.username) + '/' + __sharePost.id;
}

function getShareCaption() {
    const text = String((__sharePost && __sharePost.text) || '').trim();
    return 'Lihat postingan ini di PaddockID' + (text ? ': "' + text.slice(0, 140) + '"' : '');
}

function loadDmRecipients() {
    const list = document.getElementById('share-dm-list');
    const empty = document.getElementById('share-dm-empty');
    if (!list) return;

    if (__shareDmUsers) {
        renderDmRecipients('');
        return;
    }

    fetch(BASE_URL_JS + 'dm/share_recipients')
        .then(r => r.json())
        .then(users => {
            __shareDmUsers = Array.isArray(users) ? users : [];
            renderDmRecipients('');
        })
        .catch(() => {
            list.innerHTML = '<div class="share-dm-loading c-danger">Gagal memuat kontak.</div>';
        });
}

function renderDmRecipients(query, usersOverride) {
    const list = document.getElementById('share-dm-list');
    const empty = document.getElementById('share-dm-empty');
    if (!list) return;
    list.innerHTML = '';

    const q = String(query || '').trim().toLowerCase();
    const users = usersOverride
        ? usersOverride
        : (__shareDmUsers || []).filter(user => {
            if (!q) return true;
            return String(user.username || '').toLowerCase().indexOf(q) >= 0
                || String(user.display_name || '').toLowerCase().indexOf(q) >= 0;
        });

    if (!users.length) {
        if (empty) empty.classList.remove('hidden');
        return;
    }
    if (empty) empty.classList.add('hidden');

    users.forEach(user => {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'share-dm-user';
        btn.title = '@' + (user.username || '');

        btn.innerHTML =
            '<span class="share-dm-user__avatar">' +
                (user.border ? '<img src="' + escapeHtml(user.border) + '" alt="" class="share-dm-user__border" onerror="this.remove()">' : '') +
                '<img src="' + escapeHtml(user.avatar) + '" alt="" class="share-dm-user__avatar-img" loading="lazy" onerror="this.src=\'' + BASE_URL_JS + 'uploads/default.jpg\'">' +
            '</span>' +
            '<span class="share-dm-user__name">' +
                escapeHtml(user.display_name || user.username) +
                (user.verified ? ' <i data-lucide="badge-check" style="width:10px;height:10px;color:var(--color-primary);fill:var(--color-primary);"></i>' : '') +
            '</span>';

        btn.addEventListener('click', function() {
            dmShareSend(user.id_user, btn);
        });
        list.appendChild(btn);
    });

    if (typeof lucide !== 'undefined') lucide.createIcons();
}

function dmShareSend(idUser, btn) {
    if (!__sharePost) return;
    if (!IS_LOGGED_IN) {
        closeShareModal();
        showLoginModal();
        return;
    }

    const original = btn.innerHTML;
    btn.disabled = true;
    btn.style.opacity = '0.6';

    const csrfName = document.querySelector('meta[name="csrf-token-name"]').content;
    const csrfHash = document.querySelector('meta[name="csrf-token-hash"]').content;
    const formData = new URLSearchParams();
    formData.append(csrfName, csrfHash);
    formData.append('id_user', idUser);
    formData.append('id_post', __sharePost.id);

    fetch(BASE_URL_JS + 'dm/share_send', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: formData.toString()
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            showToast('Postingan terkirim lewat DM.', 'success');
            closeShareModal();
        } else {
            showToast(res.error || 'Gagal mengirim DM.', 'error');
            btn.disabled = false;
            btn.style.opacity = '';
            btn.innerHTML = original;
        }
    })
    .catch(() => {
        showToast('Terjadi kesalahan jaringan.', 'error');
        btn.disabled = false;
        btn.style.opacity = '';
        btn.innerHTML = original;
    });
}

function shareExternal(platform) {
    const url = getSharePostUrl();
    const text = getShareCaption();
    let href = '';

    switch (platform) {
        case 'wa':
            href = 'https://wa.me/?text=' + encodeURIComponent(text + '\n' + url);
            break;
        case 'x':
            href = 'https://twitter.com/intent/tweet?text=' + encodeURIComponent(text) + '&url=' + encodeURIComponent(url);
            break;
        case 'telegram':
            href = 'https://t.me/share/url?url=' + encodeURIComponent(url) + '&text=' + encodeURIComponent(text);
            break;
        case 'facebook':
            href = 'https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(url);
            break;
    }

    if (href) window.open(href, '_blank', 'noopener,noreferrer,width=600,height=520');
}

function copyShareLink() {
    const btn = document.getElementById('share-copy-btn');
    navigator.clipboard.writeText(getSharePostUrl()).then(() => {
        if (!btn) return;
        const original = btn.innerHTML;
        btn.innerHTML = '<i data-lucide="check" style="width:13px;height:13px;"></i> Tersalin!';
        if (typeof lucide !== 'undefined') lucide.createIcons();
        setTimeout(() => {
            btn.innerHTML = original;
            if (typeof lucide !== 'undefined') lucide.createIcons();
        }, 1200);
    }).catch(() => showToast('Gagal menyalin link.', 'error'));
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
    <?php $this->config->load('ads'); ?>
    if (!<?= $this->config->item('adsense_enabled') ? 'true' : 'false'; ?>) return;
    if (Array.from(document.scripts).some(s => s.src.includes('adsbygoogle.js'))) return;
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
    updateDmBadge();
    setInterval(updateDmBadge, 30000);
}

if (IS_LOGGED_IN) {
    function pingHeartbeat() {
        fetch('<?= base_url("home/ping"); ?>', { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: getCsrfField() }).catch(() => {});
    }
    pingHeartbeat();
    setInterval(pingHeartbeat, 30000);
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

        function syncHeaderHeight() {
            const header = document.querySelector('.site-header');
            if (header) {
                document.documentElement.style.setProperty('--header-height', header.offsetHeight + 'px');
            }
        }
        document.addEventListener('DOMContentLoaded', syncHeaderHeight);
        window.addEventListener('resize', syncHeaderHeight);

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

    <script>
        // ===== Team Like Burst: hati berubah jadi logo tim F1 =====
        (function () {
            if (window.PADDOCK_TEAMS) return;
            <?php
                try {
                    $CI =& get_instance();
                    $CI->load->model('Auth_model');
                    $CI->load->helper('assets_url_helper');
                    $_aliases = [
                        'Red Bull Racing' => ['red bull'],
                        'RacingBulls'     => ['racing bulls', 'rb'],
                        'Aston Martin'    => ['aston'],
                    ];
                    $_teams_js = [];
                    foreach ($CI->Auth_model->get_all_teams() as $_t) {
                        $_logo_url = assets_url($_t['team_logo']);
                        $_logo_path = FCPATH . $_t['team_logo'];
                        if (is_file($_logo_path)) {
                            $_logo_url .= '?v=' . filemtime($_logo_path);
                        }
                        $_teams_js[] = [
                            'name'    => $_t['team_name'],
                            'logo'    => $_logo_url,
                            'color'   => $_t['team_color'] ?: '#666',
                            'aliases' => $_aliases[$_t['team_name']] ?? [],
                        ];
                    }
                } catch (Exception $_e) {
                    $_teams_js = [];
                }
            ?>
            window.PADDOCK_TEAMS = <?= json_encode($_teams_js); ?>;
            window.PADDOCK_TEAMS_READY = Promise.resolve(window.PADDOCK_TEAMS);
            window.PADDOCK_TEAMS.forEach(function (t) {
                if (t.logo) {
                    var img = new Image();
                    img.src = t.logo;
                }
            });

            function findTeam(content) {
                const text = ' ' + (content || '').toLowerCase() + ' ';
                for (const team of window.PADDOCK_TEAMS) {
                    const names = [team.name].concat(team.aliases || []);
                    for (const n of names) {
                        const needle = (n || '').toLowerCase().trim();
                        if (!needle) continue;
                        const regex = needle.split(' ').length > 1
                            ? new RegExp('\\b' + needle.replace(/\s+/g, '\\s+') + '\\b')
                            : new RegExp('\\b' + needle + '\\b');
                        if (regex.test(text)) return team;
                    }
                }
                return null;
            }

            function makeLogoEl(team) {
                const chip = document.createElement('span');
                chip.className = 'like-burst__logo';

                const img = document.createElement('img');
                img.alt = team.name || '';
                img.src = team.logo;
                img.addEventListener('load', () => { img.style.opacity = '1'; });

                chip.append(img);
                return chip;
            }

            function spawnBurst(button, team) {
                const rect = button.getBoundingClientRect();
                const x = rect.left + rect.width / 2;
                const y = rect.top + rect.height / 2;

                const burst = document.createElement('div');
                burst.className = 'like-burst';

                const heart = document.createElement('div');
                heart.className = 'like-burst__heart';
                heart.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="44" height="44" viewBox="0 0 24 24" fill="var(--color-danger)" stroke="var(--color-danger)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.51 4.04 3 5.5l7 7Z"/></svg>';

                const logo = makeLogoEl(team);

                burst.append(heart, logo);
                burst.style.left = x + 'px';
                burst.style.top = y + 'px';
                document.body.appendChild(burst);

                const done = () => burst.remove();
                burst.addEventListener('animationend', done, { once: true });
                setTimeout(done, 1600);
            }

            function findTeamByLogo(baseSrc) {
                const clean = String(baseSrc || '').split('?')[0];
                if (!clean) return null;
                for (const t of window.PADDOCK_TEAMS) {
                    if (clean === String(t.logo || '').split('?')[0]) return t;
                }
                return null;
            }

            window.playTeamLikeBurst = function (button) {
                if (!button || !window.PADDOCK_TEAMS || !window.PADDOCK_TEAMS.length) return;

                const card = button.closest('.h-post, .card, article');
                if (!card) return;

                const textEl = card.querySelector('.h-post__text') || card.querySelector('.post-content');
                const text = textEl ? textEl.textContent : '';
                if (!text.trim()) return;

                let team = findTeam(text);
                if (!team) {
                    const badgeEl = card.querySelector('.h-post__team');
                    if (badgeEl) {
                        const badgeImg = badgeEl.querySelector('img');
                        team = findTeam(badgeEl.textContent)
                            || findTeamByLogo(badgeImg ? badgeImg.getAttribute('src') : '');
                    }
                }
                if (!team) return;

                spawnBurst(button, team);
            };
        })();
    </script>

    <!-- SKELETON LOADER ENGINE -->
    <script src="<?= base_url('assets/js/skeleton.js'); ?>?v=<?= filemtime(FCPATH . 'assets/js/skeleton.js'); ?>"></script>

    <!-- SEARCH SUGGEST (header + halaman search) -->
    <script src="<?= base_url('assets/js/search-suggest.js'); ?>?v=<?= filemtime(FCPATH . 'assets/js/search-suggest.js'); ?>"></script>
</body>
</html>
