<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js"></script>
<style>
    .avatar-upload-zone {
        position: relative;
        overflow: hidden;
    }
    .avatar-upload-zone img {
        transition: all 0.3s ease;
    }
    .banner-upload-zone {
        position: relative;
        overflow: hidden;
    }
    .banner-upload-zone img {
        transition: all 0.3s ease;
    }
    .file-input-trigger:active {
        transform: scale(0.98);
    }
    .cropper-view-box,
    .cropper-face {
        border-radius: 0 !important;
    }
    .crop-avatar .cropper-view-box,
    .crop-avatar .cropper-face {
        border-radius: 50% !important;
    }
</style>

<div class="flex-1 max-w-2xl w-full mx-auto px-4 py-6">

    <div class="flex-row gap-3 mb-6 pb-4 border-b">
        <a href="<?= base_url('profile'); ?>" class="link-back p-2 c-muted rounded-xl">
            <i data-lucide="arrow-left" class="w-5 h-5"></i>
        </a>
        <h1 class="text-heading text-sm c-white">Edit Profil</h1>
    </div>

    <form id="edit-profile-form" enctype="multipart/form-data">
        <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">

        <!-- Banner Section -->
        <div class="card rounded-2xl overflow-hidden shadow-xl border mb-4">
            <div class="w-full relative overflow-hidden banner-upload-zone" id="banner-zone" style="height:160px;background:linear-gradient(to right, rgba(69,10,10,0.4), var(--bg-surface))">
                <?php if (!empty($user['banner'])): ?>
                    <img id="banner-preview-img" src="<?= base_url($user['banner']); ?>" alt="Banner" class="w-full h-full" style="object-fit:cover">
                <?php else: ?>
                    <div id="banner-preview-placeholder" class="absolute inset-0 flex-col items-center justify-center gap-2">
                        <i data-lucide="image" class="w-8 h-8 c-faint"></i>
                        <span class="text-micro c-subtle" style="text-transform:uppercase;letter-spacing:0.06em;font-size:10px">Belum ada banner</span>
                    </div>
                    <img id="banner-preview-img" src="" alt="Banner" class="w-full h-full hidden" style="object-fit:cover">
                <?php endif; ?>
                <div class="absolute inset-0" style="background:linear-gradient(to top, rgba(5,7,12,0.8), transparent, transparent)"></div>
            </div>
            <div class="px-5 py-3 flex-row justify-between border-t" style="border-color:var(--border-subtle)">
                <div class="flex-row gap-3">
                    <label class="cursor-pointer file-input-trigger">
                        <div class="flex-row gap-2 px-4 py-2 rounded-lg border border-dashed transition-colors" style="background:var(--bg-surface-active);color:var(--text-secondary);border-color:var(--border-default)">
                            <i data-lucide="upload" class="w-3-5 h-3-5"></i>
                            <span>Ganti Banner</span>
                        </div>
                        <input type="file" id="edit-banner" name="banner" accept="image/*" class="hidden">
                    </label>
                    <button type="button" id="remove-banner-btn" onclick="removeBanner()" class="btn btn-outline-red btn-sm gap-2" style="border-color:var(--color-primary-border)">
                        <i data-lucide="trash-2" class="w-3 h-3"></i>
                        <span>Hapus</span>
                    </button>
                </div>
                <span class="text-micro c-faint">Maks 10MB</span>
            </div>
        </div>

        <!-- Avatar + Basic Info Section -->
        <div class="card rounded-2xl border p-5 mb-4">
            <div class="flex-row items-start gap-5">
                <div class="relative flex-shrink-0" id="avatar-zone">
                    <div class="w-24 h-24 sm-w-28 sm-h-28 rounded-full overflow-hidden avatar-upload-zone" style="padding:2.5px;background:var(--bg-body)">
                        <img id="avatar-preview-img" src="<?= avatar_url($user['avatar']); ?>" alt="Avatar" class="w-full h-full rounded-full" style="object-fit:cover" onerror="this.src='<?= assets_url('default.jpg'); ?>';">
                    </div>
                    <?php if (!empty($user['border_image'])): ?>
                        <div class="absolute inset-0 w-full h-full pointer-events-none z-20" style="transform:scale(1.25);transform-origin:center">
                            <img src="<?= assets_url($user['border_image']); ?>" alt="F1 Border" class="w-full h-full" style="object-fit:contain">
                        </div>
                    <?php endif; ?>
                    <label class="absolute w-8 h-8 btn-primary rounded-full flex-row justify-center cursor-pointer shadow-xl z-30 file-input-trigger" style="bottom:-4px;right:-4px;box-shadow:0 4px 12px rgba(220,38,38,0.3)">
                        <i data-lucide="camera" class="w-4 h-4 c-white"></i>
                        <input type="file" id="edit-avatar" name="avatar" accept="image/*" class="hidden">
                    </label>
                </div>
                <div class="flex-1 space-y-3 pt-2">
                    <div>
                        <label class="form-label">Nama Tampilan</label>
                        <input type="text" id="edit-display-name" name="display_name"
                               class="w-full input rounded-xl"
                               placeholder="Nama tampilan" value="<?= htmlspecialchars($user['display_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                    <div>
                        <label class="form-label" style="text-transform:none;letter-spacing:normal">@<?= htmlspecialchars($user['username']); ?></label>
                    </div>
                    <div class="flex-row gap-2">
                        <button type="button" id="remove-avatar-btn" onclick="removeAvatar()" class="btn btn-outline-red btn-sm gap-1-5" style="font-size:11px;border-color:var(--color-primary-border)">
                            <i data-lucide="trash-2" class="w-3 h-3"></i>
                            Hapus Foto
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bio & Team Section -->
        <div class="card rounded-2xl border p-5 mb-4">
            <h3 class="form-label mb-4">Detail Profil</h3>
            <div class="space-y-4">
                <div>
                    <label class="text-xs c-muted mb-1-5 block">Bio</label>
                    <textarea id="edit-bio" name="bio" rows="4"
                              class="w-full textarea rounded-xl"
                              placeholder="Ceritakan tentang dirimu..."><?= htmlspecialchars($user['bio'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                    <p class="text-micro c-faint mt-1-5 text-right" style="font-size:10px"><span id="bio-char-count">0</span> karakter</p>
                </div>
                <?php if (isset($teams)): ?>
                <div>
                    <label class="text-xs c-muted mb-1-5 block">Favorite F1 Team</label>
                    <select name="team_id" class="w-full select rounded-xl">
                        <?php foreach ($teams as $t): ?>
                            <option value="<?= $t['team_id'] ?>" <?= ($user['team_id'] ?? 0) == $t['team_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($t['team_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex-row justify-end gap-3 mt-6">
            <a href="<?= base_url('profile'); ?>" class="btn btn-outline-cancel rounded-xl">
                Batal
            </a>
            <button type="submit" id="save-profile-btn" class="btn btn-primary rounded-xl shadow-lg gap-2">
                <i data-lucide="check" class="w-3-5 h-3-5"></i>
                Simpan
            </button>
        </div>
    </form>
</div>

<!-- CROP MODAL -->
<div id="crop-modal" class="fixed inset-0 hidden" style="z-index:600">
    <div class="absolute inset-0" style="background:rgba(0,0,0,0.8);backdrop-filter:blur(4px)" onclick="closeCropModal()"></div>
    <div class="absolute inset-0 flex-row justify-center p-4">
        <div class="card rounded-2xl w-full max-w-lg overflow-hidden" style="border-color:var(--border-default);box-shadow:var(--shadow-xl)">
            <div class="flex-row justify-between px-5 py-4 border-b" style="border-color:var(--border-subtle)">
                <h3 id="crop-modal-title" class="text-heading text-xs c-white">Potong Foto</h3>
                <button onclick="closeCropModal()" class="c-muted transition-colors" onmouseover="this.style.color='var(--text-primary)'" onmouseout="this.style.color=''">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
            <div class="p-4" style="background:var(--bg-surface-raised)">
                <div class="relative" style="max-height:60vh;overflow:hidden">
                    <img id="crop-image" src="" alt="Crop" class="block max-w-full">
                </div>
            </div>
            <div class="flex-row justify-between px-5 py-4 border-t" style="border-color:var(--border-subtle)">
                <div class="flex-row gap-2">
                    <button type="button" onclick="rotateCrop(-90)" class="link-back p-2 c-muted rounded-lg" title="Putar Kiri">
                        <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                    </button>
                    <button type="button" onclick="rotateCrop(90)" class="link-back p-2 c-muted rounded-lg" title="Putar Kanan">
                        <i data-lucide="rotate-cw" class="w-4 h-4"></i>
                    </button>
                    <button type="button" onclick="resetCrop()" class="link-back p-2 c-muted rounded-lg" title="Reset">
                        <i data-lucide="maximize" class="w-4 h-4"></i>
                    </button>
                </div>
                <div class="flex-row gap-2">
                    <button onclick="closeCropModal()" class="btn btn-outline-cancel rounded-xl">
                        Batal
                    </button>
                    <button onclick="applyCrop()" id="apply-crop-btn" class="btn btn-primary rounded-xl shadow-lg gap-1-5">
                        <i data-lucide="check" class="w-3-5 h-3-5"></i>
                        Terapkan
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let cropper = null;
let cropTarget = null;
let croppedBlob = { avatar: null, banner: null };

function openCropModal(imageSrc, type) {
    cropTarget = type;
    const modal = document.getElementById('crop-modal');
    const img = document.getElementById('crop-image');
    const title = document.getElementById('crop-modal-title');

    title.textContent = type === 'avatar' ? 'Potong Foto Profil' : 'Potong Banner';
    img.src = imageSrc;
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';

    if (cropper) cropper.destroy();

    img.onload = function() {
        const cropOptions = {
            viewMode: 1,
            dragMode: 'move',
            autoCropArea: 1,
            background: false,
            guides: true,
            highlight: true,
            cropBoxMovable: true,
            cropBoxResizable: true,
            minCropBoxWidth: 80,
            minCropBoxHeight: 80,
        };

        if (type === 'avatar') {
            cropOptions.aspectRatio = 1 / 1;
            img.parentElement.classList.add('crop-avatar');
        } else {
            cropOptions.aspectRatio = 3 / 1;
            img.parentElement.classList.remove('crop-avatar');
        }

        cropper = new Cropper(img, cropOptions);
    };
}

function closeCropModal() {
    const modal = document.getElementById('crop-modal');
    modal.classList.add('hidden');
    document.body.style.overflow = '';
    if (cropper) {
        cropper.destroy();
        cropper = null;
    }
    cropTarget = null;
    document.getElementById('crop-image').parentElement.classList.remove('crop-avatar');
}

function rotateCrop(degrees) {
    if (cropper) cropper.rotate(degrees);
}

function resetCrop() {
    if (cropper) cropper.reset();
}

function applyCrop() {
    if (!cropper) return;

    const btn = document.getElementById('apply-crop-btn');
    btn.disabled = true;
    btn.innerHTML = '<div class="spinner spinner--sm spinner-white"></div> Memproses...';

    const canvas = cropper.getCroppedCanvas({
        imageSmoothingEnabled: true,
        imageSmoothingQuality: 'high',
        maxWidth: cropTarget === 'avatar' ? 800 : 1920,
        maxHeight: cropTarget === 'avatar' ? 800 : 640,
    });

    canvas.toBlob(function(blob) {
        if (!blob) {
            btn.disabled = false;
            btn.innerHTML = '<i data-lucide="check" class="w-3-5 h-3-5"></i> Terapkan';
            lucide.createIcons();
            return;
        }

        croppedBlob[cropTarget] = blob;

        const previewUrl = URL.createObjectURL(blob);
        const preview = document.getElementById(cropTarget === 'avatar' ? 'avatar-preview-img' : 'banner-preview-img');
        const placeholder = document.getElementById('banner-preview-placeholder');

        preview.src = previewUrl;
        preview.classList.remove('hidden');
        if (placeholder) placeholder.classList.add('hidden');

        closeCropModal();

        btn.disabled = false;
        btn.innerHTML = '<i data-lucide="check" class="w-3-5 h-3-5"></i> Terapkan';
        lucide.createIcons();
    }, 'image/jpeg', 0.92);
}

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('edit-profile-form');
    const saveBtn = document.getElementById('save-profile-btn');
    const avatarInput = document.getElementById('edit-avatar');
    const bannerInput = document.getElementById('edit-banner');
    const bioTextarea = document.getElementById('edit-bio');
    const bioCharCount = document.getElementById('bio-char-count');

    // Bio char counter
    function updateBioCount() {
        bioCharCount.textContent = bioTextarea.value.length;
    }
    bioTextarea.addEventListener('input', updateBioCount);
    updateBioCount();

    // Avatar input -> open crop modal
    avatarInput.addEventListener('change', function() {
        const file = this.files[0];
        if (!file) return;
        if (file.size > 5 * 1024 * 1024) {
            alert('Ukuran file terlalu besar. Maksimal 5MB.');
            this.value = '';
            return;
        }
        const reader = new FileReader();
        reader.onload = function(e) {
            openCropModal(e.target.result, 'avatar');
        };
        reader.readAsDataURL(file);
        avatarInput.value = '';
    });

    // Banner input -> open crop modal
    bannerInput.addEventListener('change', function() {
        const file = this.files[0];
        if (!file) return;
        if (file.size > 10 * 1024 * 1024) {
            alert('Ukuran file terlalu besar. Maksimal 10MB.');
            this.value = '';
            return;
        }
        const reader = new FileReader();
        reader.onload = function(e) {
            openCropModal(e.target.result, 'banner');
        };
        reader.readAsDataURL(file);
        bannerInput.value = '';
    });

    // Remove avatar
    window.removeAvatar = function() {
        const avatarPreview = document.getElementById('avatar-preview-img');
        avatarPreview.src = '<?= assets_url('default.jpg'); ?>';
        avatarInput.value = '';
        croppedBlob.avatar = null;
        let hiddenInput = document.getElementById('remove-avatar-input');
        if (!hiddenInput) {
            hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'remove_avatar';
            hiddenInput.id = 'remove-avatar-input';
            hiddenInput.value = '1';
            form.appendChild(hiddenInput);
        }
    };

    // Remove banner
    window.removeBanner = function() {
        const bannerPreview = document.getElementById('banner-preview-img');
        const bannerPlaceholder = document.getElementById('banner-preview-placeholder');
        bannerPreview.src = '';
        bannerPreview.classList.add('hidden');
        if (bannerPlaceholder) bannerPlaceholder.classList.remove('hidden');
        bannerInput.value = '';
        croppedBlob.banner = null;
        let hiddenInput = document.getElementById('remove-banner-input');
        if (!hiddenInput) {
            hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'remove_banner';
            hiddenInput.id = 'remove-banner-input';
            hiddenInput.value = '1';
            form.appendChild(hiddenInput);
        }
    };

    // Form submit
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<div class="spinner spinner--sm spinner-white"></div> Menyimpan...';

        const formData = new FormData(this);

        // Append cropped blobs as files
        if (croppedBlob.avatar) {
            formData.delete('avatar');
            formData.append('avatar', croppedBlob.avatar, 'avatar.jpg');
        }
        if (croppedBlob.banner) {
            formData.delete('banner');
            formData.append('banner', croppedBlob.banner, 'banner.jpg');
        }

        fetch('<?= base_url("profile/edit_profile"); ?>', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                const toast = document.createElement('div');
                toast.className = 'toast toast--success';
                toast.textContent = data.message || 'Profil berhasil diperbarui!';
                document.body.appendChild(toast);
                setTimeout(() => {
                    toast.remove();
                    window.location.href = '<?= base_url('profile'); ?>';
                }, 1500);
            } else {
                alert(data.message || 'Gagal memperbarui profil.');
                saveBtn.disabled = false;
                saveBtn.innerHTML = '<i data-lucide="check" class="w-3-5 h-3-5"></i> Simpan';
                if (typeof lucide !== 'undefined') lucide.createIcons();
            }
        })
        .catch(err => {
            console.error('Error:', err);
            alert('Terjadi kesalahan. Silakan coba lagi.');
            saveBtn.disabled = false;
            saveBtn.innerHTML = '<i data-lucide="check" class="w-3-5 h-3-5"></i> Simpan';
            if (typeof lucide !== 'undefined') lucide.createIcons();
        });
    });

    // ESC to close crop modal
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !document.getElementById('crop-modal').classList.contains('hidden')) {
            closeCropModal();
        }
    });
});
</script>
</main>
