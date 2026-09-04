<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="font-syne text-lg uppercase tracking-tight text-white">Error Codes</h1>
            <p class="text-xs text-slate-500 mt-1"><?= count($error_codes); ?> kode terdaftar<?= $total_errors > 0 ? ' · ' . $total_errors . ' kejadian tercatat di log' : '' ?></p>
        </div>
        <div class="flex items-center gap-2">
            <input id="code-search" type="text" placeholder="Cari kode / judul / perbaikan..."
                   class="bg-slate-900/60 text-xs text-slate-300 border border-white/[0.06] rounded-xl px-3 py-2 w-64 focus:outline-none focus:border-red-500/40"
                   oninput="filterErrorCodes(this.value)">
        </div>
    </div>

    <!-- Table -->
    <div class="glass-card rounded-2xl border border-white/[0.04] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-white/[0.04] text-[10px] uppercase tracking-widest text-slate-500">
                        <th class="px-4 py-3 font-semibold">Kode</th>
                        <th class="px-4 py-3 font-semibold">Kategori</th>
                        <th class="px-4 py-3 font-semibold">Judul</th>
                        <th class="px-4 py-3 font-semibold">Penyebab</th>
                        <th class="px-4 py-3 font-semibold">Cara Perbaikan</th>
                        <th class="px-4 py-3 font-semibold text-center">Kejadian</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($error_codes as $code => $item): ?>
                        <?php
                            $count = $code_counts[$code] ?? 0;
                            $count_cls = $count > 0 ? 'bg-red-500/15 text-red-400' : 'bg-white/[0.03] text-slate-600';
                        ?>
                        <tr class="error-code-row border-b border-white/[0.03] align-top hover:bg-white/[0.01] transition-colors"
                            data-code="<?= htmlspecialchars(strtolower($code), ENT_QUOTES, 'UTF-8'); ?>"
                            data-text="<?= htmlspecialchars(strtolower($code . ' ' . ($item['title'] ?? '') . ' ' . ($item['cause'] ?? '') . ' ' . ($item['fix'] ?? '')), ENT_QUOTES, 'UTF-8'); ?>">
                            <td class="px-4 py-3">
                                <span class="font-mono font-bold text-red-400 bg-red-500/10 border border-red-500/20 px-2 py-1 rounded-lg"><?= htmlspecialchars($code, ENT_QUOTES, 'UTF-8'); ?></span>
                            </td>
                            <td class="px-4 py-3 text-slate-400 whitespace-nowrap"><?= htmlspecialchars($item['category'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td class="px-4 py-3 text-slate-200 font-medium min-w-[180px]"><?= htmlspecialchars($item['title'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td class="px-4 py-3 text-slate-400 min-w-[220px]"><?= htmlspecialchars($item['cause'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td class="px-4 py-3 text-slate-300 min-w-[260px]"><?= htmlspecialchars($item['fix'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-block min-w-[2.25rem] px-2 py-1 rounded-lg text-[10px] font-bold <?= $count_cls; ?>"><?= $count; ?></span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function filterErrorCodes(query) {
    const q = query.trim().toLowerCase();
    document.querySelectorAll('.error-code-row').forEach(row => {
        row.style.display = (q === '' || row.dataset.text.includes(q) || row.dataset.code.includes(q)) ? '' : 'none';
    });
}
</script>
