<div class="space-y-6">
    <!-- Header -->
    <div>
        <h1 class="font-syne text-lg uppercase tracking-tight text-white">Dashboard</h1>
        <p class="text-xs text-slate-500 mt-1">Overview aktivitas PaddockID</p>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <a href="<?= base_url('admin/post_reports'); ?>" class="glass-card p-4 rounded-2xl border border-white/[0.04] hover:border-red-500/20 transition-all group">
            <div class="flex items-center justify-between mb-3">
                <div class="w-8 h-8 rounded-xl bg-red-500/10 flex items-center justify-center">
                    <i data-lucide="flag" class="w-4 h-4 text-red-400"></i>
                </div>
                <?php if ($stats['pending_reports'] > 0): ?>
                    <span class="bg-red-500 text-white text-[9px] font-bold px-2 py-0.5 rounded-full animate-pulse"><?= $stats['pending_reports']; ?></span>
                <?php endif; ?>
            </div>
            <p class="font-syne text-xl font-bold text-white"><?= $stats['post_reports_count']; ?></p>
            <p class="text-[10px] text-slate-500 mt-0.5">Post Reports</p>
        </a>

        <a href="<?= base_url('admin/user_reports'); ?>" class="glass-card p-4 rounded-2xl border border-white/[0.04] hover:border-orange-500/20 transition-all group">
            <div class="flex items-center justify-between mb-3">
                <div class="w-8 h-8 rounded-xl bg-orange-500/10 flex items-center justify-center">
                    <i data-lucide="shield" class="w-4 h-4 text-orange-400"></i>
                </div>
            </div>
            <p class="font-syne text-xl font-bold text-white"><?= $stats['user_reports_count']; ?></p>
            <p class="text-[10px] text-slate-500 mt-0.5">User Reports</p>
        </a>

        <a href="<?= base_url('admin/errors'); ?>" class="glass-card p-4 rounded-2xl border border-white/[0.04] hover:border-amber-500/20 transition-all group">
            <div class="flex items-center justify-between mb-3">
                <div class="w-8 h-8 rounded-xl bg-amber-500/10 flex items-center justify-center">
                    <i data-lucide="alert-triangle" class="w-4 h-4 text-amber-400"></i>
                </div>
            </div>
            <p class="font-syne text-xl font-bold text-white"><?= count($log_files ?? []); ?></p>
            <p class="text-[10px] text-slate-500 mt-0.5">Log Files</p>
        </a>

        <a href="<?= base_url('admin/login_attempts'); ?>" class="glass-card p-4 rounded-2xl border border-white/[0.04] hover:border-blue-500/20 transition-all group">
            <div class="flex items-center justify-between mb-3">
                <div class="w-8 h-8 rounded-xl bg-blue-500/10 flex items-center justify-center">
                    <i data-lucide="log-in" class="w-4 h-4 text-blue-400"></i>
                </div>
                <?php if ($stats['failed_logins_24h'] > 5): ?>
                    <span class="bg-blue-500 text-white text-[9px] font-bold px-2 py-0.5 rounded-full"><?= $stats['failed_logins_24h']; ?> 24j</span>
                <?php endif; ?>
            </div>
            <p class="font-syne text-xl font-bold text-white"><?= number_format($stats['login_attempts_count']); ?></p>
            <p class="text-[10px] text-slate-500 mt-0.5">Login Attempts</p>
        </a>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-3 gap-3">
        <div class="glass-card p-4 rounded-2xl border border-white/[0.04]">
            <div class="flex items-center gap-2 mb-2">
                <i data-lucide="users" class="w-3.5 h-3.5 text-slate-400"></i>
                <span class="text-[10px] text-slate-500 uppercase font-semibold">Total Users</span>
            </div>
            <p class="font-syne text-lg font-bold text-white"><?= number_format($stats['total_users']); ?></p>
            <p class="text-[10px] text-emerald-400 mt-0.5">+<?= $stats['new_users_7d']; ?> minggu ini</p>
        </div>
        <div class="glass-card p-4 rounded-2xl border border-white/[0.04]">
            <div class="flex items-center gap-2 mb-2">
                <i data-lucide="file-text" class="w-3.5 h-3.5 text-slate-400"></i>
                <span class="text-[10px] text-slate-500 uppercase font-semibold">Total Posts</span>
            </div>
            <p class="font-syne text-lg font-bold text-white"><?= number_format($stats['total_posts']); ?></p>
        </div>
        <div class="glass-card p-4 rounded-2xl border border-white/[0.04]">
            <div class="flex items-center gap-2 mb-2">
                <i data-lucide="message-circle" class="w-3.5 h-3.5 text-slate-400"></i>
                <span class="text-[10px] text-slate-500 uppercase font-semibold">Total Comments</span>
            </div>
            <p class="font-syne text-lg font-bold text-white"><?= number_format($stats['total_comments']); ?></p>
        </div>
    </div>

    <!-- Recent Activity -->
    <?php if (!empty($recent_activity)): ?>
    <div class="glass-card rounded-2xl border border-white/[0.04] overflow-hidden">
        <div class="px-5 py-4 border-b border-white/[0.04]">
            <h3 class="font-syne text-xs font-bold uppercase tracking-widest text-white">Aktivitas Terbaru</h3>
        </div>
        <div class="divide-y divide-white/[0.04]">
            <?php foreach ($recent_activity as $act): ?>
                <div class="px-5 py-3 flex items-center justify-between gap-3 hover:bg-white/[0.02] transition-colors">
                    <div class="flex items-center gap-3 min-w-0">
                        <?php if ($act['type'] === 'post_report'): ?>
                            <div class="w-7 h-7 rounded-lg bg-red-500/10 flex items-center justify-center flex-shrink-0">
                                <i data-lucide="flag" class="w-3.5 h-3.5 text-red-400"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs text-white truncate">
                                    <span class="font-semibold"><?= htmlspecialchars($act['reporter'], ENT_QUOTES, 'UTF-8'); ?></span>
                                    <span class="text-slate-500">melaporkan post</span>
                                    <span class="text-slate-400 font-mono">#<?= $act['target_id']; ?></span>
                                </p>
                                <p class="text-[10px] text-slate-500 truncate mt-0.5"><?= htmlspecialchars($act['reason'], ENT_QUOTES, 'UTF-8'); ?></p>
                            </div>
                        <?php else: ?>
                            <div class="w-7 h-7 rounded-lg bg-orange-500/10 flex items-center justify-center flex-shrink-0">
                                <i data-lucide="shield" class="w-3.5 h-3.5 text-orange-400"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs text-white truncate">
                                    <span class="font-semibold"><?= htmlspecialchars($act['reporter'], ENT_QUOTES, 'UTF-8'); ?></span>
                                    <span class="text-slate-500">melaporkan user</span>
                                    <span class="text-slate-400 font-mono">#<?= $act['target_id']; ?></span>
                                </p>
                                <p class="text-[10px] text-slate-500 truncate mt-0.5"><?= htmlspecialchars($act['reason'], ENT_QUOTES, 'UTF-8'); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <span class="px-2 py-0.5 rounded-full text-[9px] font-semibold
                            <?= $act['status'] === 'pending' ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20' :
                                ($act['status'] === 'reviewed' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' :
                                'bg-slate-500/10 text-slate-400 border border-slate-500/20') ?>">
                            <?= $act['status']; ?>
                        </span>
                        <span class="text-[10px] text-slate-600 font-mono whitespace-nowrap"><?= date('d M H:i', strtotime($act['created_at'])); ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>
