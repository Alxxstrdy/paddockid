<div class="space-y-6" data-freeze-refresh>
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="font-syne text-lg uppercase tracking-tight text-white">User Activity Log</h1>
            <p class="text-xs text-slate-500 mt-1"><?= number_format($total); ?> aktivitas tercatat</p>
        </div>
        <button onclick="openClearModal()" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[10px] font-semibold bg-red-500/10 text-red-400 border border-red-500/20 hover:bg-red-500/20 transition-all">
            <i data-lucide="trash-2" class="w-3 h-3"></i> Bersihkan Log
        </button>
    </div>

    <!-- Filter Form -->
    <form method="GET" action="<?= base_url('admin/activity_logs'); ?>" class="glass-card p-4 rounded-2xl border border-white/[0.04]">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
            <div>
                <label class="text-[10px] text-slate-500 uppercase font-semibold block mb-1">Pencarian</label>
                <input type="text" name="search" value="<?= htmlspecialchars($filter['search'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                       placeholder="Username atau deskripsi..." class="w-full bg-slate-900/60 text-xs text-slate-300 border border-white/[0.06] rounded-lg px-3 py-2 focus:outline-none focus:border-red-500/50 placeholder-slate-600">
            </div>
            <div>
                <label class="text-[10px] text-slate-500 uppercase font-semibold block mb-1">Aksi</label>
                <select name="action" class="w-full bg-slate-900/60 text-xs text-slate-300 border border-white/[0.06] rounded-lg px-3 py-2 focus:outline-none focus:border-red-500/50">
                    <option value="">Semua</option>
                    <?php foreach ($action_options as $opt): ?>
                        <option value="<?= htmlspecialchars($opt, ENT_QUOTES, 'UTF-8'); ?>" <?= ($filter['action'] ?? '') === $opt ? 'selected' : ''; ?>><?= htmlspecialchars($opt, ENT_QUOTES, 'UTF-8'); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="text-[10px] text-slate-500 uppercase font-semibold block mb-1">Dari Tanggal</label>
                <input type="date" name="date_from" value="<?= htmlspecialchars($filter['date_from'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                       class="w-full bg-slate-900/60 text-xs text-slate-300 border border-white/[0.06] rounded-lg px-3 py-2 focus:outline-none focus:border-red-500/50">
            </div>
            <div class="flex items-end gap-2">
                <div class="flex-1">
                    <label class="text-[10px] text-slate-500 uppercase font-semibold block mb-1">Sampai</label>
                    <input type="date" name="date_to" value="<?= htmlspecialchars($filter['date_to'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                           class="w-full bg-slate-900/60 text-xs text-slate-300 border border-white/[0.06] rounded-lg px-3 py-2 focus:outline-none focus:border-red-500/50">
                </div>
                <button type="submit" class="px-4 py-2 bg-red-500/10 text-red-400 text-[10px] font-semibold rounded-lg border border-red-500/20 hover:bg-red-500/20 transition-all flex-shrink-0">
                    Filter
                </button>
            </div>
        </div>
    </form>

    <!-- Activity Log Table -->
    <div class="glass-card rounded-2xl border border-white/[0.04] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-white/[0.06]">
                        <th class="px-4 py-3 text-[10px] font-syne uppercase tracking-wider text-slate-500">Waktu</th>
                        <th class="px-4 py-3 text-[10px] font-syne uppercase tracking-wider text-slate-500">User</th>
                        <th class="px-4 py-3 text-[10px] font-syne uppercase tracking-wider text-slate-500">Aksi</th>
                        <th class="px-4 py-3 text-[10px] font-syne uppercase tracking-wider text-slate-500">Detail</th>
                        <th class="px-4 py-3 text-[10px] font-syne uppercase tracking-wider text-slate-500">IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/[0.04]">
                    <?php if (empty($logs)): ?>
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-xs text-slate-500">Tidak ada aktivitas.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($logs as $log): ?>
                            <tr class="hover:bg-white/[0.02] transition-colors">
                                <td class="px-4 py-2.5">
                                    <span class="text-[10px] text-slate-400 font-mono whitespace-nowrap"><?= date('d M Y H:i:s', strtotime($log['created_at'])); ?></span>
                                </td>
                                <td class="px-4 py-2.5">
                                    <span class="text-[10px] text-slate-300 font-semibold"><?= htmlspecialchars($log['username'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></span>
                                </td>
                                <td class="px-4 py-2.5">
                                    <?php
                                    $action_colors = [
                                        'login'              => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
                                        'login_success'      => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
                                        'login_failed'       => 'bg-red-500/10 text-red-400 border-red-500/20',
                                        'logout'             => 'bg-slate-500/10 text-slate-400 border-slate-500/20',
                                        'register'           => 'bg-blue-500/10 text-blue-400 border-blue-500/20',
                                        'create_post'        => 'bg-purple-500/10 text-purple-400 border-purple-500/20',
                                        'edit_post'          => 'bg-blue-500/10 text-blue-400 border-blue-500/20',
                                        'delete_post'        => 'bg-red-500/10 text-red-400 border-red-500/20',
                                        'add_comment'        => 'bg-cyan-500/10 text-cyan-400 border-cyan-500/20',
                                        'edit_comment'       => 'bg-blue-500/10 text-blue-400 border-blue-500/20',
                                        'delete_comment'     => 'bg-red-500/10 text-red-400 border-red-500/20',
                                        'like_post'          => 'bg-pink-500/10 text-pink-400 border-pink-500/20',
                                        'like_comment'       => 'bg-pink-500/10 text-pink-400 border-pink-500/20',
                                        'unlike_comment'     => 'bg-slate-500/10 text-slate-400 border-slate-500/20',
                                        'follow'             => 'bg-indigo-500/10 text-indigo-400 border-indigo-500/20',
                                        'unfollow'           => 'bg-slate-500/10 text-slate-400 border-slate-500/20',
                                        'block_user'         => 'bg-red-500/10 text-red-400 border-red-500/20',
                                        'unblock_user'       => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
                                        'change_password'    => 'bg-amber-500/10 text-amber-400 border-amber-500/20',
                                        'change_email'       => 'bg-amber-500/10 text-amber-400 border-amber-500/20',
                                        'set_password'       => 'bg-amber-500/10 text-amber-400 border-amber-500/20',
                                        'unlink_google'      => 'bg-amber-500/10 text-amber-400 border-amber-500/20',
                                    ];
                                    $color_class = $action_colors[$log['action']] ?? 'bg-slate-500/10 text-slate-400 border-slate-500/20';
                                    ?>
                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-semibold border <?= $color_class; ?>">
                                        <?= htmlspecialchars($log['action'], ENT_QUOTES, 'UTF-8'); ?>
                                    </span>
                                </td>
                                <td class="px-4 py-2.5">
                                    <span class="text-xs text-slate-300"><?= htmlspecialchars($log['details'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></span>
                                    <?php if (!empty($log['target_type']) && !empty($log['target_id'])): ?>
                                        <span class="text-[9px] text-slate-600 ml-1 font-mono">[<?= htmlspecialchars($log['target_type'], ENT_QUOTES, 'UTF-8'); ?>:<?= htmlspecialchars($log['target_id'], ENT_QUOTES, 'UTF-8'); ?>]</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-2.5">
                                    <span class="text-[10px] text-slate-500 font-mono"><?= htmlspecialchars($log['ip_address'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
    <div class="flex items-center justify-center gap-2">
        <?php if ($current_page > 1): ?>
            <a href="<?= base_url('admin/activity_logs?' . http_build_query(array_merge($filter, ['page' => $current_page - 1]))); ?>"
               class="px-3 py-1.5 rounded-lg text-[10px] font-semibold glass-card border border-white/[0.06] text-slate-400 hover:text-white transition-all">
                <i data-lucide="chevron-left" class="w-3 h-3 inline"></i> Prev
            </a>
        <?php endif; ?>
        <span class="text-[10px] text-slate-500 font-mono">Hal <?= $current_page; ?> / <?= $total_pages; ?></span>
        <?php if ($current_page < $total_pages): ?>
            <a href="<?= base_url('admin/activity_logs?' . http_build_query(array_merge($filter, ['page' => $current_page + 1]))); ?>"
               class="px-3 py-1.5 rounded-lg text-[10px] font-semibold glass-card border border-white/[0.06] text-slate-400 hover:text-white transition-all">
                Next <i data-lucide="chevron-right" class="w-3 h-3 inline"></i>
            </a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<!-- Clear Log Modal -->
<div id="clear-log-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeClearModal()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-[#0f1220] rounded-2xl border border-white/[0.06] w-full max-w-sm p-6 space-y-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-red-500/10 flex items-center justify-center flex-shrink-0">
                    <i data-lucide="alert-triangle" class="w-5 h-5 text-red-400"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-white">Bersihkan Activity Log</h3>
                    <p class="text-[10px] text-slate-500 mt-0.5">Aksi ini akan menghapus log aktivitas user & login attempts. Tidak dapat dibatalkan.</p>
                </div>
            </div>
            <div class="space-y-3">
                <label class="flex items-center gap-3 p-3 rounded-xl border border-white/[0.06] cursor-pointer hover:bg-white/[0.02] transition-all">
                    <input type="radio" name="clear_type" value="all" checked class="accent-red-500" onchange="toggleClearDate(false)">
                    <div>
                        <span class="text-xs text-white font-semibold">Semua Log</span>
                        <p class="text-[10px] text-slate-500">Hapus seluruh aktivitas & login attempts</p>
                    </div>
                </label>
                <label class="flex items-center gap-3 p-3 rounded-xl border border-white/[0.06] cursor-pointer hover:bg-white/[0.02] transition-all">
                    <input type="radio" name="clear_type" value="before" class="accent-red-500" onchange="toggleClearDate(true)">
                    <div>
                        <span class="text-xs text-white font-semibold">Sebelum Tanggal</span>
                        <p class="text-[10px] text-slate-500">Hapus aktivitas sebelum tanggal tertentu</p>
                    </div>
                </label>
                <div id="clear-date-wrapper" class="hidden">
                    <input type="date" id="clear-before-date"
                           class="w-full bg-slate-900/60 text-xs text-slate-300 border border-white/[0.06] rounded-lg px-3 py-2 focus:outline-none focus:border-red-500/50">
                </div>
            </div>
            <div class="flex gap-2">
                <button onclick="closeClearModal()" class="flex-1 px-4 py-2 rounded-lg text-[10px] font-semibold border border-white/[0.06] text-slate-400 hover:text-white hover:bg-white/[0.04] transition-all">
                    Batal
                </button>
                <button onclick="executeClear()" class="flex-1 px-4 py-2 rounded-lg text-[10px] font-semibold bg-red-500/20 text-red-400 border border-red-500/30 hover:bg-red-500/30 transition-all">
                    Bersihkan
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function openClearModal() {
    document.getElementById('clear-log-modal').classList.remove('hidden');
    lucide.createIcons();
}
function closeClearModal() {
    document.getElementById('clear-log-modal').classList.add('hidden');
}
function toggleClearDate(show) {
    document.getElementById('clear-date-wrapper').classList.toggle('hidden', !show);
}
function executeClear() {
    const type = document.querySelector('input[name="clear_type"]:checked').value;
    const csrfName = document.querySelector('meta[name="csrf-token-name"]').content;
    const csrfHash = document.querySelector('meta[name="csrf-token-hash"]').content;

    const body = new FormData();
    body.append(csrfName, csrfHash);

    if (type === 'before') {
        const date = document.getElementById('clear-before-date').value;
        if (!date) {
            showToast('Pilih tanggal terlebih dahulu', 'red');
            return;
        }
        body.append('before_date', date);
    }

    fetch('<?= base_url("admin/clear_activity_log"); ?>', { method: 'POST', body })
        .then(r => r.json())
        .then(data => {
            if (data.status === 'success') {
                showToast(data.message, 'green');
                closeClearModal();
                setTimeout(() => location.reload(), 800);
            } else {
                showToast(data.message || 'Gagal membersihkan log', 'red');
            }
        })
        .catch(() => showToast('Terjadi kesalahan', 'red'));
}
</script>
