<div class="space-y-6">
    <!-- Header -->
    <div class="flex-row items-center justify-between">
        <div>
            <h1 class="text-heading text-lg text-transform-uppercase c-white">Error Codes</h1>
            <p class="text-xs c-subtle" style="margin-top:4px;"><?= count($error_codes); ?> kode terdaftar<?= $total_errors > 0 ? ' · ' . $total_errors . ' kejadian tercatat di log' : '' ?></p>
        </div>
        <div class="flex-row items-center gap-2">
            <input id="code-search" type="text" placeholder="Cari kode / judul / perbaikan..."
                   class="input" style="width:256px;"
                   oninput="filterErrorCodes(this.value)">
        </div>
    </div>

    <!-- Table -->
    <div class="card rounded-2xl border overflow-hidden" style="border-color:var(--border-subtle);">
        <div class="overflow-x-auto">
            <table class="table w-full text-left text-xs">
                <thead>
                    <tr class="border-b" style="border-color:var(--border-subtle);">
                        <th class="px-4 py-3 text-micro text-transform-uppercase inline-letter-spacing-01em c-subtle font-semibold">Kode</th>
                        <th class="px-4 py-3 text-micro text-transform-uppercase inline-letter-spacing-01em c-subtle font-semibold">Kategori</th>
                        <th class="px-4 py-3 text-micro text-transform-uppercase inline-letter-spacing-01em c-subtle font-semibold">Judul</th>
                        <th class="px-4 py-3 text-micro text-transform-uppercase inline-letter-spacing-01em c-subtle font-semibold">Penyebab</th>
                        <th class="px-4 py-3 text-micro text-transform-uppercase inline-letter-spacing-01em c-subtle font-semibold">Cara Perbaikan</th>
                        <th class="px-4 py-3 text-micro text-transform-uppercase inline-letter-spacing-01em c-subtle font-semibold text-center">Kejadian</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($error_codes as $code => $item): ?>
                        <?php
                            $count = $code_counts[$code] ?? 0;
                            $count_style = $count > 0 ? 'background:rgba(239,68,68,0.15);color:var(--color-primary)' : 'background:rgba(255,255,255,0.03);color:var(--text-faint)';
                        ?>
                        <tr class="error-code-row align-top transition-colors" style="border-bottom:1px solid rgba(255,255,255,0.03);" onmouseover="this.style.background='rgba(255,255,255,0.01)'" onmouseout="this.style.background=''"
                            data-code="<?= htmlspecialchars(strtolower($code), ENT_QUOTES, 'UTF-8'); ?>"
                            data-text="<?= htmlspecialchars(strtolower($code . ' ' . ($item['title'] ?? '') . ' ' . ($item['cause'] ?? '') . ' ' . ($item['fix'] ?? '')), ENT_QUOTES, 'UTF-8'); ?>">
                            <td class="px-4 py-3">
                                <span class="font-mono font-bold px-2 py-1 rounded-lg" style="color:var(--color-primary);background:var(--color-primary-bg);border:1px solid var(--color-primary-border);"><?= htmlspecialchars($code, ENT_QUOTES, 'UTF-8'); ?></span>
                            </td>
                            <td class="px-4 py-3 c-muted whitespace-nowrap"><?= htmlspecialchars($item['category'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td class="px-4 py-3 font-medium min-w-[180px]" style="color:var(--text-secondary);"><?= htmlspecialchars($item['title'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td class="px-4 py-3 c-muted min-w-[220px]"><?= htmlspecialchars($item['cause'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td class="px-4 py-3 min-w-[260px]" style="color:var(--text-secondary);"><?= htmlspecialchars($item['fix'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-block min-w-[2-25rem] px-2 py-1 rounded-lg font-bold" style="font-size:10px;<?= $count_style; ?>"><?= $count; ?></span>
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
