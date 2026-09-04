<div class="space-y-6">
    <!-- Header -->
    <div class="flex-col sm:flex-row sm:items-center justify-between gap-4 flex-row">
        <div>
            <h2 class="text-lg font-bold c-white">Ads Management</h2>
            <p class="text-xs c-subtle" style="margin-top:4px;">Kelola iklan yang tampil di PaddockID</p>
        </div>
        <a href="<?= base_url('admin/create_ad'); ?>" class="inline-flex-row items-center gap-2 px-4 py-2-5 text-xs font-semibold c-white rounded-xl transition-colors shadow-lg" style="background:var(--color-primary);" onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
            <i data-lucide="plus" class="w-3-5 h-3-5"></i> Tambah Iklan
        </a>
    </div>

    <!-- Filters -->
    <div class="card rounded-xl p-4">
        <form method="GET" data-freeze-refresh class="flex-row flex-wrap items-center gap-3">
            <div class="flex-1 min-w-[200px]">
                <input type="text" name="search" value="<?= htmlspecialchars($filter['search'] ?? ''); ?>" placeholder="Cari iklan..." class="w-full input">
            </div>
            <select name="position" class="select">
                <option value="">Semua Posisi</option>
                <option value="sidebar" <?= ($filter['position'] ?? '') === 'sidebar' ? 'selected' : '' ?>>Sidebar</option>
                <option value="feed" <?= ($filter['position'] ?? '') === 'feed' ? 'selected' : '' ?>>Feed</option>
                <option value="both" <?= ($filter['position'] ?? '') === 'both' ? 'selected' : '' ?>>Sidebar + Feed</option>
            </select>
            <select name="active" class="select">
                <option value="">Semua Status</option>
                <option value="1" <?= ($filter['is_active'] ?? '') === '1' ? 'selected' : '' ?>>Aktif</option>
                <option value="0" <?= ($filter['is_active'] ?? '') === '0' ? 'selected' : '' ?>>Nonaktif</option>
            </select>
            <button type="submit" class="btn btn-secondary btn-sm">
                Filter
            </button>
        </form>
    </div>

    <!-- Ads Table -->
    <div class="card rounded-xl overflow-hidden">
        <?php if (!empty($ads)): ?>
            <div class="overflow-x-auto">
                <table class="table w-full">
                    <thead>
                        <tr class="border-b" style="border-color:var(--border-subtle);">
                            <th class="text-left text-micro font-semibold c-subtle text-transform-uppercase inline-letter-spacing-006em px-4 py-3">Banner</th>
                            <th class="text-left text-micro font-semibold c-subtle text-transform-uppercase inline-letter-spacing-006em px-4 py-3">Judul</th>
                            <th class="text-left text-micro font-semibold c-subtle text-transform-uppercase inline-letter-spacing-006em px-4 py-3">Posisi</th>
                            <th class="text-left text-micro font-semibold c-subtle text-transform-uppercase inline-letter-spacing-006em px-4 py-3">Status</th>
                            <th class="text-left text-micro font-semibold c-subtle text-transform-uppercase inline-letter-spacing-006em px-4 py-3">Klik</th>
                            <th class="text-left text-micro font-semibold c-subtle text-transform-uppercase inline-letter-spacing-006em px-4 py-3">Periode</th>
                            <th class="text-right text-micro font-semibold c-subtle text-transform-uppercase inline-letter-spacing-006em px-4 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ads as $ad): ?>
                            <tr class="transition-colors" style="border-bottom:1px solid var(--border-subtle);" onmouseover="this.style.background='rgba(255,255,255,0.02)'" onmouseout="this.style.background=''">
                                <td class="px-4 py-3">
                                    <img src="<?= base_url($ad['image_url']); ?>" alt="<?= htmlspecialchars($ad['title']); ?>" class="w-20 h-12 rounded-lg border" style="object-fit:cover;border-color:var(--border-default);">
                                </td>
                                <td class="px-4 py-3">
                                    <p class="text-xs font-semibold c-white"><?= htmlspecialchars($ad['title']); ?></p>
                                    <p class="text-micro c-subtle truncate max-w-[200px]" style="margin-top:2px;"><?= htmlspecialchars($ad['target_url']); ?></p>
                                </td>
                                <td class="px-4 py-3">
                                    <?php
                                        $pos_labels = ['sidebar' => 'Sidebar', 'feed' => 'Feed', 'both' => 'Sidebar + Feed'];
                                        $pos_css = ['sidebar' => 'background:rgba(96,165,250,0.1);color:var(--color-info);border-color:rgba(96,165,250,0.2)', 'feed' => 'background:var(--color-success-bg);color:var(--color-success);border-color:var(--color-success-border)', 'both' => 'background:rgba(167,139,250,0.1);color:var(--color-purple);border-color:rgba(167,139,250,0.2)'];
                                        $pos = $ad['position'];
                                    ?>
                                    <span class="text-micro font-semibold px-2 py-05 rounded-full border" style="<?= $pos_css[$pos] ?? 'background:rgba(100,116,139,0.1);color:var(--color-info);border-color:rgba(100,116,139,0.2)' ?>">
                                        <?= $pos_labels[$pos] ?? $pos; ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <button onclick="toggleAdStatus(<?= $ad['id_ad']; ?>, this)" class="relative inline-flex h-5 w-9 items-center rounded-full transition-colors" style="background:<?= $ad['is_active'] ? 'var(--color-primary)' : 'var(--bg-surface-subtle)' ?>">
                                        <span class="inline-block h-3-5 w-3-5 transform rounded-full bg-white transition-transform" style="transform:translateX(<?= $ad['is_active'] ? '18px' : '3px' ?>)"></span>
                                    </button>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="text-xs font-mono" style="color:var(--text-secondary);"><?= number_format($ad['click_count']); ?></span>
                                </td>
                                <td class="px-4 py-3">
                                    <p class="text-micro c-muted"><?= date('d M Y', strtotime($ad['start_date'])); ?></p>
                                    <p class="text-micro c-subtle">s/d <?= $ad['end_date'] ? date('d M Y', strtotime($ad['end_date'])) : 'Permanen'; ?></p>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex-row items-center justify-end gap-1">
                                        <a href="<?= base_url('admin/edit_ad/' . $ad['id_ad']); ?>" class="p-1-5 c-muted rounded-lg transition-colors" title="Edit" onmouseover="this.style.color='var(--text-primary)';this.style.background='rgba(255,255,255,0.06)'" onmouseout="this.style.color='';this.style.background=''">
                                            <i data-lucide="pencil" class="w-3-5 h-3-5"></i>
                                        </a>
                                        <button onclick="deleteAd(<?= $ad['id_ad']; ?>)" class="p-1-5 c-muted rounded-lg transition-colors" title="Hapus" onmouseover="this.style.color='var(--color-primary)';this.style.background='var(--color-primary-bg)'" onmouseout="this.style.color='';this.style.background=''">
                                            <i data-lucide="trash-2" class="w-3-5 h-3-5"></i>
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
                <div class="px-4 py-3 border-t flex-row items-center justify-between" style="border-color:var(--border-subtle);">
                    <span class="text-micro c-subtle">Halaman <?= $current_page; ?> dari <?= $total_pages; ?> (<?= $total; ?> total)</span>
                    <div class="flex-row gap-1">
                        <?php for ($i = max(1, $current_page - 2); $i <= min($total_pages, $current_page + 2); $i++): ?>
                            <a href="?page=<?= $i; ?>&position=<?= $filter['position'] ?? ''; ?>&active=<?= $filter['is_active'] ?? ''; ?>&search=<?= $filter['search'] ?? ''; ?>" class="px-2-5 py-1 text-micro font-semibold rounded-lg transition-colors" style="<?= $i === $current_page ? 'background:var(--color-primary);color:#fff' : 'color:var(--color-info)' ?>" onmouseover="<?= $i !== $current_page ? "this.style.background='rgba(255,255,255,0.05)'" : '' ?>" onmouseout="<?= $i !== $current_page ? "this.style.background=''" : '' ?>">
                                <?= $i; ?>
                            </a>
                        <?php endfor; ?>
                    </div>
                </div>
            <?php endif; ?>

        <?php else: ?>
            <div class="px-4 py-16 text-center">
                <div class="w-12 h-12 mx-auto mb-4 rounded-full flex-row items-center justify-center" style="background:rgba(255,255,255,0.03);">
                    <i data-lucide="megaphone" class="w-5 h-5 c-faint"></i>
                </div>
                <p class="text-xs c-subtle">Belum ada iklan.</p>
                <a href="<?= base_url('admin/create_ad'); ?>" class="inline-flex-row items-center gap-1-5 mt-3 text-xs c-primary font-semibold" onmouseover="this.style.color='var(--text-secondary)'" onmouseout="this.style.color='var(--color-primary)'">
                    <i data-lucide="plus" class="w-3 h-3"></i> Tambah Iklan Pertama
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
if (typeof window._adsFunctionsLoaded === 'undefined') {
    window._adsFunctionsLoaded = true;

    window._adsBaseUrl = '<?= base_url(); ?>';

    window.toggleAdStatus = function(id, btn) {
        if (btn.disabled) return;
        btn.disabled = true;

        var name = document.querySelector('meta[name="csrf-token-name"]').content;
        var hash = document.querySelector('meta[name="csrf-token-hash"]').content;
        var csrf = encodeURIComponent(name) + '=' + encodeURIComponent(hash);

        fetch(window._adsBaseUrl + 'admin/toggle_ad', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: csrf + '&id_ad=' + id
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            btn.disabled = false;
            if (data.status === 'success') {
                var circle = btn.querySelector('span');
                if (data.is_active) {
                    btn.style.background = 'var(--color-primary)';
                    circle.style.transform = 'translateX(18px)';
                } else {
                    btn.style.background = 'var(--bg-surface-subtle)';
                    circle.style.transform = 'translateX(3px)';
                }
                showToast(data.is_active ? 'Iklan diaktifkan' : 'Iklan dinonaktifkan', 'green');
            } else {
                showToast('Gagal mengubah status', 'red');
            }
        })
        .catch(function() {
            btn.disabled = false;
            showToast('Gagal mengubah status iklan', 'red');
        });
    };

    window.deleteAd = function(id) {
        if (!confirm('Yakin ingin menghapus iklan ini? Gambar banner juga akan dihapus.')) return;

        var name = document.querySelector('meta[name="csrf-token-name"]').content;
        var hash = document.querySelector('meta[name="csrf-token-hash"]').content;
        var csrf = encodeURIComponent(name) + '=' + encodeURIComponent(hash);

        fetch(window._adsBaseUrl + 'admin/delete_ad', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: csrf + '&id_ad=' + id
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.status === 'success') {
                location.reload();
            } else {
                showToast('Gagal menghapus iklan', 'red');
            }
        })
        .catch(function() {
            showToast('Gagal menghapus iklan', 'red');
        });
    };
}
</script>
