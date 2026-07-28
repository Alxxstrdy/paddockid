<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-lg font-bold text-white">Kirim Notifikasi</h2>
            <p class="text-xs text-slate-500 mt-1">Kirim notifikasi ke <?= number_format($user_count) ?> pengguna terdaftar</p>
        </div>
    </div>

    <!-- Compose Form -->
    <div class="admin-card rounded-xl p-5">
        <form id="notif-form" data-freeze-refresh>
            <div class="space-y-4">
                <!-- Type -->
                <div>
                    <label class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Tipe Notifikasi</label>
                    <div class="flex gap-2 mt-2">
                        <label class="flex items-center gap-2 px-3 py-2 rounded-lg border border-white/[0.06] cursor-pointer hover:bg-white/[0.02] transition-colors has-[:checked]:border-red-500/40 has-[:checked]:bg-red-500/10">
                            <input type="radio" name="admin_type" value="warning" checked class="hidden" onchange="toggleGiftOptions()">
                            <i data-lucide="alert-triangle" class="w-3.5 h-3.5 text-red-400"></i>
                            <span class="text-xs font-medium text-slate-300">Warning</span>
                        </label>
                        <label class="flex items-center gap-2 px-3 py-2 rounded-lg border border-white/[0.06] cursor-pointer hover:bg-white/[0.02] transition-colors has-[:checked]:border-emerald-500/40 has-[:checked]:bg-emerald-500/10">
                            <input type="radio" name="admin_type" value="gift" class="hidden" onchange="toggleGiftOptions()">
                            <i data-lucide="gift" class="w-3.5 h-3.5 text-emerald-400"></i>
                            <span class="text-xs font-medium text-slate-300">Gift</span>
                        </label>
                        <label class="flex items-center gap-2 px-3 py-2 rounded-lg border border-white/[0.06] cursor-pointer hover:bg-white/[0.02] transition-colors has-[:checked]:border-blue-500/40 has-[:checked]:bg-blue-500/10">
                            <input type="radio" name="admin_type" value="info" class="hidden" onchange="toggleGiftOptions()">
                            <i data-lucide="info" class="w-3.5 h-3.5 text-blue-400"></i>
                            <span class="text-xs font-medium text-slate-300">Info</span>
                        </label>
                    </div>
                </div>

                <!-- Gift Options (shown when Gift is selected) -->
                <div id="gift-options-wrap" class="hidden space-y-4 pl-4 border-l-2 border-emerald-500/30">
                    <div>
                        <label class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Jenis Gift</label>
                        <div class="flex gap-2 mt-2">
                            <label class="flex items-center gap-2 px-3 py-2 rounded-lg border border-white/[0.06] cursor-pointer hover:bg-white/[0.02] transition-colors has-[:checked]:border-emerald-500/40 has-[:checked]:bg-emerald-500/10">
                                <input type="radio" name="gift_type" value="border" checked class="hidden" onchange="toggleGiftDetail()">
                                <i data-lucide="frame" class="w-3.5 h-3.5 text-emerald-400"></i>
                                <span class="text-xs font-medium text-slate-300">Border</span>
                            </label>
                            <label class="flex items-center gap-2 px-3 py-2 rounded-lg border border-white/[0.06] cursor-pointer hover:bg-white/[0.02] transition-colors has-[:checked]:border-emerald-500/40 has-[:checked]:bg-emerald-500/10">
                                <input type="radio" name="gift_type" value="point" class="hidden" onchange="toggleGiftDetail()">
                                <i data-lucide="coins" class="w-3.5 h-3.5 text-amber-400"></i>
                                <span class="text-xs font-medium text-slate-300">Poin (Koin)</span>
                            </label>
                        </div>
                    </div>

                    <!-- Border Select -->
                    <div id="gift-border-wrap">
                        <label class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Pilih Border</label>
                        <select name="gift_border_id" class="w-full mt-1 bg-slate-800/50 text-xs text-slate-200 border border-white/[0.06] rounded-lg px-3 py-2.5 focus:outline-none focus:border-red-500/50">
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
                        <label class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Jumlah Koin</label>
                        <input type="number" name="gift_coins" min="1" placeholder="Contoh: 5000" class="w-full mt-1 bg-slate-800/50 text-xs text-slate-200 placeholder-slate-500 border border-white/[0.06] rounded-lg px-3 py-2.5 focus:outline-none focus:border-red-500/50">
                    </div>
                </div>

                <!-- Title -->
                <div>
                    <label class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Judul</label>
                    <input type="text" name="title" placeholder="Contoh: Peringatan Pelanggaran" class="w-full mt-1 bg-slate-800/50 text-xs text-slate-200 placeholder-slate-500 border border-white/[0.06] rounded-lg px-3 py-2.5 focus:outline-none focus:border-red-500/50" required>
                </div>

                <!-- Message -->
                <div>
                    <label class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Pesan</label>
                    <textarea name="message" rows="3" placeholder="Isi notifikasi yang ingin dikirim..." class="w-full mt-1 bg-slate-800/50 text-xs text-slate-200 placeholder-slate-500 border border-white/[0.06] rounded-lg px-3 py-2.5 focus:outline-none focus:border-red-500/50 resize-none" required></textarea>
                </div>

                <!-- Recipients -->
                <div>
                    <label class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Penerima</label>
                    <div class="flex gap-2 mt-2">
                        <label class="flex items-center gap-2 px-3 py-2 rounded-lg border border-white/[0.06] cursor-pointer hover:bg-white/[0.02] transition-colors has-[:checked]:border-red-500/40 has-[:checked]:bg-red-500/10">
                            <input type="radio" name="send_to" value="all" checked class="hidden" onchange="document.getElementById('user-search-wrap').classList.add('hidden')">
                            <i data-lucide="users" class="w-3.5 h-3.5 text-slate-400"></i>
                            <span class="text-xs font-medium text-slate-300">Semua User</span>
                        </label>
                        <label class="flex items-center gap-2 px-3 py-2 rounded-lg border border-white/[0.06] cursor-pointer hover:bg-white/[0.02] transition-colors has-[:checked]:border-red-500/40 has-[:checked]:bg-red-500/10">
                            <input type="radio" name="send_to" value="specific" class="hidden" onchange="document.getElementById('user-search-wrap').classList.remove('hidden')">
                            <i data-lucide="user-check" class="w-3.5 h-3.5 text-slate-400"></i>
                            <span class="text-xs font-medium text-slate-300">Pilih User</span>
                        </label>
                    </div>

                    <!-- User Search (hidden by default) -->
                    <div id="user-search-wrap" class="hidden mt-3 space-y-2">
                        <div class="relative">
                            <i data-lucide="search" class="w-3.5 h-3.5 text-slate-500 absolute left-3 top-1/2 -translate-y-1/2"></i>
                            <input type="text" id="user-search-input" placeholder="Cari username..." class="w-full bg-slate-800/50 text-xs text-slate-200 placeholder-slate-500 border border-white/[0.06] rounded-lg pl-8 pr-3 py-2 focus:outline-none focus:border-red-500/50" autocomplete="off">
                        </div>
                        <div id="user-search-results" class="max-h-40 overflow-y-auto rounded-lg border border-white/[0.06] divide-y divide-white/[0.03] hidden"></div>
                        <div id="selected-users" class="flex flex-wrap gap-1.5"></div>
                    </div>
                </div>

                <!-- Submit -->
                <div class="flex items-center gap-3 pt-2">
                    <button type="submit" id="notif-submit-btn" class="inline-flex items-center gap-2 px-5 py-2.5 text-xs font-semibold text-white bg-red-600 hover:bg-red-500 rounded-xl transition-colors shadow-lg shadow-red-600/10 disabled:opacity-50 disabled:cursor-not-allowed">
                        <i data-lucide="send" class="w-3.5 h-3.5"></i> Kirim Notifikasi
                    </button>
                    <span id="notif-status" class="text-[10px] text-slate-500"></span>
                </div>
            </div>
        </form>
    </div>

    <!-- History Table -->
    <div class="admin-card rounded-xl overflow-hidden">
        <div class="px-4 py-3 border-b border-white/[0.04]">
            <p class="text-xs font-bold text-white">Riwayat Kirim</p>
        </div>
        <?php if (!empty($notifications)): ?>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-white/[0.04]">
                            <th class="text-left text-[10px] font-semibold text-slate-500 uppercase tracking-wider px-4 py-3">Tipe</th>
                            <th class="text-left text-[10px] font-semibold text-slate-500 uppercase tracking-wider px-4 py-3">Judul</th>
                            <th class="text-left text-[10px] font-semibold text-slate-500 uppercase tracking-wider px-4 py-3">Pesan</th>
                            <th class="text-left text-[10px] font-semibold text-slate-500 uppercase tracking-wider px-4 py-3">Detail Gift</th>
                            <th class="text-left text-[10px] font-semibold text-slate-500 uppercase tracking-wider px-4 py-3">Penerima</th>
                            <th class="text-left text-[10px] font-semibold text-slate-500 uppercase tracking-wider px-4 py-3">Waktu</th>
                            <th class="text-right text-[10px] font-semibold text-slate-500 uppercase tracking-wider px-4 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="notif-history" class="divide-y divide-white/[0.03]">
                        <?php foreach ($notifications as $n): ?>
                            <tr class="hover:bg-white/[0.02] transition-colors">
                                <td class="px-4 py-3">
                                    <?php
                                        $type_config = [
                                            'warning' => ['icon' => 'alert-triangle', 'color' => 'red', 'label' => 'Warning'],
                                            'gift'    => ['icon' => 'gift', 'color' => 'emerald', 'label' => 'Gift'],
                                            'info'    => ['icon' => 'info', 'color' => 'blue', 'label' => 'Info'],
                                        ];
                                        $tc = $type_config[$n['admin_type']] ?? $type_config['info'];
                                    ?>
                                    <span class="inline-flex items-center gap-1.5 text-[10px] font-semibold px-2 py-0.5 rounded-full border border-<?= $tc['color'] ?>-500/20 bg-<?= $tc['color'] ?>-500/10 text-<?= $tc['color'] ?>-400">
                                        <i data-lucide="<?= $tc['icon'] ?>" class="w-2.5 h-2.5"></i> <?= $tc['label'] ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <p class="text-xs font-semibold text-white"><?= htmlspecialchars($n['title'], ENT_QUOTES, 'UTF-8'); ?></p>
                                </td>
                                <td class="px-4 py-3">
                                    <p class="text-xs text-slate-300 truncate max-w-[200px]"><?= htmlspecialchars($n['message'], ENT_QUOTES, 'UTF-8'); ?></p>
                                </td>
                                <td class="px-4 py-3">
                                    <?php if ($n['admin_type'] === 'gift' && $n['gift_type'] === 'border' && !empty($n['border_name'])): ?>
                                        <span class="inline-flex items-center gap-1 text-[10px] font-medium px-2 py-0.5 rounded-full bg-purple-500/10 border border-purple-500/20 text-purple-400">
                                            <i data-lucide="frame" class="w-2.5 h-2.5"></i> <?= htmlspecialchars($n['border_name'], ENT_QUOTES, 'UTF-8'); ?>
                                        </span>
                                    <?php elseif ($n['admin_type'] === 'gift' && $n['gift_type'] === 'point' && !empty($n['gift_coins'])): ?>
                                        <span class="inline-flex items-center gap-1 text-[10px] font-medium px-2 py-0.5 rounded-full bg-amber-500/10 border border-amber-500/20 text-amber-400">
                                            <i data-lucide="coins" class="w-2.5 h-2.5"></i> <?= number_format($n['gift_coins']); ?> koin
                                        </span>
                                    <?php else: ?>
                                        <span class="text-[10px] text-slate-600">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3">
                                    <?php if ($n['admin_type'] === 'gift'): ?>
                                        <span class="text-[10px] text-slate-400"><?= $n['recipient_count']; ?> penerima</span>
                                        <span class="text-[10px] text-emerald-400 ml-1">• <?= $n['claimed_count']; ?> klaim</span>
                                    <?php else: ?>
                                        <span class="text-[10px] text-slate-400">Semua User</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="text-[10px] text-slate-500 font-mono"><?= htmlspecialchars($n['created_at'], ENT_QUOTES, 'UTF-8'); ?></span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <button onclick="deleteSentNotification(this)" data-title="<?= htmlspecialchars($n['title'], ENT_QUOTES, 'UTF-8'); ?>" data-message="<?= htmlspecialchars($n['message'], ENT_QUOTES, 'UTF-8'); ?>" data-type="<?= htmlspecialchars($n['admin_type'], ENT_QUOTES, 'UTF-8'); ?>" data-actor="<?= $n['actor_id']; ?>" data-created="<?= htmlspecialchars($n['created_at'], ENT_QUOTES, 'UTF-8'); ?>" class="p-1.5 rounded-lg text-slate-500 hover:text-red-400 hover:bg-red-500/10 transition-colors">
                                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="px-4 py-12 text-center text-slate-600 text-xs">Belum ada riwayat notifikasi</div>
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
                r.closest('label').classList.remove('border-red-500/40', 'bg-red-500/10', 'border-emerald-500/40', 'bg-emerald-500/10', 'border-blue-500/40', 'bg-blue-500/10');
            });
            var colors = { warning: 'red', gift: 'emerald', info: 'blue' };
            var c = colors[this.value];
            this.closest('label').classList.add('border-' + c + '-500/40', 'bg-' + c + '-500/10');
        });
    });

    // Gift sub-type radio styling
    document.querySelectorAll('input[name="gift_type"]').forEach(function(radio) {
        radio.addEventListener('change', function() {
            document.querySelectorAll('input[name="gift_type"]').forEach(function(r) {
                r.closest('label').classList.remove('border-emerald-500/40', 'bg-emerald-500/10');
            });
            this.closest('label').classList.add('border-emerald-500/40', 'bg-emerald-500/10');
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
                        if (!users.length) { searchResults.innerHTML = '<div class="px-3 py-2 text-[10px] text-slate-500">Tidak ditemukan</div>'; searchResults.classList.remove('hidden'); return; }
                        var html = '';
                        users.forEach(function(u) {
                            if (selectedUserIds.indexOf(String(u.id_user)) !== -1) return;
                            html += '<div class="flex items-center gap-2 px-3 py-2 hover:bg-white/[0.03] cursor-pointer transition-colors" onclick="window._addUser(' + u.id_user + ', \'' + u.username.replace(/'/g, "\\'") + '\')">';
                            html += '<img src="' + (u.avatar_url || '<?= assets_url("default.jpg"); ?>') + '" class="w-5 h-5 rounded-full object-cover">';
                            html += '<span class="text-xs text-slate-200">' + u.username + '</span>';
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
        tag.className = 'inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-white/[0.05] border border-white/[0.08] text-[10px] text-slate-300';
        tag.setAttribute('data-user-id', id);
        tag.innerHTML = username + ' <button onclick="window._removeUser(' + id + ')" class="text-slate-500 hover:text-red-400 ml-0.5">&times;</button>';
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
