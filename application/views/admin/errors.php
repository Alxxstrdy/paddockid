<div class="space-y-6">
    <!-- Header -->
    <div class="flex-row justify-between">
        <div>
            <h1 class="text-page-title">Error Logs</h1>
            <p class="text-caption mt-1"><?= count($log_files); ?> file log tersedia</p>
        </div>
    </div>

    <div class="page-grid">
        <!-- File List -->
        <div style="grid-column:span 4;">
            <p class="text-section-title px-1 mb-2">Log Files</p>
            <div class="space-y-2">
            <?php if (empty($log_files)): ?>
                <div class="card text-center" style="padding:16px;color:var(--text-subtle);font-size:12px;">Belum ada log.</div>
            <?php else: ?>
                <?php foreach ($log_files as $f): ?>
                    <?php
                        $is_active = $active_file === $f['name'];
                        $size_kb = round($f['size'] / 1024, 1);
                    ?>
                    <a href="<?= base_url('admin/errors?file=' . $f['name']); ?>"
                       class="flex-row justify-between rounded-xl transition-all text-xs <?= $is_active ? 'badge-danger' : '' ?>"
                       style="padding:10px 12px;<?= $is_active ? 'background:var(--color-danger-bg);border:1px solid var(--color-danger-border);' : 'background:var(--bg-surface);border:1px solid var(--border-subtle);' ?>"
                       onmouseover="if(!this.classList.contains('badge-danger')){this.style.background='var(--bg-surface-subtle)';this.style.borderColor='var(--border-default)'}"
                       onmouseout="if(!this.classList.contains('badge-danger')){this.style.background='var(--bg-surface)';this.style.borderColor='var(--border-subtle)'}">
                        <div class="flex-row gap-2 min-w-0">
                            <i data-lucide="file-text" class="w-3-5 h-3-5 flex-shrink-0"></i>
                            <span class="text-mono truncate"><?= $f['name']; ?></span>
                        </div>
                        <div class="flex-row gap-2 flex-shrink-0">
                            <span class="text-micro"><?= $size_kb; ?>KB</span>
                            <button onclick="event.preventDefault(); event.stopPropagation(); deleteLogFile('<?= $f['name']; ?>', this)"
                                    class="transition-colors rounded" style="padding:2px;color:var(--text-subtle);"
                                    onmouseover="this.style.color='var(--color-primary)';this.style.background='var(--color-primary-bg)'"
                                    onmouseout="this.style.color='var(--text-subtle)';this.style.background='transparent'"
                                    title="Hapus log">
                                <i data-lucide="trash-2" class="w-3 h-3"></i>
                            </button>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
            </div>
        </div>

        <!-- Log Content -->
        <div style="grid-column:span 8;">
            <?php if ($active_file && !empty($log_entries)): ?>
                <div class="card rounded-2xl overflow-hidden">
                    <div class="flex-row justify-between border-b" style="padding:12px 16px;border-color:var(--border-subtle);">
                        <div class="flex-row gap-2">
                            <span class="font-semibold c-white" style="font-size:12px;"><?= $active_file; ?></span>
                            <span class="text-micro">(<?= $log_count; ?> entries)</span>
                        </div>
                        <select onchange="filterLogEntries(this.value)" class="select select--sm">
                            <option value="all">Semua</option>
                            <option value="error">Error</option>
                            <option value="warning">Warning</option>
                            <option value="debug">Debug</option>
                        </select>
                    </div>
                    <div class="overflow-y-auto no-scrollbar space-y-1" style="max-height:65vh;padding:16px;font-family:var(--font-mono);font-size:11px;line-height:1.625;">
                        <?php foreach ($log_entries as $entry): ?>
                            <?php
                                $level_color = match($entry['level']) {
                                    'error'   => 'c-danger',
                                    'warning' => 'c-warning',
                                    'info'    => 'c-info',
                                    default   => 'c-subtle',
                                };
                                $level_bg = match($entry['level']) {
                                    'error'   => 'border-l-2',
                                    'warning' => 'border-l-2',
                                    default   => '',
                                };
                                $level_bg_style = match($entry['level']) {
                                    'error'   => 'border-left-color:var(--color-danger);background:rgba(239,68,68,0.05);',
                                    'warning' => 'border-left-color:var(--color-warning);background:rgba(245,158,11,0.05);',
                                    default   => '',
                                };
                            ?>
                            <div class="log-entry rounded-lg transition-colors <?= $level_bg; ?>" style="padding:8px 12px;<?= $level_bg_style; ?>" data-level="<?= $entry['level']; ?>" onmouseover="this.style.background='var(--bg-surface-subtle)'" onmouseout="this.style.background='<?= $level_bg_style ? addslashes($level_bg_style) : '' ?>'">
                                <div class="flex-row gap-2">
                                    <span class="flex-shrink-0" style="width:144px;color:var(--text-subtle);"><?= $entry['datetime']; ?></span>
                                    <span class="<?= $level_color; ?> uppercase font-bold flex-shrink-0" style="width:56px;"><?= $entry['level']; ?></span>
                                    <span class="c-subtle break-all" style="white-space:pre-wrap;"><?= htmlspecialchars($entry['message'], ENT_QUOTES, 'UTF-8'); ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php elseif ($active_file): ?>
                <div class="card text-center" style="padding:32px;color:var(--text-subtle);font-size:12px;">
                    <p>File log kosong atau tidak ditemukan.</p>
                </div>
            <?php else: ?>
                <div class="card text-center" style="padding:32px;color:var(--text-subtle);font-size:12px;">
                    <div class="rounded-full flex items-center justify-center mx-auto mb-3" style="width:48px;height:48px;background:var(--bg-surface-subtle);">
                        <i data-lucide="file-text" class="w-5 h-5" style="color:var(--text-faint);"></i>
                    </div>
                    <p>Pilih file log dari daftar di sebelah kiri.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function filterLogEntries(level) {
    document.querySelectorAll('.log-entry').forEach(el => {
        el.style.display = (level === 'all' || el.dataset.level === level) ? '' : 'none';
    });
}

function deleteLogFile(filename, btn) {
    if (!confirm('Yakin ingin menghapus ' + filename + '?')) return;

    const csrfName = document.querySelector('meta[name="csrf-token-name"]').content;
    const csrfHash = document.querySelector('meta[name="csrf-token-hash"]').content;

    const body = new FormData();
    body.append('file', filename);
    body.append(csrfName, csrfHash);

    fetch('<?= base_url("admin/delete_log"); ?>', { method: 'POST', body })
        .then(r => r.json())
        .then(data => {
            if (data.status === 'success') {
                const link = btn.closest('a');
                if (link) {
                    link.style.transition = 'all 0.3s';
                    link.style.opacity = '0';
                    setTimeout(() => link.remove(), 300);
                }
                showToast(data.message, 'green');
            }
        });
}
</script>
