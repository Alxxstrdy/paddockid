<div class="space-y-6">
    <!-- Header -->
    <div class="flex-row items-center justify-between">
        <div>
            <h1 class="text-heading text-lg text-transform-uppercase c-white">Login Attempts</h1>
            <p class="text-xs c-subtle" style="margin-top:4px;"><?= number_format($total); ?> total percobaan login</p>
        </div>
    </div>

    <!-- Filter Form -->
    <form method="GET" action="<?= base_url('admin/login_attempts'); ?>" class="card p-4 rounded-2xl border" style="border-color:var(--border-subtle);">
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-3">
            <div>
                <label class="text-micro c-subtle text-transform-uppercase font-semibold block mb-1">Status</label>
                <select name="success" class="w-full select--sm">
                    <option value="">Semua</option>
                    <option value="1" <?= isset($filter['success']) && $filter['success'] == 1 ? 'selected' : ''; ?>>Berhasil</option>
                    <option value="0" <?= isset($filter['success']) && $filter['success'] == 0 ? 'selected' : ''; ?>>Gagal</option>
                </select>
            </div>
            <div>
                <label class="text-micro c-subtle text-transform-uppercase font-semibold block mb-1">Username/Email</label>
                <input type="text" name="identity" value="<?= htmlspecialchars($filter['identity'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                       placeholder="Cari identity..." class="w-full input--sm">
            </div>
            <div>
                <label class="text-micro c-subtle text-transform-uppercase font-semibold block mb-1">IP Address</label>
                <input type="text" name="ip_address" value="<?= htmlspecialchars($filter['ip_address'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                       placeholder="IP address..." class="w-full input--sm">
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

    <!-- Table -->
    <div class="card rounded-2xl border overflow-hidden" style="border-color:var(--border-subtle);">
        <div class="overflow-x-auto">
            <table class="table w-full text-left">
                <thead>
                    <tr class="border-b" style="border-color:var(--border-default);">
                        <th class="px-4 py-3 text-micro text-heading text-transform-uppercase inline-letter-spacing-006em c-subtle">Waktu</th>
                        <th class="px-4 py-3 text-micro text-heading text-transform-uppercase inline-letter-spacing-006em c-subtle">Identity</th>
                        <th class="px-4 py-3 text-micro text-heading text-transform-uppercase inline-letter-spacing-006em c-subtle">IP Address</th>
                        <th class="px-4 py-3 text-micro text-heading text-transform-uppercase inline-letter-spacing-006em c-subtle">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($attempts)): ?>
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-xs c-subtle">Tidak ada data login attempts.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($attempts as $a): ?>
                            <tr class="transition-colors" style="border-bottom:1px solid var(--border-subtle);" onmouseover="this.style.background='rgba(255,255,255,0.02)'" onmouseout="this.style.background=''">
                                <td class="px-4 py-2-5">
                                    <span class="text-micro c-muted font-mono whitespace-nowrap"><?= date('d M Y H:i:s', strtotime($a['attempted_at'])); ?></span>
                                </td>
                                <td class="px-4 py-2-5">
                                    <span class="text-xs font-medium" style="color:var(--text-secondary);"><?= htmlspecialchars($a['identity'], ENT_QUOTES, 'UTF-8'); ?></span>
                                </td>
                                <td class="px-4 py-2-5">
                                    <span class="text-micro c-subtle font-mono"><?= htmlspecialchars($a['ip_address'], ENT_QUOTES, 'UTF-8'); ?></span>
                                </td>
                                <td class="px-4 py-2-5">
                                    <?php if ($a['success']): ?>
                                        <span class="badge-success">Berhasil</span>
                                    <?php else: ?>
                                        <span class="badge-danger">Gagal</span>
                                    <?php endif; ?>
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
            <a href="<?= base_url('admin/login_attempts?' . http_build_query(array_merge($filter, ['page' => $current_page - 1]))); ?>"
               class="pagination-btn card">
                <i data-lucide="chevron-left" class="w-3 h-3 inline"></i> Prev
            </a>
        <?php endif; ?>
        <span class="text-micro c-subtle font-mono">Hal <?= $current_page; ?> / <?= $total_pages; ?></span>
        <?php if ($current_page < $total_pages): ?>
            <a href="<?= base_url('admin/login_attempts?' . http_build_query(array_merge($filter, ['page' => $current_page + 1]))); ?>"
               class="pagination-btn card">
                Next <i data-lucide="chevron-right" class="w-3 h-3 inline"></i>
            </a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>
