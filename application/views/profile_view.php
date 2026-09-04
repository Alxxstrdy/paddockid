<div class="flex-1 max-w-2xl w-full mx-auto px-4 py-6">
    
    <div class="card rounded-2xl overflow-hidden shadow-xl relative mb-8" style="border:1px solid var(--border-default);">
    
    <div class="w-full relative overflow-hidden" style="height:144px;">
        <?php if (!empty($user['banner'])): ?>
            <img src="<?= base_url($user['banner']); ?>" alt="User Banner" class="w-full h-full" style="object-fit:cover;">
        <?php else: ?>
            <div class="absolute inset-0" style="background:radial-gradient(ellipse at top right, rgba(127,29,29,0.2) 0%, var(--bg-body) 60%, var(--bg-body) 100%);"></div>
        <?php endif; ?>
        <div class="absolute inset-0" style="background:linear-gradient(to top, rgba(5,7,12,0.9) 0%, rgba(5,7,12,0.3) 50%, transparent 100%);"></div>
    </div>

    <div class="relative px-5 pb-5" style="margin-top:-56px;">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-4">
            
            <div class="relative flex-shrink-0 mx-auto sm:mx-0" data-user-id="<?= $user['id_user']; ?>" style="width:96px;height:96px;">
                <div class="w-full h-full rounded-full overflow-hidden" style="padding:2.5px;background:var(--bg-body);box-shadow:0 0 0 2px var(--border-strong);">
                    <img src="<?= avatar_url($user['avatar']); ?>" 
                         alt="Avatar" class="w-full h-full rounded-full"
                         style="object-fit:cover;"
                         onerror="this.src='<?= assets_url('default.jpg'); ?>';">
                </div>
                <?php if (!empty($user['border_image'])): ?>
                    <div class="absolute pointer-events-none z-20" style="inset:-6px;">
                        <img src="<?= assets_url($user['border_image']); ?>" alt="F1 Border" class="w-full h-full" style="object-fit:contain;">
                    </div>
                <?php endif; ?>
                <?php if (!empty($user['is_online'])): ?>
                    <div class="online-indicator"></div>
                <?php endif; ?>
            </div>

            <div class="flex flex-row items-center sm:items-end justify-center sm:justify-end gap-2">
                <a href="<?= base_url('profile/edit'); ?>" class="btn btn-secondary btn-sm">
                    Edit Profil
                </a>
                <a href="<?= base_url('settings'); ?>" class="btn btn-secondary btn-sm">
                    <i data-lucide="settings" class="w-3 h-3 inline-block mr-1"></i> Settings
                </a>
            </div>
            
        </div>

        <div class="text-center sm:text-left space-y-2">
            <div class="flex items-center justify-center sm:justify-start gap-2">
                <h2 class="text-heading text-lg sm:text-xl" style="letter-spacing:-0.01em;">
                    <?= htmlspecialchars(!empty($user['display_name']) ? $user['display_name'] : $user['username'], ENT_QUOTES, 'UTF-8'); ?>
                </h2>
                <?php if ($user['verified'] == 1): ?>
                    <span class="c-primary" title="Verified Driver"><i data-lucide="badge-check" class="w-4 h-4 inline-block" style="fill:var(--color-primary);"></i></span>
                <?php endif; ?>
            </div>
            <p class="text-small" style="margin-top:-4px;">@<?= $user['username']; ?></p>
            
            <div class="flex items-center justify-center sm:justify-start gap-5 text-xs pt-1">
                <button onclick="openFollowModal('following')" class="transition-colors flex gap-1 cursor-pointer" style="color:var(--text-muted);">
                    <span class="font-bold c-white"><?= number_format($user['total_following']); ?></span>
                    <span>Following</span>
                </button>
                <button onclick="openFollowModal('followers')" class="transition-colors flex gap-1 cursor-pointer" style="color:var(--text-muted);">
                    <span class="font-bold c-white"><?= number_format($user['total_followers']); ?></span>
                    <span>Followers</span>
                </button>
            </div>
            
            <p class="text-xs sm:text-sm leading-relaxed pt-3 border-t" style="color:var(--text-secondary);border-color:var(--border-subtle);">
                <?= !empty($user['bio']) ? nl2br(htmlentities($user['bio'])) : '<span class="c-subtle italic">Belum ada biografi yang ditulis.</span>'; ?>
            </p>

            <?php if (!empty($user['team_name'])): ?>
                <div class="flex items-center justify-center sm:justify-start gap-2 pt-2">
                    <span class="inline-flex items-center gap-1-5 badge-pill" style="font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:0.06em;padding:2px 8px;border:1px solid var(--border-strong);background:<?= $user['team_color'] ?? '#666' ?>15;">
                        <img src="<?= assets_url($user['team_logo']) ?>" alt="<?= htmlspecialchars($user['team_name']) ?>" class="w-4 h-4" style="object-fit:contain;">
                        <?= htmlspecialchars($user['team_name']) ?>
                    </span>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

    <div class="tabs mb-6 text-xs sm:text-sm font-semibold" style="color:var(--text-muted);">
        <button onclick="switchTab('uploads')" id="tab-uploads" class="tab flex-1 is-active" style="display:flex;align-items:center;justify-content:center;gap:8px;">
            <i data-lucide="grid" class="w-4 h-4"></i> Postingan Saya
        </button>
        <button onclick="switchTab('liked')" id="tab-liked" class="tab flex-1" style="display:flex;align-items:center;justify-content:center;gap:8px;">
            <i data-lucide="heart" class="w-4 h-4"></i> Menyukai
        </button>
    </div>

    <div id="post-container" class="space-y-4">
        </div>

    <div id="loading-badge" class="py-8 text-center flex justify-center items-center hidden">
        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full" style="background:var(--bg-body);border:1px solid var(--border-subtle);">
            <div class="spinner spinner--sm"></div>
            <span class="text-xs font-medium" style="color:var(--text-muted);">Memuat data paddock...</span>
        </div>
    </div>

</div>

<!-- FOLLOW MODAL -->
<div id="follow-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0" style="background:var(--bg-overlay);backdrop-filter:blur(4px);" onclick="closeFollowModal()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="card rounded-2xl w-full max-w-sm flex flex-col shadow-xl" style="border:1px solid var(--border-default);max-height:70vh;">
            <div class="flex items-center justify-between px-5 py-4 border-b" style="border-color:var(--border-subtle);">
                <h3 id="follow-modal-title" class="text-section-title" style="font-size:14px;">Following</h3>
                <button onclick="closeFollowModal()" class="modal-close">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
            <div id="follow-modal-body" class="overflow-y-auto no-scrollbar flex-1 border-child">
                <div class="flex items-center justify-center py-8">
                    <div class="spinner spinner--sm"></div>
                </div>
            </div>
        </div>
    </div>
    </div>
</main>

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
            tabUploads.className = "tab flex-1 is-active";
            tabLiked.className = "tab flex-1";
        } else {
            tabLiked.className = "tab flex-1 is-active";
            tabUploads.className = "tab flex-1";
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
                <div class="spinner spinner--sm"></div>
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
                    body.innerHTML = `<div class="text-center py-10 text-xs c-subtle" style="text-transform:uppercase;letter-spacing:0.06em;">Tidak ada ${type}</div>`;
                    return;
                }
                body.innerHTML = data.map(user => {
                    const borderHTML = user.border_image
                        ? `<div class="absolute inset-0 w-full h-full pointer-events-none" style="transform:scale(1.25);transform-origin:center;">
                               <img src="${escapeHtml(user.border_image)}" alt="" class="w-full h-full" style="object-fit:contain;">
                           </div>`
                        : '';
                    const verifiedHTML = user.verified == 1
                        ? `<span class="c-primary inline-flex"><i data-lucide="badge-check" class="w-3 h-3 inline-block" style="fill:var(--color-primary);"></i></span>`
                        : '';
                    const onlineHTML = user.is_online ? '<div class="online-indicator"></div>' : '';
                    const followUsername = escapeHtml(user.username);
                    const followUserUrl = encodeURIComponent(user.username);
                    const followAvatar = escapeHtml(user.avatar);
                    const followDisplayName = escapeHtml(user.display_name || user.username);
                    return `
                        <a href="<?= base_url('user/'); ?>${followUserUrl}" class="flex items-center gap-3 px-5 py-3 transition-colors border-b" style="border-color:var(--border-subtle);">
                            <div class="relative flex items-center justify-center flex-shrink-0" style="width:36px;height:36px;">
                                <div class="w-full h-full rounded-full overflow-hidden" style="background:var(--bg-surface-raised);">
                                    <img src="${followAvatar}" alt="" class="w-full h-full rounded-full" style="object-fit:cover;" onerror="this.src='<?= assets_url('default.jpg'); ?>';">
                                </div>
                                ${borderHTML}
                                ${onlineHTML}
                            </div>
                            <div class="flex flex-col min-w-0">
                                <div class="flex items-center gap-1-5">
                                    <span class="font-semibold text-xs c-white truncate">${followDisplayName}</span>
                                    ${verifiedHTML}
                                </div>
                                <span class="truncate" style="font-size:10px;color:var(--text-subtle);">@${followUsername}</span>
                            </div>
                        </a>
                    `;
                }).join('');
                if (typeof lucide !== 'undefined') lucide.createIcons();
            })
            .catch(err => {
                body.innerHTML = `<div class="text-center py-10 text-xs c-primary">${err.message || 'Gagal memuat data'}</div>`;
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
        
        const url = `<?= base_url('profile/get_profile_posts_ajax'); ?>?type=${currentTab}&offset=${offset}&limit=${limit}&user_id=${userId}`;
        
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
                    loadingBadge.innerHTML = "<div style='color:var(--text-subtle);text-transform:uppercase;letter-spacing:0.06em;font-size:10px;' class='py-4'>Kamu telah mencapai batas akhir postingan</div>";
                    return;
                }
                
                data.forEach(post => {
                    const avatarBorderHTML = post.border 
                        ? `<div class="absolute inset-0 w-full h-full pointer-events-none" style="transform:scale(1.25);transform-origin:center;">
                            <img src="${escapeHtml(post.border)}" alt="F1 Border Decoration" class="w-full h-full" style="object-fit:contain;">
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
                            gridClass = 'post-images--1';
                        } else if (totalImages === 2) {
                            gridClass = 'post-images--2';
                        } else if (totalImages === 3) {
                            gridClass = 'post-images--3';
                        } else {
                            gridClass = 'post-images--4';
                        }

                        const imagesToShow = images.slice(0, 4);
                        imagesToShow.forEach((url, index) => {
                            const itemClass = (totalImages === 3 && index === 0) ? 'row-span-2 h-full' : 'h-full';
                            imagesTemplate += `
                                <div class="relative w-full ${itemClass} overflow-hidden" style="background:var(--bg-body);">
                                    <img src="${escapeHtml(url)}" alt="Post Media" loading="lazy" class="w-full h-full" style="object-fit:cover;">
                                </div>
                            `;
                        });

                        mediaHTML = `
                            <div class="px-4 sm:px-5 mb-1">
                                <div class="post-images ${gridClass}" style="aspect-ratio:4/3;">
                                    ${imagesTemplate}
                                </div>
                            </div>
                        `;
                    }

                    const dynamicLikeBtnClass = post.is_liked ? 'c-primary' : '';
                    const dynamicLikeIconClass = post.is_liked ? 'c-primary' : '';
                    const dynamicLikeIconFill = post.is_liked ? 'fill:var(--color-primary);' : '';
                    const escapedContent = escapeHtml(post.content);
                    const escapedUsername = escapeHtml(post.username);
                    const userUrl = encodeURIComponent(post.username);
                    const userJs = escapeJsString(encodeURIComponent(post.username));
                    const escapedCategory = escapeHtml(post.category);
                    const escapedTeamName = escapeHtml(post.team_name || '');
                    const escapedTeamColor = escapeHtml(post.team_color || '#666');
                    const escapedTeamLogo = escapeHtml(post.team_logo || '');
                    const escapedAvatar = escapeHtml(post.avatar);
                    const escapedCreatedAt = escapeHtml(post.created_at);

                    const cardHTML = `
                        <article class="card rounded-xl overflow-hidden relative transition-colors" data-post-id="${post.id_post}" data-user-id="${post.user_id}">
                            <a href="<?= base_url('post/'); ?>${userUrl}/${post.id_post}" class="absolute inset-0 z-10"></a>
                            
                            <div class="flex items-center justify-between p-4 sm:p-5">
                                <div class="flex items-center gap-3">
                                    <div class="relative flex items-center justify-center select-none z-20" style="width:36px;height:36px;">
                                        <div class="w-full h-full rounded-full overflow-hidden" style="background:var(--bg-surface-raised);">
                                            <a href="<?= base_url('user/'); ?>${userUrl}">
                                                <img src="${escapedAvatar}" alt="User" class="w-full h-full rounded-full" style="object-fit:cover;">
                                            </a>
                                        </div>
                                        ${avatarBorderHTML}
                                        ${onlineHTML}
                                    </div>
                                    
                                    <div class="flex flex-col justify-center">
                                        <div class="flex items-center gap-2">
                                            <a href="<?= base_url('user/'); ?>${userUrl}" class="font-semibold text-xs sm:text-sm cursor-pointer transition-colors relative z-20">${escapedUsername}</a>
                                            ${post.team_name ? '<span class="inline-flex items-center gap-1 badge-pill" style="font-size:9px;font-weight:600;text-transform:uppercase;letter-spacing:0.06em;padding:2px 6px;border:1px solid var(--border-strong);background:' + escapedTeamColor + '15;"><img src="<?= base_url(''); ?>' + escapedTeamLogo + '" alt="' + escapedTeamName + '" class="w-3 h-3" style="object-fit:contain;"> ' + escapedTeamName + '</span>' : ''}
                                            <span class="c-faint" style="font-size:10px;">&bull;</span>
                                            <span class="inline-flex items-center badge" style="font-size:8px;padding:2px 6px;font-weight:600;border-radius:9999px;text-transform:uppercase;letter-spacing:0.06em;">${escapedCategory}</span>
                                        </div>
                                        <span class="mt-0-5" style="font-size:10px;color:var(--text-subtle);">${escapedCreatedAt}</span>
                                    </div>
                                </div>
                                
                                <div class="relative z-30 flex items-center">
                                    <button onclick="toggleDropdown(event, ${post.id_post})" class="transition-colors p-1 rounded-md" style="color:var(--text-subtle);">
                                        <i data-lucide="more-horizontal" class="w-4 h-4"></i>
                                    </button>
                                    <div id="dropdown-${post.id_post}" class="hidden absolute right-0 w-36 rounded-lg shadow-xl overflow-hidden py-1 text-xs" style="top:32px;background:rgba(15,22,38,0.95);backdrop-filter:blur(12px);border:1px solid var(--border-strong);color:var(--text-secondary);">
                                        <button 
                                            onclick="copyPostLink(event, '<?= base_url('post/'); ?>${userJs}/${post.id_post}', this)"
                                            class="w-full text-left px-3 py-2 flex items-center gap-2 transition-colors"
                                        >
                                            <i data-lucide="link" class="w-3.5 h-3.5"></i>
                                            <span>Copy Link</span>
                                        </button>
                                        <a 
                                            href="<?= base_url('post/edit/'); ?>${post.id_post}"
                                            onclick="event.stopPropagation();"
                                            class="w-full text-left px-3 py-2 flex items-center gap-2 transition-colors border-t"
                                            style="border-color:var(--border-subtle);"
                                        >
                                            <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                                            <span>Edit</span>
                                        </a>
                                        <button 
                                            onclick="event.stopPropagation(); deletePost(${post.id_post})"
                                            class="w-full text-left px-3 py-2 flex items-center gap-2 transition-colors border-t c-primary"
                                            style="border-color:var(--border-subtle);"
                                        >
                                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                            <span>Hapus</span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            ${mediaHTML}

                            <div class="p-4 sm:p-5 pt-2 space-y-3">
                                <p class="text-xs sm:text-sm leading-relaxed" style="color:var(--text-secondary);">${escapedContent}</p>
                                
                                <div class="flex items-center gap-4 pt-2 border-t relative z-20" style="border-color:var(--border-subtle);color:var(--text-muted);font-size:12px;">
                                    <button onclick="toggleLike(event, ${post.id_post}, this)" class="flex items-center gap-1-5 transition-colors ${dynamicLikeBtnClass}">
                                        <i data-lucide="heart" class="w-4 h-4 ${dynamicLikeIconClass}" style="${dynamicLikeIconFill}"></i>
                                        <span class="count-likes font-semibold">${post.likes_count}</span>
                                    </button>
                                    <a href="<?= base_url('post/'); ?>${userUrl}/${post.id_post}" class="flex items-center gap-1-5 transition-colors">
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
                    document.getElementById('post-container').innerHTML = '<div class="card p-8 text-center text-xs" style="color:var(--text-subtle);">Gagal memuat postingan. Silakan muat ulang halaman.</div>';
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
                        buttonElement.classList.add('c-primary');
                        icon.classList.add('c-primary');
                        icon.style.fill = 'var(--color-primary)';
                    } else {
                        buttonElement.classList.remove('c-primary');
                        icon.classList.remove('c-primary');
                        icon.style.fill = '';
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
            textSpan.classList.add('c-success');
            setTimeout(() => {
                textSpan.innerText = originalText;
                textSpan.classList.remove('c-success');
                element.parentElement.classList.add('hidden');
            }, 1000);
        }).catch(err => console.error('Gagal menyalin link: ', err));
    }

    function escapeHtml(text) {
        return text
            .replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;").replace(/'/g, "&#039;");
    }
</script>
