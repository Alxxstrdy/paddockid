<div class="chat-room">
    <!-- Header -->
    <div class="chat-room__header">
        <a href="<?= base_url('chat'); ?>" class="chat-room__back" title="Kembali ke daftar room" aria-label="Kembali">
            <i data-lucide="arrow-left" style="width:16px;height:16px;"></i>
        </a>

        <div class="chat-room__title">
            <span class="chat-room__race"><?= htmlspecialchars($room['race_name']); ?></span>
            <h1 class="chat-room__session">
                <i data-lucide="message-square"></i>
                <?= htmlspecialchars($room['session_name']); ?>
            </h1>
        </div>

        <div class="chat-room__badges">
            <?php if ($room['room_status'] === 'active'): ?>
                <span class="chat-pill" title="Pengguna yang sedang online">
                    <i data-lucide="users"></i>
                    <span id="online-count" class="chat-pill__count">0</span>
                </span>
                <span class="chat-pill chat-pill--live">
                    <span class="chat-pill__dot"></span>
                    Live
                </span>
            <?php elseif ($room['room_status'] === 'upcoming'): ?>
                <span class="chat-pill chat-pill--upcoming">Upcoming</span>
            <?php else: ?>
                <span class="chat-pill">Selesai</span>
            <?php endif; ?>
        </div>
    </div>

    <!-- Messages area -->
    <div id="chat-messages" class="chat-room__messages">
        <div id="chat-load-more" class="chat-loadmore" hidden>
            <button type="button" id="chat-load-more-btn" class="chat-loadmore__btn">
                <i data-lucide="chevron-up"></i>
                Muat pesan sebelumnya
            </button>
        </div>
        <div class="empty-state" style="margin:auto;">
            <div class="spinner spinner--sm mb-3"></div>
            <p class="empty-state__text">Memuat pesan...</p>
        </div>
    </div>

    <!-- Composer -->
    <div class="chat-room__composer">
        <?php if ($room['room_status'] === 'active'): ?>
            <form id="chat-form" class="chat-composer" autocomplete="off">
                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                <input type="hidden" name="id_room" value="<?= $room['id_room']; ?>">
                <textarea
                    name="content"
                    id="chat-input"
                    class="chat-composer__input"
                    placeholder="Tulis pesan..."
                    maxlength="1000"
                    rows="1"
                    autocomplete="off"
                ></textarea>
                <button type="submit" id="chat-send-btn" class="chat-composer__send" title="Kirim pesan" aria-label="Kirim pesan">
                    <i data-lucide="send"></i>
                </button>
            </form>
            <p class="chat-composer__hint">Enter untuk kirim, Shift+Enter untuk baris baru.</p>
        <?php elseif ($room['room_status'] === 'upcoming'): ?>
            <div class="chat-room__closed">
                <i data-lucide="clock"></i>
                Room ini dibuka <?= date('d M Y · H:i', strtotime($room['opens_at'])); ?>
            </div>
        <?php else: ?>
            <div class="chat-room__closed">
                <i data-lucide="check-check"></i>
                Sesi ini sudah selesai. Room hanya bisa dibaca.
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="<?= base_url('assets/js/pusher.min.js'); ?>"></script>
<script>
function initChatRoom() {
    var messagesEl = document.getElementById('chat-messages');
    var idRoom = '<?= $room['id_room']; ?>';
    var currentUserId = String(<?= json_encode($current_user_id); ?>);
    var roomSlug = <?= json_encode($room['slug']); ?>;
    var pusherKey = <?= json_encode($pusher_key); ?>;
    var pusherCluster = <?= json_encode($pusher_cluster); ?>;
    var isRoomActive = <?= json_encode($room['room_status'] === 'active'); ?>;
    var baseUrl = '<?= base_url(); ?>';
    var loadedMessageIds = {};
    var onlineUsers = {};
    var oldestLoadedId = null;
    var hasMoreMessages = true;
    var isLoadingOlder = false;
    var onlineCountEl = document.getElementById('online-count');
    var loadMoreEl = document.getElementById('chat-load-more');
    var loadMoreBtn = document.getElementById('chat-load-more-btn');
    // Jarak waktu yang masih dianggap "pesan beruntun" dari user sama.
    var GROUP_WINDOW_MS = 3 * 60 * 1000;

    function log() { console.log.apply(console, ['[Chat]'].concat(Array.prototype.slice.call(arguments))); }
    function logErr() { console.error.apply(console, ['[Chat]'].concat(Array.prototype.slice.call(arguments))); }

    function scrollToBottom() {
        messagesEl.scrollTop = messagesEl.scrollHeight;
    }

    // Kalau user sudah scroll ke atas, pesan baru tidak boleh mencuri posisi.
    function isNearBottom() {
        return messagesEl.scrollHeight - messagesEl.scrollTop - messagesEl.clientHeight < 120;
    }

    function updateOnlineCount() {
        var count = Object.keys(onlineUsers).length;
        if (onlineCountEl) onlineCountEl.textContent = count;
    }

    // Notifikasi join/leave sebagai pill di tengah, bukan teks miring.
    function appendSystemNotice(text) {
        var div = document.createElement('div');
        div.className = 'chat-notice';
        div.textContent = text;
        messagesEl.appendChild(div);
        scrollToBottom();
    }

    function clearEmptyState() {
        var aux = messagesEl.querySelector('[data-sk-aux], .empty-state');
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

    // Pembatas tanggal supaya percakapan panjang tetap mudah dipindai.
    function appendDayDivider(dateStr, target) {
        var d = parseDate(dateStr);
        if (!d) return null;
        var div = document.createElement('div');
        div.className = 'chat-daydiv';
        div.setAttribute('data-day', d.toDateString());
        div.textContent = d.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
        (target || messagesEl).appendChild(div);
        return div;
    }

    // Hanya sisipkan pembatas kalau hari berubah dari pesan yang berdampingan.
    // Saat prepend, pesanchio dibandingkan dengan hari paling awal yang sudah tampil,
    // sedangkan pesan berikutnya dalam batch yang sama dibandingkan antar batch.
    function appendDayDividerIfNeeded(dateStr, target, prepend) {
        var d = parseDate(dateStr);
        if (!d) return;
        var day = d.toDateString();
        var host = target || messagesEl;
        var own = host.querySelectorAll('.chat-daydiv');
        var shown = messagesEl.querySelectorAll('.chat-daydiv');

        var anchor;
        if (host === messagesEl) {
            anchor = prepend ? shown[0] : shown[shown.length - 1];
        } else {
            // Batch prepend: pakai pembatas terakhir di dalam batch, atau
            // pembatas pertama yang sudah tampil kalau batch masih kosong.
            anchor = own.length ? own[own.length - 1] : shown[0];
        }

        if (anchor && anchor.getAttribute('data-day') === day) return;
        appendDayDivider(dateStr, host);
    }

    function appendMessage(data, skipScroll, target) {
        if (data.id_message && loadedMessageIds[data.id_message]) return;
        if (data.id_message) loadedMessageIds[data.id_message] = true;

        var host = target || messagesEl;
        if (!target) clearEmptyState();

        var isOwn = String(data.user_id) === currentUserId;
        var row = document.createElement('div');
        row.className = 'chat-msg' + (isOwn ? ' chat-msg--own' : '');

        // Grouping: pesan beruntun dari user yang sama jadi satu blok rapat.
        var prev = host.lastElementChild;
        if (prev && prev.classList.contains('chat-msg')) {
            var sameUser = prev.getAttribute('data-user') === String(data.user_id);
            var gap = Math.abs(dateValue(data.created_at) - dateValue(prev.getAttribute('data-time')));
            if (sameUser && gap <= GROUP_WINDOW_MS) row.classList.add('chat-msg--grouped');
        }

        row.setAttribute('data-user', String(data.user_id));
        row.setAttribute('data-time', data.created_at || '');

        var html = '<div class="chat-msg__avatar">' +
            '<img src="' + esc(data.avatar) + '" alt="" onerror="this.src=\'' + baseUrl + 'uploads/default.jpg\'">' +
            '</div>' +
            '<div class="chat-msg__content">';

        // Nama disembunyikan pada pesan lanjutan agar bubble tetap rapat.
        if (!isOwn && !row.classList.contains('chat-msg--grouped')) {
            html += '<span class="chat-msg__name">' + esc(data.username) + '</span>';
        }

        html += '<div class="chat-msg__bubble">' + linkifyEscaped(esc(data.content)) + '</div>';
        html += '<span class="chat-msg__time">' + esc(formatTime(data.created_at)) + '</span>';
        html += '</div>';

        row.innerHTML = html;
        host.appendChild(row);
        if (!skipScroll) scrollToBottom();
    }

    // Notifikasi singkat; pakai toast global kalau tersedia.
    function notify(text) {
        if (typeof showToast === 'function') {
            showToast(text, 'error');
        } else {
            alert(text);
        }
    }

    // Textarea tumbuh mengikuti isi, dibatasi tinggi supaya composer tidak
    // menutupi area pesan di layar kecil.
    function autoGrow() {
        var inputEl = document.getElementById('chat-input');
        if (!inputEl) return;
        inputEl.style.height = 'auto';
        inputEl.style.height = Math.min(inputEl.scrollHeight, 120) + 'px';
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
        // Tanggal rusak/jam di luar rentang menghasilkan "Invalid Date";
        // treat sebagai tidak ada tanggal supaya tidak muncul label aneh.
        return isNaN(d.getTime()) ? null : d;
    }

    function dateValue(dateStr) {
        var d = parseDate(dateStr);
        return d ? d.getTime() : 0;
    }

    // Jam locais, sama seperti DM, supaya konsisten antar fitur chat.
    function formatTime(dateStr) {
        var d = parseDate(dateStr);
        if (!d) return '';
        var h = String(d.getHours()).padStart(2, '0');
        var m = String(d.getMinutes()).padStart(2, '0');
        return h + ':' + m;
    }

    // escapeHtml & linkifyContent globale ada di layout/footer.php. Room ini
    // memakainya, tapi fallback menjaga agar renderer tetap jalan bila footer
    // gagal dimuat atau urutan load berubah.
    function esc(text) {
        if (typeof escapeHtml === 'function') return escapeHtml(text);
        var div = document.createElement('div');
        div.textContent = text == null ? '' : text;
        return div.innerHTML;
    }

    function linkifyEscaped(text) {
        var out = String(text).replace(/(https?:\/\/[^\s<]+)/g, function(m) {
            var safe = m.replace(/&amp;/g, '&');
            return '<a href="' + safe + '" target="_blank" rel="noopener noreferrer" class="chat-link">' + m + '</a>';
        });
        // @mention dan #hashtag ditangani helper yang sama denganyli post.
        if (typeof linkifyContent === 'function') out = linkifyContent(out);
        return out;
    }

    function loadMessages() {
        resetMessages();
        messagesEl.insertAdjacentHTML('beforeend', '<div class="empty-state" style="margin:auto;"><div class="spinner spinner--sm mb-3"></div><p class="empty-state__text">Memuat pesan...</p></div>');

        fetch(baseUrl + 'chat/get_messages?id_room=' + idRoom)
            .then(function(r) { return r.json(); })
            .then(function(messages) {
                resetMessages();
                if (!messages || !messages.length) {
                    messagesEl.insertAdjacentHTML('beforeend', '<div class="empty-state" style="margin:auto;">' +
                        '<i data-lucide="message-square" class="empty-state__icon" style="width:30px;height:30px;"></i>' +
                        '<p class="empty-state__text">Belum ada pesan di room ini.</p>' +
                        '<p class="empty-state__text mt-1">Jadilah yang pertama bicara.</p></div>');
                    if (typeof lucide !== 'undefined') lucide.createIcons();
                    return;
                }

                // Satu pembatas tanggal per hari, disisipkan sebelum pesan pertama hari itu.
                messages.forEach(function(msg) {
                    appendDayDividerIfNeeded(msg.created_at);
                    appendMessage(msg, true);
                });

                updatePagination(messages);
                scrollToBottom();
                log('Loaded ' + messages.length + ' messages');
            })
            .catch(function(err) {
                logErr('Failed to load messages:', err);
                resetMessages();
                messagesEl.insertAdjacentHTML('beforeend', '<div class="empty-state" style="margin:auto;"><p class="empty-state__text">Gagal memuat pesan. Coba muat ulang halaman.</p></div>');
            });
    }

    // `oldestId` opsional: dipakai saat sebagian batch sudah pernah dimuat,
    // supaya penanda halaman lama tetap menunjuk pesan tertua yang tampil.
    function updatePagination(messages, oldestId) {
        var oldest = oldestId || (messages && messages.length ? messages[0].id_message : null);
        if (oldest) oldestLoadedId = oldest;
        // API mengembalikan satu halaman 50 pesan; kurang dari itu berarti sudah habis.
        hasMoreMessages = !!(messages && messages.length >= 50);
        if (loadMoreEl) loadMoreEl.hidden = !hasMoreMessages;
    }

    // Muat halaman lama di atas daftar, lalu kunci posisi scroll supaya
    // pesan yang tadi dibaca tidak meloncat.
    function loadOlderMessages() {
        if (isLoadingOlder || !hasMoreMessages || !oldestLoadedId) return;
        isLoadingOlder = true;
        loadMoreBtn.disabled = true;
        loadMoreBtn.textContent = 'Memuat...';

        var prevHeight = messagesEl.scrollHeight;
        var prevTop = messagesEl.scrollTop;

        fetch(baseUrl + 'chat/get_messages?id_room=' + idRoom + '&before_id=' + encodeURIComponent(oldestLoadedId))
            .then(function(r) { return r.json(); })
            .then(function(messages) {
                if (!messages || !messages.length) {
                    hasMoreMessages = false;
                    loadMoreEl.hidden = true;
                    return;
                }

                var batch = document.createElement('div');
                var addedIds = [];
                messages.forEach(function(msg) {
                    if (msg.id_message && loadedMessageIds[msg.id_message]) return;
                    // Bandingkan hari dengan pesan paling awal yang sudah tampil
                    // supaya tidak ada dua pembatas untuk tanggal yang sama.
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

    if (loadMoreBtn) loadMoreBtn.addEventListener('click', loadOlderMessages);

    loadMessages();

    if (isRoomActive) {
        var formEl = document.getElementById('chat-form');
        var inputEl = document.getElementById('chat-input');
        var sendBtn = document.getElementById('chat-send-btn');

        if (pusherKey) {
            initPusher();
        } else {
            logErr('No Pusher key configured');
        }

        if (formEl && inputEl && sendBtn) {
            formEl.addEventListener('submit', function(e) {
                e.preventDefault();
                var content = inputEl.value.trim();
                if (!content) return;

                sendBtn.disabled = true;

                var formData = new URLSearchParams(new FormData(formEl));

                fetch(baseUrl + 'chat/send_message', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: formData.toString()
                })
                .then(function(r) { return r.json(); })
                .then(function(res) {
                    if (res.success && res.message) {
                        appendMessage(res.message);
                        inputEl.value = '';
                        inputEl.focus();
                        autoGrow();
                    } else {
                        notify(res.error || 'Gagal mengirim pesan.');
                    }
                })
                .catch(function(err) { logErr('Send failed:', err); notify('Koneksi bermasalah. Coba lagi.'); })
                .finally(function() { sendBtn.disabled = false; });
            });

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

    function initPusher() {
        if (typeof Pusher === 'undefined') {
            logErr('Pusher JS library not loaded! Real-time disabled.');
            return;
        }

        log('Init Pusher, key=' + pusherKey + ', cluster=' + pusherCluster);

        var pusher = new Pusher(pusherKey, {
            cluster: pusherCluster,
            authEndpoint: baseUrl + 'chat/pusher_auth',
            auth: {
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                }
            },
            enabledTransports: ['ws', 'wss'],
            disabledTransports: []
        });

        pusher.connection.bind('state_change', function(states) {
            log('Connection state: ' + states.previous + ' -> ' + states.current);
        });

        pusher.connection.bind('connected', function() {
            log('WebSocket connected!');
        });

        pusher.connection.bind('disconnected', function() {
            logErr('WebSocket disconnected!');
        });

        pusher.connection.bind('error', function(err) {
            logErr('Connection error:', err);
        });

        var presenceChannelName = 'presence-chat-' + roomSlug;
        var messageChannelName = 'private-chat-' + roomSlug;
        log('Subscribing to: ' + presenceChannelName + ' + ' + messageChannelName);

        var presenceChannel = pusher.subscribe(presenceChannelName);

        presenceChannel.bind('pusher:member_added', function(member) {
            var id = String(member.id);
            var name = (member.info && member.info.username) || 'Seseorang';
            var isNew = !onlineUsers[id];
            onlineUsers[id] = true;
            updateOnlineCount();
            if (isNew) appendSystemNotice(name + ' bergabung ke room');
        });

        presenceChannel.bind('pusher:member_removed', function(member) {
            var id = String(member.id);
            var name = (member.info && member.info.username) || 'Seseorang';
            if (onlineUsers[id]) {
                delete onlineUsers[id];
                updateOnlineCount();
            }
            appendSystemNotice(name + ' meninggalkan room');
        });

        presenceChannel.bind('pusher:subscription_succeeded', function() {
            log('Subscribed to ' + presenceChannelName + '!');
            var members = (presenceChannel.members && presenceChannel.members.members) || {};
            onlineUsers = {};
            Object.keys(members).forEach(function(id) { onlineUsers[id] = true; });
            updateOnlineCount();
            log('Online members: ' + Object.keys(onlineUsers).length);
        });

        presenceChannel.bind('pusher:subscription_error', function(status) {
            logErr('Presence subscription FAILED (' + status + '). Online features disabled; messages still work via private channel.');
        });

        var messageChannel = pusher.subscribe(messageChannelName);

        messageChannel.bind('pusher:subscription_succeeded', function() {
            log('Subscribed to ' + messageChannelName + '!');
        });

        messageChannel.bind('pusher:subscription_error', function(status) {
            logErr('Subscription FAILED for ' + messageChannelName + ':', status);
        });

        messageChannel.bind('new-message', function(data) {
            if (String(data.user_id) !== currentUserId) {
                appendDayDividerIfNeeded(data.created_at);
                // Hormati posisi scroll: user yang sedang baca riwayat
                // tidak boleh suddenly terlempar ke bawah.
                appendMessage(data, isNearBottom());
            }
        });
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initChatRoom);
} else {
    initChatRoom();
}

if (typeof lucide !== 'undefined') lucide.createIcons();
</script>
