<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="font-syne text-lg uppercase tracking-tight text-white">Login Attempts</h1>
            <p class="text-xs text-slate-500 mt-1"><?= number_format($total); ?> total percobaan login</p>
        </div>
    </div>

    <!-- Filter Form -->
    <form method="GET" action="<?= base_url('admin/login_attempts'); ?>" class="glass-card p-4 rounded-2xl border border-white/[0.04]">
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-3">
            <div>
                <label class="text-[10px] text-slate-500 uppercase font-semibold block mb-1">Status</label>
                <select name="success" class="w-full bg-slate-900/60 text-xs text-slate-300 border border-white/[0.06] rounded-lg px-3 py-2 focus:outline-none focus:border-red-500/50">
                    <option value="">Semua</option>
                    <option value="1" <?= isset($filter['success']) && $filter['success'] == 1 ? 'selected' : ''; ?>>Berhasil</option>
                    <option value="0" <?= isset($filter['success']) && $filter['success'] == 0 ? 'selected' : ''; ?>>Gagal</option>
                </select>
            </div>
            <div>
                <label class="text-[10px] text-slate-500 uppercase font-semibold block mb-1">Username/Email</label>
                <input type="text" name="identity" value="<?= htmlspecialchars($filter['identity'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                       placeholder="Cari identity..." class="w-full bg-slate-900/60 text-xs text-slate-300 border border-white/[0.06] rounded-lg px-3 py-2 focus:outline-none focus:border-red-500/50 placeholder-slate-600">
            </div>
            <div>
                <label class="text-[10px] text-slate-500 uppercase font-semibold block mb-1">IP Address</label>
                <input type="text" name="ip_address" value="<?= htmlspecialchars($filter['ip_address'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                       placeholder="IP address..." class="w-full bg-slate-900/60 text-xs text-slate-300 border border-white/[0.06] rounded-lg px-3 py-2 focus:outline-none focus:border-red-500/50 placeholder-slate-600">
            </div>
            <div>
                <label class="text-[10px] text-slate-500 uppercase font-semibold block mb-1">Dari Tanggal</label>
                <input type="date" name="date_from" value="<?= htmlspecialchars($filter['date_from'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                       class="w-full bg-slate-900/60 text-xs text-slate-300 border border-white/[0.06] rounded-lg px-3 py-2 focus:outline-none focus:border-red-500/50">
            </div>
            <div class="flex items-end gap-2">
                <div class="flex-1">
                    <label class="text-[10px] text-slate-500 uppercase font-semibold block mb-1">Sampai</label>
                    <input type="date" name="date_to" value="<?= htmlspecialchars($filter['date_to'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                           class="w-full bg-slate-900/60 text-xs text-slate-300 border border-white/[0.06] rounded-lg px-3 py-2 focus:outline-none focus:border-red-500/50">
                </div>
                <button type="submit" class="px-4 py-2 bg-red-500/10 text-red-400 text-[10px] font-semibold rounded-lg border border-red-500/20 hover:bg-red-500/20 transition-all flex-shrink-0">
                    Filter
                </button>
            </div>
        </div>
    </form>

    <!-- Table -->
    <div class="glass-card rounded-2xl border border-white/[0.04] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-white/[0.06]">
                        <th class="px-4 py-3 text-[10px] font-syne uppercase tracking-wider text-slate-500">Waktu</th>
                        <th class="px-4 py-3 text-[10px] font-syne uppercase tracking-wider text-slate-500">Identity</th>
                        <th class="px-4 py-3 text-[10px] font-syne uppercase tracking-wider text-slate-500">IP Address</th>
                        <th class="px-4 py-3 text-[10px] font-syne uppercase tracking-wider text-slate-500">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/[0.04]">
                    <?php if (empty($attempts)): ?>
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-xs text-slate-500">Tidak ada data login attempts.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($attempts as $a): ?>
                            <tr class="hover:bg-white/[0.02] transition-colors">
                                <td class="px-4 py-2.5">
                                    <span class="text-[10px] text-slate-400 font-mono whitespace-nowrap"><?= date('d M Y H:i:s', strtotime($a['attempted_at'])); ?></span>
                                </td>
                                <td class="px-4 py-2.5">
                                    <span class="text-xs text-slate-300 font-medium"><?= htmlspecialchars($a['identity'], ENT_QUOTES, 'UTF-8'); ?></span>
                                </td>
                                <td class="px-4 py-2.5">
                                    <span class="text-[10px] text-slate-500 font-mono"><?= htmlspecialchars($a['ip_address'], ENT_QUOTES, 'UTF-8'); ?></span>
                                </td>
                                <td class="px-4 py-2.5">
                                    <?php if ($a['success']): ?>
                                        <span class="px-2 py-0.5 rounded-full text-[9px] font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Berhasil</span>
                                    <?php else: ?>
                                        <span class="px-2 py-0.5 rounded-full text-[9px] font-semibold bg-red-500/10 text-red-400 border border-red-500/20">Gagal</span>
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
    <div class="flex items-center justify-center gap-2">
        <?php if ($current_page > 1): ?>
            <a href="<?= base_url('admin/login_attempts?' . http_build_query(array_merge($filter, ['page' => $current_page - 1]))); ?>"
               class="px-3 py-1.5 rounded-lg text-[10px] font-semibold glass-card border border-white/[0.06] text-slate-400 hover:text-white transition-all">
                <i data-lucide="chevron-left" class="w-3 h-3 inline"></i> Prev
            </a>
        <?php endif; ?>
        <span class="text-[10px] text-slate-500 font-mono">Hal <?= $current_page; ?> / <?= $total_pages; ?></span>
        <?php if ($current_page < $total_pages): ?>
            <a href="<?= base_url('admin/login_attempts?' . http_build_query(array_merge($filter, ['page' => $current_page + 1]))); ?>"
               class="px-3 py-1.5 rounded-lg text-[10px] font-semibold glass-card border border-white/[0.06] text-slate-400 hover:text-white transition-all">
                Next <i data-lucide="chevron-right" class="w-3 h-3 inline"></i>
            </a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>
