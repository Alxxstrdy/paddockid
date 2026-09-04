<div class="space-y-6">
    <!-- Header -->
    <div class="flex-col sm:flex-row sm:items-center justify-between gap-4 flex-row">
        <div>
            <h2 class="text-lg font-bold c-white">Kirim Notifikasi</h2>
            <p class="text-xs c-subtle" style="margin-top:4px;">Kirim notifikasi ke <?= number_format($user_count) ?> pengguna terdaftar</p>
        </div>
    </div>

    <!-- Compose Form -->
    <div class="card rounded-xl p-5">
        <form id="notif-form" data-freeze-refresh>
            <div class="space-y-4">
                <!-- Type -->
                <div>
                    <label class="text-micro font-semibold c-subtle text-transform-uppercase inline-letter-spacing-006em">Tipe Notifikasi</label>
                    <div class="flex-row gap-2" style="margin-top:8px;">
                        <label class="flex-row items-center gap-2 px-3 py-2 rounded-lg border cursor-pointer transition-colors" style="border-color:var(--border-default);" onmouseover="this.style.background='rgba(255,255,255,0.02)'" onmouseout="this.style.background=''">
                            <input type="radio" name="admin_type" value="warning" checked class="hidden" onchange="toggleGiftOptions()">
                            <i data-lucide="alert-triangle" class="w-3-5 h-3-5 c-primary"></i>
                            <span class="text-xs font-medium" style="color:var(--text-secondary);">Warning</span>
                        </label>
                        <label class="flex-row items-center gap-2 px-3 py-2 rounded-lg border cursor-pointer transition-colors" style="border-color:var(--border-default);" onmouseover="this.style.background='rgba(255,255,255,0.02)'" onmouseout="this.style.background=''">
                            <input type="radio" name="admin_type" value="gift" class="hidden" onchange="toggleGiftOptions()">
                            <i data-lucide="gift" class="w-3-5 h-3-5 c-success"></i>
                            <span class="text-xs font-medium" style="color:var(--text-secondary);">Gift</span>
                        </label>
                        <label class="flex-row items-center gap-2 px-3 py-2 rounded-lg border cursor-pointer transition-colors" style="border-color:var(--border-default);" onmouseover="this.style.background='rgba(255,255,255,0.02)'" onmouseout="this.style.background=''">
                            <input type="radio" name="admin_type" value="info" class="hidden" onchange="toggleGiftOptions()">
                            <i data-lucide="info" class="w-3-5 h-3-5 c-info"></i>
                            <span class="text-xs font-medium" style="color:var(--text-secondary);">Info</span>
                        </label>
                    </div>
                </div>

                <!-- Gift Options (shown when Gift is selected) -->
                <div id="gift-options-wrap" class="hidden space-y-4 pl-4" style="border-left:2px solid rgba(34,197,94,0.3);">
                    <div>
                        <label class="text-micro font-semibold c-subtle text-transform-uppercase inline-letter-spacing-006em">Jenis Gift</label>
                        <div class="flex-row gap-2" style="margin-top:8px;">
                            <label class="flex-row items-center gap-2 px-3 py-2 rounded-lg border cursor-pointer transition-colors" style="border-color:var(--border-default);" onmouseover="this.style.background='rgba(255,255,255,0.02)'" onmouseout="this.style.background=''">
                                <input type="radio" name="gift_type" value="border" checked class="hidden" onchange="toggleGiftDetail()">
                                <i data-lucide="frame" class="w-3-5 h-3-5 c-success"></i>
                                <span class="text-xs font-medium" style="color:var(--text-secondary);">Border</span>
                            </label>
                            <label class="flex-row items-center gap-2 px-3 py-2 rounded-lg border cursor-pointer transition-colors" style="border-color:var(--border-default);" onmouseover="this.style.background='rgba(255,255,255,0.02)'" onmouseout="this.style.background=''">
                                <input type="radio" name="gift_type" value="point" class="hidden" onchange="toggleGiftDetail()">
                                <i data-lucide="coins" class="w-3-5 h-3-5 c-warning"></i>
                                <span class="text-xs font-medium" style="color:var(--text-secondary);">Poin (Koin)</span>
                            </label>
                        </div>
                    </div>

                    <!-- Border Select -->
                    <div id="gift-border-wrap">
                        <label class="text-micro font-semibold c-subtle text-transform-uppercase inline-letter-spacing-006em">Pilih Border</label>
                        <select name="gift_border_id" class="w-full select" style="margin-top:4px;">
                            <option value="">-- Pilih Border --</option>
                            <?php if (!empty($borders)): ?>
                                <?php foreach ($borders as $b): ?>
                                    <option value="<?= $b['id_border']; ?>"><?= htmlspecialchars($b['border_name'], ENT_QUOTES, 'UTF-8'); ?><?= $b['is_premium'] ? ' (Premium)' : ''; ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <!-- Coins Input -->
                    <div id="gift-coins-wrap" class="hidden">
                        <label class="text-micro font-semibold c-subtle text-transform-uppercase inline-letter-spacing-006em">Jumlah Koin</label>
                        <input type="number" name="gift_coins" min="1" placeholder="Contoh: 5000" class="w-full select" style="margin-top:4px;">
                    </div>
                </div>

                <!-- Title -->
                <div>
                    <label class="text-micro font-semibold c-subtle text-transform-uppercase inline-letter-spacing-006em">Judul</label>
                    <input type="text" name="title" placeholder="Contoh: Peringatan Pelanggaran" class="w-full select" style="margin-top:4px;" required>
                </div>

                <!-- Message -->
                <div>
                    <label class="text-micro font-semibold c-subtle text-transform-uppercase inline-letter-spacing-006em">Pesan</label>
                    <textarea name="message" rows="3" placeholder="Isi notifikasi yang ingin dikirim..." class="w-full select resize-none" style="margin-top:4px;" required></textarea>
                </div>

                <!-- Recipients -->
                <div>
                    <label class="text-micro font-semibold c-subtle text-transform-uppercase inline-letter-spacing-006em">Penerima</label>
                    <div class="flex-row gap-2" style="margin-top:8px;">
                        <label class="flex-row items-center gap-2 px-3 py-2 rounded-lg border cursor-pointer transition-colors" style="border-color:var(--border-default);" onmouseover="this.style.background='rgba(255,255,255,0.02)'" onmouseout="this.style.background=''">
                            <input type="radio" name="send_to" value="all" checked class="hidden" onchange="document.getElementById('user-search-wrap').classList.add('hidden')">
                            <i data-lucide="users" class="w-3-5 h-3-5 c-muted"></i>
                            <span class="text-xs font-medium" style="color:var(--text-secondary);">Semua User</span>
                        </label>
                        <label class="flex-row items-center gap-2 px-3 py-2 rounded-lg border cursor-pointer transition-colors" style="border-color:var(--border-default);" onmouseover="this.style.background='rgba(255,255,255,0.02)'" onmouseout="this.style.background=''">
                            <input type="radio" name="send_to" value="specific" class="hidden" onchange="document.getElementById('user-search-wrap').classList.remove('hidden')">
                            <i data-lucide="user-check" class="w-3-5 h-3-5 c-muted"></i>
                            <span class="text-xs font-medium" style="color:var(--text-secondary);">Pilih User</span>
                        </label>
                    </div>

                    <!-- User Search (hidden by default) -->
                    <div id="user-search-wrap" class="hidden" style="margin-top:12px;">
                        <div class="relative">
                            <i data-lucide="search" class="w-3-5 h-3-5 c-subtle absolute" style="left:12px;top:50%;transform:translateY(-50%);"></i>
                            <input type="text" id="user-search-input" placeholder="Cari username..." class="w-full select" style="padding-left:32px;" autocomplete="off">
                        </div>
                        <div id="user-search-results" class="max-h-40 overflow-y-auto rounded-lg border hidden" style="border-color:var(--border-default);"></div>
                        <div id="selected-users" class="flex-row flex-wrap gap-1-5"></div>
                    </div>
                </div>

                <!-- Submit -->
                <div class="flex-row items-center gap-3" style="padding-top:8px;">
                    <button type="submit" id="notif-submit-btn" class="inline-flex-row items-center gap-2 px-5 py-2-5 text-xs font-semibold c-white rounded-xl transition-colors shadow-lg disabled:opacity-50 disabled:cursor-not-allowed" style="background:var(--color-primary);" onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                        <i data-lucide="send" class="w-3-5 h-3-5"></i> Kirim Notifikasi
                    </button>
                    <span id="notif-status" class="text-micro c-subtle"></span>
                </div>
            </div>
        </form>
    </div>

    <!-- History Table -->
    <div class="card rounded-xl overflow-hidden">
        <div class="px-4 py-3 border-b" style="border-color:var(--border-subtle);">
            <p class="text-xs font-bold c-white">Riwayat Kirim</p>
        </div>
        <?php if (!empty($notifications)): ?>
            <div class="overflow-x-auto">
                <table class="table w-full">
                    <thead>
                        <tr class="border-b" style="border-color:var(--border-subtle);">
                            <th class="text-left text-micro font-semibold c-subtle text-transform-uppercase inline-letter-spacing-006em px-4 py-3">Tipe</th>
                            <th class="text-left text-micro font-semibold c-subtle text-transform-uppercase inline-letter-spacing-006em px-4 py-3">Judul</th>
                            <th class="text-left text-micro font-semibold c-subtle text-transform-uppercase inline-letter-spacing-006em px-4 py-3">Pesan</th>
                            <th class="text-left text-micro font-semibold c-subtle text-transform-uppercase inline-letter-spacing-006em px-4 py-3">Detail Gift</th>
                            <th class="text-left text-micro font-semibold c-subtle text-transform-uppercase inline-letter-spacing-006em px-4 py-3">Penerima</th>
                            <th class="text-left text-micro font-semibold c-subtle text-transform-uppercase inline-letter-spacing-006em px-4 py-3">Waktu</th>
                            <th class="text-right text-micro font-semibold c-subtle text-transform-uppercase inline-letter-spacing-006em px-4 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="notif-history">
                        <?php foreach ($notifications as $n): ?>
                            <tr class="transition-colors" style="border-bottom:1px solid var(--border-subtle);" onmouseover="this.style.background='rgba(255,255,255,0.02)'" onmouseout="this.style.background=''">
                                <td class="px-4 py-3">
                                    <?php
                                        $type_config = [
                                            'warning' => ['icon' => 'alert-triangle', 'css' => 'background:var(--color-primary-bg);color:var(--color-primary);border-color:var(--color-primary-border)', 'label' => 'Warning'],
                                            'gift'    => ['icon' => 'gift', 'css' => 'background:var(--color-success-bg);color:var(--color-success);border-color:var(--color-success-border)', 'label' => 'Gift'],
                                            'info'    => ['icon' => 'info', 'css' => 'background:rgba(96,165,250,0.1);color:var(--color-info);border-color:rgba(96,165,250,0.2)', 'label' => 'Info'],
                                        ];
                                        $tc = $type_config[$n['admin_type']] ?? $type_config['info'];
                                    ?>
                                    <span class="inline-flex-row items-center gap-1-5 text-micro font-semibold px-2 py-05 rounded-full border" style="<?= $tc['css'] ?>">
                                        <i data-lucide="<?= $tc['icon'] ?>" class="w-2-5 h-2-5"></i> <?= $tc['label'] ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <p class="text-xs font-semibold c-white"><?= htmlspecialchars($n['title'], ENT_QUOTES, 'UTF-8'); ?></p>
                                </td>
                                <td class="px-4 py-3">
                                    <p class="text-xs truncate max-w-[200px]" style="color:var(--text-secondary);"><?= htmlspecialchars($n['message'], ENT_QUOTES, 'UTF-8'); ?></p>
                                </td>
                                <td class="px-4 py-3">
                                    <?php if ($n['admin_type'] === 'gift' && $n['gift_type'] === 'border' && !empty($n['border_name'])): ?>
                                        <span class="inline-flex-row items-center gap-1 text-micro font-medium px-2 py-05 rounded-full border" style="background:rgba(167,139,250,0.1);color:var(--color-purple);border-color:rgba(167,139,250,0.2);">
                                            <i data-lucide="frame" class="w-2-5 h-2-5"></i> <?= htmlspecialchars($n['border_name'], ENT_QUOTES, 'UTF-8'); ?>
                                        </span>
                                    <?php elseif ($n['admin_type'] === 'gift' && $n['gift_type'] === 'point' && !empty($n['gift_coins'])): ?>
                                        <span class="inline-flex-row items-center gap-1 text-micro font-medium px-2 py-05 rounded-full border" style="background:var(--color-warning-bg);color:var(--color-warning);border-color:var(--color-warning-border);">
                                            <i data-lucide="coins" class="w-2-5 h-2-5"></i> <?= number_format($n['gift_coins']); ?> koin
                                        </span>
                                    <?php else: ?>
                                        <span class="text-micro c-faint">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3">
                                    <?php if ($n['admin_type'] === 'gift'): ?>
                                        <span class="text-micro c-muted"><?= $n['recipient_count']; ?> penerima</span>
                                        <span class="text-micro c-success" style="margin-left:4px;">• <?= $n['claimed_count']; ?> klaim</span>
                                    <?php else: ?>
                                        <span class="text-micro c-muted">Semua User</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="text-micro c-subtle font-mono"><?= htmlspecialchars($n['created_at'], ENT_QUOTES, 'UTF-8'); ?></span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <button onclick="deleteSentNotification(this)" data-title="<?= htmlspecialchars($n['title'], ENT_QUOTES, 'UTF-8'); ?>" data-message="<?= htmlspecialchars($n['message'], ENT_QUOTES, 'UTF-8'); ?>" data-type="<?= htmlspecialchars($n['admin_type'], ENT_QUOTES, 'UTF-8'); ?>" data-actor="<?= $n['actor_id']; ?>" data-created="<?= htmlspecialchars($n['created_at'], ENT_QUOTES, 'UTF-8'); ?>" class="p-1-5 c-subtle rounded-lg transition-colors" onmouseover="this.style.color='var(--color-primary)';this.style.background='var(--color-primary-bg)'" onmouseout="this.style.color='';this.style.background=''">
                                        <i data-lucide="trash-2" class="w-3-5 h-3-5"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="px-4 py-12 text-center c-faint text-xs">Belum ada riwayat notifikasi</div>
        <?php endif; ?>
    </div>
</div>

<script>
(function() {
    var selectedUserIds = [];
    var searchTimeout = null;
    var csrfName = document.querySelector('meta[name="csrf-token-name"]').content;
    var csrfHash = document.querySelector('meta[name="csrf-token-hash"]').content;

    // Type radio styling
    document.querySelectorAll('input[name="admin_type"]').forEach(function(radio) {
        radio.addEventListener('change', function() {
            document.querySelectorAll('input[name="admin_type"]').forEach(function(r) {
                r.closest('label').style.borderColor = '';
                r.closest('label').style.background = '';
            });
            var colors = { warning: 'var(--color-primary)', gift: 'var(--color-success)', info: 'var(--color-info)' };
            var bgColors = { warning: 'var(--color-primary-bg)', gift: 'var(--color-success-bg)', info: 'rgba(96,165,250,0.1)' };
            var borderColors = { warning: 'var(--color-primary-border)', gift: 'var(--color-success-border)', info: 'rgba(96,165,250,0.2)' };
            var c = this.value;
            this.closest('label').style.borderColor = borderColors[c];
            this.closest('label').style.background = bgColors[c];
        });
    });

    // Gift sub-type radio styling
    document.querySelectorAll('input[name="gift_type"]').forEach(function(radio) {
        radio.addEventListener('change', function() {
            document.querySelectorAll('input[name="gift_type"]').forEach(function(r) {
                r.closest('label').style.borderColor = '';
                r.closest('label').style.background = '';
            });
            this.closest('label').style.borderColor = 'var(--color-success-border)';
            this.closest('label').style.background = 'var(--color-success-bg)';
        });
    });

    // Toggle gift options visibility
    window.toggleGiftOptions = function() {
        var adminType = document.querySelector('input[name="admin_type"]:checked').value;
        var giftWrap = document.getElementById('gift-options-wrap');
        if (adminType === 'gift') {
            giftWrap.classList.remove('hidden');
            toggleGiftDetail();
        } else {
            giftWrap.classList.add('hidden');
        }
    };

    // Toggle border select vs coins input
    window.toggleGiftDetail = function() {
        var giftType = document.querySelector('input[name="gift_type"]:checked').value;
        var borderWrap = document.getElementById('gift-border-wrap');
        var coinsWrap = document.getElementById('gift-coins-wrap');
        if (giftType === 'border') {
            borderWrap.classList.remove('hidden');
            coinsWrap.classList.add('hidden');
        } else {
            borderWrap.classList.add('hidden');
            coinsWrap.classList.remove('hidden');
        }
    };

    // User search
    var searchInput = document.getElementById('user-search-input');
    var searchResults = document.getElementById('user-search-results');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            var q = this.value.trim();
            clearTimeout(searchTimeout);
            if (q.length < 2) { searchResults.classList.add('hidden'); return; }
            searchTimeout = setTimeout(function() {
                fetch('<?= base_url("admin/search_users_ajax"); ?>?q=' + encodeURIComponent(q))
                    .then(function(r) { return r.json(); })
                    .then(function(users) {
                        if (!users.length) { searchResults.innerHTML = '<div class="px-3 py-2 text-micro c-subtle">Tidak ditemukan</div>'; searchResults.classList.remove('hidden'); return; }
                        var html = '';
                        users.forEach(function(u) {
                            if (selectedUserIds.indexOf(String(u.id_user)) !== -1) return;
                            html += '<div class="flex-row items-center gap-2 px-3 py-2 cursor-pointer transition-colors" style="border-bottom:1px solid rgba(255,255,255,0.03);" onmouseover="this.style.background=\'rgba(255,255,255,0.03)\'" onmouseout="this.style.background=\'\'" onclick="window._addUser(' + u.id_user + ', \'' + u.username.replace(/'/g, "\\'") + '\')">';
                            html += '<img src="' + (u.avatar_url || '<?= assets_url("default.jpg"); ?>') + '" class="w-5 h-5 rounded-full" style="object-fit:cover;">';
                            html += '<span class="text-xs" style="color:var(--text-secondary);">' + u.username + '</span>';
                            html += '</div>';
                        });
                        searchResults.innerHTML = html;
                        searchResults.classList.remove('hidden');
                    });
            }, 300);
        });
    }

    window._addUser = function(id, username) {
        if (selectedUserIds.indexOf(String(id)) !== -1) return;
        selectedUserIds.push(String(id));
        var container = document.getElementById('selected-users');
        var tag = document.createElement('span');
        tag.className = 'inline-flex-row items-center gap-1 px-2 py-05 rounded-full text-micro';
        tag.style.cssText = 'background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.08);color:var(--text-secondary);';
        tag.setAttribute('data-user-id', id);
        tag.innerHTML = username + ' <button onclick="window._removeUser(' + id + ')" class="c-subtle ml-05" onmouseover="this.style.color=\'var(--color-primary)\'" onmouseout="this.style.color=\'\'">&times;</button>';
        container.appendChild(tag);
        searchResults.classList.add('hidden');
        searchInput.value = '';
    };

    window._removeUser = function(id) {
        selectedUserIds = selectedUserIds.filter(function(uid) { return uid !== String(id); });
        var tag = document.querySelector('[data-user-id="' + id + '"]');
        if (tag) tag.remove();
    };

    // Form submit
    var form = document.getElementById('notif-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            var btn = document.getElementById('notif-submit-btn');
            var status = document.getElementById('notif-status');
            var sendTo = form.querySelector('input[name="send_to"]:checked').value;
            var adminType = form.querySelector('input[name="admin_type"]:checked').value;

            if (sendTo === 'specific' && selectedUserIds.length === 0) {
                showToast('Pilih minimal satu penerima', 'red');
                return;
            }

            var fd = new FormData();
            fd.append(csrfName, csrfHash);
            fd.append('admin_type', adminType);
            fd.append('title', form.querySelector('input[name="title"]').value);
            fd.append('message', form.querySelector('textarea[name="message"]').value);
            fd.append('send_to', sendTo);
            if (sendTo === 'specific') fd.append('user_ids', selectedUserIds.join(','));

            if (adminType === 'gift') {
                var giftType = form.querySelector('input[name="gift_type"]:checked').value;
                fd.append('gift_type', giftType);
                if (giftType === 'border') {
                    var borderId = form.querySelector('select[name="gift_border_id"]').value;
                    if (!borderId) { showToast('Pilih border yang akan diberikan', 'red'); return; }
                    fd.append('gift_border_id', borderId);
                } else {
                    var coins = form.querySelector('input[name="gift_coins"]').value;
                    if (!coins || parseInt(coins) <= 0) { showToast('Jumlah koin tidak valid', 'red'); return; }
                    fd.append('gift_coins', coins);
                }
            }

            btn.disabled = true;
            status.textContent = 'Mengirim...';

            fetch('<?= base_url("admin/send_notification"); ?>', { method: 'POST', body: fd })
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    btn.disabled = false;
                    if (data.status === 'success') {
                        status.textContent = 'Terkirim ke ' + data.count + ' user';
                        showToast('Notifikasi terkirim ke ' + data.count + ' user', 'green');
                        setTimeout(function() { location.reload(); }, 1500);
                    } else {
                        status.textContent = data.message || 'Gagal';
                        showToast(data.message || 'Gagal mengirim', 'red');
                    }
                })
                .catch(function() {
                    btn.disabled = false;
                    status.textContent = 'Error jaringan';
                    showToast('Error jaringan', 'red');
                });
        });
    }

    // Delete
    window.deleteSentNotification = function(btn) {
        var fd = new FormData();
        fd.append(csrfName, csrfHash);
        fd.append('title', btn.getAttribute('data-title'));
        fd.append('message', btn.getAttribute('data-message'));
        fd.append('admin_type', btn.getAttribute('data-type'));
        fd.append('actor_id', btn.getAttribute('data-actor'));
        fd.append('created_at', btn.getAttribute('data-created'));

        if (!confirm('Hapus notifikasi ini dari semua penerima?')) return;

        fetch('<?= base_url("admin/delete_notification"); ?>', { method: 'POST', body: fd })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.status === 'success') {
                    var row = btn.closest('tr');
                    if (row) { row.style.opacity = '0'; setTimeout(function() { row.remove(); }, 300); }
                    showToast('Notifikasi dihapus', 'green');
                }
            });
    };
})();
</script>
