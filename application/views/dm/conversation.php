<div class="flex-col max-w-3xl mx-auto" style="height: calc(100vh - 140px);">
    <!-- Header -->
    <div class="flex-row justify-between items-center mb-3 pb-3 border-b flex-shrink-0">
        <a href="<?= base_url('dm'); ?>" class="btn btn-secondary btn-icon-sm mr-2" title="Kembali ke daftar pesan" style="border-radius:var(--radius-xl);">
            <i data-lucide="arrow-left" style="width:16px;height:16px;"></i>
        </a>
        <div class="flex-row items-center gap-3 flex-1 min-w-0">
            <div class="relative flex-shrink-0" data-user-id="<?= $other['id_user']; ?>" style="width:36px;height:36px;">
                <div class="w-full h-full rounded-full overflow-hidden" style="background:var(--bg-surface);border:1px solid var(--border-strong);">
                    <img src="<?= $other['avatar']; ?>" alt="" class="w-full h-full" style="object-fit:cover;"
                         onerror="this.src='<?= assets_url('default.jpg'); ?>';">
                </div>
                <?php if (!empty($other['border'])): ?>
                    <div class="absolute inset-0 w-full h-full pointer-events-none" style="transform:scale(1.25);transform-origin:center;">
                        <img src="<?= $other['border']; ?>" alt="" class="w-full h-full" style="object-fit:contain;">
                    </div>
                <?php endif; ?>
            </div>
            <div class="min-w-0">
                <h1 class="text-sm font-bold c-white text-truncate">
                    <a href="<?= base_url('user/' . $other['username']); ?>" style="color:inherit;text-decoration:none;">
                        <?= htmlspecialchars($other['display_name'] ?: $other['username'], ENT_QUOTES, 'UTF-8'); ?>
                    </a>
                    <?php if ($other['verified'] == 1): ?>
                        <i data-lucide="badge-check" class="inline-block ml-1 c-primary" style="width:14px;height:14px;fill:var(--color-primary);"></i>
                    <?php endif; ?>
                </h1>
                <span class="text-caption <?= $other['is_online'] ? 'c-success' : 'c-subtle'; ?>">
                    <?= $other['is_online'] ? 'Online' : '@' . $other['username']; ?>
                </span>
            </div>
        </div>
        <div class="relative" id="dm-menu-container">
            <button onclick="toggleDmMenu()" class="btn btn-secondary btn-icon-sm" title="Opsi" style="border-radius:var(--radius-xl);">
                <i data-lucide="ellipsis-vertical" style="width:16px;height:16px;"></i>
            </button>
            <div id="dm-menu" class="hidden absolute right-0 w-48 rounded-xl shadow-xl py-1.5 z-50" style="margin-top:8px;background:var(--bg-surface);border:1px solid var(--border-default);">
                <button onclick="openDmImageMaker()" class="flex items-center gap-2.5 w-full px-4 py-2.5 text-xs transition-colors" style="color:var(--text-secondary);text-align:left;">
                    <i data-lucide="image" class="w-3.5 h-3.5 c-subtle"></i>
                    Lampirkan Gambar
                </button>
                <button onclick="deleteConversation()" class="flex items-center gap-2.5 w-full px-4 py-2.5 text-xs transition-colors" style="color:var(--text-secondary);text-align:left;">
                    <i data-lucide="trash-2" class="w-3.5 h-3.5 c-subtle"></i>
                    Hapus Percakapan
                </button>
            </div>
        </div>
    </div>

    <?php if ($is_blocked): ?>
        <div class="flex-col items-center justify-center flex-1 text-center c-subtle text-xs space-y-2">
            <i data-lucide="ban" class="c-faint" style="width:36px;height:36px;"></i>
            <p>Tidak dapat mengirim atau melihat pesan dari pengguna ini.</p>
        </div>
    <?php else: ?>
        <!-- Messages area -->
        <div id="dm-messages" class="flex-1 overflow-y-auto space-y-2-5 pr-1" data-sk="chat">
            <div class="flex-col items-center justify-center h-full text-center c-subtle text-xs" data-sk-aux>
                <i data-lucide="mail" class="mb-3 c-faint" style="width:32px;height:32px;"></i>
                <p>Memuat pesan...</p>
            </div>
        </div>

        <!-- Input area -->
        <div class="mt-3 pt-3 border-t flex-shrink-0">
            <form id="dm-form" class="flex-col gap-2" enctype="multipart/form-data">
                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                <input type="hidden" name="id_dm" value="<?= $id_dm; ?>">
                <input type="file" id="dm-image-input" name="image" accept="image/*" class="hidden">
                <div id="dm-image-preview" class="relative hidden" style="width:96px;height:96px;border-radius:var(--radius-md);overflow:hidden;border:1px solid var(--border-strong);">
                    <img id="dm-image-preview-img" src="" alt="" class="w-full h-full" style="object-fit:cover;">
                    <button type="button" onclick="clearDmImage()" class="absolute" style="top:4px;right:4px;width:20px;height:20px;border-radius:50%;background:var(--color-primary);color:#fff;display:flex;align-items:center;justify-content:center;">
                        <i data-lucide="x" style="width:12px;height:12px;"></i>
                    </button>
                </div>
                <div class="flex-row gap-2">
                    <button type="button" onclick="openDmImageMaker()" class="btn btn-secondary" title="Lampirkan Gambar" style="border-radius:var(--radius-xl);padding:10px 14px;">
                        <i data-lucide="image" style="width:16px;height:16px;"></i>
                    </button>
                    <input
                        type="text"
                        name="content"
                        id="dm-input"
                        placeholder="Tulis pesan..."
                        maxlength="1000"
                        autocomplete="off"
                        class="input flex-1"
                        style="border-radius:var(--radius-xl);font-size:12px;padding:10px 16px;"
                    >
                    <button type="submit" id="dm-send-btn" class="btn btn-primary" style="border-radius:var(--radius-xl);padding:10px 16px;">
                        <i data-lucide="send" style="width:16px;height:16px;"></i>
                    </button>
                </div>
            </form>
        </div>
    <?php endif; ?>
</div>

<!-- Image Lightbox -->
<div id="dm-lightbox" class="hidden" style="position:fixed;inset:0;z-index:9999;">
    <div class="absolute inset-0" style="background:rgba(0,0,0,0.88);" onclick="dmLightboxClose()"></div>

    <div class="absolute flex-row items-center gap-3" style="top:16px;right:16px;z-index:10;">
        <a id="dm-lightbox-download" href="#" download class="btn btn-primary btn-sm" style="border-radius:var(--radius-xl);">
            <i data-lucide="download" class="w-3.5 h-3.5 inline-block mr-1"></i> Download
        </a>
        <button onclick="dmLightboxClose()" class="btn btn-secondary btn-icon-sm" title="Tutup" style="border-radius:50%;">
            <i data-lucide="x" style="width:20px;height:20px;"></i>
        </button>
    </div>

    <button id="dm-lightbox-prev" onclick="dmLightboxNav(-1)" class="btn btn-secondary btn-icon hidden" title="Sebelumnya" style="position:absolute;left:16px;top:50%;transform:translateY(-50%);z-index:10;border-radius:50%;box-shadow:0 4px 20px rgba(0,0,0,0.4);">
        <i data-lucide="chevron-left" style="width:24px;height:24px;"></i>
    </button>

    <div class="flex items-center justify-center" style="position:absolute;inset:0;z-index:5;padding:80px 72px;cursor:zoom-out;" onclick="dmLightboxClose()">
        <img id="dm-lightbox-img" src="" alt="Gambar" class="rounded-lg" style="max-width:100%;max-height:100%;object-fit:contain;box-shadow:0 10px 50px rgba(0,0,0,0.55);" onclick="event.stopPropagation();">
    </div>

    <button id="dm-lightbox-next" onclick="dmLightboxNav(1)" class="btn btn-secondary btn-icon hidden" title="Berikutnya" style="position:absolute;right:16px;top:50%;transform:translateY(-50%);z-index:10;border-radius:50%;box-shadow:0 4px 20px rgba(0,0,0,0.4);">
        <i data-lucide="chevron-right" style="width:24px;height:24px;"></i>
    </button>

    <div id="dm-lightbox-counter" class="c-white text-xs" style="position:absolute;bottom:20px;left:50%;transform:translateX(-50%);z-index:10;font-variant-numeric:tabular-nums;"></div>
</div>

<script src="<?= base_url('assets/js/pusher.min.js'); ?>"></script>
<script>
function initDmConversation() {
    var messagesEl = document.getElementById('dm-messages');
    var idDm = '<?= $id_dm; ?>';
    var currentUserId = String(<?= json_encode($current_user_id); ?>);
    var pusherKey = <?= json_encode($pusher_key); ?>;
    var pusherCluster = <?= json_encode($pusher_cluster); ?>;
    var baseUrl = '<?= base_url(); ?>';
    var isBlocked = <?= json_encode((bool) $is_blocked); ?>;
    var loadedMessageIds = {};
    var oldestId = null;
    var isLoadingOlder = false;

    function log() { console.log.apply(console, ['[DM]'].concat(Array.prototype.slice.call(arguments))); }
    function logErr() { console.error.apply(console, ['[DM]'].concat(Array.prototype.slice.call(arguments))); }

    function scrollToBottom() {
        messagesEl.scrollTop = messagesEl.scrollHeight;
    }

    function timeAgo(dateStr) {
        if (!dateStr) return '';
        var date;
        if (dateStr.indexOf('+') !== -1) {
            date = new Date(dateStr.replace(' ', 'T'));
        } else {
            date = new Date(dateStr.replace(' ', 'T') + '+07:00');
        }
        var h = String(date.getHours()).padStart(2, '0');
        var m = String(date.getMinutes()).padStart(2, '0');
        return h + ':' + m;
    }

    function linkifyDmUrl(text) {
        return String(text).replace(/(https?:\/\/[^\s<]+)/g, function(m) {
            var safe = m.replace(/&amp;/g, '&');
            return '<a href="' + safe + '" target="_blank" rel="noopener noreferrer" style="color:var(--color-primary);text-decoration:underline;">' + m + '</a>';
        });
    }

    function appendMessage(data, skipScroll) {
        if (data.id_message && loadedMessageIds[data.id_message]) return;
        if (data.id_message) loadedMessageIds[data.id_message] = true;

        var empty = messagesEl.querySelector('.empty-state');
        if (empty) messagesEl.innerHTML = '';

        var isOwn = String(data.sender_id || data.user_id) === currentUserId;
        var div = document.createElement('div');
        div.className = 'flex-row gap-2-5 items-start mb-2 ' + (isOwn ? 'justify-end' : '');
        div.setAttribute('data-dm-message', data.id_message);

        var avatar = '';
        if (!isOwn) {
            avatar = '<div class="relative rounded-full overflow-hidden flex-shrink-0 mt-0-5" style="width:26px;height:26px;background:var(--bg-surface);">' +
                '<img src="' + escapeHtml(data.avatar || baseUrl + 'uploads/default.jpg') + '" alt="" style="width:100%;height:100%;object-fit:cover;border-radius:50%;" onerror="this.src=\'' + baseUrl + 'uploads/default.jpg\'">' +
            '</div>';
        }

        var bubble = '<div class="rounded-xl px-3 py-2 border max-w-80" style="' +
            (isOwn
                ? 'background:var(--color-primary-bg);border-color:var(--color-primary-border);'
                : 'background:var(--bg-surface-hover);border-color:var(--border-default);') +
            '">';

        if (data.image_url) {
            bubble += '<div class="block dm-img-wrap mb-1" style="cursor:zoom-in;" onclick="openDmLightbox(\'' + escapeJsString(data.image_url) + '\')">' +
                '<img src="' + escapeHtml(data.image_url) + '" alt="Gambar" loading="lazy" class="rounded-lg dm-img" style="max-width:260px;max-height:220px;object-fit:cover;display:block;border-radius:8px;border:1px solid var(--border-subtle);">' +
            '</div>';
        }

        if (data.post && data.post.id_post) {
            var post = data.post;
            var postHref = baseUrl + 'post/' + encodeURIComponent(post.username || '') + '/' + post.id_post;
            var postCard = '<a href="' + postHref + '" target="_blank" rel="noopener noreferrer" class="dm-post-card" ' +
                'style="display:block;margin-top:2px;border:1px solid var(--border-default);border-radius:10px;overflow:hidden;background:var(--bg-surface);text-decoration:none;color:var(--text-primary);">';
            if (post.media) {
                postCard += '<div class="dm-post-card__media" style="width:100%;max-width:240px;max-height:150px;overflow:hidden;background:var(--bg-surface);">' +
                    '<img src="' + escapeHtml(post.media) + '" alt="Postingan" loading="lazy" ' +
                    'style="width:100%;height:100%;max-height:150px;object-fit:cover;display:block;" ' +
                    'onerror="this.parentNode.style.display=\'none\'"></div>';
            }
            postCard += '<div style="padding:8px 10px;">' +
                '<div class="flex-row items-center gap-1-5" style="margin-bottom:4px;">' +
                    '<span class="text-micro" style="font-size:9px;color:var(--color-primary);font-weight:700;">@' + escapeHtml(post.username || '') + '</span>' +
                    '<span class="text-micro c-faint" style="font-size:9px;">membagikan postingan</span>' +
                '</div>' +
                '<p class="text-xs" style="color:var(--text-secondary);word-break:break-word;white-space:pre-wrap;line-height:1.5;">' + escapeHtml(String(post.content || '').slice(0, 120)) + '</p>' +
                '<span class="text-micro" style="font-size:9px;color:var(--color-primary);font-weight:600;display:block;margin-top:6px;">Buka postingan &rarr;</span>' +
            '</div></a>';
            bubble += postCard;
        } else if (data.content) {
            bubble += '<p class="text-xs c-white leading-relaxed" style="word-break:break-word;white-space:pre-wrap;">' + linkifyDmUrl(escapeHtml(data.content)) + '</p>';
        }

        bubble += '<span class="text-micro c-faint" style="font-size:9px;display:flex;' + (isOwn ? 'justify-content:flex-start;' : 'justify-content:flex-end;') + 'margin-top:3px;">' + escapeHtml(timeAgo(data.created_at)) + '</span>';

        bubble += '</div>';

        div.innerHTML = avatar + '<div class="flex flex-col ' + (isOwn ? 'items-end' : 'items-start') + '">' + bubble +
            '<button type="button" onclick="deleteDmMessage(' + escapeJsString(data.id_message) + ')" class="text-micro c-faint dm-msg-del" style="font-size:8px;margin-top:1px;display:none;">Hapus</button>' +
        '</div>';

        messagesEl.appendChild(div);
        if (!skipScroll) scrollToBottom();
    }

    function sortMessagesInto(messages) {
        var oldScroll = messagesEl.scrollHeight;
        messages.forEach(function(msg) { appendMessage(msg, true); });
        messagesEl.scrollTop = messagesEl.scrollHeight - oldScroll;
    }

    function loadMessages() {
        messagesEl.innerHTML = '<div class="flex-col items-center justify-center h-full c-subtle text-xs"><div class="spinner spinner--sm mb-2"></div><p>Memuat pesan...</p></div>';

        fetch(baseUrl + 'dm/get_messages?id_dm=' + idDm)
            .then(function(r) { return r.json(); })
            .then(function(messages) {
                messagesEl.innerHTML = '';
                if (!messages || !messages.length) {
                    messagesEl.innerHTML = '<div class="empty-state h-full"><i data-lucide="mail" class="empty-state__icon" style="width:32px;height:32px;"></i><p class="empty-state__text">Belum ada pesan.</p><p class="empty-state__text mt-1">Sapalah pengguna ini!</p></div>';
                    if (typeof lucide !== 'undefined') lucide.createIcons();
                    return;
                }
                messages.forEach(function(msg) { appendMessage(msg, true); });
                oldestId = messages[0] ? messages[0].id_message : null;
                scrollToBottom();
                updateDmBadgeNow();
                log('Loaded ' + messages.length + ' messages');
            })
            .catch(function(err) {
                logErr('Failed to load messages:', err);
                messagesEl.innerHTML = '<div class="empty-state h-full"><p class="empty-state__text">Gagal memuat pesan.</p></div>';
            });
    }

    function loadOlder() {
        if (isLoadingOlder || !oldestId) return;
        isLoadingOlder = true;
        fetch(baseUrl + 'dm/get_messages?id_dm=' + idDm + '&before_id=' + oldestId)
            .then(function(r) { return r.json(); })
            .then(function(messages) {
                if (Array.isArray(messages) && messages.length) {
                    sortMessagesInto(messages);
                    oldestId = messages[0] ? messages[0].id_message : null;
                } else {
                    oldestId = null;
                }
                isLoadingOlder = false;
            })
            .catch(function() { isLoadingOlder = false; });
    }

    messagesEl.addEventListener('scroll', function() {
        if (messagesEl.scrollTop < 40) loadOlder();
    });

    loadMessages();

    if (!isBlocked && pusherKey) {
        initPusher();
    } else if (!isBlocked) {
        logErr('No Pusher key configured');
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
            appendMessage(data);
            if (String(data.sender_id) !== currentUserId) {
                updateDmBadgeNow();
            }
        });
    }

    var formEl = document.getElementById('dm-form');
    var inputEl = document.getElementById('dm-input');
    var sendBtn = document.getElementById('dm-send-btn');
    var fileInput = document.getElementById('dm-image-input');

    function openImagePicker() {
        if (fileInput) fileInput.click();
    }
    window.openDmImageMaker = openImagePicker;

    if (fileInput) {
        fileInput.addEventListener('change', function() {
            var previewWrap = document.getElementById('dm-image-preview');
            var previewImg = document.getElementById('dm-image-preview-img');
            if (this.files && this.files[0]) {
                previewImg.src = URL.createObjectURL(this.files[0]);
                previewWrap.classList.remove('hidden');
                if (typeof lucide !== 'undefined') lucide.createIcons();
            }
        });
    }

    window.clearDmImage = function() {
        if (fileInput) fileInput.value = '';
        var previewWrap = document.getElementById('dm-image-preview');
        previewWrap.classList.add('hidden');
    };

    if (formEl && !isBlocked) {
        formEl.addEventListener('submit', function(e) {
            e.preventDefault();
            var content = inputEl ? inputEl.value.trim() : '';
            var hasImage = fileInput && fileInput.files && fileInput.files.length > 0;
            if (!content && !hasImage) return;

            sendBtn.disabled = true;

            var formData = new FormData();
            formData.append(document.querySelector('meta[name="csrf-token-name"]').content, document.querySelector('meta[name="csrf-token-hash"]').content);
            formData.append('id_dm', idDm);
            formData.append('content', content);
            if (hasImage) {
                formData.append('image', fileInput.files[0]);
            }

            fetch(baseUrl + 'dm/send_message', {
                method: 'POST',
                body: formData
            })
            .then(function(r) { return r.json(); })
            .then(function(res) {
                if (res.success && res.message) {
                    appendMessage(res.message);
                    if (inputEl) { inputEl.value = ''; inputEl.focus(); }
                    window.clearDmImage();
                } else {
                    alert(res.error || 'Gagal mengirim pesan.');
                }
            })
            .catch(function(err) { logErr('Send failed:', err); alert('Kesalahan jaringan. Coba lagi.'); })
            .finally(function() { sendBtn.disabled = false; });
        });

        if (inputEl) {
            inputEl.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    formEl.dispatchEvent(new Event('submit'));
                }
            });
        }
    }
}

var dmMenuOpen = false;
function toggleDmMenu() {
    var menu = document.getElementById('dm-menu');
    dmMenuOpen = !dmMenuOpen;
    if (dmMenuOpen) {
        menu.classList.remove('hidden');
    } else {
        menu.classList.add('hidden');
    }
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
    if (!confirm('Hapus percakapan ini untuk kedua pihak? Tindakan ini tidak dapat dibatalkan.')) return;
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
            alert(data.error || 'Gagal menghapus percakapan.');
        }
    })
    .catch(function() { alert('Kesalahan jaringan. Coba lagi.'); });
}

function deleteDmMessage(idMessage) {
    if (!confirm('Hapus pesan ini?')) return;
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
            alert(data.error || 'Gagal menghapus pesan.');
        }
    })
    .catch(function() { alert('Kesalahan jaringan. Coba lagi.'); });
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