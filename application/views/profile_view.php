<style>
    body {
        font-family: 'Plus Jakarta Sans', sans-serif;
        background-color: #05070c;
        background-image: radial-gradient(circle at 50% 0%, rgba(255, 24, 24, 0.03) 0%, transparent 40%);
    }
    .font-syne { font-family: 'Syne', sans-serif; }
    .glass-card {
        background: rgba(15, 22, 38, 0.4);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.04);
    }
</style>

<div class="flex-1 max-w-2xl w-full mx-auto px-4 py-6">
    
    <div class="glass-card rounded-2xl overflow-hidden shadow-2xl relative border border-white/[0.06] mb-8">
    
    <div class="h-36 sm:h-48 w-full relative bg-gradient-to-r from-red-950/40 to-slate-900 overflow-hidden">
        <?php if (!empty($user['banner'])): ?>
            <img src="<?= base_url($user['banner']); ?>" alt="User Banner" class="w-full h-full object-cover">
        <?php else: ?>
            <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-red-900/20 via-slate-950 to-slate-950"></div>
        <?php endif; ?>
        <div class="absolute inset-0 bg-gradient-to-t from-[#05070c]/90 via-[#05070c]/30 to-transparent"></div>
    </div>

    <div class="px-5 pb-5 relative -mt-14 sm:-mt-16">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-4">
            
            <div class="relative w-24 h-24 sm:w-28 sm:h-28 flex-shrink-0 mx-auto sm:mx-0" data-user-id="<?= $user['id_user']; ?>">
                <div class="w-full h-full rounded-full p-[2.5px] bg-slate-950 ring-2 ring-white/[0.08] overflow-hidden">
                    <img src="<?= avatar_url($user['avatar']); ?>" 
                         alt="Avatar" class="w-full h-full object-cover rounded-full"
                         onerror="this.src='<?= assets_url('default.jpg'); ?>';">
                </div>
                <?php if (!empty($user['border_image'])): ?>
                    <div class="absolute inset-0 w-full h-full pointer-events-none scale-[1] transform origin-center z-20">
                        <img src="<?= assets_url($user['border_image']); ?>" alt="F1 Border" class="w-full h-full object-contain">
                    </div>
                <?php endif; ?>
                <?php if (!empty($user['is_online'])): ?>
                    <div class="online-indicator"></div>
                <?php endif; ?>
            </div>

            <div class="text-center sm:text-right flex flex-col items-center sm:items-end gap-2">                    
                <button onclick="openEditProfileModal()" class="bg-white/[0.05] hover:bg-red-600 text-slate-200 hover:text-white text-[11px] font-semibold px-4 py-2 rounded-xl border border-white/[0.06] transition-all duration-300">
                    Edit Profil
                </button>
            </div>
            
        </div>

        <div class="text-center sm:text-left space-y-2">
            <div class="flex items-center justify-center sm:justify-start gap-2">
                <h2 class="font-syne text-lg sm:text-xl uppercase tracking-tight text-white">
                    <?= htmlspecialchars(!empty($user['display_name']) ? $user['display_name'] : $user['username'], ENT_QUOTES, 'UTF-8'); ?>
                </h2>
                <?php if ($user['verified'] == 1): ?>
                    <span class="text-red-500" title="Verified Driver"><i data-lucide="badge-check" class="w-4 h-4 fill-red-500/10"></i></span>
                <?php endif; ?>
            </div>
            <p class="text-xs text-slate-400 -mt-1">@<?= $user['username']; ?></p>
            
            <div class="flex items-center justify-center sm:justify-start gap-5 text-xs pt-1">
                <button onclick="openFollowModal('following')" class="hover:text-red-400 transition-colors duration-200 flex gap-1 cursor-pointer">
                    <span class="font-bold text-white"><?= number_format($user['total_following']); ?></span>
                    <span class="text-slate-400">Following</span>
                </button>
                <button onclick="openFollowModal('followers')" class="hover:text-red-400 transition-colors duration-200 flex gap-1 cursor-pointer">
                    <span class="font-bold text-white"><?= number_format($user['total_followers']); ?></span>
                    <span class="text-slate-400">Followers</span>
                </button>
            </div>
            
            <p class="text-xs sm:text-sm text-slate-300 leading-relaxed pt-3 border-t border-white/[0.03]">
                <?= !empty($user['bio']) ? nl2br(htmlentities($user['bio'])) : '<span class="text-slate-500 italic">Belum ada biografi yang ditulis.</span>'; ?>
            </p>

            <?php if (!empty($user['team_name'])): ?>
                <div class="flex items-center justify-center sm:justify-start gap-2 pt-2">
                    <span class="inline-flex items-center gap-1.5 text-[10px] font-semibold uppercase tracking-wider px-2 py-0.5 rounded-full border border-white/[0.08]" style="background:<?= $user['team_color'] ?? '#666' ?>15;">
                        <img src="<?= assets_url($user['team_logo']) ?>" alt="<?= htmlspecialchars($user['team_name']) ?>" class="w-4 h-4 object-contain">
                        <?= htmlspecialchars($user['team_name']) ?>
                    </span>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

    <div class="flex border-b border-white/[0.04] mb-6 text-xs sm:text-sm font-semibold text-slate-400">
        <button onclick="switchTab('uploads')" id="tab-uploads" class="flex-1 py-3 text-center border-b-2 border-red-500 text-white transition-all duration-300 flex items-center justify-center gap-2">
            <i data-lucide="grid" class="w-4 h-4"></i> Postingan Saya
        </button>
        <button onclick="switchTab('liked')" id="tab-liked" class="flex-1 py-3 text-center border-b-2 border-transparent hover:text-slate-200 transition-all duration-300 flex items-center justify-center gap-2">
            <i data-lucide="heart" class="w-4 h-4"></i> Menyukai
        </button>
    </div>

    <div id="post-container" class="space-y-4">
        </div>

    <div id="loading-badge" class="py-8 text-center flex justify-center items-center hidden">
        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-slate-950 border border-white/[0.05]">
            <div class="w-3 h-3 border-2 border-red-500 border-t-transparent rounded-full animate-spin"></div>
            <span class="text-xs text-slate-400 font-medium">Memuat data paddock...</span>
        </div>
    </div>

</div>

<!-- FOLLOW MODAL -->
<div id="follow-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeFollowModal()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="glass-card rounded-2xl w-full max-w-sm max-h-[70vh] flex flex-col border border-white/[0.06] shadow-2xl">
            <div class="flex items-center justify-between px-5 py-4 border-b border-white/[0.04]">
                <h3 id="follow-modal-title" class="font-syne text-sm uppercase tracking-tight text-white">Following</h3>
                <button onclick="closeFollowModal()" class="text-slate-400 hover:text-white transition-colors">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
            <div id="follow-modal-body" class="overflow-y-auto no-scrollbar flex-1">
                <div class="flex items-center justify-center py-8">
                    <div class="w-5 h-5 border-2 border-red-500 border-t-transparent rounded-full animate-spin"></div>
                </div>
            </div>
        </div>
    </div>
    </div>
</main>

<!-- EDIT PROFILE MODAL -->
<div id="edit-profile-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeEditProfileModal()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="glass-card rounded-2xl w-full max-w-md border border-white/[0.06] shadow-2xl p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-syne text-sm uppercase tracking-tight text-white">Edit Profil</h3>
                <button onclick="closeEditProfileModal()" class="text-slate-400 hover:text-white transition-colors">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <form id="edit-profile-form" enctype="multipart/form-data">
                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                <div class="space-y-4">
                    <div>
                        <label class="text-[10px] text-slate-500 uppercase tracking-wider mb-1 block">Nama Tampilan</label>
                        <input type="text" id="edit-display-name" name="display_name" 
                               class="w-full bg-slate-800 text-xs sm:text-sm text-slate-200 placeholder-slate-500 focus:outline-none border border-white/[0.06] rounded-lg px-3 py-2.5 focus:border-red-500/50 transition-colors"
                               placeholder="Nama tampilan" value="<?= htmlspecialchars($user['display_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                    <div>
                        <label class="text-[10px] text-slate-500 uppercase tracking-wider mb-1 block">Bio</label>
                        <textarea id="edit-bio" name="bio" rows="3" 
                                  class="w-full bg-slate-800 text-xs sm:text-sm text-slate-200 placeholder-slate-500 focus:outline-none border border-white/[0.06] rounded-lg px-3 py-2.5 focus:border-red-500/50 transition-colors resize-none"
                                  placeholder="Ceritakan tentang dirimu..."><?= htmlspecialchars($user['bio'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                    </div>
                    <?php if (isset($teams)): ?>
                    <div>
                        <label class="text-[10px] text-slate-500 uppercase tracking-wider mb-1 block">Favorite Team</label>
                        <select name="team_id" class="w-full bg-slate-800 text-xs sm:text-sm text-slate-200 focus:outline-none border border-white/[0.06] rounded-lg px-3 py-2.5 focus:border-red-500/50 transition-colors">
                            <?php foreach ($teams as $t): ?>
                                <option value="<?= $t['team_id'] ?>" <?= ($user['team_id'] ?? 0) == $t['team_id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($t['team_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php endif; ?>
                    <div>
                        <label class="text-[10px] text-slate-500 uppercase tracking-wider mb-1 block">Foto Profil</label>
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-full overflow-hidden bg-slate-800 shrink-0 ring-2 ring-white/[0.06]">
                                <img id="avatar-preview-img" src="<?= avatar_url($user['avatar']); ?>" alt="Avatar" class="w-full h-full object-cover">
                            </div>
                            <div class="flex flex-col gap-1.5 flex-1">
                                <label class="cursor-pointer">
                                    <div class="text-center px-4 py-2 bg-slate-800 hover:bg-slate-700 text-xs text-slate-300 rounded-lg border border-white/[0.06] border-dashed transition-colors">
                                        <i data-lucide="upload" class="w-3.5 h-3.5 inline-block mr-1"></i> Ganti Foto
                                    </div>
                                    <input type="file" id="edit-avatar" name="avatar" accept="image/*" class="hidden">
                                </label>
                                <button type="button" id="remove-avatar-btn" onclick="removeAvatar()" class="text-center px-4 py-2 bg-red-600/10 hover:bg-red-600/20 text-red-400 hover:text-red-300 text-xs rounded-lg border border-red-600/20 transition-colors">
                                    <i data-lucide="trash-2" class="w-3 h-3 inline-block mr-1"></i> Hapus Foto
                                </button>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="text-[10px] text-slate-500 uppercase tracking-wider mb-1 block">Banner</label>
                        <div class="flex items-center gap-3">
                            <div class="w-20 h-12 rounded-lg overflow-hidden bg-gradient-to-r from-red-950/40 to-slate-900 shrink-0 ring-1 ring-white/[0.06]">
                                <img id="banner-preview-img" src="<?= !empty($user['banner']) ? base_url($user['banner']) : ''; ?>" alt="Banner" class="w-full h-full object-cover">
                            </div>
                            <div class="flex flex-col gap-1.5 flex-1">
                                <label class="cursor-pointer">
                                    <div class="text-center px-4 py-2 bg-slate-800 hover:bg-slate-700 text-xs text-slate-300 rounded-lg border border-white/[0.06] border-dashed transition-colors">
                                        <i data-lucide="image" class="w-3.5 h-3.5 inline-block mr-1"></i> Ganti Banner
                                    </div>
                                    <input type="file" id="edit-banner" name="banner" accept="image/*" class="hidden">
                                </label>
                                <button type="button" id="remove-banner-btn" onclick="removeBanner()" class="text-center px-4 py-2 bg-red-600/10 hover:bg-red-600/20 text-red-400 hover:text-red-300 text-xs rounded-lg border border-red-600/20 transition-colors">
                                    <i data-lucide="trash-2" class="w-3 h-3 inline-block mr-1"></i> Hapus Banner
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex justify-between gap-3 mt-6 pt-4 border-t border-white/[0.04]">
                    <a href="<?= base_url('auth/logout'); ?>" class="px-4 py-2.5 text-xs font-semibold text-red-400 bg-red-600/10 hover:bg-red-600/20 rounded-xl transition-colors border border-red-600/20">
                        <i data-lucide="log-out" class="w-3.5 h-3.5 inline-block mr-1"></i> Logout
                    </a>
                    <div class="flex gap-3">
                        <button type="button" onclick="closeEditProfileModal()" class="px-4 py-2.5 text-xs font-semibold text-slate-300 bg-white/[0.05] hover:bg-white/[0.08] rounded-xl transition-colors border border-white/[0.06]">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2.5 text-xs font-semibold text-white bg-red-600 hover:bg-red-500 rounded-xl transition-colors shadow-lg shadow-red-600/10">
                            Simpan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let currentTab = 'uploads'; 
    let offset = 0;             
    const limit = 5;      
    let isLoading = false;
    let hasMoreData = true;

    window.addEventListener('scroll', () => {
        if (isLoading || !hasMoreData) return;
        if (window.innerHeight + window.scrollY >= document.documentElement.scrollHeight - 150) {
            loadMorePosts();
        }
    });

    document.addEventListener("DOMContentLoaded", () => {
        loadMorePosts();
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeFollowModal();
        });
    });

    function switchTab(tabType) {
        if (currentTab === tabType) return;
        
        currentTab = tabType;
        offset = 0;
        hasMoreData = true;
        isLoading = false;
        
        const tabUploads = document.getElementById('tab-uploads');
        const tabLiked = document.getElementById('tab-liked');
        
        if(tabType === 'uploads') {
            tabUploads.className = "flex-1 py-3 text-center border-b-2 border-red-500 text-white transition-all duration-300 flex items-center justify-center gap-2";
            tabLiked.className = "flex-1 py-3 text-center border-b-2 border-transparent text-slate-400 hover:text-slate-200 transition-all duration-300 flex items-center justify-center gap-2";
        } else {
            tabLiked.className = "flex-1 py-3 text-center border-b-2 border-red-500 text-white transition-all duration-300 flex items-center justify-center gap-2";
            tabUploads.className = "flex-1 py-3 text-center border-b-2 border-transparent text-slate-400 hover:text-slate-200 transition-all duration-300 flex items-center justify-center gap-2";
        }

        document.getElementById('post-container').innerHTML = '';
        loadMorePosts();
    }

    const userId = <?= isset($user['id_user']) ? $user['id_user'] : 0; ?>;

    function openFollowModal(type) {
        const modal = document.getElementById('follow-modal');
        const title = document.getElementById('follow-modal-title');
        const body = document.getElementById('follow-modal-body');

        title.textContent = type === 'following' ? 'Following' : 'Followers';
        body.innerHTML = `
            <div class="flex items-center justify-center py-8">
                <div class="w-5 h-5 border-2 border-red-500 border-t-transparent rounded-full animate-spin"></div>
            </div>
        `;
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';

        fetch(`<?= base_url('profile/get_follows_ajax'); ?>?type=${type}&user_id=${userId}`)
            .then(r => {
                if (!r.ok) return r.json().then(e => { throw new Error(e.error || 'Server error'); });
                return r.json();
            })
            .then(data => {
                if (data.length === 0) {
                    body.innerHTML = `<div class="text-center py-10 text-slate-500 text-xs uppercase tracking-wider">Tidak ada ${type}</div>`;
                    return;
                }
                body.innerHTML = data.map(user => {
                    const borderClass = user.border_image ? 'w-[84%] h-[84%]' : 'w-full h-full';
                    const borderHTML = user.border_image
                        ? `<div class="absolute inset-0 w-full h-full pointer-events-none scale-[1] transform origin-center">
                               <img src="${user.border_image}" alt="" class="w-full h-full object-contain">
                           </div>`
                        : '';
                    const verifiedHTML = user.verified == 1
                        ? `<span class="text-red-500 inline-flex"><i data-lucide="badge-check" class="w-3 h-3 fill-red-500/10"></i></span>`
                        : '';
                    const onlineHTML = user.is_online ? '<div class="online-indicator"></div>' : '';
                    return `
                        <a href="<?= base_url('user/'); ?>${user.username}" class="flex items-center gap-3 px-5 py-3 hover:bg-white/[0.02] transition-colors border-b border-white/[0.02] last:border-0">
                            <div class="relative w-9 h-9 flex items-center justify-center flex-shrink-0">
                                <div class="${borderClass} rounded-full overflow-hidden bg-slate-800">
                                    <img src="${user.avatar}" alt="" class="w-full h-full object-cover rounded-full" onerror="this.src='<?= assets_url('default.jpg'); ?>';">
                                </div>
                                ${borderHTML}
                                ${onlineHTML}
                            </div>
                            <div class="flex flex-col min-w-0">
                                <div class="flex items-center gap-1.5">
                                    <span class="font-semibold text-xs text-white truncate">${user.display_name || user.username}</span>
                                    ${verifiedHTML}
                                </div>
                                <span class="text-[10px] text-slate-500 truncate">@${user.username}</span>
                            </div>
                        </a>
                    `;
                }).join('');
                if (typeof lucide !== 'undefined') lucide.createIcons();
            })
            .catch(err => {
                body.innerHTML = `<div class="text-center py-10 text-red-400 text-xs">${err.message || 'Gagal memuat data'}</div>`;
            });
    }

    function closeFollowModal() {
        document.getElementById('follow-modal').classList.add('hidden');
        document.body.style.overflow = '';
    }

    function loadMorePosts() {
        isLoading = true;
        const loadingBadge = document.getElementById('loading-badge');
        loadingBadge.classList.remove('hidden'); 
        
        const url = `<?= base_url('profile/get_profile_posts_ajax'); ?>?type=${currentTab}&offset=${offset}&limit=${limit}`;
        
        fetch(url)
            .then(r => {
                if (!r.ok) return r.json().then(e => { throw new Error(e.error || 'Server error'); });
                return r.json();
            })
            .then(data => {
                const container = document.getElementById('post-container');

                if (data.length === 0) {
                    hasMoreData = false;
                    isLoading = false;
                    loadingBadge.classList.remove('hidden');
                    loadingBadge.innerHTML = "<div class='text-slate-500 uppercase tracking-wider text-[10px] py-4'>Kamu telah mencapai batas akhir postingan</div>";
                    return;
                }
                
                data.forEach(post => {
                    const avatarClass = post.border ? 'w-[84%] h-[84%]' : 'w-full h-full';
                    const avatarBorderHTML = post.border 
                        ? `<div class="absolute inset-0 w-full h-full pointer-events-none scale-[1] transform origin-center">
                            <img src="${post.border}" alt="F1 Border Decoration" class="w-full h-full object-contain">
                           </div>` 
                        : '';
                    const onlineHTML = post.is_online ? '<div class="online-indicator"></div>' : '';

                    let mediaHTML = '';
                    if (post.file_url) {
                        const images = post.file_url.split(',').map(img => img.trim());
                        const totalImages = images.length;
                        let gridClass = '';
                        let imagesTemplate = '';

                        if (totalImages === 1) {
                            gridClass = 'grid-cols-1 aspect-[4/3]';
                        } else if (totalImages === 2) {
                            gridClass = 'grid-cols-2 aspect-[4/3] gap-1';
                        } else if (totalImages === 3) {
                            gridClass = 'grid-cols-2 aspect-[4/3] gap-1';
                        } else {
                            gridClass = 'grid-cols-2 grid-rows-2 aspect-[4/3] gap-1';
                        }

                        const imagesToShow = images.slice(0, 4);
                        imagesToShow.forEach((url, index) => {
                            const itemClass = (totalImages === 3 && index === 0) ? 'row-span-2 h-full' : 'h-full';
                            imagesTemplate += `
                                <div class="relative w-full ${itemClass} overflow-hidden bg-slate-950">
                                    <img src="${url}" alt="Post Media" loading="lazy" class="w-full h-full object-cover">
                                </div>
                            `;
                        });

                        mediaHTML = `
                            <div class="px-4 sm:px-5 mb-1">
                                <div class="grid ${gridClass} bg-slate-900 border border-white/[0.03] rounded-lg overflow-hidden">
                                    ${imagesTemplate}
                                </div>
                            </div>
                        `;
                    }

                    const dynamicLikeBtnClass = post.is_liked ? 'text-red-500' : 'hover:text-red-500';
                    const dynamicLikeIconClass = post.is_liked ? 'fill-red-500 text-red-500' : '';

                    const cardHTML = `
                        <article class="glass-card rounded-xl overflow-hidden group transition-all duration-300 relative hover:bg-white/[0.02]" data-post-id="${post.id_post}" data-user-id="${post.user_id}">
                            <a href="<?= base_url('post/'); ?>${post.username}/${post.id_post}" class="absolute inset-0 z-10"></a>
                            
                            <div class="p-4 sm:p-5 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="relative w-9 h-9 flex items-center justify-center select-none z-20">
                                        <div class="${avatarClass} rounded-full overflow-hidden bg-slate-800">
                                            <a href="<?= base_url('user/'); ?>${post.username}">
                                                <img src="${post.avatar}" alt="User" class="w-full h-full object-cover rounded-full">
                                            </a>
                                        </div>
                                        ${avatarBorderHTML}
                                        ${onlineHTML}
                                    </div>
                                    
                                    <div class="flex flex-col justify-center">
                                        <div class="flex items-center gap-2">
                                            <a href="<?= base_url('user/'); ?>${post.username}" class="font-semibold text-xs sm:text-sm hover:text-red-400 cursor-pointer transition-colors relative z-20">${post.username}</a>
                                            ${post.team_name ? '<span class="inline-flex items-center gap-1 text-[9px] font-semibold uppercase tracking-wider px-1.5 py-0.5 rounded-full border border-white/[0.08]" style="background:' + (post.team_color || '#666') + '15;"><img src="<?= base_url(''); ?>' + post.team_logo + '" alt="' + post.team_name + '" class="w-3 h-3 object-contain"> ' + post.team_name + '</span>' : ''}
                                            <span class="text-slate-600 text-[10px]">•</span>
                                            <span class="inline-flex items-center text-[8px] px-1.5 py-0.5 font-semibold text-white bg-white/[0.04] border border-white/[0.06] rounded-full uppercase tracking-wider">${post.category}</span>
                                        </div>
                                        <span class="text-[10px] text-slate-500 mt-0.5">${post.created_at}</span>
                                    </div>
                                </div>
                                
                                <div class="relative z-30 flex items-center">
                                    <button onclick="toggleDropdown(event, ${post.id_post})" class="text-slate-500 hover:text-slate-300 transition-colors p-1 rounded-md hover:bg-white/[0.05]">
                                        <i data-lucide="more-horizontal" class="w-4 h-4"></i>
                                    </button>
                                    <div id="dropdown-${post.id_post}" class="hidden absolute right-0 top-8 w-36 bg-slate-900/95 backdrop-blur-md border border-white/[0.08] rounded-lg shadow-xl overflow-hidden py-1 text-xs text-slate-300">
                                        <button 
                                            onclick="copyPostLink(event, '<?= base_url('post/'); ?>${post.username}/${post.id_post}', this)"
                                            class="w-full text-left px-3 py-2 hover:bg-white/[0.05] hover:text-white flex items-center gap-2 transition-colors"
                                        >
                                            <i data-lucide="link" class="w-3.5 h-3.5"></i>
                                            <span>Copy Link</span>
                                        </button>
                                        <button 
                                            onclick="event.stopPropagation(); openEditPostModal(${post.id_post}, '${escapeJsString(post.content)}', '${post.post_category || ''}')"
                                            class="w-full text-left px-3 py-2 hover:bg-white/[0.05] hover:text-white flex items-center gap-2 transition-colors border-t border-white/[0.03]"
                                        >
                                            <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                                            <span>Edit</span>
                                        </button>
                                        <button 
                                            onclick="event.stopPropagation(); deletePost(${post.id_post})"
                                            class="w-full text-left px-3 py-2 hover:bg-red-500/10 hover:text-red-400 flex items-center gap-2 transition-colors border-t border-white/[0.03]"
                                        >
                                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                            <span>Hapus</span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            ${mediaHTML}

                            <div class="p-4 sm:p-5 pt-2 space-y-3">
                                <p class="text-xs sm:text-sm text-slate-300 leading-relaxed">${post.content}</p>
                                
                                <div class="flex items-center gap-4 pt-2 border-t border-white/[0.03] text-slate-400 text-[11px] sm:text-xs relative z-20">
                                    <button onclick="toggleLike(event, ${post.id_post}, this)" class="flex items-center gap-1.5 transition-colors group/btn ${dynamicLikeBtnClass}">
                                        <i data-lucide="heart" class="w-4 h-4 ${dynamicLikeIconClass}"></i>
                                        <span class="count-likes font-semibold">${post.likes_count}</span>
                                    </button>
                                    <a href="<?= base_url('post/'); ?>${post.username}/${post.id_post}" class="flex items-center gap-1.5 hover:text-blue-400 transition-colors">
                                        <i data-lucide="message-square" class="w-4 h-4"></i>
                                        <span class="font-semibold">${post.comments_count}</span>
                                    </a>
                                </div>
                            </div>
                        </article>
                    `;
                    
                    container.insertAdjacentHTML('beforeend', cardHTML);
                });

                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }

                offset += limit;               
                isLoading = false;             
                loadingBadge.classList.add('hidden'); 
            })
            .catch(err => {
                console.error('Gagal memproses lazy-load posts:', err);
                isLoading = false;
                loadingBadge.classList.add('hidden');
                if (offset === 0) {
                    document.getElementById('post-container').innerHTML = '<div class="glass-card p-8 text-center text-slate-500 text-xs">Gagal memuat postingan. Silakan muat ulang halaman.</div>';
                }
                hasMoreData = false;
            });
    }

    function toggleLike(event, idPost, buttonElement) {
        event.preventDefault();
        event.stopPropagation();

        // Cek login: jika guest, tampilkan modal login
        if (!IS_LOGGED_IN) {
            showLoginModal();
            return;
        }

        const icon = buttonElement.querySelector('[data-lucide="heart"]');
        const countSpan = buttonElement.querySelector('.count-likes');

        const url = `<?= base_url('home/toggle_like_post'); ?>/${idPost}`;

        fetch(url, { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: getCsrfField() })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    if (data.action === 'liked') {
                        buttonElement.classList.remove('hover:text-red-500');
                        buttonElement.classList.add('text-red-500');
                        icon.classList.add('fill-red-500', 'text-red-500');
                    } else {
                        buttonElement.classList.remove('text-red-500');
                        buttonElement.classList.add('hover:text-red-500');
                        icon.classList.remove('fill-red-500', 'text-red-500');
                    }
                    countSpan.innerText = data.likes_count;
                }
            })
            .catch(err => console.error('Gagal memproses like:', err));
    }

    // Toggle Dropdown untuk menu postingan
    function toggleDropdown(event, postId) {
        event.preventDefault();
        event.stopPropagation();
        document.querySelectorAll('[id^="dropdown-"]').forEach(dropdown => {
            if (dropdown.id !== `dropdown-${postId}`) dropdown.classList.add('hidden');
        });
        const targetDropdown = document.getElementById(`dropdown-${postId}`);
        if (targetDropdown) targetDropdown.classList.toggle('hidden');
    }

    document.addEventListener('click', function() {
        document.querySelectorAll('[id^="dropdown-"]').forEach(dropdown => dropdown.classList.add('hidden'));
    });

    function copyPostLink(event, url, element) {
        event.preventDefault();
        event.stopPropagation();
        navigator.clipboard.writeText(url).then(() => {
            const textSpan = element.querySelector('span');
            const originalText = textSpan.innerText;
            textSpan.innerText = 'Copied!';
            textSpan.classList.add('text-green-400');
            setTimeout(() => {
                textSpan.innerText = originalText;
                textSpan.classList.remove('text-green-400');
                element.parentElement.classList.add('hidden');
            }, 1000);
        }).catch(err => console.error('Gagal menyalin link: ', err));
    }

    function escapeHtml(text) {
        return text
            .replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;").replace(/'/g, "&#039;");
    }

    // Edit Profile Modal
    function openEditProfileModal() {
        document.getElementById('edit-profile-modal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeEditProfileModal() {
        document.getElementById('edit-profile-modal').classList.add('hidden');
        document.body.style.overflow = '';
    }

    function removeAvatar() {
        const avatarPreview = document.getElementById('avatar-preview-img');
        const avatarInput = document.getElementById('edit-avatar');
        avatarPreview.src = '<?= assets_url('default.jpg'); ?>';
        avatarInput.value = '';
        let hiddenInput = document.getElementById('remove-avatar-input');
        if (!hiddenInput) {
            hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'remove_avatar';
            hiddenInput.id = 'remove-avatar-input';
            hiddenInput.value = '1';
            document.getElementById('edit-profile-form').appendChild(hiddenInput);
        }
    }

    function removeBanner() {
        const bannerPreview = document.getElementById('banner-preview-img');
        const bannerInput = document.getElementById('edit-banner');
        bannerPreview.src = '';
        bannerInput.value = '';
        let hiddenInput = document.getElementById('remove-banner-input');
        if (!hiddenInput) {
            hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'remove_banner';
            hiddenInput.id = 'remove-banner-input';
            hiddenInput.value = '1';
            document.getElementById('edit-profile-form').appendChild(hiddenInput);
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Preview avatar sebelum upload
        const avatarInput = document.getElementById('edit-avatar');
        const avatarPreview = document.getElementById('avatar-preview-img');
        if (avatarInput && avatarPreview) {
            // Reset preview saat modal dibuka (kembalikan ke avatar asli)
            const originalSrc = avatarPreview.src;
            document.querySelector('button[onclick="openEditProfileModal()"]')?.addEventListener('click', function() {
                // Hapus hidden input remove_avatar jika ada
                const hidden = document.getElementById('remove-avatar-input');
                if (hidden) hidden.remove();
                // Reset preview ke avatar asli dari database
                avatarPreview.src = '<?= avatar_url($user['avatar']); ?>';
            });

            avatarInput.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        avatarPreview.src = e.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            });
        }

        // Preview banner sebelum upload
        const bannerInput = document.getElementById('edit-banner');
        const bannerPreview = document.getElementById('banner-preview-img');
        if (bannerInput && bannerPreview) {
            const originalBannerSrc = bannerPreview.src;
            document.querySelector('button[onclick="openEditProfileModal()"]')?.addEventListener('click', function() {
                const hidden = document.getElementById('remove-banner-input');
                if (hidden) hidden.remove();
                bannerPreview.src = '<?= !empty($user['banner']) ? base_url($user['banner']) : ''; ?>';
            });

            bannerInput.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        bannerPreview.src = e.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            });
        }

        const editForm = document.getElementById('edit-profile-form');
        if (editForm) {
            editForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const submitBtn = this.querySelector('button[type="submit"]');
                submitBtn.disabled = true;
                submitBtn.textContent = 'Menyimpan...';

                const formData = new FormData(this);

                fetch('<?= base_url("profile/edit_profile"); ?>', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        closeEditProfileModal();
                        // Update UI
                        if (data.user.display_name) {
                            document.querySelector('h2.font-syne').textContent = data.user.display_name;
                            // Update header nama
                            const headerName = document.querySelector('header .text-xs.font-semibold');
                            if (headerName) headerName.textContent = data.user.display_name;
                        }
                        if (data.user.avatar) {
                            // Update avatar di profile card
                            const profileAvatars = document.querySelectorAll('.rounded-full img[src*="avatar"], img[alt="Avatar"]');
                            profileAvatars.forEach(img => {
                                if (img.closest('.glass-card')) {
                                    img.src = data.user.avatar;
                                }
                            });
                            // Update avatar di header
                            const headerAvatar = document.querySelector('header a[title="Lihat Profil"] img');
                            if (headerAvatar) headerAvatar.src = data.user.avatar;
                        }
                        if (data.user.banner) {
                            const bannerImg = document.querySelector('.h-36.sm\\:h-48 img');
                            if (bannerImg) bannerImg.src = data.user.banner;
                        }
                        if (data.user.bio !== undefined) {
                            const bioEl = document.querySelector('.border-t.border-white\\/\\[0\\.03\\]');
                            if (bioEl && bioEl.nextElementSibling) {
                                bioEl.nextElementSibling.innerHTML = data.user.bio
                                    ? data.user.bio.replace(/\n/g, '<br>')
                                    : '<span class="text-slate-500 italic">Belum ada biografi yang ditulis.</span>';
                            }
                        }
                        // Update team badge
                        const teamBadgeContainer = document.querySelector('.border-t.border-white\\/\\[0\\.03\\]');
                        if (teamBadgeContainer) {
                            let teamBadge = teamBadgeContainer.parentElement.querySelector('.pt-2');
                            if (teamBadge) teamBadge.remove();
                            if (data.user.team_name) {
                                const div = document.createElement('div');
                                div.className = 'flex items-center justify-center sm:justify-start gap-2 pt-2';
                                div.innerHTML = '<span class="inline-flex items-center gap-1.5 text-[10px] font-semibold uppercase tracking-wider px-2 py-0.5 rounded-full border border-white/[0.08]" style="background:' + (data.user.team_color || '#666') + '15;"><img src="<?= base_url(''); ?>' + data.user.team_logo + '" alt="' + data.user.team_name + '" class="w-4 h-4 object-contain"> ' + data.user.team_name + '</span>';
                                teamBadgeContainer.parentElement.appendChild(div);
                            }
                        }
                        const toast = document.createElement('div');
                        toast.className = 'fixed bottom-20 left-1/2 -translate-x-1/2 z-[9999] bg-emerald-600 text-white text-xs font-semibold px-4 py-2.5 rounded-xl shadow-lg shadow-emerald-600/20';
                        toast.textContent = data.message || 'Profil berhasil diperbarui!';
                        document.body.appendChild(toast);
                        setTimeout(() => toast.remove(), 3000);
                        // Reload to reflect all changes
                        setTimeout(() => window.location.reload(), 1000);
                    } else {
                        alert(data.message || 'Gagal memperbarui profil.');
                    }
                })
                .catch(err => {
                    console.error('Error:', err);
                    alert('Terjadi kesalahan. Silakan coba lagi.');
                })
                .finally(() => {
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Simpan';
                });
            });
        }
    });
</script>