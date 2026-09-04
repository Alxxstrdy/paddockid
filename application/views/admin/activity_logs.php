<div class="space-y-6" data-freeze-refresh>
    <!-- Header -->
    <div class="flex-row items-center justify-between">
        <div>
            <h1 class="text-heading text-lg text-transform-uppercase c-white">User Activity Log</h1>
            <p class="text-xs c-subtle" style="margin-top:4px;"><?= number_format($total); ?> aktivitas tercatat</p>
        </div>
        <button onclick="openClearModal()" class="flex-row items-center gap-1-5 px-3 py-1-5 rounded-lg font-semibold transition" style="font-size:10px;background:var(--color-primary-bg);color:var(--color-primary);border:1px solid var(--color-primary-border);" onmouseover="this.style.background='rgba(239,68,68,0.2)'" onmouseout="this.style.background='var(--color-primary-bg)'">
            <i data-lucide="trash-2" class="w-3 h-3"></i> Bersihkan Log
        </button>
    </div>

    <!-- Filter Form -->
    <form method="GET" action="<?= base_url('admin/activity_logs'); ?>" class="card p-4 rounded-2xl border" style="border-color:var(--border-subtle);">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
            <div>
                <label class="text-micro c-subtle text-transform-uppercase font-semibold block mb-1">Pencarian</label>
                <input type="text" name="search" value="<?= htmlspecialchars($filter['search'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                       placeholder="Username atau deskripsi..." class="w-full input--sm">
            </div>
            <div>
                <label class="text-micro c-subtle text-transform-uppercase font-semibold block mb-1">Aksi</label>
                <select name="action" class="w-full select--sm">
                    <option value="">Semua</option>
                    <?php foreach ($action_options as $opt): ?>
                        <option value="<?= htmlspecialchars($opt, ENT_QUOTES, 'UTF-8'); ?>" <?= ($filter['action'] ?? '') === $opt ? 'selected' : ''; ?>><?= htmlspecialchars($opt, ENT_QUOTES, 'UTF-8'); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="text-micro c-subtle text-transform-uppercase font-semibold block mb-1">Dari Tanggal</label>
                <input type="date" name="date_from" value="<?= htmlspecialchars($filter['date_from'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                       class="w-full input--sm">
            </div>
            <div class="flex-row items-end gap-2">
                <div class="flex-1">
                    <label class="text-micro c-subtle text-transform-uppercase font-semibold block mb-1">Sampai</label>
                    <input type="date" name="date_to" value="<?= htmlspecialchars($filter['date_to'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                           class="w-full input--sm">
                </div>
                <button type="submit" class="btn btn-primary btn-sm flex-shrink-0" style="font-size:10px;">
                    Filter
                </button>
            </div>
        </div>
    </form>

    <!-- Activity Log Table -->
    <div class="card rounded-2xl border overflow-hidden" style="border-color:var(--border-subtle);">
        <div class="overflow-x-auto">
            <table class="table w-full text-left">
                <thead>
                    <tr class="border-b" style="border-color:var(--border-default);">
                        <th class="px-4 py-3 text-micro text-heading text-transform-uppercase inline-letter-spacing-006em c-subtle">Waktu</th>
                        <th class="px-4 py-3 text-micro text-heading text-transform-uppercase inline-letter-spacing-006em c-subtle">User</th>
                        <th class="px-4 py-3 text-micro text-heading text-transform-uppercase inline-letter-spacing-006em c-subtle">Aksi</th>
                        <th class="px-4 py-3 text-micro text-heading text-transform-uppercase inline-letter-spacing-006em c-subtle">Detail</th>
                        <th class="px-4 py-3 text-micro text-heading text-transform-uppercase inline-letter-spacing-006em c-subtle">IP</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($logs)): ?>
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-xs c-subtle">Tidak ada aktivitas.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($logs as $log): ?>
                            <tr class="transition-colors" style="border-bottom:1px solid var(--border-subtle);" onmouseover="this.style.background='rgba(255,255,255,0.02)'" onmouseout="this.style.background=''">
                                <td class="px-4 py-2-5">
                                    <span class="text-micro c-muted font-mono whitespace-nowrap"><?= date('d M Y H:i:s', strtotime($log['created_at'])); ?></span>
                                </td>
                                <td class="px-4 py-2-5">
                                    <span class="text-micro font-semibold" style="color:var(--text-secondary);"><?= htmlspecialchars($log['username'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></span>
                                </td>
                                <td class="px-4 py-2-5">
                                    <?php
                                    $action_colors = [
                                        'login'              => 'background:var(--color-success-bg);color:var(--color-success);border-color:var(--color-success-border)',
                                        'login_success'      => 'background:var(--color-success-bg);color:var(--color-success);border-color:var(--color-success-border)',
                                        'login_failed'       => 'background:var(--color-primary-bg);color:var(--color-primary);border-color:var(--color-primary-border)',
                                        'logout'             => 'background:rgba(100,116,139,0.1);color:var(--color-info);border-color:rgba(100,116,139,0.2)',
                                        'register'           => 'background:rgba(96,165,250,0.1);color:var(--color-info);border-color:rgba(96,165,250,0.2)',
                                        'create_post'        => 'background:rgba(167,139,250,0.1);color:var(--color-purple);border-color:rgba(167,139,250,0.2)',
                                        'edit_post'          => 'background:rgba(96,165,250,0.1);color:var(--color-info);border-color:rgba(96,165,250,0.2)',
                                        'delete_post'        => 'background:var(--color-primary-bg);color:var(--color-primary);border-color:var(--color-primary-border)',
                                        'add_comment'        => 'background:rgba(34,211,238,0.1);color:var(--color-info);border-color:rgba(34,211,238,0.2)',
                                        'edit_comment'       => 'background:rgba(96,165,250,0.1);color:var(--color-info);border-color:rgba(96,165,250,0.2)',
                                        'delete_comment'     => 'background:var(--color-primary-bg);color:var(--color-primary);border-color:var(--color-primary-border)',
                                        'like_post'          => 'background:rgba(244,114,182,0.1);color:var(--color-info);border-color:rgba(244,114,182,0.2)',
                                        'like_comment'       => 'background:rgba(244,114,182,0.1);color:var(--color-info);border-color:rgba(244,114,182,0.2)',
                                        'unlike_comment'     => 'background:rgba(100,116,139,0.1);color:var(--color-info);border-color:rgba(100,116,139,0.2)',
                                        'follow'             => 'background:rgba(129,140,248,0.1);color:var(--color-info);border-color:rgba(129,140,248,0.2)',
                                        'unfollow'           => 'background:rgba(100,116,139,0.1);color:var(--color-info);border-color:rgba(100,116,139,0.2)',
                                        'block_user'         => 'background:var(--color-primary-bg);color:var(--color-primary);border-color:var(--color-primary-border)',
                                        'unblock_user'       => 'background:var(--color-success-bg);color:var(--color-success);border-color:var(--color-success-border)',
                                        'change_password'    => 'background:var(--color-warning-bg);color:var(--color-warning);border-color:var(--color-warning-border)',
                                        'change_email'       => 'background:var(--color-warning-bg);color:var(--color-warning);border-color:var(--color-warning-border)',
                                        'set_password'       => 'background:var(--color-warning-bg);color:var(--color-warning);border-color:var(--color-warning-border)',
                                        'unlink_google'      => 'background:var(--color-warning-bg);color:var(--color-warning);border-color:var(--color-warning-border)',
                                    ];
                                    $color_style = $action_colors[$log['action']] ?? 'background:rgba(100,116,139,0.1);color:var(--color-info);border-color:rgba(100,116,139,0.2)';
                                    ?>
                                    <span class="px-2 py-05 rounded-full font-semibold border" style="font-size:9px;<?= $color_style; ?>">
                                        <?= htmlspecialchars($log['action'], ENT_QUOTES, 'UTF-8'); ?>
                                    </span>
                                </td>
                                <td class="px-4 py-2-5">
                                    <span class="text-xs" style="color:var(--text-secondary);"><?= htmlspecialchars($log['details'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></span>
                                    <?php if (!empty($log['target_type']) && !empty($log['target_id'])): ?>
                                        <span class="text-micro c-faint ml-1 font-mono">[<?= htmlspecialchars($log['target_type'], ENT_QUOTES, 'UTF-8'); ?>:<?= htmlspecialchars($log['target_id'], ENT_QUOTES, 'UTF-8'); ?>]</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-2-5">
                                    <span class="text-micro c-subtle font-mono"><?= htmlspecialchars($log['ip_address'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></span>
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
    <div class="flex-row items-center justify-center gap-2">
        <?php if ($current_page > 1): ?>
            <a href="<?= base_url('admin/activity_logs?' . http_build_query(array_merge($filter, ['page' => $current_page - 1]))); ?>"
               class="pagination-btn card">
                <i data-lucide="chevron-left" class="w-3 h-3 inline"></i> Prev
            </a>
        <?php endif; ?>
        <span class="text-micro c-subtle font-mono">Hal <?= $current_page; ?> / <?= $total_pages; ?></span>
        <?php if ($current_page < $total_pages): ?>
            <a href="<?= base_url('admin/activity_logs?' . http_build_query(array_merge($filter, ['page' => $current_page + 1]))); ?>"
               class="pagination-btn card">
                Next <i data-lucide="chevron-right" class="w-3 h-3 inline"></i>
            </a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<!-- Clear Log Modal -->
<div id="clear-log-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 backdrop-blur-sm" style="background:var(--bg-overlay);" onclick="closeClearModal()"></div>
    <div class="absolute inset-0 flex-row items-center justify-center p-4">
        <div class="rounded-2xl border w-full max-w-sm p-6 space-y-4" style="background:var(--bg-surface);border-color:var(--border-default);">
            <div class="flex-row items-center gap-3">
                <div class="w-10 h-10 rounded-full flex-row items-center justify-center flex-shrink-0" style="background:var(--color-primary-bg);">
                    <i data-lucide="alert-triangle" class="w-5 h-5 c-primary"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold c-white">Bersihkan Activity Log</h3>
                    <p class="text-micro c-subtle" style="margin-top:2px;">Aksi ini akan menghapus log aktivitas user & login attempts. Tidak dapat dibatalkan.</p>
                </div>
            </div>
            <div class="space-y-3">
                <label class="flex-row items-center gap-3 p-3 rounded-xl border cursor-pointer transition" style="border-color:var(--border-default);" onmouseover="this.style.background='rgba(255,255,255,0.02)'" onmouseout="this.style.background=''">
                    <input type="radio" name="clear_type" value="all" checked class="accent-red-500" onchange="toggleClearDate(false)">
                    <div>
                        <span class="text-xs font-semibold c-white">Semua Log</span>
                        <p class="text-micro c-subtle">Hapus seluruh aktivitas & login attempts</p>
                    </div>
                </label>
                <label class="flex-row items-center gap-3 p-3 rounded-xl border cursor-pointer transition" style="border-color:var(--border-default);" onmouseover="this.style.background='rgba(255,255,255,0.02)'" onmouseout="this.style.background=''">
                    <input type="radio" name="clear_type" value="before" class="accent-red-500" onchange="toggleClearDate(true)">
                    <div>
                        <span class="text-xs font-semibold c-white">Sebelum Tanggal</span>
                        <p class="text-micro c-subtle">Hapus aktivitas sebelum tanggal tertentu</p>
                    </div>
                </label>
                <div id="clear-date-wrapper" class="hidden">
                    <input type="date" id="clear-before-date" class="w-full input--sm">
                </div>
            </div>
            <div class="flex-row gap-2">
                <button onclick="closeClearModal()" class="flex-1 px-4 py-2 rounded-lg font-semibold transition" style="font-size:10px;border:1px solid var(--border-default);color:var(--color-info);" onmouseover="this.style.background='rgba(255,255,255,0.04)'" onmouseout="this.style.background=''">
                    Batal
                </button>
                <button onclick="executeClear()" class="flex-1 px-4 py-2 rounded-lg font-semibold transition" style="font-size:10px;background:rgba(239,68,68,0.2);color:var(--color-primary);border:1px solid rgba(239,68,68,0.3);" onmouseover="this.style.background='rgba(239,68,68,0.3)'" onmouseout="this.style.background='rgba(239,68,68,0.2)'">
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
