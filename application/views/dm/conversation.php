<div class="dm-room">
    <!-- Header -->
    <div class="dm-room__header">
        <a href="<?= base_url('dm'); ?>" class="dm-room__back" title="Kembali ke daftar pesan" aria-label="Kembali">
            <i data-lucide="arrow-left"></i>
        </a>

        <a href="<?= base_url('user/' . $other['username']); ?>" class="dm-room__person">
            <span class="dm-room__avatar">
                <span class="dm-room__avatar-img">
                    <img src="<?= $other['avatar']; ?>" alt="" loading="lazy"
                         onerror="this.src='<?= assets_url('default.jpg'); ?>';">
                </span>
                <?php if (!empty($other['border'])): ?>
                    <span class="dm-room__avatar-border">
                        <img src="<?= $other['border']; ?>" alt="" loading="lazy">
                    </span>
                <?php endif; ?>
            </span>

            <span class="dm-room__ident">
                <span class="dm-room__name">
                    <span class="dm-room__name-text">
                        <?= htmlspecialchars($other['display_name'] ?: $other['username'], ENT_QUOTES, 'UTF-8'); ?>
                    </span>
                    <?php if ($other['verified'] == 1): ?>
                        <i data-lucide="badge-check" class="dm-room__verified"></i>
                    <?php endif; ?>
                </span>
                <span class="dm-room__status <?= $other['is_online'] ? 'is-online' : ''; ?>">
                    <span class="dm-room__status-dot"></span>
                    <?= $other['is_online'] ? 'Online' : '@' . htmlspecialchars($other['username'], ENT_QUOTES, 'UTF-8'); ?>
                </span>
            </span>
        </a>

        <div class="dm-room__menu" id="dm-menu-container">
            <button type="button" onclick="toggleDmMenu()" class="dm-room__icon-btn" title="Opsi" aria-label="Opsi">
                <i data-lucide="ellipsis-vertical"></i>
            </button>
            <div id="dm-menu" class="dm-menu hidden">
                <button type="button" onclick="openDmImageMaker()" class="dm-menu__item">
                    <i data-lucide="image"></i>
                    Lampirkan Gambar
                </button>
                <button type="button" onclick="deleteConversation()" class="dm-menu__item dm-menu__item--danger">
                    <i data-lucide="trash-2"></i>
                    Hapus Percakapan
                </button>
            </div>
        </div>
    </div>

    <?php if ($is_blocked): ?>
        <div class="dm-room__blocked">
            <i data-lucide="ban"></i>
            <p>Tidak dapat mengirim atau melihat pesan dari pengguna ini.</p>
        </div>
    <?php else: ?>
        <!-- Messages area -->
        <div id="dm-messages" class="dm-room__messages">
            <div class="empty-state dm-room__aux">
                <div class="spinner spinner--sm mb-3"></div>
                <p class="empty-state__text">Memuat pesan...</p>
            </div>
        </div>

        <!-- Composer -->
        <div class="dm-room__composer">
            <form id="dm-form" class="dm-composer" enctype="multipart/form-data" autocomplete="off">
                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                <input type="hidden" name="id_dm" value="<?= $id_dm; ?>">
                <input type="file" id="dm-image-input" name="image" accept="image/*" class="hidden">
                <input type="hidden" id="dm-reply-to-input" value="">

                <div id="dm-reply-preview" class="dm-reply-preview hidden">
                    <span class="dm-reply-preview__bar"></span>
                    <span class="dm-reply-preview__body">
                        <span class="dm-reply-preview__label">Membalas <b id="dm-reply-preview-user"></b></span>
                        <span class="dm-reply-preview__text" id="dm-reply-preview-text"></span>
                    </span>
                    <button type="button" onclick="cancelDmReply()" class="dm-reply-preview__close" title="Batal membalas" aria-label="Batal membalas">
                        <i data-lucide="x"></i>
                    </button>
                </div>

                <div id="dm-image-preview" class="dm-preview hidden">
                    <img id="dm-image-preview-img" src="" alt="Pratinjau gambar">
                    <button type="button" onclick="clearDmImage()" class="dm-preview__remove" title="Hapus gambar" aria-label="Hapus gambar">
                        <i data-lucide="x"></i>
                    </button>
                </div>

                <div class="dm-composer__row">
                    <button type="button" onclick="openDmImageMaker()" class="dm-composer__icon" title="Lampirkan gambar" aria-label="Lampirkan gambar">
                        <i data-lucide="image"></i>
                    </button>

                    <textarea
                        name="content"
                        id="dm-input"
                        class="dm-composer__input"
                        placeholder="Tulis pesan..."
                        maxlength="1000"
                        rows="1"
                        autocomplete="off"
                    ></textarea>

                    <button type="submit" id="dm-send-btn" class="dm-composer__send" title="Kirim pesan" aria-label="Kirim pesan">
                        <i data-lucide="send"></i>
                    </button>
                </div>

                <p class="dm-composer__hint">Enter untuk kirim, Shift+Enter untuk baris baru.</p>
            </form>
        </div>
    <?php endif; ?>
</div>

<!-- Image Lightbox -->
<div id="dm-lightbox" class="dm-lightbox hidden">
    <div class="dm-lightbox__backdrop" onclick="dmLightboxClose()"></div>

    <div class="dm-lightbox__tools">
        <a id="dm-lightbox-download" href="#" download class="dm-lightbox__download">
            <i data-lucide="download"></i>
            Download
        </a>
        <button type="button" onclick="dmLightboxClose()" class="dm-lightbox__close" title="Tutup" aria-label="Tutup">
            <i data-lucide="x"></i>
        </button>
    </div>

    <button id="dm-lightbox-prev" onclick="dmLightboxNav(-1)" class="dm-lightbox__nav dm-lightbox__nav--prev hidden" title="Sebelumnya" aria-label="Gambar sebelumnya">
        <i data-lucide="chevron-left"></i>
    </button>

    <div class="dm-lightbox__stage" onclick="dmLightboxClose()">
        <img id="dm-lightbox-img" src="" alt="Gambar" onclick="event.stopPropagation();">
    </div>

    <button id="dm-lightbox-next" onclick="dmLightboxNav(1)" class="dm-lightbox__nav dm-lightbox__nav--next hidden" title="Berikutnya" aria-label="Gambar berikutnya">
        <i data-lucide="chevron-right"></i>
    </button>

    <div id="dm-lightbox-counter" class="dm-lightbox__counter"></div>
</div>

<script src="<?= base_url('assets/js/pusher.min.js'); ?>"></script>
<script>
// Mobile keyboard/viewport fix for fullscreen conversation layout
(function() {
    var vv = window.visualViewport;
    var root = document.documentElement;
    function updateAppVh() {
        var h = (vv ? vv.height : window.innerHeight);
        var top = (vv ? (vv.offsetTop || 0) : 0);
        root.style.setProperty('--app-vh', (h / 100) + 'px');
        root.style.setProperty('--app-vv-top', top + 'px');
        var bottomNav = document.querySelector('.bottom-nav');
        if (bottomNav) {
            // Use actual rendered height (includes safe-area padding).
            var navH = bottomNav.offsetHeight || 0;
            if (navH > 0) root.style.setProperty('--bottom-nav-h', navH + 'px');
            var kb = window.innerHeight - h - top;
            if (kb > 80) {
                bottomNav.style.bottom = kb + 'px';
                root.style.setProperty('--bottom-nav-kb', kb + 'px');
            } else {
                bottomNav.style.bottom = '';
                root.style.setProperty('--bottom-nav-kb', '0px');
            }
        }
    }
    if (vv) {
        vv.addEventListener('resize', updateAppVh);
        vv.addEventListener('scroll', updateAppVh);
    }
    window.addEventListener('resize', updateAppVh);
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', updateAppVh);
    }
    updateAppVh();
    // Re-measure after fonts/layout settle.
    setTimeout(updateAppVh, 60);
    setTimeout(updateAppVh, 250);
})();
function initDmConversation() {
    var messagesEl = document.getElementById('dm-messages');
    var idDm = '<?= $id_dm; ?>';
    var currentUserId = String(<?= json_encode($current_user_id); ?>);
    var pusherKey = <?= json_encode($pusher_key); ?>;
    var pusherCluster = <?= json_encode($pusher_cluster); ?>;
    var baseUrl = '<?= base_url(); ?>';
    var defaultAvatar = '<?= assets_url('default.jpg'); ?>';
    var isBlocked = <?= json_encode((bool) $is_blocked); ?>;
    var loadedMessageIds = {};
    var oldestId = null;
    var hasMoreMessages = true;
    var isLoadingOlder = false;
    // Jarak waktu yang masih dianggap "pesan beruntun" dari user sama.
    var GROUP_WINDOW_MS = 3 * 60 * 1000;
    var loadMoreEl = document.getElementById('dm-load-more');
    var loadMoreBtn = document.getElementById('dm-load-more-btn');

    function log() { console.log.apply(console, ['[DM]'].concat(Array.prototype.slice.call(arguments))); }
    function logErr() { console.error.apply(console, ['[DM]'].concat(Array.prototype.slice.call(arguments))); }

    function scrollToBottom() {
        messagesEl.scrollTop = messagesEl.scrollHeight;
    }

    // Kalau user sudah scroll ke atas, pesan baru tidak boleh mencuri posisi.
    function isNearBottom() {
        return messagesEl.scrollHeight - messagesEl.scrollTop - messagesEl.clientHeight < 120;
    }

    // Notifikasi singkat; pakai toast global kalau tersedia.
    function notify(text) {
        if (typeof showToast === 'function') {
            showToast(text, 'error');
        } else {
            alert(text);
        }
    }

    // escapeHtml & linkifyContent global ada di layout/footer.php. Fallback
    // menjaga renderer tetap jalan bila footer gagal dimuat.
    function esc(text) {
        if (typeof escapeHtml === 'function') return escapeHtml(text);
        var div = document.createElement('div');
        div.textContent = text == null ? '' : text;
        return div.innerHTML;
    }

    // Dipakai untuk argumen string di dalam atribut onclick.
    function jsStr(str) {
        if (typeof escapeJsString === 'function') return escapeJsString(str);
        // Use JSON.stringify for robust escaping - it adds quotes and escapes all special chars
        // CACHE BUST: v20241013
        return JSON.stringify(String(str));
    }

    function linkifyEscaped(text) {
        var out = String(text).replace(/(https?:\/\/[^\s<]+)/g, function(m) {
            var safe = m.replace(/&amp;/g, '&');
            return '<a href="' + safe + '" target="_blank" rel="noopener noreferrer" class="dm-link">' + m + '</a>';
        });
        // @mention dan #hashtag ditangani helper yang sama seperti di halaman lain.
        if (typeof linkifyContent === 'function') out = linkifyContent(out);
        return out;
    }

    // Waktu database disimpan dalam zona WIB (+07:00).
    function parseDate(dateStr) {
        if (!dateStr) return null;
        var d;
        if (dateStr.indexOf('+') !== -1) {
            d = new Date(dateStr.replace(' ', 'T'));
        } else {
            d = new Date(dateStr.replace(' ', 'T') + '+07:00');
        }
        return isNaN(d.getTime()) ? null : d;
    }

    function dateValue(dateStr) {
        var d = parseDate(dateStr);
        return d ? d.getTime() : 0;
    }

    function formatTime(dateStr) {
        var d = parseDate(dateStr);
        if (!d) return '';
        var h = String(d.getHours()).padStart(2, '0');
        var m = String(d.getMinutes()).padStart(2, '0');
        return h + ':' + m;
    }

    // Textarea tumbuh mengikuti isi, dibatasi supaya composer tidak menutupi
    // area pesan di layar kecil.
    function autoGrow() {
        var el = document.getElementById('dm-input');
        if (!el) return;
        el.style.height = 'auto';
        el.style.height = Math.min(el.scrollHeight, 120) + 'px';
    }

    function clearEmptyState() {
        var aux = messagesEl.querySelector('.empty-state');
        if (aux) aux.remove();
    }

    // Kosongkan daftar tanpa ikut membuang tombol "muat pesan sebelumnya",
    // karena tombol itu anak dari kontainer scroll ini.
    function resetMessages() {
        var nodes = Array.prototype.slice.call(messagesEl.children);
        nodes.forEach(function(node) {
            if (node !== loadMoreEl) node.remove();
        });
    }

    function setAuxState(html) {
        resetMessages();
        messagesEl.insertAdjacentHTML('beforeend', html);
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    // Pembatas tanggal supaya percakapan panjang tetap mudah dipindai.
    function appendDayDivider(dateStr, target) {
        var d = parseDate(dateStr);
        if (!d) return null;
        var div = document.createElement('div');
        div.className = 'dm-daydiv';
        div.setAttribute('data-day', d.toDateString());
        div.textContent = d.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
        (target || messagesEl).appendChild(div);
        return div;
    }

    // Saat prepend, bandingkan dengan hari paling awal yang sudah tampil;
    // pesan berikutnya dalam batch yang sama dibandingkan antar batch.
    function appendDayDividerIfNeeded(dateStr, target, prepend) {
        var d = parseDate(dateStr);
        if (!d) return;
        var day = d.toDateString();
        var host = target || messagesEl;
        var own = host.querySelectorAll('.dm-daydiv');
        var shown = messagesEl.querySelectorAll('.dm-daydiv');

        var anchor;
        if (host === messagesEl) {
            anchor = prepend ? shown[0] : shown[shown.length - 1];
        } else {
            anchor = own.length ? own[own.length - 1] : shown[0];
        }

        if (anchor && anchor.getAttribute('data-day') === day) return;
        appendDayDivider(dateStr, host);
    }

    function buildPostCard(post) {
        var postHref = baseUrl + 'post/' + encodeURIComponent(post.username || '') + '/' + post.id_post;
        var html = '<a href="' + esc(postHref) + '" target="_blank" rel="noopener noreferrer" class="dm-post">';
        if (post.media) {
            html += '<span class="dm-post__media">' +
                '<img src="' + esc(post.media) + '" alt="Postingan" loading="lazy" ' +
                'onerror="this.parentNode.style.display=\'none\'">' +
            '</span>';
        }
        html += '<span class="dm-post__body">' +
            '<span class="dm-post__meta">' +
                '<span class="dm-post__author">@' + esc(post.username || '') + '</span>' +
                '<span class="dm-post__label">membagikan postingan</span>' +
            '</span>' +
            '<span class="dm-post__excerpt">' + esc(String(post.content || '').slice(0, 120)) + '</span>' +
            '<span class="dm-post__cta">Buka postingan &rarr;</span>' +
        '</span></a>';
        return html;
    }

    // Cari kutipan dari pesan yang sudah tampil (fallback bila payload tanpa lampiran reply).
    function findReplyFromDom(replyId) {
        if (!replyId) return null;
        var row = document.querySelector('[data-dm-message="' + String(replyId).replace(/"/g, '') + '"]');
        if (!row) return null;
        var textEl = row.querySelector('.dm-msg__text');
        var nameEl = row.querySelector('.dm-msg__name');
        var text = textEl ? (textEl.textContent || '') : '';
        if (!text.trim()) return null;
        return {
            id_message: String(replyId),
            username: nameEl ? nameEl.textContent.replace(/^@/, '') : '',
            content: text
        };
    }

    function buildReplyQuote(data) {
        var rp = data.reply || null;
        if (!rp && data.reply_to_message_id) rp = findReplyFromDom(data.reply_to_message_id);
        if (!rp || !rp.content) return '';
        return '<span class="dm-reply">' +
            '<span class="dm-reply__bar"></span>' +
            '<span class="dm-reply__inner">' +
                '<span class="dm-reply__author">@' + esc(rp.display_name || rp.username || '') + '</span>' +
                '<span class="dm-reply__text">' + esc(String(rp.content).slice(0, 120)) + '</span>' +
            '</span>' +
        '</span>';
    }

    function appendMessage(data, skipScroll, target) {
        if (data.id_message && loadedMessageIds[data.id_message]) return;
        if (data.id_message) loadedMessageIds[data.id_message] = true;

        var host = target || messagesEl;
        if (!target) clearEmptyState();

        var isOwn = String(data.sender_id || data.user_id) === currentUserId;
        var row = document.createElement('div');
        row.className = 'dm-msg' + (isOwn ? ' dm-msg--own' : '');

        // Grouping: pesan beruntun dari user yang sama jadi satu blok rapat.
        var prev = host.lastElementChild;
        if (prev && prev.classList.contains('dm-msg')) {
            var sameUser = prev.getAttribute('data-user') === String(data.sender_id || data.user_id);
            var gap = Math.abs(dateValue(data.created_at) - dateValue(prev.getAttribute('data-time')));
            if (sameUser && gap <= GROUP_WINDOW_MS) row.classList.add('dm-msg--grouped');
        }

        row.setAttribute('data-user', String(data.sender_id || data.user_id));
        row.setAttribute('data-time', data.created_at || '');
        row.setAttribute('data-dm-message', data.id_message);

        var avatar = '<span class="dm-msg__avatar">' +
            '<img src="' + esc(data.avatar || defaultAvatar) + '" alt="" loading="lazy" ' +
            'onerror="this.src=\'' + defaultAvatar + '\'">' +
        '</span>';

        // Nama disembunyikan pada pesan lanjutan agar bubble tetap rapat.
        var name = '';
        if (!isOwn && !row.classList.contains('dm-msg--grouped')) {
            name = '<span class="dm-msg__name">' + esc(data.username || '') + '</span>';
        }

        var body = '';
        if (data.image_url) {
            body += '<span class="dm-msg__image-wrap" onclick="openDmLightbox(\'' + jsStr(data.image_url) + '\')">' +
                '<img src="' + esc(data.image_url) + '" alt="Gambar" loading="lazy" class="dm-msg__image dm-img">' +
            '</span>';
        }

        if (data.post && data.post.id_post) {
            body += buildPostCard(data.post);
        } else if (data.content) {
            body += '<span class="dm-msg__text">' + linkifyEscaped(esc(data.content)) + '</span>';
        }

        // Pesan tanpa teks, gambar, dan kartu kiriman post tidak perlu dirender.
        if (!body) return;

        var inner = '<span class="dm-msg__content">' + name +
            '<span class="dm-msg__bubble">' + buildReplyQuote(data) + body + '</span>' +
            '<span class="dm-msg__time">' + esc(formatTime(data.created_at)) + '</span>' +
        '</span>';

        // Tombol balas cepat (semua pesan); menu 3-dot tetap khusus pesan sendiri.
        var replyBtn = '<button type="button" class="dm-msg__reply-btn" onclick="startDmReply(' + jsStr(data.id_message) + ')" title="Balas pesan" aria-label="Balas pesan">' +
            '<i data-lucide="reply"></i>' +
        '</button>';

        // Message menu (3-dot) - only for own messages
        var menuHtml = '';
        if (isOwn) {
            menuHtml = '<div class="dm-msg__menu-wrap" data-message-id="' + jsStr(data.id_message) + '">' +
                '<button type="button" class="dm-msg__menu-btn" onclick="toggleDmMessageMenu(this)" title="Opsi pesan" aria-label="Opsi pesan">' +
                    '<i data-lucide="ellipsis-vertical"></i>' +
                '</button>' +
                '<div class="dm-msg__menu hidden">' +
                    '<button type="button" class="dm-msg__menu-item" onclick="startDmReply(' + jsStr(data.id_message) + ')">' +
                        '<i data-lucide="reply"></i> Balas' +
                    '</button>' +
                    '<button type="button" class="dm-msg__menu-item dm-msg__menu-item--danger" onclick="deleteDmMessage(' + jsStr(data.id_message) + ')">' +
                        '<i data-lucide="trash-2"></i> Unsend' +
                    '</button>' +
                '</div>' +
            '</div>';
        }

        row.innerHTML = avatar + inner + replyBtn + menuHtml;

        host.appendChild(row);
        if (typeof lucide !== 'undefined') lucide.createIcons();
        if (!skipScroll) scrollToBottom();
    }

    // `oldestId` opsional: dipakai saat sebagian batch sudah pernah dimuat,
    // supaya penanda halaman lama tetap menunjuk pesan tertua yang tampil.
    function updatePagination(messages, oldest) {
        var first = oldest || (messages && messages.length ? messages[0].id_message : null);
        if (first) oldestId = first;
        // API mengembalikan satu halaman 50 pesan; kurang dari itu berarti habis.
        hasMoreMessages = !!(messages && messages.length >= 50);
        if (loadMoreEl) loadMoreEl.hidden = !hasMoreMessages;
    }

    // Muat halaman lama di atas daftar, lalu kunci posisi scroll supaya
    // pesan yang tadi dibaca tidak meloncat.
    function loadOlderMessages() {
        if (isLoadingOlder || !hasMoreMessages || !oldestId) return;
        isLoadingOlder = true;
        loadMoreBtn.disabled = true;
        loadMoreBtn.textContent = 'Memuat...';

        var prevHeight = messagesEl.scrollHeight;
        var prevTop = messagesEl.scrollTop;

        fetch(baseUrl + 'dm/get_messages?id_dm=' + idDm + '&before_id=' + encodeURIComponent(oldestId))
            .then(function(r) { return r.json(); })
            .then(function(messages) {
                if (!Array.isArray(messages) || !messages.length) {
                    hasMoreMessages = false;
                    if (loadMoreEl) loadMoreEl.hidden = true;
                    return;
                }

                var batch = document.createElement('div');
                var addedIds = [];
                messages.forEach(function(msg) {
                    if (msg.id_message && loadedMessageIds[msg.id_message]) return;
                    appendDayDividerIfNeeded(msg.created_at, batch, true);
                    appendMessage(msg, true, batch);
                    addedIds.push(msg.id_message);
                });

                // Tidak ada yang baru: jangan sentuh posisi scroll.
                if (!addedIds.length) {
                    hasMoreMessages = false;
                    if (loadMoreEl) loadMoreEl.hidden = true;
                    return;
                }

                var anchor = loadMoreEl.nextSibling;
                while (batch.firstChild) messagesEl.insertBefore(batch.firstChild, anchor);

                updatePagination(messages, addedIds[0]);
                messagesEl.scrollTop = prevTop + (messagesEl.scrollHeight - prevHeight);
            })
            .catch(function(err) { logErr('Failed to load older messages:', err); })
            .finally(function() {
                isLoadingOlder = false;
                loadMoreBtn.disabled = false;
                loadMoreBtn.innerHTML = '<i data-lucide="chevron-up"></i> Muat pesan sebelumnya';
                if (typeof lucide !== 'undefined') lucide.createIcons();
            });
    }

    function loadMessages() {
        setAuxState('<div class="empty-state dm-room__aux"><div class="spinner spinner--sm mb-3"></div><p class="empty-state__text">Memuat pesan...</p></div>');

        fetch(baseUrl + 'dm/get_messages?id_dm=' + idDm)
            .then(function(r) { return r.json(); })
            .then(function(messages) {
                if (!messages || !messages.length) {
                    setAuxState('<div class="empty-state dm-room__aux">' +
                        '<i data-lucide="mail" class="empty-state__icon"></i>' +
                        '<p class="empty-state__text">Belum ada pesan.</p>' +
                        '<p class="empty-state__text mt-1">Sapalah pengguna ini!</p></div>');
                    updatePagination([], null);
                    return;
                }

                resetMessages();
                messages.forEach(function(msg) {
                    appendDayDividerIfNeeded(msg.created_at);
                    appendMessage(msg, true);
                });

                updatePagination(messages);
                scrollToBottom();
                updateDmBadgeNow();
                log('Loaded ' + messages.length + ' messages');
            })
            .catch(function(err) {
                logErr('Failed to load messages:', err);
                setAuxState('<div class="empty-state dm-room__aux"><p class="empty-state__text">Gagal memuat pesan. Coba muat ulang halaman.</p></div>');
            });
    }

    if (loadMoreBtn) loadMoreBtn.addEventListener('click', loadOlderMessages);

    if (!isBlocked) {
        loadMessages();

        if (pusherKey) {
            initPusher();
        } else {
            logErr('No Pusher key configured');
        }
    }

    function initPusher() {
        if (typeof Pusher === 'undefined') {
            logErr('Pusher JS library not loaded! Real-time disabled.');
            return;
        }

        var pusher = new Pusher(pusherKey, {
            cluster: pusherCluster,
            authEndpoint: baseUrl + 'dm/pusher_auth',
            auth: {
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                }
            },
            enabledTransports: ['ws', 'wss'],
            disabledTransports: []
        });

        pusher.connection.bind('connected', function() {
            log('WebSocket connected!');
        });

        pusher.connection.bind('error', function(err) {
            logErr('Connection error:', err);
        });

        var channelName = 'private-dm-' + idDm;
        log('Subscribing to: ' + channelName);

        var channel = pusher.subscribe(channelName);

        channel.bind('pusher:subscription_succeeded', function() {
            log('Subscribed to ' + channelName + '!');
        });

        channel.bind('pusher:subscription_error', function(status) {
            logErr('Subscription FAILED for ' + channelName + ':', status);
        });

        channel.bind('new-message', function(data) {
            if (String(data.sender_id) === currentUserId) return; // sudah dirender saat kirim
            appendDayDividerIfNeeded(data.created_at);
            appendMessage(data, isNearBottom());
            updateDmBadgeNow();
        });
    }

    // ===== Lampiran gambar =====

    var formEl = document.getElementById('dm-form');
    var inputEl = document.getElementById('dm-input');
    var sendBtn = document.getElementById('dm-send-btn');
    var fileInput = document.getElementById('dm-image-input');

    function openImagePicker() {
        if (fileInput) fileInput.click();
    }

    function clearDmImage() {
        var previewWrap = document.getElementById('dm-image-preview');
        var previewImg = document.getElementById('dm-image-preview-img');
        if (previewImg && previewImg.src && previewImg.src.indexOf('blob:') === 0) {
            URL.revokeObjectURL(previewImg.src);
            previewImg.removeAttribute('src');
        }
        if (fileInput) fileInput.value = '';
        if (previewWrap) previewWrap.classList.add('hidden');
    }

    // Dipakai dari atribut onclick pada tombol di markup.
    window.openDmImageMaker = openImagePicker;
    window.clearDmImage = clearDmImage;

    if (fileInput) {
        fileInput.addEventListener('change', function() {
            var previewWrap = document.getElementById('dm-image-preview');
            var previewImg = document.getElementById('dm-image-preview-img');
            if (!this.files || !this.files[0] || !previewWrap || !previewImg) return;
            if (previewImg.src && previewImg.src.indexOf('blob:') === 0) {
                URL.revokeObjectURL(previewImg.src);
            }
            previewImg.src = URL.createObjectURL(this.files[0]);
            previewWrap.classList.remove('hidden');
        });
    }

    if (formEl && !isBlocked) {
        formEl.addEventListener('submit', function(e) {
            e.preventDefault();
            var content = inputEl ? inputEl.value.trim() : '';
            var hasImage = fileInput && fileInput.files && fileInput.files.length > 0;
            if (!content && !hasImage) return;

            sendBtn.disabled = true;

            var formData = new FormData();
            formData.append(getCsrfTokenName(), getCsrfToken());
            formData.append('id_dm', idDm);
            formData.append('content', content);
            
            // Include reply_to_message_id if replying
            var replyInput = document.getElementById('dm-reply-to-input');
            if (replyInput && replyInput.value) {
                formData.append('reply_to_message_id', replyInput.value);
            }
            
            if (hasImage) formData.append('image', fileInput.files[0]);

            fetch(baseUrl + 'dm/send_message', {
                method: 'POST',
                body: formData
            })
            .then(function(r) { return r.json(); })
            .then(function(res) {
                if (res.success && res.message) {
                    appendMessage(res.message);
                    if (inputEl) { inputEl.value = ''; inputEl.focus(); }
                    autoGrow();
                    window.clearDmImage();
                    if (typeof cancelDmReply === 'function') cancelDmReply(); // Clear reply after successful send
                } else {
                    notify(res.error || 'Gagal mengirim pesan.');
                }
            })
            .catch(function(err) { logErr('Send failed:', err); notify('Koneksi bermasalah. Coba lagi.'); })
            .finally(function() { sendBtn.disabled = false; });
        });

        if (inputEl) {
            // Enter kirim, Shift+Enter untuk baris baru.
            inputEl.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    formEl.requestSubmit ? formEl.requestSubmit() : formEl.dispatchEvent(new Event('submit'));
                }
            });

            inputEl.addEventListener('input', autoGrow);
            autoGrow();
        }
    }

    function closeAllDmMessageMenus() {
        document.querySelectorAll('.dm-msg__menu').forEach(function(menu) {
            menu.classList.add('hidden');
        });
    }

    function toggleDmMessageMenu(btn) {
        closeAllDmMessageMenus();
        var wrap = btn.closest('.dm-msg__menu-wrap');
        if (wrap) {
            var menu = wrap.querySelector('.dm-msg__menu');
            if (menu) menu.classList.toggle('hidden');
        }
    }

    // Expose globally for onclick handlers in dynamically created HTML
    window.toggleDmMessageMenu = toggleDmMessageMenu;

    // Close message menus on outside click
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.dm-msg__menu-wrap')) {
            closeAllDmMessageMenus();
        }
    });
}

var dmMenuOpen = false;
function toggleDmMenu() {
    var menu = document.getElementById('dm-menu');
    if (!menu) return;
    dmMenuOpen = !dmMenuOpen;
    menu.classList.toggle('hidden', !dmMenuOpen);
}
document.addEventListener('click', function(e) {
    var container = document.getElementById('dm-menu-container');
    if (dmMenuOpen && container && !container.contains(e.target)) {
        dmMenuOpen = false;
        document.getElementById('dm-menu').classList.add('hidden');
    }
});

function updateDmBadgeNow() {
    if (typeof updateDmBadge === 'function') updateDmBadge();
}

function deleteConversation() {
    if (typeof showConfirmModal === 'function') {
        showConfirmModal('Hapus percakapan ini untuk kedua pihak? Tindakan ini tidak dapat dibatalkan.', {
            title: 'Hapus Percakapan',
            danger: true,
            confirmText: 'Ya, Hapus',
            cancelText: 'Batal'
        }).then(function(confirmed) {
            if (!confirmed) return;
            fetch('<?= base_url('dm/delete_conversation'); ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: getCsrfField() + '&id_dm=' + '<?= $id_dm; ?>'
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.success) {
                    window.location.href = '<?= base_url('dm'); ?>';
                } else {
                    notify(data.error || 'Gagal menghapus percakapan.');
                }
            })
            .catch(function() { notify('Kesalahan jaringan. Coba lagi.'); });
        });
        return;
    }
    fetch('<?= base_url('dm/delete_conversation'); ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: getCsrfField() + '&id_dm=' + '<?= $id_dm; ?>'
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.success) {
            window.location.href = '<?= base_url('dm'); ?>';
        } else {
            notify(data.error || 'Gagal menghapus percakapan.');
        }
    })
    .catch(function() { notify('Kesalahan jaringan. Coba lagi.'); });
}

function deleteDmMessage(idMessage) {
    openConfirmModal('Hapus pesan ini?');
    window.confirmCallback = function() {
        fetch('<?= base_url('dm/delete_message'); ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: getCsrfField() + '&id_message=' + encodeURIComponent(idMessage)
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.success) {
                var el = document.querySelector('[data-dm-message="' + idMessage + '"]');
                if (el) el.remove();
            } else {
                notify(data.error || 'Gagal menghapus pesan.');
            }
        })
        .catch(function() { notify('Kesalahan jaringan. Coba lagi.'); });
    };
}

function startDmReply(idMessage) {
    var row = document.querySelector('[data-dm-message="' + String(idMessage).replace(/"/g, '') + '"]');
    if (!row) return;
    var textEl = row.querySelector('.dm-msg__text');
    var nameEl = row.querySelector('.dm-msg__name');
    var text = textEl ? (textEl.textContent || '').trim() : '';
    if (!text) return;
    var input = document.getElementById('dm-reply-to-input');
    var prev = document.getElementById('dm-reply-preview');
    var prevUser = document.getElementById('dm-reply-preview-user');
    var prevText = document.getElementById('dm-reply-preview-text');
    if (!input || !prev) return;
    input.value = String(idMessage);
    if (prevUser) prevUser.textContent = '@' + (nameEl ? nameEl.textContent.replace(/^@/, '') : '');
    if (prevText) prevText.textContent = text.slice(0, 120);
    prev.classList.remove('hidden');
    if (typeof lucide !== 'undefined') lucide.createIcons();
    var ta = document.getElementById('dm-input');
    if (ta) ta.focus();
}

function cancelDmReply() {
    var input = document.getElementById('dm-reply-to-input');
    var prev = document.getElementById('dm-reply-preview');
    if (input) input.value = '';
    if (prev) prev.classList.add('hidden');
}

var dmImages = [];
var dmLightboxIndex = 0;

function collectDmImages() {
    dmImages = [];
    var imgs = document.querySelectorAll('#dm-messages .dm-img');
    for (var i = 0; i < imgs.length; i++) {
        if (imgs[i].src) dmImages.push(imgs[i].src);
    }
}

function openDmLightbox(url) {
    if (!url) return;
    collectDmImages();
    var idx = dmImages.indexOf(url);
    if (idx === -1) {
        dmImages.unshift(url);
        idx = 0;
    }
    dmLightboxIndex = idx;
    dmLightboxShow();
    document.getElementById('dm-lightbox').classList.remove('hidden');
}

function dmLightboxShow() {
    var total = dmImages.length;
    document.getElementById('dm-lightbox-img').src = dmImages[dmLightboxIndex];
    document.getElementById('dm-lightbox-download').href = dmImages[dmLightboxIndex];
    var prev = document.getElementById('dm-lightbox-prev');
    var next = document.getElementById('dm-lightbox-next');
    prev.classList.toggle('hidden', total <= 1 || dmLightboxIndex === 0);
    next.classList.toggle('hidden', total <= 1 || dmLightboxIndex >= total - 1);
    document.getElementById('dm-lightbox-counter').textContent = total > 1 ? (dmLightboxIndex + 1) + ' / ' + total : '';
    if (typeof lucide !== 'undefined') lucide.createIcons();
}

function dmLightboxNav(dir) {
    var total = dmImages.length;
    if (total <= 1) return;
    dmLightboxIndex = (dmLightboxIndex + dir + total) % total;
    dmLightboxShow();
}

function dmLightboxClose() {
    document.getElementById('dm-lightbox').classList.add('hidden');
    document.getElementById('dm-lightbox-img').src = '';
}

document.addEventListener('keydown', function(e) {
    var lb = document.getElementById('dm-lightbox');
    if (!lb || lb.classList.contains('hidden')) return;
    if (e.key === 'Escape') dmLightboxClose();
    else if (e.key === 'ArrowLeft') dmLightboxNav(-1);
    else if (e.key === 'ArrowRight') dmLightboxNav(1);
});

(function() {
    var lb = document.getElementById('dm-lightbox');
    if (!lb) return;
    var touchX = null;
    lb.addEventListener('touchstart', function(e) { touchX = e.changedTouches[0].clientX; }, { passive: true });
    lb.addEventListener('touchend', function(e) {
        if (touchX === null) return;
        var dx = e.changedTouches[0].clientX - touchX;
        if (Math.abs(dx) > 50) dmLightboxNav(dx < 0 ? 1 : -1);
        touchX = null;
    }, { passive: true });
})();

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initDmConversation);
} else {
    initDmConversation();
}

if (typeof lucide !== 'undefined') lucide.createIcons();
</script>