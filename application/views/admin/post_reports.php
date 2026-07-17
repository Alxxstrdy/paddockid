<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="font-syne text-lg uppercase tracking-tight text-white">Post Reports</h1>
            <p class="text-xs text-slate-500 mt-1"><?= number_format($total); ?> total laporan</p>
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
            $count = $key === 'all' ? $total : ($key === 'pending' ? $stats['pending_reports'] ?? 0 : 0);
        ?>
            <a href="<?= base_url('admin/post_reports?status=' . $key); ?>"
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
            <p class="text-xs text-slate-400">Tidak ada laporan ditemukan.</p>
        </div>
    <?php else: ?>
        <div class="space-y-3">
            <?php foreach ($reports as $r): ?>
                <div class="glass-card rounded-2xl border border-white/[0.04] overflow-hidden" id="report-<?= $r['id_report']; ?>">
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

                    <!-- Post Content Preview -->
                    <div class="px-4 py-3">
                        <div class="flex items-start gap-3">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1.5">
                                    <span class="text-[10px] text-slate-500">Post oleh</span>
                                    <a href="<?= base_url('user/' . ($r['post_owner_name'] ?? '')); ?>" class="text-[10px] text-red-400 hover:text-red-300 font-semibold"><?= htmlspecialchars($r['post_owner_name'] ?? 'Unknown', ENT_QUOTES, 'UTF-8'); ?></a>
                                    <?php if (!empty($r['post_deleted'])): ?>
                                        <span class="text-[9px] text-slate-600 bg-slate-800 px-1.5 py-0.5 rounded">deleted</span>
                                    <?php endif; ?>
                                </div>
                                <p class="text-xs text-slate-300 leading-relaxed line-clamp-3"><?= htmlspecialchars(mb_substr($r['post_content'] ?? '', 0, 200), ENT_QUOTES, 'UTF-8'); ?></p>
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
                        <button onclick="resolveReport(<?= $r['id_report']; ?>, 'reviewed')"
                                class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[10px] font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 hover:bg-emerald-500/20 transition-all">
                            <i data-lucide="check" class="w-3 h-3"></i> Reviewed
                        </button>
                        <button onclick="resolveReport(<?= $r['id_report']; ?>, 'dismissed')"
                                class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[10px] font-semibold bg-slate-500/10 text-slate-400 border border-slate-500/20 hover:bg-slate-500/20 transition-all">
                            <i data-lucide="x" class="w-3 h-3"></i> Dismiss
                        </button>
                        <?php if (!empty($r['id_post']) && empty($r['post_deleted'])): ?>
                        <button onclick="deletePost('<?= $r['id_post']; ?>', <?= $r['id_report']; ?>)"
                                class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[10px] font-semibold bg-red-500/10 text-red-400 border border-red-500/20 hover:bg-red-500/20 transition-all">
                            <i data-lucide="trash-2" class="w-3 h-3"></i> Hapus Post
                        </button>
                        <?php endif; ?>
                        <a href="<?= base_url('post/detail/' . ($r['id_post'] ?? '')); ?>" target="_blank"
                           class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[10px] font-semibold bg-white/[0.03] text-slate-400 border border-white/[0.06] hover:bg-white/[0.06] transition-all ml-auto">
                            <i data-lucide="external-link" class="w-3 h-3"></i> Lihat Post
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
                <a href="<?= base_url('admin/post_reports?status=' . $current_status . '&page=' . ($current_page - 1)); ?>"
                   class="px-3 py-1.5 rounded-lg text-[10px] font-semibold glass-card border border-white/[0.06] text-slate-400 hover:text-white transition-all">
                    <i data-lucide="chevron-left" class="w-3 h-3 inline"></i> Prev
                </a>
            <?php endif; ?>
            <span class="text-[10px] text-slate-500 font-mono">Hal <?= $current_page; ?> / <?= $total_pages; ?></span>
            <?php if ($current_page < $total_pages): ?>
                <a href="<?= base_url('admin/post_reports?status=' . $current_status . '&page=' . ($current_page + 1)); ?>"
                   class="px-3 py-1.5 rounded-lg text-[10px] font-semibold glass-card border border-white/[0.06] text-slate-400 hover:text-white transition-all">
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
