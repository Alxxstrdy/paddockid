<div class="space-y-6">
    <!-- Header -->
    <div class="pb-4" style="border-bottom:1px solid var(--border-subtle);">
        <h1 class="text-heading text-lg text-transform-uppercase c-white">Dashboard</h1>
        <p class="text-xs c-subtle" style="margin-top:4px;">Overview aktivitas PaddockID</p>

        <!-- Platform metrics -->
        <div class="admin-metrics" style="margin-top:16px;">
            <div class="admin-metrics__item">
                <span class="admin-metrics__value"><?= number_format($stats['total_users']); ?></span>
                <span class="admin-metrics__label">Pengguna</span>
            </div>
            <div class="admin-metrics__item">
                <span class="admin-metrics__value"><?= number_format($stats['total_posts']); ?></span>
                <span class="admin-metrics__label">Postingan</span>
            </div>
            <div class="admin-metrics__item">
                <span class="admin-metrics__value"><?= number_format($stats['total_comments']); ?></span>
                <span class="admin-metrics__label">Komentar</span>
            </div>
            <?php if ($stats['new_users_7d'] > 0): ?>
                <div class="admin-metrics__item">
                    <span class="admin-metrics__value c-success">+<?= $stats['new_users_7d']; ?></span>
                    <span class="admin-metrics__label">Pengguna baru · 7 hari</span>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Moderation queue -->
    <div class="card overflow-hidden">
        <div class="px-5 py-3 border-b" style="border-color:var(--border-subtle);">
            <h3 class="text-micro font-semibold">Antrian Moderasi</h3>
        </div>
        <div class="admin-summary">
            <a href="<?= base_url('admin/post_reports'); ?>" class="admin-summary__cell">
                <p class="admin-summary__value"><?= $stats['post_reports_count']; ?></p>
                <p class="admin-summary__label">
                    <i data-lucide="flag" class="w-3-5 h-3-5"></i>
                    Post Reports
                </p>
                <?php if ($stats['pending_reports'] > 0): ?>
                    <p class="admin-summary__delta--warn admin-summary__delta"><?= $stats['pending_reports']; ?> menunggu review</p>
                <?php endif; ?>
            </a>
            <a href="<?= base_url('admin/user_reports'); ?>" class="admin-summary__cell">
                <p class="admin-summary__value"><?= $stats['user_reports_count']; ?></p>
                <p class="admin-summary__label">
                    <i data-lucide="shield" class="w-3-5 h-3-5"></i>
                    User Reports
                </p>
            </a>
            <a href="<?= base_url('admin/errors'); ?>" class="admin-summary__cell">
                <p class="admin-summary__value"><?= count($log_files ?? []); ?></p>
                <p class="admin-summary__label">
                    <i data-lucide="alert-triangle" class="w-3-5 h-3-5"></i>
                    Log Files
                </p>
            </a>
            <a href="<?= base_url('admin/login_attempts'); ?>" class="admin-summary__cell">
                <p class="admin-summary__value"><?= number_format($stats['login_attempts_count']); ?></p>
                <p class="admin-summary__label">
                    <i data-lucide="log-in" class="w-3-5 h-3-5"></i>
                    Login Attempts
                </p>
                <?php if ($stats['failed_logins_24h'] > 5): ?>
                    <p class="admin-summary__delta--info admin-summary__delta"><?= $stats['failed_logins_24h']; ?> gagal dalam 24 jam</p>
                <?php endif; ?>
            </a>
        </div>
    </div>

    <!-- Recent Activity -->
    <?php if (!empty($recent_activity)): ?>
    <div class="card overflow-hidden">
        <div class="px-5 py-4 border-b" style="border-color:var(--border-subtle);">
            <h3 class="text-caption font-bold text-transform-uppercase inline-letter-spacing-01em c-white">Aktivitas Terbaru</h3>
        </div>
        <div>
            <?php foreach ($recent_activity as $act): ?>
                <div class="px-5 py-3 flex-row items-center justify-between gap-3 transition-colors" style="border-bottom:1px solid var(--border-subtle);">
                    <div class="flex-row items-center gap-3 min-w-0">
                        <?php if ($act['type'] === 'post_report'): ?>
                            <i data-lucide="flag" class="w-3-5 h-3-5 c-primary flex-shrink-0"></i>
                        <?php else: ?>
                            <i data-lucide="shield" class="w-3-5 h-3-5 c-orange flex-shrink-0"></i>
                        <?php endif; ?>
                        <div class="min-w-0">
                            <p class="text-xs c-white truncate">
                                <span class="font-semibold"><?= htmlspecialchars($act['reporter'], ENT_QUOTES, 'UTF-8'); ?></span>
                                <span class="c-subtle">melaporkan <?= $act['type'] === 'post_report' ? 'post' : 'user'; ?></span>
                                <span class="c-muted font-mono">#<?= $act['target_id']; ?></span>
                            </p>
                            <p class="text-micro c-subtle truncate" style="margin-top:2px;"><?= htmlspecialchars($act['reason'], ENT_QUOTES, 'UTF-8'); ?></p>
                        </div>
                    </div>
                    <div class="flex-row items-center gap-2 flex-shrink-0">
                        <span class="px-2 py-05 rounded-full font-semibold"
                            style="font-size:9px;<?= $act['status'] === 'pending' ? 'background:var(--color-warning-bg);color:var(--color-warning);border:1px solid var(--color-warning-border)' :
                                ($act['status'] === 'reviewed' ? 'background:var(--color-success-bg);color:var(--color-success);border:1px solid var(--color-success-border)' :
                                'background:rgba(100,116,139,0.1);color:var(--color-info);border:1px solid rgba(100,116,139,0.2)') ?>">
                            <?= $act['status']; ?>
                        </span>
                        <span class="text-micro c-faint font-mono whitespace-nowrap"><?= date('d M H:i', strtotime($act['created_at'])); ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>