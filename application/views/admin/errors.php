<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="font-syne text-lg uppercase tracking-tight text-white">Error Logs</h1>
            <p class="text-xs text-slate-500 mt-1"><?= count($log_files); ?> file log tersedia</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- File List -->
        <div class="lg:col-span-4 space-y-2">
            <p class="text-[10px] font-syne uppercase tracking-widest text-slate-500 px-1 mb-2">Log Files</p>
            <?php if (empty($log_files)): ?>
                <div class="glass-card p-4 rounded-2xl text-center text-slate-500 text-xs">Belum ada log.</div>
            <?php else: ?>
                <?php foreach ($log_files as $f): ?>
                    <?php
                        $is_active = $active_file === $f['name'];
                        $size_kb = round($f['size'] / 1024, 1);
                    ?>
                    <a href="<?= base_url('admin/errors?file=' . $f['name']); ?>"
                       class="flex items-center justify-between px-3 py-2.5 rounded-xl text-xs transition-all <?= $is_active ? 'bg-red-500/10 border border-red-500/20 text-red-400' : 'glass-card hover:bg-white/[0.02] text-slate-400 border border-white/[0.04]' ?>">
                        <div class="flex items-center gap-2 min-w-0">
                            <i data-lucide="file-text" class="w-3.5 h-3.5 flex-shrink-0"></i>
                            <span class="font-mono truncate"><?= $f['name']; ?></span>
                        </div>
                        <div class="flex items-center gap-2 flex-shrink-0">
                            <span class="text-[10px] text-slate-600"><?= $size_kb; ?>KB</span>
                            <button onclick="event.preventDefault(); event.stopPropagation(); deleteLogFile('<?= $f['name']; ?>', this)"
                                    class="text-slate-600 hover:text-red-400 transition-colors p-0.5 rounded hover:bg-red-500/10"
                                    title="Hapus log">
                                <i data-lucide="trash-2" class="w-3 h-3"></i>
                            </button>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Log Content -->
        <div class="lg:col-span-8">
            <?php if ($active_file && !empty($log_entries)): ?>
                <div class="glass-card rounded-2xl border border-white/[0.04] overflow-hidden">
                    <div class="flex items-center justify-between px-4 py-3 border-b border-white/[0.04]">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-semibold text-white"><?= $active_file; ?></span>
                            <span class="text-[10px] text-slate-500 font-mono">(<?= $log_count; ?> entries)</span>
                        </div>
                        <select onchange="filterLogEntries(this.value)" class="bg-slate-900/60 text-[10px] text-slate-300 border border-white/[0.06] rounded-lg px-2 py-1 focus:outline-none">
                            <option value="all">Semua</option>
                            <option value="error">Error</option>
                            <option value="warning">Warning</option>
                            <option value="debug">Debug</option>
                        </select>
                    </div>
                    <div class="max-h-[65vh] overflow-y-auto p-4 space-y-1 font-mono text-[11px] leading-relaxed no-scrollbar">
                        <?php foreach ($log_entries as $entry): ?>
                            <?php
                                $level_color = match($entry['level']) {
                                    'error'   => 'text-red-400',
                                    'warning' => 'text-amber-400',
                                    'info'    => 'text-blue-400',
                                    default   => 'text-slate-500',
                                };
                                $level_bg = match($entry['level']) {
                                    'error'   => 'bg-red-500/5 border-l-2 border-red-500/30',
                                    'warning' => 'bg-amber-500/5 border-l-2 border-amber-500/30',
                                    default   => '',
                                };
                            ?>
                            <div class="log-entry <?= $level_bg; ?> px-3 py-2 rounded-lg hover:bg-white/[0.02] transition-colors" data-level="<?= $entry['level']; ?>">
                                <div class="flex items-start gap-2">
                                    <span class="text-slate-600 flex-shrink-0 w-36"><?= $entry['datetime']; ?></span>
                                    <span class="<?= $level_color; ?> uppercase font-bold w-14 flex-shrink-0"><?= $entry['level']; ?></span>
                                    <span class="text-slate-400 break-all whitespace-pre-wrap"><?= htmlspecialchars($entry['message'], ENT_QUOTES, 'UTF-8'); ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php elseif ($active_file): ?>
                <div class="glass-card p-8 rounded-2xl text-center text-slate-500 text-xs">
                    <p>File log kosong atau tidak ditemukan.</p>
                </div>
            <?php else: ?>
                <div class="glass-card p-8 rounded-2xl text-center text-slate-500 text-xs">
                    <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-white/[0.03] flex items-center justify-center">
                        <i data-lucide="file-text" class="w-5 h-5 text-slate-600"></i>
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
