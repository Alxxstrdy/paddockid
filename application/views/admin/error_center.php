<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php
$filters = isset($filters) && is_array($filters) ? $filters : array();
$summary = isset($summary) && is_array($summary) ? $summary : array();
$incidents = isset($incidents) && is_array($incidents) ? $incidents : array();
$total = isset($total) ? (int) $total : 0;
$total_pages = isset($total_pages) ? max(1, (int) $total_pages) : 1;
$current_page = isset($current_page) ? max(1, (int) $current_page) : 1;
$table_available = !empty($table_available);
$base_list_url = base_url('admin/error_center');

function ec_qs($filters, $overrides = array()) {
    $merged = array_merge(array(
        'search' => $filters['search'] ?? '',
        'status' => $filters['status'] ?? '',
        'error_type' => $filters['error_type'] ?? '',
        'severity' => $filters['severity'] ?? '',
        'date_from' => $filters['date_from'] ?? '',
        'date_to' => $filters['date_to'] ?? '',
    ), $overrides);
    $merged = array_filter($merged, function ($v) { return $v !== null && $v !== ''; });
    return $merged ? ('?' . http_build_query($merged)) : '';
}
?>
<div class="space-y-6 ec-wrap">
    <div class="flex-row justify-between" style="gap:12px;flex-wrap:wrap;align-items:flex-start;">
        <div>
            <h1 class="text-page-title">Developer Error Center</h1>
            <p class="text-caption mt-1">Insiden error terpusat (PDK-*) untuk investigasi, status penanganan, dan catatan resolusi.</p>
        </div>
        <div class="flex-row gap-2" style="flex-wrap:wrap;">
            <a href="<?= base_url('admin/errors'); ?>" class="btn btn-ghost btn-sm">File Logs</a>
            <a href="<?= base_url('admin/error_codes'); ?>" class="btn btn-ghost btn-sm">Error Codes</a>
        </div>
    </div>

    <?php if (!$table_available): ?>
        <div class="card" style="padding:16px;border:1px solid var(--color-warning-border, rgba(245,158,11,0.25));background:var(--color-warning-bg, rgba(245,158,11,0.08));">
            <p class="font-semibold c-white" style="font-size:13px;">Tabel <code>error_logs</code> belum tersedia.</p>
            <p class="text-caption mt-1">Jalankan migrasi aman: <code>database/migration_create_error_logs.sql</code>. File log fallback tetap tersedia di menu Error Logs.</p>
        </div>
    <?php endif; ?>

    <div class="page-grid ec-stats-grid">
        <div class="card" style="padding:16px;grid-column:span 3;">
            <p class="text-micro">Hari Ini</p>
            <p class="admin-summary__value mt-1"><?= (int) ($summary['today'] ?? 0); ?></p>
        </div>
        <div class="card" style="padding:16px;grid-column:span 3;">
            <p class="text-micro">Unresolved</p>
            <p class="admin-summary__value mt-1" style="color:var(--color-warning, #f59e0b);"><?= (int) ($summary['unresolved'] ?? 0); ?></p>
            <p class="text-caption mt-1">Open + Investigating</p>
        </div>
        <div class="card" style="padding:16px;grid-column:span 3;">
            <p class="text-micro">Resolved</p>
            <p class="admin-summary__value mt-1" style="color:var(--color-success, #22c55e);"><?= (int) ($summary['resolved'] ?? 0); ?></p>
        </div>
        <div class="card" style="padding:16px;grid-column:span 3;">
            <p class="text-micro">Ignored</p>
            <p class="admin-summary__value mt-1"><?= (int) ($summary['ignored'] ?? 0); ?></p>
        </div>
    </div>

    <div class="page-grid" style="gap:16px;">
        <div style="grid-column:span 8;min-width:0;">
            <div class="card" style="padding:16px;">
                <form method="get" action="<?= htmlspecialchars($base_list_url, ENT_QUOTES, 'UTF-8'); ?>" class="ec-filters">
                    <input type="text" name="search" class="input" placeholder="Cari kode, pesan, URI, tipe..." value="<?= htmlspecialchars((string) ($filters['search'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                    <select name="status" class="input">
                        <?php $st = (string) ($filters['status'] ?? ''); ?>
                        <option value="">Semua Status</option>
                        <option value="open" <?= $st === 'open' ? 'selected' : ''; ?>>Open</option>
                        <option value="investigating" <?= $st === 'investigating' ? 'selected' : ''; ?>>Investigating</option>
                        <option value="resolved" <?= $st === 'resolved' ? 'selected' : ''; ?>>Resolved</option>
                        <option value="ignored" <?= $st === 'ignored' ? 'selected' : ''; ?>>Ignored</option>
                    </select>
                    <input type="text" name="error_type" class="input" placeholder="Kategori/tipe" value="<?= htmlspecialchars((string) ($filters['error_type'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                    <select name="severity" class="input">
                        <?php $sv = (string) ($filters['severity'] ?? ''); ?>
                        <option value="">Semua Severity</option>
                        <option value="warning" <?= $sv === 'warning' ? 'selected' : ''; ?>>Warning</option>
                        <option value="error" <?= $sv === 'error' ? 'selected' : ''; ?>>Error</option>
                        <option value="critical" <?= $sv === 'critical' ? 'selected' : ''; ?>>Critical</option>
                    </select>
                    <input type="date" name="date_from" class="input" value="<?= htmlspecialchars((string) ($filters['date_from'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="date" name="date_to" class="input" value="<?= htmlspecialchars((string) ($filters['date_to'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                    <div class="flex-row gap-2" style="grid-column:1/-1;flex-wrap:wrap;">
                        <button type="submit" class="btn btn-primary btn-sm">Terapkan Filter</button>
                        <a href="<?= htmlspecialchars($base_list_url, ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-ghost btn-sm">Reset</a>
                    </div>
                </form>
            </div>

            <div class="card overflow-hidden" style="margin-top:16px;">
                <div class="flex-row justify-between" style="padding:14px 16px;border-bottom:1px solid var(--border-subtle);">
                    <p class="text-label">Insiden (<?= $total; ?>)</p>
                    <p class="text-caption">Halaman <?= $current_page; ?> / <?= $total_pages; ?></p>
                </div>
                <?php if (empty($incidents)): ?>
                    <div class="empty-state" style="padding:28px 16px;">
                        <i data-lucide="shield-check" class="empty-state__icon"></i>
                        <p class="empty-state__text">Tidak ada insiden ditemukan.</p>
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="table w-full text-left text-xs">
                            <thead>
                                <tr style="border-bottom:1px solid var(--border-subtle);">
                                    <th class="px-4 py-3">Kode</th>
                                    <th class="px-4 py-3">Waktu</th>
                                    <th class="px-4 py-3">Tipe</th>
                                    <th class="px-4 py-3">Severity</th>
                                    <th class="px-4 py-3">Status</th>
                                    <th class="px-4 py-3">Ringkasan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($incidents as $row): ?>
                                    <?php
                                        $status = (string) ($row['status'] ?? 'open');
                                        $badge_class = 'badge-default';
                                        if ($status === 'resolved') $badge_class = 'badge-success';
                                        elseif ($status === 'investigating') $badge_class = 'badge-warning';
                                        elseif ($status === 'ignored') $badge_class = 'badge-default';
                                        else $badge_class = 'badge-danger';
                                        $sev = (string) ($row['severity'] ?? 'error');
                                        $msg = (string) ($row['message'] ?? '');
                                        $msg_short = mb_strlen($msg) > 120 ? (mb_substr($msg, 0, 120) . '…') : $msg;
                                    ?>
                                    <tr style="border-bottom:1px solid rgba(255,255,255,0.03);">
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <a href="<?= base_url('admin/error_center/detail/' . rawurlencode((string) $row['error_code'])); ?>" class="font-mono font-semibold c-primary">
                                                <?= htmlspecialchars((string) $row['error_code'], ENT_QUOTES, 'UTF-8'); ?>
                                            </a>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap c-muted"><?= htmlspecialchars((string) ($row['created_at'] ?? '-'), ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td class="px-4 py-3 whitespace-nowrap"><span class="badge badge-default"><?= htmlspecialchars((string) ($row['error_type'] ?? 'unknown'), ENT_QUOTES, 'UTF-8'); ?></span></td>
                                        <td class="px-4 py-3 whitespace-nowrap c-muted"><?= htmlspecialchars($sev, ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td class="px-4 py-3 whitespace-nowrap"><span class="badge <?= $badge_class; ?>"><?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8'); ?></span></td>
                                        <td class="px-4 py-3 c-muted" style="min-width:260px;"><?= htmlspecialchars($msg_short, ENT_QUOTES, 'UTF-8'); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>

                <?php if ($total_pages > 1): ?>
                    <div class="flex-row justify-between" style="padding:12px 16px;border-top:1px solid var(--border-subtle);gap:8px;flex-wrap:wrap;">
                        <div class="text-caption">Menampilkan <?= count($incidents); ?> dari <?= $total; ?> insiden</div>
                        <div class="flex-row gap-2" style="flex-wrap:wrap;">
                            <?php $prev = max(1, $current_page - 1); $next = min($total_pages, $current_page + 1); ?>
                            <a class="btn btn-ghost btn-sm <?= $current_page <= 1 ? 'is-disabled' : ''; ?>" href="<?= $current_page <= 1 ? '#' : htmlspecialchars($base_list_url . ec_qs($filters, array('page' => $prev)), ENT_QUOTES, 'UTF-8'); ?>">Sebelumnya</a>
                            <a class="btn btn-ghost btn-sm <?= $current_page >= $total_pages ? 'is-disabled' : ''; ?>" href="<?= $current_page >= $total_pages ? '#' : htmlspecialchars($base_list_url . ec_qs($filters, array('page' => $next)), ENT_QUOTES, 'UTF-8'); ?>">Berikutnya</a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div style="grid-column:span 4;min-width:0;">
            <div class="card" style="padding:16px;">
                <p class="text-label mb-3">Kategori Terbanyak</p>
                <?php $by_type = isset($summary['by_type']) && is_array($summary['by_type']) ? $summary['by_type'] : array(); ?>
                <?php if (empty($by_type)): ?>
                    <p class="text-caption c-subtle">Belum ada data.</p>
                <?php else: ?>
                    <div class="space-y-2">
                        <?php foreach ($by_type as $t): ?>
                            <div class="flex-row justify-between" style="gap:8px;">
                                <span class="text-xs c-muted"><?= htmlspecialchars((string) ($t['error_type'] ?? 'unknown'), ENT_QUOTES, 'UTF-8'); ?></span>
                                <span class="badge badge-default"><?= (int) ($t['total'] ?? 0); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="card" style="padding:16px;margin-top:16px;">
                <p class="text-label mb-3">Insiden Terbaru</p>
                <?php $recent = isset($summary['recent']) && is_array($summary['recent']) ? $summary['recent'] : array(); ?>
                <?php if (empty($recent)): ?>
                    <p class="text-caption c-subtle">Belum ada insiden.</p>
                <?php else: ?>
                    <div class="space-y-2">
                        <?php foreach ($recent as $r): ?>
                            <?php $rstatus = (string) ($r['status'] ?? 'open'); ?>
                            <a href="<?= base_url('admin/error_center/detail/' . rawurlencode((string) $r['error_code'])); ?>" class="ec-recent-item">
                                <div class="flex-row justify-between" style="gap:8px;">
                                    <span class="font-mono text-xs c-primary"><?= htmlspecialchars((string) $r['error_code'], ENT_QUOTES, 'UTF-8'); ?></span>
                                    <span class="text-micro c-subtle"><?= htmlspecialchars((string) ($r['created_at'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></span>
                                </div>
                                <div class="flex-row gap-2 mt-1" style="flex-wrap:wrap;">
                                    <span class="badge badge-default"><?= htmlspecialchars((string) ($r['error_type'] ?? 'unknown'), ENT_QUOTES, 'UTF-8'); ?></span>
                                    <span class="badge <?= $rstatus === 'resolved' ? 'badge-success' : ($rstatus === 'investigating' ? 'badge-warning' : ($rstatus === 'ignored' ? 'badge-default' : 'badge-danger')); ?>"><?= htmlspecialchars($rstatus, ENT_QUOTES, 'UTF-8'); ?></span>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
