<div class="ad-form-wrapper">
    <!-- Header -->
    <div class="ad-form-header">
        <a href="<?= base_url('admin/ads'); ?>" class="btn btn-ghost btn-icon">
            <i data-lucide="arrow-left"></i>
        </a>
        <div>
            <h2 class="text-heading"><?= $ad ? 'Edit Iklan' : 'Tambah Iklan Baru'; ?></h2>
            <p class="text-caption mt-1"><?= $ad ? 'Perbarui data iklan' : 'Isi data iklan yang akan ditampilkan'; ?></p>
        </div>
    </div>

    <!-- Error Banner -->
    <div id="ad-error-banner" class="alert alert-error hidden">
        <i data-lucide="alert-circle"></i>
        <span id="ad-error-text"></span>
    </div>

    <!-- Form -->
    <div class="admin-card">
        <form id="ad-form" enctype="multipart/form-data" class="ad-form">
            <!-- Title -->
            <div class="form-group">
                <label class="form-label">Judul / Brand</label>
                <input type="text" name="title" value="<?= htmlspecialchars($ad['title'] ?? ''); ?>" required placeholder="Contoh: Toko Racing Parts ABC" class="input">
            </div>

            <!-- Description -->
            <div class="form-group">
                <label class="form-label">Deskripsi (opsional)</label>
                <textarea name="description" rows="2" placeholder="Deskripsi singkat iklan..." class="input textarea"></textarea>
            </div>

            <!-- Banner Image -->
            <div class="form-group">
                <label class="form-label">Gambar Banner</label>
                <div id="drop-zone" class="drop-zone">
                    <input type="file" name="banner_image" id="banner-input" accept="image/jpeg,image/png,image/gif,image/webp" class="drop-zone-input">
                    <!-- Placeholder -->
                    <div id="drop-placeholder">
                        <i data-lucide="image-plus" class="drop-zone-icon"></i>
                        <p class="text-caption">Klik atau seret gambar ke sini</p>
                        <p class="text-micro mt-1">JPG, PNG, GIF, WebP. Maks 2MB.</p>
                        <p class="text-micro">Rekomendasi: <strong class="text-muted">1200x628px</strong></p>
                    </div>
                    <!-- Preview (shown after file selected) -->
                    <div id="drop-preview" class="hidden">
                        <img id="preview-img" class="preview-image">
                        <div class="preview-info">
                            <span id="file-name" class="text-caption"></span>
                            <span id="file-size" class="text-micro"></span>
                        </div>
                        <button type="button" id="clear-preview-btn" class="btn-link text-danger">Ganti gambar</button>
                    </div>
                </div>
                <?php if (!empty($ad['image_url'])): ?>
                    <p class="text-micro mt-1">Gambar saat ini: <?= basename($ad['image_url']); ?></p>
                <?php endif; ?>
            </div>

            <!-- Target URL -->
            <div class="form-group">
                <label class="form-label">URL Tujuan</label>
                <input type="url" name="target_url" value="<?= htmlspecialchars($ad['target_url'] ?? ''); ?>" required placeholder="https://example.com" class="input">
            </div>

            <!-- Position -->
            <div class="form-group">
                <label class="form-label">Posisi Tampil</label>
                <div class="position-grid">
                    <label class="position-option">
                        <input type="radio" name="position" value="sidebar" <?= ($ad['position'] ?? 'sidebar') === 'sidebar' ? 'checked' : ''; ?> class="sr-only">
                        <div class="position-card">
                            <i data-lucide="panel-right" class="position-icon"></i>
                            <span class="position-label">Sidebar</span>
                        </div>
                    </label>
                    <label class="position-option">
                        <input type="radio" name="position" value="feed" <?= ($ad['position'] ?? '') === 'feed' ? 'checked' : ''; ?> class="sr-only">
                        <div class="position-card">
                            <i data-lucide="list" class="position-icon"></i>
                            <span class="position-label">Feed</span>
                        </div>
                    </label>
                    <label class="position-option">
                        <input type="radio" name="position" value="both" <?= ($ad['position'] ?? '') === 'both' ? 'checked' : ''; ?> class="sr-only">
                        <div class="position-card">
                            <i data-lucide="layout-grid" class="position-icon"></i>
                            <span class="position-label">Keduanya</span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Active Status -->
            <div class="status-toggle">
                <div>
                    <p class="text-body">Status Aktif</p>
                    <p class="text-caption">Iklan akan ditampilkan jika aktif</p>
                </div>
                <label class="toggle-switch">
                    <input type="checkbox" name="is_active" value="1" <?= ($ad['is_active'] ?? 1) ? 'checked' : ''; ?>>
                    <span class="toggle-slider"></span>
                </label>
            </div>

            <!-- Date Range -->
            <div class="date-grid">
                <div class="form-group">
                    <label class="form-label">Mulai Tampil</label>
                    <input type="datetime-local" name="start_date" value="<?= $ad ? date('Y-m-d\TH:i', strtotime($ad['start_date'])) : ''; ?>" class="input">
                </div>
                <div class="form-group">
                    <label class="form-label">Berhenti Tampil (opsional)</label>
                    <input type="datetime-local" name="end_date" value="<?= $ad && $ad['end_date'] ? date('Y-m-d\TH:i', strtotime($ad['end_date'])) : ''; ?>" class="input">
                    <p class="text-micro mt-1">Kosongkan untuk tampil permanen</p>
                </div>
            </div>

            <!-- Submit -->
            <div class="form-actions">
                <a href="<?= base_url('admin/ads'); ?>" class="btn btn-secondary">Batal</a>
                <button type="submit" id="ad-submit-btn" class="btn btn-primary">
                    <span id="submit-text"><?= $ad ? 'Simpan Perubahan' : 'Buat Iklan'; ?></span>
                    <svg id="submit-spinner" class="spinner hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
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