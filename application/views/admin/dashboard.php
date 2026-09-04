<div class="space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-heading text-lg text-transform-uppercase c-white">Dashboard</h1>
        <p class="text-xs c-subtle" style="margin-top:4px;">Overview aktivitas PaddockID</p>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <a href="<?= base_url('admin/post_reports'); ?>" class="card p-4 rounded-2xl border transition" style="border-color:var(--border-subtle);" onmouseover="this.style.borderColor='var(--color-primary-border)'" onmouseout="this.style.borderColor='var(--border-subtle)'">
            <div class="flex-row items-center justify-between mb-3">
                <div class="w-8 h-8 rounded-xl flex-row items-center justify-center" style="background:var(--color-primary-bg);">
                    <i data-lucide="flag" class="w-4 h-4 c-primary"></i>
                </div>
                <?php if ($stats['pending_reports'] > 0): ?>
                    <span style="background:var(--color-primary);color:#fff;font-size:9px;" class="font-bold px-2 py-05 rounded-full animate-pulse"><?= $stats['pending_reports']; ?></span>
                <?php endif; ?>
            </div>
            <p class="text-heading text-xl font-bold c-white"><?= $stats['post_reports_count']; ?></p>
            <p class="text-micro c-subtle" style="margin-top:2px;">Post Reports</p>
        </a>

        <a href="<?= base_url('admin/user_reports'); ?>" class="card p-4 rounded-2xl border transition" style="border-color:var(--border-subtle);" onmouseover="this.style.borderColor='var(--color-orange-border)'" onmouseout="this.style.borderColor='var(--border-subtle)'">
            <div class="flex-row items-center justify-between mb-3">
                <div class="w-8 h-8 rounded-xl flex-row items-center justify-center" style="background:rgba(255,159,67,0.1);">
                    <i data-lucide="shield" class="w-4 h-4 c-orange"></i>
                </div>
            </div>
            <p class="text-heading text-xl font-bold c-white"><?= $stats['user_reports_count']; ?></p>
            <p class="text-micro c-subtle" style="margin-top:2px;">User Reports</p>
        </a>

        <a href="<?= base_url('admin/errors'); ?>" class="card p-4 rounded-2xl border transition" style="border-color:var(--border-subtle);" onmouseover="this.style.borderColor='var(--color-warning-border)'" onmouseout="this.style.borderColor='var(--border-subtle)'">
            <div class="flex-row items-center justify-between mb-3">
                <div class="w-8 h-8 rounded-xl flex-row items-center justify-center" style="background:var(--color-warning-bg);">
                    <i data-lucide="alert-triangle" class="w-4 h-4 c-warning"></i>
                </div>
            </div>
            <p class="text-heading text-xl font-bold c-white"><?= count($log_files ?? []); ?></p>
            <p class="text-micro c-subtle" style="margin-top:2px;">Log Files</p>
        </a>

        <a href="<?= base_url('admin/login_attempts'); ?>" class="card p-4 rounded-2xl border transition" style="border-color:var(--border-subtle);" onmouseover="this.style.borderColor='var(--color-info-border)'" onmouseout="this.style.borderColor='var(--border-subtle)'">
            <div class="flex-row items-center justify-between mb-3">
                <div class="w-8 h-8 rounded-xl flex-row items-center justify-center" style="background:rgba(96,165,250,0.1);">
                    <i data-lucide="log-in" class="w-4 h-4 c-info"></i>
                </div>
                <?php if ($stats['failed_logins_24h'] > 5): ?>
                    <span style="background:var(--color-info);color:#fff;font-size:9px;" class="font-bold px-2 py-05 rounded-full"><?= $stats['failed_logins_24h']; ?> 24j</span>
                <?php endif; ?>
            </div>
            <p class="text-heading text-xl font-bold c-white"><?= number_format($stats['login_attempts_count']); ?></p>
            <p class="text-micro c-subtle" style="margin-top:2px;">Login Attempts</p>
        </a>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-3 gap-3">
        <div class="card p-4 rounded-2xl border" style="border-color:var(--border-subtle);">
            <div class="flex-row items-center gap-2 mb-2">
                <i data-lucide="users" class="w-3-5 h-3-5 c-muted"></i>
                <span class="text-micro c-subtle text-transform-uppercase font-semibold">Total Users</span>
            </div>
            <p class="text-heading text-lg font-bold c-white"><?= number_format($stats['total_users']); ?></p>
            <p class="text-micro c-success" style="margin-top:2px;">+<?= $stats['new_users_7d']; ?> minggu ini</p>
        </div>
        <div class="card p-4 rounded-2xl border" style="border-color:var(--border-subtle);">
            <div class="flex-row items-center gap-2 mb-2">
                <i data-lucide="file-text" class="w-3-5 h-3-5 c-muted"></i>
                <span class="text-micro c-subtle text-transform-uppercase font-semibold">Total Posts</span>
            </div>
            <p class="text-heading text-lg font-bold c-white"><?= number_format($stats['total_posts']); ?></p>
        </div>
        <div class="card p-4 rounded-2xl border" style="border-color:var(--border-subtle);">
            <div class="flex-row items-center gap-2 mb-2">
                <i data-lucide="message-circle" class="w-3-5 h-3-5 c-muted"></i>
                <span class="text-micro c-subtle text-transform-uppercase font-semibold">Total Comments</span>
            </div>
            <p class="text-heading text-lg font-bold c-white"><?= number_format($stats['total_comments']); ?></p>
        </div>
    </div>

    <!-- Recent Activity -->
    <?php if (!empty($recent_activity)): ?>
    <div class="card rounded-2xl border overflow-hidden" style="border-color:var(--border-subtle);">
        <div class="px-5 py-4 border-b" style="border-color:var(--border-subtle);">
            <h3 class="text-caption font-bold text-transform-uppercase inline-letter-spacing-01em c-white">Aktivitas Terbaru</h3>
        </div>
        <div>
            <?php foreach ($recent_activity as $act): ?>
                <div class="px-5 py-3 flex-row items-center justify-between gap-3 transition-colors" style="border-bottom:1px solid var(--border-subtle);" onmouseover="this.style.background='rgba(255,255,255,0.02)'" onmouseout="this.style.background=''">
                    <div class="flex-row items-center gap-3 min-w-0">
                        <?php if ($act['type'] === 'post_report'): ?>
                            <div class="w-7 h-7 rounded-lg flex-row items-center justify-center flex-shrink-0" style="background:var(--color-primary-bg);">
                                <i data-lucide="flag" class="w-3-5 h-3-5 c-primary"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs c-white truncate">
                                    <span class="font-semibold"><?= htmlspecialchars($act['reporter'], ENT_QUOTES, 'UTF-8'); ?></span>
                                    <span class="c-subtle">melaporkan post</span>
                                    <span class="c-muted font-mono">#<?= $act['target_id']; ?></span>
                                </p>
                                <p class="text-micro c-subtle truncate" style="margin-top:2px;"><?= htmlspecialchars($act['reason'], ENT_QUOTES, 'UTF-8'); ?></p>
                            </div>
                        <?php else: ?>
                            <div class="w-7 h-7 rounded-lg flex-row items-center justify-center flex-shrink-0" style="background:rgba(255,159,67,0.1);">
                                <i data-lucide="shield" class="w-3-5 h-3-5 c-orange"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs c-white truncate">
                                    <span class="font-semibold"><?= htmlspecialchars($act['reporter'], ENT_QUOTES, 'UTF-8'); ?></span>
                                    <span class="c-subtle">melaporkan user</span>
                                    <span class="c-muted font-mono">#<?= $act['target_id']; ?></span>
                                </p>
                                <p class="text-micro c-subtle truncate" style="margin-top:2px;"><?= htmlspecialchars($act['reason'], ENT_QUOTES, 'UTF-8'); ?></p>
                            </div>
                        <?php endif; ?>
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
