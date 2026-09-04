<div class="flex-col max-w-3xl mx-auto" style="height: calc(100vh - 140px);">
    <!-- Header -->
    <div class="flex-row justify-between mb-3 pb-3 border-b flex-shrink-0">
        <div>
            <span class="text-label" style="letter-spacing: 0.08em; text-transform: uppercase;"><?= htmlspecialchars($room['race_name']); ?></span>
            <h1 class="text-sm font-bold c-white mt-0-5">
                <i data-lucide="message-square" class="inline-block mr-1-5 c-primary" style="width: 16px; height: 16px;"></i>
                <?= htmlspecialchars($room['session_name']); ?>
            </h1>
        </div>
        <?php if ($room['room_status'] === 'active'): ?>
            <div class="flex-row gap-1-5 px-2-5 py-1 rounded-full" style="background: var(--color-success-bg); border: 1px solid var(--color-success-border);">
                <span class="animate-pulse rounded-full" style="width: 6px; height: 6px; background: var(--color-success);"></span>
                <span class="text-label c-success">LIVE</span>
            </div>
        <?php elseif ($room['room_status'] === 'upcoming'): ?>
            <div class="text-label c-info">Upcoming</div>
        <?php else: ?>
            <div class="text-label c-subtle">Completed</div>
        <?php endif; ?>
    </div>

    <!-- Messages area -->
    <div id="chat-messages" class="flex-1 overflow-y-auto space-y-2-5 pr-1">
        <div class="flex-col items-center justify-center h-full text-center c-subtle text-xs">
            <i data-lucide="message-circle" class="mb-3 c-faint" style="width: 32px; height: 32px;"></i>
            <p>Sending messages to chat you need to login first.</p>
            <p class="mt-1">If you already logged in, you can send message now!</p>
        </div>
    </div>

    <!-- Input area -->
    <div class="mt-3 pt-3 border-t flex-shrink-0">
        <?php if ($room['room_status'] === 'active'): ?>
            <form id="chat-form" class="flex-row gap-2">
                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                <input type="hidden" name="id_room" value="<?= $room['id_room']; ?>">
                <input
                    type="text"
                    name="content"
                    id="chat-input"
                    placeholder="Type a message..."
                    maxlength="1000"
                    autocomplete="off"
                    class="input flex-1"
                    style="border-radius: var(--radius-xl); font-size: 12px; padding: 10px 16px;"
                >
                <button
                    type="submit"
                    id="chat-send-btn"
                    class="btn btn-primary"
                    style="border-radius: var(--radius-xl); padding: 10px 16px;"
                >
                    <i data-lucide="send" style="width: 16px; height: 16px;"></i>
                </button>
            </form>
        <?php elseif ($room['room_status'] === 'upcoming'): ?>
            <div class="text-center text-label c-subtle py-3 rounded-xl" style="background: var(--bg-surface-subtle); border: 1px solid var(--border-subtle);">
                This chat room hasn't opened yet. Opens <?= date('d M H:i', strtotime($room['opens_at'])); ?>
            </div>
        <?php else: ?>
            <div class="text-center text-label c-subtle py-3 rounded-xl" style="background: var(--bg-surface-subtle); border: 1px solid var(--border-subtle);">
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

        var empty = messagesEl.querySelector('.empty-state');
        if (empty) messagesEl.innerHTML = '';

        var isOwn = String(data.user_id) === currentUserId;
        var div = document.createElement('div');
        div.className = 'flex-row gap-2-5 items-start ' + (isOwn ? 'justify-end' : '');
        div.innerHTML =
            '<div class="relative rounded-full overflow-hidden flex-shrink-0 mt-0-5" style="width: 24px; height: 24px; background: var(--bg-surface);">' +
                '<img src="' + escapeHtml(data.avatar) + '" alt="" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;" onerror="this.src=\'' + baseUrl + 'uploads/default.jpg\'">' +
            '</div>' +
            '<div class="' + (isOwn ? '' : '') + ' rounded-xl px-3 py-2 border max-w-80" style="' + (isOwn ? 'background: var(--color-primary-bg); border-color: var(--color-primary-border);' : 'background: var(--bg-surface-hover); border-color: var(--border-default);') + '">' +
                '<div class="flex-row gap-2 mb-0-5">' +
                    '<span class="text-label font-semibold ' + (isOwn ? 'c-primary' : 'c-white') + '">' + escapeHtml(data.username) + '</span>' +
                    '<span class="text-micro c-faint" style="font-size: 9px;">' + escapeHtml(timeAgo(data.created_at)) + '</span>' +
                '</div>' +
                '<p class="text-xs c-white leading-relaxed" style="word-break: break-word; white-space: pre-wrap;">' + escapeHtml(data.content) + '</p>' +
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
        messagesEl.innerHTML = '<div class="flex-col items-center justify-center h-full c-subtle text-xs"><div class="spinner spinner--sm mb-2"></div><p>Loading messages...</p></div>';

        fetch(baseUrl + 'chat/get_messages?id_room=' + idRoom)
            .then(function(r) { return r.json(); })
            .then(function(messages) {
                messagesEl.innerHTML = '';
                if (!messages || !messages.length) {
                    messagesEl.innerHTML = '<div class="empty-state h-full"><i data-lucide="message-circle" class="empty-state__icon" style="width: 32px; height: 32px;"></i><p class="empty-state__text">No messages yet.</p><p class="empty-state__text mt-1">Be the first to say something!</p></div>';
                    if (typeof lucide !== 'undefined') lucide.createIcons();
                    return;
                }
                messages.forEach(function(msg) { appendMessage(msg, true); });
                scrollToBottom();
                log('Loaded ' + messages.length + ' messages');
            })
            .catch(function(err) {
                logErr('Failed to load messages:', err);
                messagesEl.innerHTML = '<div class="empty-state h-full"><p class="empty-state__text">Failed to load messages.</p></div>';
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
