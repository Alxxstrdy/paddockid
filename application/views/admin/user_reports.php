<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="font-syne text-lg uppercase tracking-tight text-white">User Reports</h1>
            <p class="text-xs text-slate-500 mt-1"><?= number_format($total); ?> total laporan user</p>
        </div>
    </div>

    <!-- Status Filter -->
    <div class="flex gap-2 overflow-x-auto no-scrollbar">
        <?php
        $statuses = [
            'all'     => ['label' => 'Semua', 'icon' => 'layers'],
            'pending' => ['label' => 'Pending', 'icon' => 'clock'],
            'reviewed' => ['label' => 'Reviewed', 'icon' => 'check-circle'],
            'dismissed' => ['label' => 'Dismissed', 'icon' => 'x-circle'],
        ];
        foreach ($statuses as $key => $val):
        ?>
            <a href="<?= base_url('admin/user_reports?status=' . $key); ?>"
               class="flex-shrink-0 flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[10px] font-semibold border transition-all <?= $current_status === $key ? 'bg-red-500/10 text-red-400 border-red-500/20' : 'border-white/[0.06] text-slate-400 hover:border-white/10' ?>">
                <i data-lucide="<?= $val['icon']; ?>" class="w-3 h-3"></i> <?= $val['label']; ?>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Reports List -->
    <?php if (empty($reports)): ?>
        <div class="glass-card p-8 rounded-2xl text-center">
            <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-white/[0.03] flex items-center justify-center">
                <i data-lucide="check-circle" class="w-5 h-5 text-emerald-500"></i>
            </div>
            <p class="text-xs text-slate-400">Tidak ada laporan user ditemukan.</p>
        </div>
    <?php else: ?>
        <div class="space-y-3">
            <?php foreach ($reports as $r): ?>
                <div class="glass-card rounded-2xl border border-white/[0.04] overflow-hidden" id="ureport-<?= $r['id_report']; ?>">
                    <!-- Report Header -->
                    <div class="px-4 py-3 border-b border-white/[0.04] flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-semibold
                                <?= $r['status'] === 'pending' ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20' :
                                    ($r['status'] === 'reviewed' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' :
                                    'bg-slate-500/10 text-slate-400 border border-slate-500/20') ?>">
                                <?= $r['status']; ?>
                            </span>
                            <span class="text-[10px] text-slate-500">oleh <span class="font-semibold text-slate-300"><?= htmlspecialchars($r['reporter'], ENT_QUOTES, 'UTF-8'); ?></span></span>
                        </div>
                        <span class="text-[10px] text-slate-600 font-mono"><?= date('d M Y H:i', strtotime($r['created_at'])); ?></span>
                    </div>

                    <!-- Reported User Info -->
                    <div class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center overflow-hidden flex-shrink-0 border border-white/[0.06]">
                                <?php
                                    $avatar = !empty($r['reported_avatar']) ? $r['reported_avatar'] : 'default.jpg';
                                    $avatar_url = strpos($avatar, 'http') === 0 ? $avatar : assets_url('uploads/profile/' . $avatar);
                                ?>
                                <img src="<?= $avatar_url; ?>" alt="" class="w-full h-full object-cover" onerror="this.src='<?= assets_url('default.jpg'); ?>';">
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <a href="<?= base_url('user/' . ($r['reported_name'] ?? '')); ?>" target="_blank" class="text-xs font-bold text-white hover:text-red-400 transition-colors">
                                        @<?= htmlspecialchars($r['reported_name'] ?? 'Unknown', ENT_QUOTES, 'UTF-8'); ?>
                                    </a>
                                    <span class="px-1.5 py-0.5 rounded text-[8px] font-semibold
                                        <?= ($r['reported_status'] ?? '') === 'banned' ? 'bg-red-500/10 text-red-400 border border-red-500/20' :
                                            (($r['reported_status'] ?? '') === 'active' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-slate-500/10 text-slate-400 border border-slate-500/20') ?>">
                                        <?= $r['reported_status'] ?? 'unknown'; ?>
                                    </span>
                                </div>
                                <p class="text-[10px] text-slate-500 mt-0.5">User ID: <?= htmlspecialchars($r['reported_id'], ENT_QUOTES, 'UTF-8'); ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Report Reason -->
                    <div class="px-4 py-2.5 bg-white/[0.01] border-t border-white/[0.03]">
                        <p class="text-[10px] text-slate-500 mb-1">Alasan Report:</p>
                        <p class="text-xs text-slate-300"><?= htmlspecialchars($r['reason'], ENT_QUOTES, 'UTF-8'); ?></p>
                    </div>

                    <!-- Actions -->
                    <?php if ($r['status'] === 'pending'): ?>
                    <div class="px-4 py-3 border-t border-white/[0.04] flex items-center gap-2 flex-wrap">
                        <button onclick="resolveUserReport(<?= $r['id_report']; ?>, 'reviewed')"
                                class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[10px] font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 hover:bg-emerald-500/20 transition-all">
                            <i data-lucide="check" class="w-3 h-3"></i> Reviewed
                        </button>
                        <button onclick="resolveUserReport(<?= $r['id_report']; ?>, 'dismissed')"
                                class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[10px] font-semibold bg-slate-500/10 text-slate-400 border border-slate-500/20 hover:bg-slate-500/20 transition-all">
                            <i data-lucide="x" class="w-3 h-3"></i> Dismiss
                        </button>
                        <?php if (($r['reported_status'] ?? '') !== 'banned'): ?>
                        <button onclick="banUser('<?= $r['reported_id']; ?>', <?= $r['id_report']; ?>)"
                                class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[10px] font-semibold bg-red-500/10 text-red-400 border border-red-500/20 hover:bg-red-500/20 transition-all">
                            <i data-lucide="ban" class="w-3 h-3"></i> Ban User
                        </button>
                        <?php else: ?>
                        <button onclick="unbanUser('<?= $r['reported_id']; ?>', <?= $r['id_report']; ?>)"
                                class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[10px] font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 hover:bg-emerald-500/20 transition-all">
                            <i data-lucide="shield-check" class="w-3 h-3"></i> Unban User
                        </button>
                        <?php endif; ?>
                        <a href="<?= base_url('user/' . ($r['reported_name'] ?? '')); ?>" target="_blank"
                           class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[10px] font-semibold bg-white/[0.03] text-slate-400 border border-white/[0.06] hover:bg-white/[0.06] transition-all ml-auto">
                            <i data-lucide="external-link" class="w-3 h-3"></i> Lihat Profil
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
        <div class="flex items-center justify-center gap-2">
            <?php if ($current_page > 1): ?>
                <a href="<?= base_url('admin/user_reports?status=' . $current_status . '&page=' . ($current_page - 1)); ?>"
                   class="px-3 py-1.5 rounded-lg text-[10px] font-semibold glass-card border border-white/[0.06] text-slate-400 hover:text-white transition-all">
                    <i data-lucide="chevron-left" class="w-3 h-3 inline"></i> Prev
                </a>
            <?php endif; ?>
            <span class="text-[10px] text-slate-500 font-mono">Hal <?= $current_page; ?> / <?= $total_pages; ?></span>
            <?php if ($current_page < $total_pages): ?>
                <a href="<?= base_url('admin/user_reports?status=' . $current_status . '&page=' . ($current_page + 1)); ?>"
                   class="px-3 py-1.5 rounded-lg text-[10px] font-semibold glass-card border border-white/[0.06] text-slate-400 hover:text-white transition-all">
                    Next <i data-lucide="chevron-right" class="w-3 h-3 inline"></i>
                </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<script>
function resolveUserReport(id, status) {
    const csrfName = document.querySelector('meta[name="csrf-token-name"]').content;
    const csrfHash = document.querySelector('meta[name="csrf-token-hash"]').content;

    const body = new FormData();
    body.append('id_report', id);
    body.append('status', status);
    body.append(csrfName, csrfHash);

    fetch('<?= base_url("admin/resolve_user_report"); ?>', { method: 'POST', body })
        .then(r => r.json())
        .then(data => {
            if (data.status === 'success') {
                const el = document.getElementById('ureport-' + id);
                if (el) {
                    el.style.transition = 'all 0.3s';
                    el.style.opacity = '0.3';
                    const actions = el.querySelector('.flex.items-center.gap-2.flex-wrap');
                    if (actions) actions.remove();
                }
                showToast(data.message, 'green');
            }
        });
}

function banUser(userId, reportId) {
    if (!confirm('Yakin ingin ban user ini?')) return;

    const csrfName = document.querySelector('meta[name="csrf-token-name"]').content;
    const csrfHash = document.querySelector('meta[name="csrf-token-hash"]').content;

    const body = new FormData();
    body.append('user_id', userId);
    body.append(csrfName, csrfHash);

    fetch('<?= base_url("admin/ban_user"); ?>', { method: 'POST', body })
        .then(r => r.json())
        .then(data => {
            if (data.status === 'success') {
                showToast(data.message, 'green');
                resolveUserReport(reportId, 'reviewed');
            }
        });
}

function unbanUser(userId, reportId) {
    if (!confirm('Yakin ingin unban user ini?')) return;

    const csrfName = document.querySelector('meta[name="csrf-token-name"]').content;
    const csrfHash = document.querySelector('meta[name="csrf-token-hash"]').content;

    const body = new FormData();
    body.append('user_id', userId);
    body.append(csrfName, csrfHash);

    fetch('<?= base_url("admin/unban_user"); ?>', { method: 'POST', body })
        .then(r => r.json())
        .then(data => {
            if (data.status === 'success') {
                showToast(data.message, 'green');
            }
        });
}
</script>
