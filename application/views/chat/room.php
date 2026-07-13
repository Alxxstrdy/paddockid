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

<script src="https://js.pusher.com/7.2/pusher.min.js"></script>
<script>
<?php if ($room['room_status'] === 'active'): ?>
(function() {
    const messagesEl = document.getElementById('chat-messages');
    const formEl = document.getElementById('chat-form');
    const inputEl = document.getElementById('chat-input');
    const sendBtn = document.getElementById('chat-send-btn');

    if (!formEl || !messagesEl || !inputEl) return;

    <?php if (!empty($pusher_key)): ?>
    // Init Pusher for real-time
    const pusher = new Pusher('<?= $pusher_key; ?>', {
        cluster: '<?= $pusher_cluster; ?>',
        authEndpoint: '<?= base_url('chat/pusher_auth'); ?>',
        auth: {
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        }
    });

    const channel = pusher.subscribe('private-chat-<?= $room['slug']; ?>');
    channel.bind('new-message', function(data) {
        appendMessage(data);
    });

    pusher.connection.bind('connected', function() {
        console.log('[Pusher] Connected');
    });
    pusher.connection.bind('disconnected', function() {
        console.log('[Pusher] Disconnected');
    });
    <?php endif; ?>

    function appendMessage(data) {
        const empty = messagesEl.querySelector('.flex-col.items-center.justify-center.h-full');
        if (empty) messagesEl.innerHTML = '';

        const isOwn = data.user_id === '<?= $current_user_id; ?>';
        const div = document.createElement('div');
        div.className = 'flex items-start gap-2.5 ' + (isOwn ? 'flex-row-reverse' : '');
        div.innerHTML = `
            <div class="relative w-6 h-6 shrink-0 mt-0.5 rounded-full overflow-hidden bg-slate-800">
                <img src="${data.avatar}" alt=""
                     class="w-full h-full object-cover"
                     onerror="this.src='<?= base_url('uploads/default.jpg'); ?>'">
            </div>
            <div class="${isOwn ? 'bg-red-500/10 border-red-500/20' : 'bg-white/[0.04] border-white/[0.06]'} rounded-xl px-3 py-2 border max-w-[80%]">
                <div class="flex items-center gap-2 mb-0.5">
                    <span class="text-[10px] font-semibold ${isOwn ? 'text-red-400' : 'text-slate-300'}">${data.username}</span>
                    <span class="text-[9px] text-slate-600">${timeAgo(data.created_at)}</span>
                </div>
                <p class="text-xs text-slate-200 leading-relaxed break-words">${data.content}</p>
            </div>
        `;
        messagesEl.appendChild(div);
        messagesEl.scrollTop = messagesEl.scrollHeight;
    }

    function timeAgo(dateStr) {
        const now = new Date();
        const then = new Date(dateStr.replace(' ', 'T') + '+07:00');
        const diff = Math.floor((now - then) / 1000);
        if (diff < 60) return 'now';
        if (diff < 3600) return Math.floor(diff / 60) + 'm';
        return Math.floor(diff / 3600) + 'h';
    }

    // Send message via AJAX + show own message immediately
    formEl.addEventListener('submit', function(e) {
        e.preventDefault();
        const content = inputEl.value.trim();
        if (!content) return;

        sendBtn.disabled = true;

        const formData = new URLSearchParams(new FormData(formEl));

        fetch('<?= base_url('chat/send_message'); ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: formData.toString()
        })
        .then(r => r.json())
        .then(res => {
            if (res.success) {
                appendMessage({
                    user_id: '<?= $current_user_id; ?>',
                    username: '<?= $session_data['username']; ?>',
                    avatar: '<?= avatar_url($session_data['profile_pic']); ?>',
                    content: content.replace(/</g, '&lt;').replace(/>/g, '&gt;'),
                    created_at: new Date().toISOString().replace('T', ' ').substring(0, 19) + '+07:00'
                });
                inputEl.value = '';
                inputEl.focus();
            }
        })
        .catch(() => {})
        .finally(() => {
            sendBtn.disabled = false;
        });
    });

    inputEl.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            formEl.dispatchEvent(new Event('submit'));
        }
    });

    messagesEl.scrollTop = messagesEl.scrollHeight;
})();
<?php endif; ?>

if (typeof lucide !== 'undefined') lucide.createIcons();
</script>
