<div class="max-w-3xl mx-auto flex flex-col h-[calc(100vh-140px)]">
    <!-- Header -->
    <div class="flex items-center justify-between mb-3 pb-3 border-b border-white/[0.04] shrink-0">
        <div>
            <span class="text-[10px] uppercase tracking-wider text-slate-500"><?= htmlspecialchars($room['race_name']); ?></span>
            <h1 class="text-base font-bold text-white mt-0.5">
                <i data-lucide="message-square" class="w-4 h-4 inline-block mr-1.5 text-red-500"></i>
                <?= htmlspecialchars($room['session_name']); ?>
            </h1>
        </div>
        <?php if ($room['room_status'] === 'active'): ?>
            <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                <span class="text-[10px] font-semibold text-emerald-400 uppercase tracking-wider">LIVE</span>
            </div>
        <?php elseif ($room['room_status'] === 'upcoming'): ?>
            <div class="text-[10px] text-blue-400 font-semibold uppercase tracking-wider">Upcoming</div>
        <?php else: ?>
            <div class="text-[10px] text-slate-500 font-semibold uppercase tracking-wider">Completed</div>
        <?php endif; ?>
    </div>

    <!-- Messages area -->
    <div id="chat-messages" class="flex-1 overflow-y-auto space-y-2.5 pr-1 scrollbar-thin">
        <div class="flex flex-col items-center justify-center h-full text-slate-500 text-xs">
            <i data-lucide="message-circle" class="w-8 h-8 mb-3 text-slate-600"></i>
            <p>Sending messages to chat you need to login first.</p>
            <p class="mt-1">If you already logged in, you can send message now!</p>
        </div>
    </div>

    <!-- Input area -->
    <div class="mt-3 pt-3 border-t border-white/[0.04] shrink-0">
        <?php if ($room['room_status'] === 'active'): ?>
            <form id="chat-form" class="flex items-center gap-2">
                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                <input type="hidden" name="id_room" value="<?= $room['id_room']; ?>">
                <input
                    type="text"
                    name="content"
                    id="chat-input"
                    placeholder="Type a message..."
                    maxlength="1000"
                    autocomplete="off"
                    class="flex-1 bg-white/[0.04] border border-white/[0.08] rounded-xl px-4 py-2.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-red-500/40 focus:ring-1 focus:ring-red-500/20 transition-all"
                >
                <button
                    type="submit"
                    id="chat-send-btn"
                    class="bg-red-500 hover:bg-red-600 disabled:opacity-40 disabled:cursor-not-allowed text-white rounded-xl px-4 py-2.5 transition-all"
                >
                    <i data-lucide="send" class="w-4 h-4"></i>
                </button>
            </form>
        <?php elseif ($room['room_status'] === 'upcoming'): ?>
            <div class="text-center text-[10px] text-slate-500 py-3 bg-white/[0.02] rounded-xl border border-white/[0.04]">
                This chat room hasn't opened yet. Opens <?= date('d M H:i', strtotime($room['opens_at'])); ?>
            </div>
        <?php else: ?>
            <div class="text-center text-[10px] text-slate-500 py-3 bg-white/[0.02] rounded-xl border border-white/[0.04]">
                This session has ended.
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

    function log() { console.log.apply(console, ['[Chat]'].concat(Array.prototype.slice.call(arguments))); }
    function logErr() { console.error.apply(console, ['[Chat]'].concat(Array.prototype.slice.call(arguments))); }

    function scrollToBottom() {
        messagesEl.scrollTop = messagesEl.scrollHeight;
    }

    function appendMessage(data, skipScroll) {
        if (data.id_message && loadedMessageIds[data.id_message]) return;
        if (data.id_message) loadedMessageIds[data.id_message] = true;

        var empty = messagesEl.querySelector('.flex-col.items-center.justify-center');
        if (empty) messagesEl.innerHTML = '';

        var isOwn = String(data.user_id) === currentUserId;
        var div = document.createElement('div');
        div.className = 'flex items-start gap-2.5 ' + (isOwn ? 'flex-row-reverse' : '');
        div.innerHTML =
            '<div class="relative w-6 h-6 shrink-0 mt-0.5 rounded-full overflow-hidden bg-slate-800">' +
                '<img src="' + data.avatar + '" alt="" class="w-full h-full object-cover" onerror="this.src=\'' + baseUrl + 'uploads/default.jpg\'">' +
            '</div>' +
            '<div class="' + (isOwn ? 'bg-red-500/10 border-red-500/20' : 'bg-white/[0.04] border-white/[0.06]') + ' rounded-xl px-3 py-2 border max-w-[80%]">' +
                '<div class="flex items-center gap-2 mb-0.5">' +
                    '<span class="text-[10px] font-semibold ' + (isOwn ? 'text-red-400' : 'text-slate-300') + '">' + data.username + '</span>' +
                    '<span class="text-[9px] text-slate-600">' + timeAgo(data.created_at) + '</span>' +
                '</div>' +
                '<p class="text-xs text-slate-200 leading-relaxed break-words">' + data.content + '</p>' +
            '</div>';
        messagesEl.appendChild(div);
        if (!skipScroll) scrollToBottom();
    }

    function timeAgo(dateStr) {
        if (!dateStr) return '';
        var date;
        if (dateStr.indexOf('+') !== -1) {
            date = new Date(dateStr.replace(' ', 'T'));
        } else {
            date = new Date(dateStr.replace(' ', 'T') + '+07:00');
        }
        var diff = Math.floor((new Date() - date) / 1000);
        if (diff < 0 || diff < 60) return 'now';
        if (diff < 3600) return Math.floor(diff / 60) + 'm';
        if (diff < 86400) return Math.floor(diff / 3600) + 'h';
        return Math.floor(diff / 86400) + 'd';
    }

    function loadMessages() {
        messagesEl.innerHTML = '<div class="flex flex-col items-center justify-center h-full text-slate-500 text-xs"><div class="w-5 h-5 border-2 border-red-500 border-t-transparent rounded-full animate-spin mb-2"></div><p>Loading messages...</p></div>';

        fetch(baseUrl + 'chat/get_messages?id_room=' + idRoom)
            .then(function(r) { return r.json(); })
            .then(function(messages) {
                messagesEl.innerHTML = '';
                if (!messages || !messages.length) {
                    messagesEl.innerHTML = '<div class="flex flex-col items-center justify-center h-full text-slate-500 text-xs"><i data-lucide="message-circle" class="w-8 h-8 mb-3 text-slate-600"></i><p>No messages yet.</p><p class="mt-1">Be the first to say something!</p></div>';
                    if (typeof lucide !== 'undefined') lucide.createIcons();
                    return;
                }
                messages.forEach(function(msg) { appendMessage(msg, true); });
                scrollToBottom();
                log('Loaded ' + messages.length + ' messages');
            })
            .catch(function(err) {
                logErr('Failed to load messages:', err);
                messagesEl.innerHTML = '<div class="flex flex-col items-center justify-center h-full text-slate-500 text-xs"><p>Failed to load messages.</p></div>';
            });
    }

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
                    } else {
                        alert(res.error || 'Failed to send message.');
                    }
                })
                .catch(function(err) { logErr('Send failed:', err); alert('Network error. Please try again.'); })
                .finally(function() { sendBtn.disabled = false; });
            });

            inputEl.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    formEl.dispatchEvent(new Event('submit'));
                }
            });
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

        var channelName = 'private-chat-' + roomSlug;
        log('Subscribing to: ' + channelName);

        var channel = pusher.subscribe(channelName);

        channel.bind('pusher:subscription_succeeded', function() {
            log('Subscribed to ' + channelName + '!');
        });

        channel.bind('pusher:subscription_error', function(status) {
            logErr('Subscription FAILED for ' + channelName + ':', status);
        });

        channel.bind('new-message', function(data) {
            log('Event received! user_id=' + data.user_id + ', currentUserId=' + currentUserId + ', match=' + (String(data.user_id) === currentUserId));
            appendMessage(data);
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
