<div class="space-y-6">
    <!-- Header -->
    <div class="flex-row items-center justify-between">
        <div>
            <h1 class="text-heading text-lg text-transform-uppercase c-white">Post Reports</h1>
            <p class="text-xs c-subtle" style="margin-top:4px;"><?= number_format($total); ?> total laporan</p>
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
            $count = $key === 'all' ? $total : ($key === 'pending' ? $stats['pending_reports'] ?? 0 : 0);
        ?>
            <a href="<?= base_url('admin/post_reports?status=' . $key); ?>"
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
            <p class="text-xs c-muted">Tidak ada laporan ditemukan.</p>
        </div>
    <?php else: ?>
        <div class="space-y-3">
            <?php foreach ($reports as $r): ?>
                <div class="card rounded-2xl border overflow-hidden" style="border-color:var(--border-subtle);" id="report-<?= $r['id_report']; ?>">
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

                    <!-- Post Content Preview -->
                    <div class="px-4 py-3">
                        <div class="flex-row items-start gap-3">
                            <div class="flex-1 min-w-0">
                                <div class="flex-row items-center gap-2 mb-1-5">
                                    <span class="text-micro c-subtle">Post oleh</span>
                                    <a href="<?= base_url('user/' . ($r['post_owner_name'] ?? '')); ?>" class="text-micro c-primary font-semibold" onmouseover="this.style.color='var(--text-secondary)'" onmouseout="this.style.color='var(--color-primary)'"><?= htmlspecialchars($r['post_owner_name'] ?? 'Unknown', ENT_QUOTES, 'UTF-8'); ?></a>
                                    <?php if (!empty($r['post_deleted'])): ?>
                                        <span class="text-micro c-faint px-1-5 py-05 rounded" style="background:var(--bg-surface-raised);">deleted</span>
                                    <?php endif; ?>
                                </div>
                                <p class="text-xs leading-relaxed line-clamp-3" style="color:var(--text-secondary);"><?= htmlspecialchars(mb_substr($r['post_content'] ?? '', 0, 200), ENT_QUOTES, 'UTF-8'); ?></p>
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
                        <button onclick="resolveReport(<?= $r['id_report']; ?>, 'reviewed')"
                                class="btn btn-sm" style="font-size:10px;background:var(--color-success-bg);color:var(--color-success);border:1px solid var(--color-success-border);" onmouseover="this.style.background='rgba(34,197,94,0.2)'" onmouseout="this.style.background='var(--color-success-bg)'">
                            <i data-lucide="check" class="w-3 h-3"></i> Reviewed
                        </button>
                        <button onclick="resolveReport(<?= $r['id_report']; ?>, 'dismissed')"
                                class="btn btn-sm" style="font-size:10px;background:rgba(100,116,139,0.1);color:var(--color-info);border:1px solid rgba(100,116,139,0.2);" onmouseover="this.style.background='rgba(100,116,139,0.2)'" onmouseout="this.style.background='rgba(100,116,139,0.1)'">
                            <i data-lucide="x" class="w-3 h-3"></i> Dismiss
                        </button>
                        <?php if (!empty($r['id_post']) && empty($r['post_deleted'])): ?>
                        <button onclick="deletePost('<?= $r['id_post']; ?>', <?= $r['id_report']; ?>)"
                                class="btn btn-sm" style="font-size:10px;background:var(--color-primary-bg);color:var(--color-primary);border:1px solid var(--color-primary-border);" onmouseover="this.style.background='rgba(239,68,68,0.2)'" onmouseout="this.style.background='var(--color-primary-bg)'">
                            <i data-lucide="trash-2" class="w-3 h-3"></i> Hapus Post
                        </button>
                        <?php endif; ?>
                        <a href="<?= base_url('post/detail/' . ($r['id_post'] ?? '')); ?>" target="_blank"
                           class="btn btn-sm ml-auto" style="font-size:10px;background:rgba(255,255,255,0.03);color:var(--color-info);border:1px solid var(--border-default);" onmouseover="this.style.background='rgba(255,255,255,0.06)'" onmouseout="this.style.background='rgba(255,255,255,0.03)'">
                            <i data-lucide="external-link" class="w-3 h-3"></i> Lihat Post
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
                <a href="<?= base_url('admin/post_reports?status=' . $current_status . '&page=' . ($current_page - 1)); ?>"
                   class="pagination-btn card">
                    <i data-lucide="chevron-left" class="w-3 h-3 inline"></i> Prev
                </a>
            <?php endif; ?>
            <span class="text-micro c-subtle font-mono">Hal <?= $current_page; ?> / <?= $total_pages; ?></span>
            <?php if ($current_page < $total_pages): ?>
                <a href="<?= base_url('admin/post_reports?status=' . $current_status . '&page=' . ($current_page + 1)); ?>"
                   class="pagination-btn card">
                    Next <i data-lucide="chevron-right" class="w-3 h-3 inline"></i>
                </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<script>
function resolveReport(id, status) {
    const csrfName = document.querySelector('meta[name="csrf-token-name"]').content;
    const csrfHash = document.querySelector('meta[name="csrf-token-hash"]').content;

    const body = new FormData();
    body.append('id_report', id);
    body.append('status', status);
    body.append(csrfName, csrfHash);

    fetch('<?= base_url("admin/resolve_post_report"); ?>', { method: 'POST', body })
        .then(r => r.json())
        .then(data => {
            if (data.status === 'success') {
                const el = document.getElementById('report-' + id);
                if (el) {
                    el.style.transition = 'all 0.3s';
                    el.style.opacity = '0.3';
                    const badge = el.querySelector('.rounded-full');
                    if (badge) {
                        badge.className = badge.className.replace(/bg-amber-500\/10 text-amber-400 border-amber-500\/20/, status === 'reviewed' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-slate-500/10 text-slate-400 border border-slate-500/20');
                        badge.textContent = status;
                    }
                    const actions = el.querySelector('.flex.items-center.gap-2.flex-wrap');
                    if (actions) actions.remove();
                }
                showToast(data.message, 'green');
            }
        });
}

function deletePost(idPost, idReport) {
    if (!confirm('Yakin ingin menghapus post ini?')) return;

    const csrfName = document.querySelector('meta[name="csrf-token-name"]').content;
    const csrfHash = document.querySelector('meta[name="csrf-token-hash"]').content;

    const body = new FormData();
    body.append('id_post', idPost);
    body.append(csrfName, csrfHash);

    fetch('<?= base_url("admin/delete_reported_post"); ?>', { method: 'POST', body })
        .then(r => r.json())
        .then(data => {
            if (data.status === 'success') {
                showToast(data.message, 'green');
                resolveReport(idReport, 'reviewed');
            }
        });
}
</script>
