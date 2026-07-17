<div class="max-w-2xl">
    <!-- Header -->
    <div class="flex items-center gap-3 mb-6">
        <a href="<?= base_url('admin/ads'); ?>" class="p-2 text-slate-400 hover:text-white hover:bg-white/[0.05] rounded-lg transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
        </a>
        <div>
            <h2 class="text-lg font-bold text-white"><?= $ad ? 'Edit Iklan' : 'Tambah Iklan Baru'; ?></h2>
            <p class="text-xs text-slate-500 mt-0.5"><?= $ad ? 'Perbarui data iklan' : 'Isi data iklan yang akan ditampilkan'; ?></p>
        </div>
    </div>

    <!-- Error Banner -->
    <div id="ad-error-banner" class="hidden mb-4 p-3 bg-red-500/10 border border-red-500/20 rounded-lg text-red-400 text-xs flex items-start gap-2">
        <i data-lucide="alert-circle" class="w-4 h-4 mt-0.5 flex-shrink-0"></i>
        <span id="ad-error-text"></span>
    </div>

    <!-- Form -->
    <div class="admin-card rounded-xl p-6">
        <form id="ad-form" enctype="multipart/form-data" class="space-y-5">
            <!-- Title -->
            <div>
                <label class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider block mb-1.5">Judul / Brand</label>
                <input type="text" name="title" value="<?= htmlspecialchars($ad['title'] ?? ''); ?>" required placeholder="Contoh: Toko Racing Parts ABC" class="w-full bg-slate-800/50 text-xs text-slate-200 placeholder-slate-600 border border-white/[0.06] rounded-lg px-3 py-2.5 focus:outline-none focus:border-red-500/50">
            </div>

            <!-- Description -->
            <div>
                <label class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider block mb-1.5">Deskripsi (opsional)</label>
                <textarea name="description" rows="2" placeholder="Deskripsi singkat iklan..." class="w-full bg-slate-800/50 text-xs text-slate-200 placeholder-slate-600 border border-white/[0.06] rounded-lg px-3 py-2.5 focus:outline-none focus:border-red-500/50 resize-none"><?= htmlspecialchars($ad['description'] ?? ''); ?></textarea>
            </div>

            <!-- Banner Image -->
            <div>
                <label class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider block mb-1.5">Gambar Banner</label>
                <div id="drop-zone" class="relative border-2 border-dashed border-white/[0.06] rounded-xl p-6 text-center hover:border-red-500/30 transition-colors cursor-pointer">
                    <input type="file" name="banner_image" id="banner-input" accept="image/jpeg,image/png,image/gif,image/webp" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                    <!-- Placeholder -->
                    <div id="drop-placeholder">
                        <i data-lucide="image-plus" class="w-8 h-8 text-slate-600 mx-auto mb-2"></i>
                        <p class="text-xs text-slate-500">Klik atau seret gambar ke sini</p>
                        <p class="text-[10px] text-slate-600 mt-1">JPG, PNG, GIF, WebP. Maks 2MB.</p>
                        <p class="text-[10px] text-slate-600">Rekomendasi: <strong class="text-slate-400">1200x628px</strong></p>
                    </div>
                    <!-- Preview (shown after file selected) -->
                    <div id="drop-preview" class="hidden">
                        <img id="preview-img" class="max-h-40 mx-auto rounded-lg border border-white/[0.06]">
                        <div class="mt-2 flex items-center justify-center gap-2">
                            <span id="file-name" class="text-[10px] text-slate-400"></span>
                            <span id="file-size" class="text-[10px] text-slate-600"></span>
                        </div>
                        <button type="button" id="clear-preview-btn" class="mt-2 text-[10px] text-red-400 hover:text-red-300 font-semibold">Ganti gambar</button>
                    </div>
                </div>
                <?php if (!empty($ad['image_url'])): ?>
                    <p class="text-[10px] text-slate-500 mt-1.5">Gambar saat ini: <?= basename($ad['image_url']); ?></p>
                <?php endif; ?>
            </div>

            <!-- Target URL -->
            <div>
                <label class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider block mb-1.5">URL Tujuan</label>
                <input type="url" name="target_url" value="<?= htmlspecialchars($ad['target_url'] ?? ''); ?>" required placeholder="https://example.com" class="w-full bg-slate-800/50 text-xs text-slate-200 placeholder-slate-600 border border-white/[0.06] rounded-lg px-3 py-2.5 focus:outline-none focus:border-red-500/50">
            </div>

            <!-- Position -->
            <div>
                <label class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider block mb-1.5">Posisi Tampil</label>
                <div class="grid grid-cols-3 gap-3">
                    <label class="relative">
                        <input type="radio" name="position" value="sidebar" <?= ($ad['position'] ?? 'sidebar') === 'sidebar' ? 'checked' : ''; ?> class="peer hidden">
                        <div class="border border-white/[0.06] rounded-lg p-3 text-center cursor-pointer transition-all peer-checked:border-red-500/50 peer-checked:bg-red-500/10 hover:bg-white/[0.02]">
                            <i data-lucide="panel-right" class="w-4 h-4 text-slate-400 mx-auto mb-1 peer-checked:text-red-400"></i>
                            <span class="text-[10px] font-semibold text-slate-400 peer-checked:text-red-400">Sidebar</span>
                        </div>
                    </label>
                    <label class="relative">
                        <input type="radio" name="position" value="feed" <?= ($ad['position'] ?? '') === 'feed' ? 'checked' : ''; ?> class="peer hidden">
                        <div class="border border-white/[0.06] rounded-lg p-3 text-center cursor-pointer transition-all peer-checked:border-red-500/50 peer-checked:bg-red-500/10 hover:bg-white/[0.02]">
                            <i data-lucide="list" class="w-4 h-4 text-slate-400 mx-auto mb-1 peer-checked:text-red-400"></i>
                            <span class="text-[10px] font-semibold text-slate-400 peer-checked:text-red-400">Feed</span>
                        </div>
                    </label>
                    <label class="relative">
                        <input type="radio" name="position" value="both" <?= ($ad['position'] ?? '') === 'both' ? 'checked' : ''; ?> class="peer hidden">
                        <div class="border border-white/[0.06] rounded-lg p-3 text-center cursor-pointer transition-all peer-checked:border-red-500/50 peer-checked:bg-red-500/10 hover:bg-white/[0.02]">
                            <i data-lucide="layout-grid" class="w-4 h-4 text-slate-400 mx-auto mb-1 peer-checked:text-red-400"></i>
                            <span class="text-[10px] font-semibold text-slate-400 peer-checked:text-red-400">Keduanya</span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Active Status -->
            <div class="flex items-center justify-between py-3 px-4 bg-slate-800/30 rounded-lg border border-white/[0.04]">
                <div>
                    <p class="text-xs font-semibold text-white">Status Aktif</p>
                    <p class="text-[10px] text-slate-500">Iklan akan ditampilkan jika aktif</p>
                </div>
                <label class="relative inline-flex cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" class="peer hidden" <?= ($ad['is_active'] ?? 1) ? 'checked' : ''; ?>>
                    <div class="w-9 h-5 bg-slate-700 rounded-full peer peer-checked:bg-red-600 transition-colors after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:after:translate-x-full"></div>
                </label>
            </div>

            <!-- Date Range -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider block mb-1.5">Mulai Tampil</label>
                    <input type="datetime-local" name="start_date" value="<?= $ad ? date('Y-m-d\TH:i', strtotime($ad['start_date'])) : ''; ?>" class="w-full bg-slate-800/50 text-xs text-slate-200 border border-white/[0.06] rounded-lg px-3 py-2.5 focus:outline-none focus:border-red-500/50">
                </div>
                <div>
                    <label class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider block mb-1.5">Berhenti Tampil (opsional)</label>
                    <input type="datetime-local" name="end_date" value="<?= $ad && $ad['end_date'] ? date('Y-m-d\TH:i', strtotime($ad['end_date'])) : ''; ?>" class="w-full bg-slate-800/50 text-xs text-slate-200 border border-white/[0.06] rounded-lg px-3 py-2.5 focus:outline-none focus:border-red-500/50">
                    <p class="text-[9px] text-slate-600 mt-1">Kosongkan untuk tampil permanen</p>
                </div>
            </div>

            <!-- Submit -->
            <div class="flex justify-end gap-3 pt-4 border-t border-white/[0.04]">
                <a href="<?= base_url('admin/ads'); ?>" class="px-4 py-2.5 text-xs font-semibold text-slate-300 bg-white/[0.05] hover:bg-white/[0.08] rounded-xl transition-colors border border-white/[0.06]">
                    Batal
                </a>
                <button type="submit" id="ad-submit-btn" class="px-6 py-2.5 text-xs font-semibold text-white bg-red-600 hover:bg-red-500 rounded-xl transition-colors shadow-lg shadow-red-600/10 flex items-center gap-2">
                    <span id="submit-text"><?= $ad ? 'Simpan Perubahan' : 'Buat Iklan'; ?></span>
                    <svg id="submit-spinner" class="hidden animate-spin h-3.5 w-3.5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
(function() {
    const bannerInput = document.getElementById('banner-input');
    const placeholder = document.getElementById('drop-placeholder');
    const previewDiv = document.getElementById('drop-preview');
    const previewImg = document.getElementById('preview-img');
    const fileNameEl = document.getElementById('file-name');
    const fileSizeEl = document.getElementById('file-size');
    const clearBtn = document.getElementById('clear-preview-btn');
    const errorBanner = document.getElementById('ad-error-banner');
    const errorText = document.getElementById('ad-error-text');
    const submitBtn = document.getElementById('ad-submit-btn');
    const submitText = document.getElementById('submit-text');
    const submitSpinner = document.getElementById('submit-spinner');
    const form = document.getElementById('ad-form');
    const <?= $ad ? 'IS_EDIT' : 'IS_EDIT' ?> = <?= $ad ? 'true' : 'false'; ?>;
    const createLabel = 'Buat Iklan';
    const editLabel = 'Simpan Perubahan';

    function showError(msg) {
        errorText.textContent = msg;
        errorBanner.classList.remove('hidden');
        lucide.createIcons();
    }
    function clearError() {
        errorBanner.classList.add('hidden');
    }

    function formatSize(bytes) {
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / 1048576).toFixed(1) + ' MB';
    }

    function setLoading(on) {
        submitBtn.disabled = on;
        if (on) {
            submitText.textContent = 'Menyimpan...';
            submitSpinner.classList.remove('hidden');
        } else {
            submitText.textContent = IS_EDIT ? editLabel : createLabel;
            submitSpinner.classList.add('hidden');
        }
    }

    function showPreview(file) {
        if (file.size > 2 * 1024 * 1024) {
            showError('Ukuran gambar maksimal 2MB. File Anda: ' + formatSize(file.size));
            bannerInput.value = '';
            return;
        }
        clearError();
        var reader = new FileReader();
        reader.onload = function(ev) {
            previewImg.src = ev.target.result;
            fileNameEl.textContent = file.name;
            fileSizeEl.textContent = '(' + formatSize(file.size) + ')';
            placeholder.classList.add('hidden');
            previewDiv.classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    }

    bannerInput.addEventListener('change', function(e) {
        if (e.target.files && e.target.files[0]) {
            showPreview(e.target.files[0]);
        }
    });

    clearBtn.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        bannerInput.value = '';
        previewDiv.classList.add('hidden');
        placeholder.classList.remove('hidden');
    });

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        clearError();

        var file = bannerInput.files[0];
        <?php if (empty($ad)): ?>
        if (!file) {
            showError('Gambar banner wajib diupload.');
            return;
        }
        <?php endif; ?>
        if (file && file.size > 2 * 1024 * 1024) {
            showError('Ukuran gambar maksimal 2MB. File Anda: ' + formatSize(file.size));
            return;
        }

        setLoading(true);

        var formData = new FormData(form);
        var csrfName = document.querySelector('meta[name="csrf-token-name"]').content;
        var csrfHash = document.querySelector('meta[name="csrf-token-hash"]').content;
        formData.append(csrfName, csrfHash);

        fetch('<?= base_url("admin/" . $form_action); ?>', {
            method: 'POST',
            body: formData
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.status === 'success') {
                window.location.href = '<?= base_url("admin/ads"); ?>';
            } else {
                showError(data.message || 'Gagal menyimpan iklan.');
                setLoading(false);
            }
        })
        .catch(function(err) {
            showError('Terjadi kesalahan jaringan. Silakan coba lagi.');
            setLoading(false);
        });
    });
})();
</script>
