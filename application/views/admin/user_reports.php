<div class="space-y-6">
    <!-- Header -->
    <div class="flex-row items-center justify-between">
        <div>
            <h1 class="text-heading text-lg text-transform-uppercase c-white">User Reports</h1>
            <p class="text-xs c-subtle" style="margin-top:4px;"><?= number_format($total); ?> total laporan user</p>
        </div>
    </div>

    <!-- Status Filter -->
    <div class="flex-row gap-2 overflow-x-auto no-scrollbar">
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
               class="flex-shrink-0 flex-row items-center gap-1-5 px-3 py-1-5 rounded-full font-semibold transition" style="font-size:10px;<?= $current_status === $key ? 'background:var(--color-primary-bg);color:var(--color-primary);border:1px solid var(--color-primary-border)' : 'border:1px solid var(--border-default);color:var(--color-info)' ?>" onmouseover="this.style.borderColor='var(--border-strong)'" onmouseout="this.style.borderColor='<?= $current_status === $key ? 'var(--color-primary-border)' : 'var(--border-default)' ?>'">
                <i data-lucide="<?= $val['icon']; ?>" class="w-3 h-3"></i> <?= $val['label']; ?>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Reports List -->
    <?php if (empty($reports)): ?>
        <div class="card p-8 rounded-2xl text-center">
            <div class="w-12 h-12 mx-auto mb-3 rounded-full flex-row items-center justify-center" style="background:rgba(255,255,255,0.03);">
                <i data-lucide="check-circle" class="w-5 h-5 c-success"></i>
            </div>
            <p class="text-xs c-muted">Tidak ada laporan user ditemukan.</p>
        </div>
    <?php else: ?>
        <div class="space-y-3">
            <?php foreach ($reports as $r): ?>
                <div class="card rounded-2xl border overflow-hidden" style="border-color:var(--border-subtle);" id="ureport-<?= $r['id_report']; ?>">
                    <!-- Report Header -->
                    <div class="px-4 py-3 border-b flex-row items-center justify-between" style="border-color:var(--border-subtle);">
                        <div class="flex-row items-center gap-2">
                            <span class="px-2 py-05 rounded-full font-semibold"
                                style="font-size:9px;<?= $r['status'] === 'pending' ? 'background:var(--color-warning-bg);color:var(--color-warning);border:1px solid var(--color-warning-border)' :
                                    ($r['status'] === 'reviewed' ? 'background:var(--color-success-bg);color:var(--color-success);border:1px solid var(--color-success-border)' :
                                    'background:rgba(100,116,139,0.1);color:var(--color-info);border:1px solid rgba(100,116,139,0.2)') ?>">
                                <?= $r['status']; ?>
                            </span>
                            <span class="text-micro c-subtle">oleh <span class="font-semibold" style="color:var(--text-secondary);"><?= htmlspecialchars($r['reporter'], ENT_QUOTES, 'UTF-8'); ?></span></span>
                        </div>
                        <span class="text-micro c-faint font-mono"><?= date('d M Y H:i', strtotime($r['created_at'])); ?></span>
                    </div>

                    <!-- Reported User Info -->
                    <div class="px-4 py-3">
                        <div class="flex-row items-center gap-3">
                            <div class="w-10 h-10 rounded-full flex-row items-center justify-center overflow-hidden flex-shrink-0 border" style="background:var(--bg-surface-raised);border-color:var(--border-default);">
                                <?php
                                    $avatar = !empty($r['reported_avatar']) ? $r['reported_avatar'] : 'default.jpg';
                                    $avatar_url = strpos($avatar, 'http') === 0 ? $avatar : assets_url('uploads/profile/' . $avatar);
                                ?>
                                <img src="<?= $avatar_url; ?>" alt="" class="w-full h-full" style="object-fit:cover;" onerror="this.src='<?= assets_url('default.jpg'); ?>';">
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex-row items-center gap-2">
                                    <a href="<?= base_url('user/' . ($r['reported_name'] ?? '')); ?>" target="_blank" class="text-xs font-bold c-white transition-colors" onmouseover="this.style.color='var(--color-primary)'" onmouseout="this.style.color=''">
                                        @<?= htmlspecialchars($r['reported_name'] ?? 'Unknown', ENT_QUOTES, 'UTF-8'); ?>
                                    </a>
                                    <span class="px-1-5 py-05 rounded font-semibold"
                                        style="font-size:8px;<?= ($r['reported_status'] ?? '') === 'banned' ? 'background:var(--color-primary-bg);color:var(--color-primary);border:1px solid var(--color-primary-border)' :
                                            (($r['reported_status'] ?? '') === 'active' ? 'background:var(--color-success-bg);color:var(--color-success);border:1px solid var(--color-success-border)' : 'background:rgba(100,116,139,0.1);color:var(--color-info);border:1px solid rgba(100,116,139,0.2)') ?>">
                                        <?= $r['reported_status'] ?? 'unknown'; ?>
                                    </span>
                                </div>
                                <p class="text-micro c-subtle" style="margin-top:2px;">User ID: <?= htmlspecialchars($r['reported_id'], ENT_QUOTES, 'UTF-8'); ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Report Reason -->
                    <div class="px-4 py-2-5 border-t" style="background:rgba(255,255,255,0.01);border-color:rgba(255,255,255,0.03);">
                        <p class="text-micro c-subtle mb-1">Alasan Report:</p>
                        <p class="text-xs" style="color:var(--text-secondary);"><?= htmlspecialchars($r['reason'], ENT_QUOTES, 'UTF-8'); ?></p>
                    </div>

                    <!-- Actions -->
                    <?php if ($r['status'] === 'pending'): ?>
                    <div class="px-4 py-3 border-t flex-row items-center gap-2 flex-wrap" style="border-color:var(--border-subtle);">
                        <button onclick="resolveUserReport(<?= $r['id_report']; ?>, 'reviewed')"
                                class="btn btn-sm" style="font-size:10px;background:var(--color-success-bg);color:var(--color-success);border:1px solid var(--color-success-border);" onmouseover="this.style.background='rgba(34,197,94,0.2)'" onmouseout="this.style.background='var(--color-success-bg)'">
                            <i data-lucide="check" class="w-3 h-3"></i> Reviewed
                        </button>
                        <button onclick="resolveUserReport(<?= $r['id_report']; ?>, 'dismissed')"
                                class="btn btn-sm" style="font-size:10px;background:rgba(100,116,139,0.1);color:var(--color-info);border:1px solid rgba(100,116,139,0.2);" onmouseover="this.style.background='rgba(100,116,139,0.2)'" onmouseout="this.style.background='rgba(100,116,139,0.1)'">
                            <i data-lucide="x" class="w-3 h-3"></i> Dismiss
                        </button>
                        <?php if (($r['reported_status'] ?? '') !== 'banned'): ?>
                        <button onclick="banUser('<?= $r['reported_id']; ?>', <?= $r['id_report']; ?>)"
                                class="btn btn-sm" style="font-size:10px;background:var(--color-primary-bg);color:var(--color-primary);border:1px solid var(--color-primary-border);" onmouseover="this.style.background='rgba(239,68,68,0.2)'" onmouseout="this.style.background='var(--color-primary-bg)'">
                            <i data-lucide="ban" class="w-3 h-3"></i> Ban User
                        </button>
                        <?php else: ?>
                        <button onclick="unbanUser('<?= $r['reported_id']; ?>', <?= $r['id_report']; ?>)"
                                class="btn btn-sm" style="font-size:10px;background:var(--color-success-bg);color:var(--color-success);border:1px solid var(--color-success-border);" onmouseover="this.style.background='rgba(34,197,94,0.2)'" onmouseout="this.style.background='var(--color-success-bg)'">
                            <i data-lucide="shield-check" class="w-3 h-3"></i> Unban User
                        </button>
                        <?php endif; ?>
                        <a href="<?= base_url('user/' . ($r['reported_name'] ?? '')); ?>" target="_blank"
                           class="btn btn-sm ml-auto" style="font-size:10px;background:rgba(255,255,255,0.03);color:var(--color-info);border:1px solid var(--border-default);" onmouseover="this.style.background='rgba(255,255,255,0.06)'" onmouseout="this.style.background='rgba(255,255,255,0.03)'">
                            <i data-lucide="external-link" class="w-3 h-3"></i> Lihat Profil
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
        <div class="flex-row items-center justify-center gap-2">
            <?php if ($current_page > 1): ?>
                <a href="<?= base_url('admin/user_reports?status=' . $current_status . '&page=' . ($current_page - 1)); ?>"
                   class="pagination-btn card">
                    <i data-lucide="chevron-left" class="w-3 h-3 inline"></i> Prev
                </a>
            <?php endif; ?>
            <span class="text-micro c-subtle font-mono">Hal <?= $current_page; ?> / <?= $total_pages; ?></span>
            <?php if ($current_page < $total_pages): ?>
                <a href="<?= base_url('admin/user_reports?status=' . $current_status . '&page=' . ($current_page + 1)); ?>"
                   class="pagination-btn card">
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
