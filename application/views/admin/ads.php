<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-lg font-bold text-white">Ads Management</h2>
            <p class="text-xs text-slate-500 mt-1">Kelola iklan yang tampil di PaddockID</p>
        </div>
        <a href="<?= base_url('admin/create_ad'); ?>" class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-semibold text-white bg-red-600 hover:bg-red-500 rounded-xl transition-colors shadow-lg shadow-red-600/10">
            <i data-lucide="plus" class="w-3.5 h-3.5"></i> Tambah Iklan
        </a>
    </div>

    <!-- Filters -->
    <div class="admin-card rounded-xl p-4">
        <form method="GET" class="flex flex-wrap items-center gap-3">
            <div class="flex-1 min-w-[200px]">
                <input type="text" name="search" value="<?= htmlspecialchars($filter['search'] ?? ''); ?>" placeholder="Cari iklan..." class="w-full bg-slate-800/50 text-xs text-slate-200 placeholder-slate-500 border border-white/[0.06] rounded-lg px-3 py-2 focus:outline-none focus:border-red-500/50">
            </div>
            <select name="position" class="bg-slate-800/50 text-xs text-slate-300 border border-white/[0.06] rounded-lg px-3 py-2 focus:outline-none focus:border-red-500/50">
                <option value="">Semua Posisi</option>
                <option value="sidebar" <?= ($filter['position'] ?? '') === 'sidebar' ? 'selected' : '' ?>>Sidebar</option>
                <option value="feed" <?= ($filter['position'] ?? '') === 'feed' ? 'selected' : '' ?>>Feed</option>
                <option value="both" <?= ($filter['position'] ?? '') === 'both' ? 'selected' : '' ?>>Sidebar + Feed</option>
            </select>
            <select name="active" class="bg-slate-800/50 text-xs text-slate-300 border border-white/[0.06] rounded-lg px-3 py-2 focus:outline-none focus:border-red-500/50">
                <option value="">Semua Status</option>
                <option value="1" <?= ($filter['is_active'] ?? '') === '1' ? 'selected' : '' ?>>Aktif</option>
                <option value="0" <?= ($filter['is_active'] ?? '') === '0' ? 'selected' : '' ?>>Nonaktif</option>
            </select>
            <button type="submit" class="px-4 py-2 text-xs font-medium text-slate-300 bg-white/[0.04] hover:bg-white/[0.08] border border-white/[0.06] rounded-lg transition-colors">
                Filter
            </button>
        </form>
    </div>

    <!-- Ads Table -->
    <div class="admin-card rounded-xl overflow-hidden">
        <?php if (!empty($ads)): ?>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-white/[0.04]">
                            <th class="text-left text-[10px] font-semibold text-slate-500 uppercase tracking-wider px-4 py-3">Banner</th>
                            <th class="text-left text-[10px] font-semibold text-slate-500 uppercase tracking-wider px-4 py-3">Judul</th>
                            <th class="text-left text-[10px] font-semibold text-slate-500 uppercase tracking-wider px-4 py-3">Posisi</th>
                            <th class="text-left text-[10px] font-semibold text-slate-500 uppercase tracking-wider px-4 py-3">Status</th>
                            <th class="text-left text-[10px] font-semibold text-slate-500 uppercase tracking-wider px-4 py-3">Klik</th>
                            <th class="text-left text-[10px] font-semibold text-slate-500 uppercase tracking-wider px-4 py-3">Periode</th>
                            <th class="text-right text-[10px] font-semibold text-slate-500 uppercase tracking-wider px-4 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/[0.03]">
                        <?php foreach ($ads as $ad): ?>
                            <tr class="hover:bg-white/[0.02] transition-colors">
                                <td class="px-4 py-3">
                                    <img src="<?= base_url($ad['image_url']); ?>" alt="<?= htmlspecialchars($ad['title']); ?>" class="w-20 h-12 object-cover rounded-lg border border-white/[0.06]">
                                </td>
                                <td class="px-4 py-3">
                                    <p class="text-xs font-semibold text-white"><?= htmlspecialchars($ad['title']); ?></p>
                                    <p class="text-[10px] text-slate-500 mt-0.5 truncate max-w-[200px]"><?= htmlspecialchars($ad['target_url']); ?></p>
                                </td>
                                <td class="px-4 py-3">
                                    <?php
                                        $pos_labels = ['sidebar' => 'Sidebar', 'feed' => 'Feed', 'both' => 'Sidebar + Feed'];
                                        $pos_colors = ['sidebar' => 'blue', 'feed' => 'emerald', 'both' => 'purple'];
                                        $pos = $ad['position'];
                                    ?>
                                    <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full border border-<?= $pos_colors[$pos] ?? 'slate' ?>-500/20 bg-<?= $pos_colors[$pos] ?? 'slate' ?>-500/10 text-<?= $pos_colors[$pos] ?? 'slate' ?>-400">
                                        <?= $pos_labels[$pos] ?? $pos; ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <button onclick="toggleAdStatus(<?= $ad['id_ad']; ?>, this)" class="relative inline-flex h-5 w-9 items-center rounded-full transition-colors <?= $ad['is_active'] ? 'bg-red-600' : 'bg-slate-700' ?>">
                                        <span class="inline-block h-3.5 w-3.5 transform rounded-full bg-white transition-transform <?= $ad['is_active'] ? 'translate-x-[18px]' : 'translate-x-[3px]' ?>"></span>
                                    </button>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="text-xs font-mono text-slate-300"><?= number_format($ad['click_count']); ?></span>
                                </td>
                                <td class="px-4 py-3">
                                    <p class="text-[10px] text-slate-400"><?= date('d M Y', strtotime($ad['start_date'])); ?></p>
                                    <p class="text-[10px] text-slate-500">s/d <?= $ad['end_date'] ? date('d M Y', strtotime($ad['end_date'])) : 'Permanen'; ?></p>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="<?= base_url('admin/edit_ad/' . $ad['id_ad']); ?>" class="p-1.5 text-slate-400 hover:text-white hover:bg-white/[0.06] rounded-lg transition-colors" title="Edit">
                                            <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                                        </a>
                                        <button onclick="deleteAd(<?= $ad['id_ad']; ?>)" class="p-1.5 text-slate-400 hover:text-red-400 hover:bg-red-500/10 rounded-lg transition-colors" title="Hapus">
                                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <div class="px-4 py-3 border-t border-white/[0.04] flex items-center justify-between">
                    <span class="text-[10px] text-slate-500">Halaman <?= $current_page; ?> dari <?= $total_pages; ?> (<?= $total; ?> total)</span>
                    <div class="flex gap-1">
                        <?php for ($i = max(1, $current_page - 2); $i <= min($total_pages, $current_page + 2); $i++): ?>
                            <a href="?page=<?= $i; ?>&position=<?= $filter['position'] ?? ''; ?>&active=<?= $filter['is_active'] ?? ''; ?>&search=<?= $filter['search'] ?? ''; ?>" class="px-2.5 py-1 text-[10px] font-semibold rounded-lg transition-colors <?= $i === $current_page ? 'bg-red-600 text-white' : 'text-slate-400 hover:bg-white/[0.05]' ?>">
                                <?= $i; ?>
                            </a>
                        <?php endfor; ?>
                    </div>
                </div>
            <?php endif; ?>

        <?php else: ?>
            <div class="px-4 py-16 text-center">
                <div class="w-12 h-12 mx-auto mb-4 rounded-full bg-white/[0.03] flex items-center justify-center">
                    <i data-lucide="megaphone" class="w-5 h-5 text-slate-600"></i>
                </div>
                <p class="text-xs text-slate-500">Belum ada iklan.</p>
                <a href="<?= base_url('admin/create_ad'); ?>" class="inline-flex items-center gap-1.5 mt-3 text-xs text-red-400 hover:text-red-300 font-semibold">
                    <i data-lucide="plus" class="w-3 h-3"></i> Tambah Iklan Pertama
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function getCsrfField() {
    var name = document.querySelector('meta[name="csrf-token-name"]').content;
    var hash = document.querySelector('meta[name="csrf-token-hash"]').content;
    return encodeURIComponent(name) + '=' + encodeURIComponent(hash);
}

function toggleAdStatus(id, btn) {
    fetch('<?= base_url("admin/toggle_ad"); ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: getCsrfField() + '&id_ad=' + id
    })
    .then(r => r.json())
    .then(data => {
        if (data.status === 'success') {
            const toggle = btn;
            const circle = toggle.querySelector('span');
            if (data.is_active) {
                toggle.classList.remove('bg-slate-700');
                toggle.classList.add('bg-red-600');
                circle.classList.remove('translate-x-[3px]');
                circle.classList.add('translate-x-[18px]');
            } else {
                toggle.classList.remove('bg-red-600');
                toggle.classList.add('bg-slate-700');
                circle.classList.remove('translate-x-[18px]');
                circle.classList.add('translate-x-[3px]');
            }
        }
    });
}

function deleteAd(id) {
    if (!confirm('Yakin ingin menghapus iklan ini? Gambar banner juga akan dihapus.')) return;

    fetch('<?= base_url("admin/delete_ad"); ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: getCsrfField() + '&id_ad=' + id
    })
    .then(r => r.json())
    .then(data => {
        if (data.status === 'success') {
            location.reload();
        }
    });
}
</script>
