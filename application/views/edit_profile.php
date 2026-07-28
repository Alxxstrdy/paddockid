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

    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-white/[0.04]">
        <a href="<?= base_url('profile'); ?>" class="p-2 text-slate-400 hover:text-white hover:bg-white/[0.05] rounded-xl transition-colors">
            <i data-lucide="arrow-left" class="w-5 h-5"></i>
        </a>
        <h1 class="font-syne text-sm uppercase tracking-tight text-white">Edit Profil</h1>
    </div>

    <form id="edit-profile-form" enctype="multipart/form-data">
        <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">

        <!-- Banner Section -->
        <div class="glass-card rounded-2xl overflow-hidden shadow-2xl border border-white/[0.06] mb-4">
            <div class="h-40 sm:h-52 w-full relative bg-gradient-to-r from-red-950/40 to-slate-900 overflow-hidden banner-upload-zone" id="banner-zone">
                <?php if (!empty($user['banner'])): ?>
                    <img id="banner-preview-img" src="<?= base_url($user['banner']); ?>" alt="Banner" class="w-full h-full object-cover">
                <?php else: ?>
                    <div id="banner-preview-placeholder" class="absolute inset-0 flex flex-col items-center justify-center gap-2">
                        <i data-lucide="image" class="w-8 h-8 text-slate-600"></i>
                        <span class="text-[10px] text-slate-500 uppercase tracking-wider">Belum ada banner</span>
                    </div>
                    <img id="banner-preview-img" src="" alt="Banner" class="w-full h-full object-cover hidden">
                <?php endif; ?>
                <div class="absolute inset-0 bg-gradient-to-t from-[#05070c]/80 via-transparent to-transparent"></div>
            </div>
            <div class="px-5 py-3 flex items-center justify-between border-t border-white/[0.03]">
                <div class="flex items-center gap-3">
                    <label class="cursor-pointer file-input-trigger">
                        <div class="flex items-center gap-2 px-4 py-2 bg-slate-800 hover:bg-slate-700 text-xs text-slate-300 rounded-lg border border-white/[0.06] border-dashed transition-colors">
                            <i data-lucide="upload" class="w-3.5 h-3.5"></i>
                            <span>Ganti Banner</span>
                        </div>
                        <input type="file" id="edit-banner" name="banner" accept="image/*" class="hidden">
                    </label>
                    <button type="button" id="remove-banner-btn" onclick="removeBanner()" class="flex items-center gap-2 px-4 py-2 bg-red-600/10 hover:bg-red-600/20 text-red-400 hover:text-red-300 text-xs rounded-lg border border-red-600/20 transition-colors">
                        <i data-lucide="trash-2" class="w-3 h-3"></i>
                        <span>Hapus</span>
                    </button>
                </div>
                <span class="text-[10px] text-slate-600">Maks 10MB</span>
            </div>
        </div>

        <!-- Avatar + Basic Info Section -->
        <div class="glass-card rounded-2xl border border-white/[0.06] p-5 mb-4">
            <div class="flex items-start gap-5">
                <div class="relative flex-shrink-0" id="avatar-zone">
                    <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-full p-[2.5px] bg-slate-950 ring-2 ring-white/[0.08] overflow-hidden avatar-upload-zone">
                        <img id="avatar-preview-img" src="<?= avatar_url($user['avatar']); ?>" alt="Avatar" class="w-full h-full object-cover rounded-full" onerror="this.src='<?= assets_url('default.jpg'); ?>';">
                    </div>
                    <?php if (!empty($user['border_image'])): ?>
                        <div class="absolute inset-0 w-full h-full pointer-events-none scale-[1.25] transform origin-center z-20">
                            <img src="<?= assets_url($user['border_image']); ?>" alt="F1 Border" class="w-full h-full object-contain">
                        </div>
                    <?php endif; ?>
                    <label class="absolute -bottom-1 -right-1 w-8 h-8 bg-red-600 hover:bg-red-500 rounded-full flex items-center justify-center cursor-pointer shadow-lg shadow-red-600/30 transition-colors z-30 file-input-trigger">
                        <i data-lucide="camera" class="w-4 h-4 text-white"></i>
                        <input type="file" id="edit-avatar" name="avatar" accept="image/*" class="hidden">
                    </label>
                </div>
                <div class="flex-1 space-y-3 pt-2">
                    <div>
                        <label class="text-[10px] text-slate-500 uppercase tracking-wider mb-1.5 block font-semibold">Nama Tampilan</label>
                        <input type="text" id="edit-display-name" name="display_name"
                               class="w-full bg-slate-800 text-sm text-slate-200 placeholder-slate-500 focus:outline-none border border-white/[0.06] rounded-xl px-4 py-2.5 focus:border-red-500/50 transition-colors"
                               placeholder="Nama tampilan" value="<?= htmlspecialchars($user['display_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                    <div>
                        <label class="text-[10px] text-slate-500 uppercase tracking-wider mb-1.5 block font-semibold">@<?= htmlspecialchars($user['username']); ?></label>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" id="remove-avatar-btn" onclick="removeAvatar()" class="flex items-center gap-1.5 px-3 py-1.5 bg-red-600/10 hover:bg-red-600/20 text-red-400 hover:text-red-300 text-[11px] rounded-lg border border-red-600/20 transition-colors">
                            <i data-lucide="trash-2" class="w-3 h-3"></i>
                            Hapus Foto
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bio & Team Section -->
        <div class="glass-card rounded-2xl border border-white/[0.06] p-5 mb-4">
            <h3 class="text-[10px] text-slate-500 uppercase tracking-wider mb-4 font-semibold">Detail Profil</h3>
            <div class="space-y-4">
                <div>
                    <label class="text-xs text-slate-400 mb-1.5 block">Bio</label>
                    <textarea id="edit-bio" name="bio" rows="4"
                              class="w-full bg-slate-800 text-sm text-slate-200 placeholder-slate-500 focus:outline-none border border-white/[0.06] rounded-xl px-4 py-3 focus:border-red-500/50 transition-colors resize-none"
                              placeholder="Ceritakan tentang dirimu..."><?= htmlspecialchars($user['bio'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                    <p class="text-[10px] text-slate-600 mt-1.5 text-right"><span id="bio-char-count">0</span> karakter</p>
                </div>
                <?php if (isset($teams)): ?>
                <div>
                    <label class="text-xs text-slate-400 mb-1.5 block">Favorite F1 Team</label>
                    <select name="team_id" class="w-full bg-slate-800 text-sm text-slate-200 focus:outline-none border border-white/[0.06] rounded-xl px-4 py-2.5 focus:border-red-500/50 transition-colors appearance-none cursor-pointer">
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
        <div class="flex items-center justify-end gap-3 mt-6">
            <a href="<?= base_url('profile'); ?>" class="px-5 py-2.5 text-xs font-semibold text-slate-300 bg-white/[0.05] hover:bg-white/[0.08] rounded-xl transition-colors border border-white/[0.06]">
                Batal
            </a>
            <button type="submit" id="save-profile-btn" class="px-6 py-2.5 text-xs font-semibold text-white bg-red-600 hover:bg-red-500 rounded-xl transition-colors shadow-lg shadow-red-600/10 flex items-center gap-2 active:scale-[0.98]">
                <i data-lucide="check" class="w-3.5 h-3.5"></i>
                Simpan
            </button>
        </div>
    </form>
</div>

<!-- CROP MODAL -->
<div id="crop-modal" class="fixed inset-0 z-[100] hidden">
    <div class="absolute inset-0 bg-black/80 backdrop-blur-sm" onclick="closeCropModal()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="glass-card rounded-2xl w-full max-w-lg border border-white/[0.06] shadow-2xl overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-white/[0.04]">
                <h3 id="crop-modal-title" class="font-syne text-xs uppercase tracking-tight text-white">Potong Foto</h3>
                <button onclick="closeCropModal()" class="text-slate-400 hover:text-white transition-colors">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
            <div class="p-4 bg-slate-900/50">
                <div class="relative" style="max-height: 60vh; overflow: hidden;">
                    <img id="crop-image" src="" alt="Crop" class="block max-w-full">
                </div>
            </div>
            <div class="flex items-center justify-between px-5 py-4 border-t border-white/[0.04]">
                <div class="flex items-center gap-2">
                    <button type="button" onclick="rotateCrop(-90)" class="p-2 text-slate-400 hover:text-white hover:bg-white/[0.05] rounded-lg transition-colors" title="Putar Kiri">
                        <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                    </button>
                    <button type="button" onclick="rotateCrop(90)" class="p-2 text-slate-400 hover:text-white hover:bg-white/[0.05] rounded-lg transition-colors" title="Putar Kanan">
                        <i data-lucide="rotate-cw" class="w-4 h-4"></i>
                    </button>
                    <button type="button" onclick="resetCrop()" class="p-2 text-slate-400 hover:text-white hover:bg-white/[0.05] rounded-lg transition-colors" title="Reset">
                        <i data-lucide="maximize" class="w-4 h-4"></i>
                    </button>
                </div>
                <div class="flex gap-2">
                    <button onclick="closeCropModal()" class="px-4 py-2 text-xs font-semibold text-slate-300 bg-white/[0.05] hover:bg-white/[0.08] rounded-xl transition-colors border border-white/[0.06]">
                        Batal
                    </button>
                    <button onclick="applyCrop()" id="apply-crop-btn" class="px-4 py-2 text-xs font-semibold text-white bg-red-600 hover:bg-red-500 rounded-xl transition-colors shadow-lg shadow-red-600/10 flex items-center gap-1.5">
                        <i data-lucide="check" class="w-3.5 h-3.5"></i>
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
    btn.innerHTML = '<div class="w-3.5 h-3.5 border-2 border-white border-t-transparent rounded-full animate-spin"></div> Memproses...';

    const canvas = cropper.getCroppedCanvas({
        imageSmoothingEnabled: true,
        imageSmoothingQuality: 'high',
        maxWidth: cropTarget === 'avatar' ? 800 : 1920,
        maxHeight: cropTarget === 'avatar' ? 800 : 640,
    });

    canvas.toBlob(function(blob) {
        if (!blob) {
            btn.disabled = false;
            btn.innerHTML = '<i data-lucide="check" class="w-3.5 h-3.5"></i> Terapkan';
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
        btn.innerHTML = '<i data-lucide="check" class="w-3.5 h-3.5"></i> Terapkan';
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
        saveBtn.innerHTML = '<div class="w-3.5 h-3.5 border-2 border-white border-t-transparent rounded-full animate-spin"></div> Menyimpan...';

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
                toast.className = 'fixed bottom-20 left-1/2 -translate-x-1/2 z-[9999] bg-emerald-600 text-white text-xs font-semibold px-4 py-2.5 rounded-xl shadow-lg shadow-emerald-600/20';
                toast.textContent = data.message || 'Profil berhasil diperbarui!';
                document.body.appendChild(toast);
                setTimeout(() => {
                    toast.remove();
                    window.location.href = '<?= base_url('profile'); ?>';
                }, 1500);
            } else {
                alert(data.message || 'Gagal memperbarui profil.');
                saveBtn.disabled = false;
                saveBtn.innerHTML = '<i data-lucide="check" class="w-3.5 h-3.5"></i> Simpan';
                if (typeof lucide !== 'undefined') lucide.createIcons();
            }
        })
        .catch(err => {
            console.error('Error:', err);
            alert('Terjadi kesalahan. Silakan coba lagi.');
            saveBtn.disabled = false;
            saveBtn.innerHTML = '<i data-lucide="check" class="w-3.5 h-3.5"></i> Simpan';
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
