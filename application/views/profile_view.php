<?php
    $profile_name  = !empty($user['display_name']) ? $user['display_name'] : $user['username'];
    $profile_joined = !empty($user['created_at']) ? date('M Y', strtotime($user['created_at'])) : null;
    $profile_team_color = !empty($user['team_color']) ? $user['team_color'] : '#666666';
?>
<div class="profile-page">

    <section class="profile-hero">
        <div class="profile-cover<?= empty($user['banner']) ? ' profile-cover--empty' : ''; ?>">
            <?php if (!empty($user['banner'])): ?>
                <img src="<?= base_url($user['banner']); ?>" alt="Cover <?= htmlspecialchars($profile_name, ENT_QUOTES, 'UTF-8'); ?>">
            <?php else: ?>
                <span class="profile-cover__placeholder" aria-hidden="true">
                    <i data-lucide="image"></i>
                </span>
            <?php endif; ?>
        </div>

        <div class="profile-hero__body">
            <div class="profile-hero__top">
                <div class="profile-avatar" data-user-id="<?= $user['id_user']; ?>">
                    <div class="profile-avatar__img">
                        <img src="<?= avatar_url($user['avatar']); ?>"
                             alt="Avatar <?= htmlspecialchars($profile_name, ENT_QUOTES, 'UTF-8'); ?>"
                             onerror="this.src='<?= assets_url('default.jpg'); ?>';">
                    </div>
                    <?php if (!empty($user['border_image'])): ?>
                        <span class="avatar-border"><img src="<?= assets_url($user['border_image']); ?>" alt="Border profil"></span>
                    <?php endif; ?>
                </div>

                <nav class="profile-shortcuts" aria-label="Pintasan profil">
                    <a href="<?= base_url('profile/edit_profile_page'); ?>" class="btn btn-secondary btn-sm profile-shortcut">
                        <i data-lucide="user-pen"></i> Edit Profil
                    </a>
                    <a href="<?= base_url('settings'); ?>" class="btn btn-secondary btn-sm profile-shortcut">
                        <i data-lucide="settings"></i> Pengaturan
                    </a>
                    <?= form_open('auth/logout', ['id' => 'profile-logout-form', 'class' => 'profile-shortcut-form']); ?>
                        <button type="button" onclick="confirmProfileLogout()" class="btn btn-secondary btn-sm profile-shortcut profile-shortcut--danger">
                            <i data-lucide="log-out"></i> Logout
                        </button>
                    <?= form_close(); ?>
                </nav>
            </div>

            <div class="profile-identity">
                <div class="profile-name-row">
                    <h1 class="profile-name"><?= htmlspecialchars($profile_name, ENT_QUOTES, 'UTF-8'); ?></h1>
                    <?php if ($user['verified'] == 1): ?>
                        <span class="profile-verified" title="Verified Driver">
                            <i data-lucide="badge-check"></i>
                        </span>
                    <?php endif; ?>
                </div>
                <p class="profile-handle">@<?= htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8'); ?></p>

                <div class="profile-meta">
                    <?php if (!empty($user['team_name'])): ?>
                        <span class="profile-team" style="background:<?= htmlspecialchars($profile_team_color, ENT_QUOTES, 'UTF-8'); ?>1f;border-color:<?= htmlspecialchars($profile_team_color, ENT_QUOTES, 'UTF-8'); ?>45;">
                            <img src="<?= assets_url($user['team_logo']); ?>" alt="<?= htmlspecialchars($user['team_name'], ENT_QUOTES, 'UTF-8'); ?>">
                            <?= htmlspecialchars($user['team_name'], ENT_QUOTES, 'UTF-8'); ?>
                        </span>
                    <?php endif; ?>
                    <?php if ($profile_joined): ?>
                        <span class="profile-joined">
                            <i data-lucide="calendar-days"></i> Bergabung <?= htmlspecialchars($profile_joined, ENT_QUOTES, 'UTF-8'); ?>
                        </span>
                    <?php endif; ?>
                </div>
            </div>

            <p class="profile-bio">
                <?= !empty($user['bio']) ? nl2br(htmlentities($user['bio'])) : '<span class="profile-bio--empty">Belum ada biografi yang ditulis.</span>'; ?>
            </p>

            <div class="profile-stats">
                <div class="profile-stat">
                    <strong><?= number_format((int) ($user['total_posts'] ?? 0)); ?></strong>
                    <span>Postingan</span>
                </div>
                <button type="button" onclick="openFollowModal('following')" class="profile-stat profile-stat--action">
                    <strong><?= number_format((int) $user['total_following']); ?></strong>
                    <span>Following</span>
                </button>
                <button type="button" onclick="openFollowModal('followers')" class="profile-stat profile-stat--action">
                    <strong><?= number_format((int) $user['total_followers']); ?></strong>
                    <span>Followers</span>
                </button>
            </div>
        </div>
    </section>

    <div class="profile-tabs" role="tablist" aria-label="Aktivitas profil">
        <button onclick="switchTab('uploads')" id="tab-uploads" class="profile-tab is-active" role="tab" aria-selected="true">
            <i data-lucide="grid"></i> Postingan Saya
        </button>
        <button onclick="switchTab('liked')" id="tab-liked" class="profile-tab" role="tab" aria-selected="false">
            <i data-lucide="heart"></i> Menyukai
        </button>
    </div>

    <div id="post-container" class="profile-feed" data-sk="feed">
        </div>

    <div id="loading-badge" class="profile-loading hidden">
        <div class="profile-loading__badge">
            <div class="spinner spinner--sm"></div>
            <span>Memuat data paddock...</span>
        </div>
    </div>

</div>

<!-- FOLLOW MODAL -->
<div id="follow-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0" style="background:var(--bg-overlay);" onclick="closeFollowModal()"></div>
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
        const isUploads = tabType === 'uploads';

        tabUploads.classList.toggle('is-active', isUploads);
        tabLiked.classList.toggle('is-active', !isUploads);
        tabUploads.setAttribute('aria-selected', isUploads ? 'true' : 'false');
        tabLiked.setAttribute('aria-selected', isUploads ? 'false' : 'true');

        document.getElementById('post-container').innerHTML = '';
        loadMorePosts();
    }

    async function confirmProfileLogout() {
        const form = document.getElementById('profile-logout-form');
        if (!form) return;

        const confirmed = await showConfirmModal(
            'Kamu akan keluar dari akun ini. Kamu perlu login lagi untuk melanjutkan.',
            { title: 'Logout', confirmText: 'Logout', cancelText: 'Batal', danger: true }
        );

        closeConfirmModal();

        if (confirmed) form.submit();
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
                        ? `<span class="avatar-border"><img src="${escapeHtml(post.border)}" alt=""></span>` 
                        : '';
                    
                    let mediaHTML = '';
                    if (post.file_url && typeof PaddockPostMedia !== 'undefined') {
                        mediaHTML = PaddockPostMedia.render(post.file_url);
                    }

                    const escapedContent = escapeHtml(post.content);
                    const escapedUsername = escapeHtml(post.username);
                    const userUrl = encodeURIComponent(post.username);
                    const escapedTeamName = escapeHtml(post.team_name || '');
                    const escapedTeamColor = escapeHtml(post.team_color || '#666');
                    const escapedTeamLogo = escapeHtml(post.team_logo || '');
                    const escapedAvatar = escapeHtml(post.avatar);
                    const escapedCreatedAt = escapeHtml(post.created_at);
                    const isLiked = post.is_liked == true;
                    const likeBtnClass = isLiked ? 'is-liked' : '';
                    const isOwner = CURRENT_USER_ID > 0 && post.user_id == CURRENT_USER_ID;

                    const sharePayload = escapeAttr(JSON.stringify({
                        id: post.id_post,
                        username: post.username,
                        text: String(post.content || '').slice(0, 160)
                    }));

                    const dropdownItems = isOwner
                        ? `
                            <a href="<?= base_url('post/edit/'); ?>${post.id_post}" onclick="event.stopPropagation();" style="width:100%;text-align:left;padding:8px 12px;font-size:12px;color:var(--text-muted);display:flex;align-items:center;gap:8px;" onmouseover="this.style.background='var(--bg-surface-hover)';this.style.color='var(--text-primary)'" onmouseout="this.style.background='';this.style.color='var(--text-muted)'">
                                <i data-lucide="pencil" style="width:14px;height:14px;"></i><span>Edit</span>
                            </a>
                            <button onclick="event.stopPropagation(); deletePost(${post.id_post})" style="width:100%;text-align:left;padding:8px 12px;font-size:12px;color:var(--text-subtle);display:flex;align-items:center;gap:8px;border-top:1px solid var(--border-subtle);" onmouseover="this.style.background='var(--color-danger-bg)';this.style.color='var(--color-danger)'" onmouseout="this.style.background='';this.style.color='var(--text-subtle)'">
                                <i data-lucide="trash-2" style="width:14px;height:14px;"></i><span>Hapus</span>
                            </button>
                        `
                        : `
                            <button onclick="event.stopPropagation(); openReportPost(${post.id_post})" style="width:100%;text-align:left;padding:8px 12px;font-size:12px;color:var(--text-subtle);display:flex;align-items:center;gap:8px;" onmouseover="this.style.background='var(--color-danger-bg)';this.style.color='var(--color-danger)'" onmouseout="this.style.background='';this.style.color='var(--text-subtle)'">
                                <i data-lucide="flag" style="width:14px;height:14px;"></i><span>Report Post</span>
                            </button>
                        `;

                    const cardHTML = `
                        <article class="h-post relative" data-post-id="${post.id_post}" data-user-id="${post.user_id}">
                            <a href="<?= base_url('post/'); ?>${userUrl}/${post.id_post}" class="h-post__link" aria-label="Lihat detail postingan"></a>

                            <header class="h-post__head">
                                <div class="h-post__author">
                                    <div class="relative flex-shrink-0 select-none" style="width:40px;height:40px;">
                                        <div class="h-post__avatar">
                                            <a href="<?= base_url('user/'); ?>${userUrl}"><img src="${escapedAvatar}" alt="Avatar ${escapedUsername}" loading="lazy" onerror="this.src='<?= assets_url('default.jpg'); ?>';"></a>
                                        </div>
                                        ${avatarBorderHTML}

                                    </div>

                                    <div class="min-w-0">
                                        <div class="h-post__who">
                                            <a href="<?= base_url('user/'); ?>${userUrl}" class="h-post__name">${escapedUsername}</a>
                                            ${post.team_name ? '<span class="h-post__team" style="--tc:' + escapedTeamColor + ';"><img src="' + escapedTeamLogo + '" alt="' + escapedTeamName + '"> ' + escapedTeamName + '</span>' : ''}
                                        </div>
                                        <div class="h-post__meta">
                                            <a href="<?= base_url('post/'); ?>${userUrl}/${post.id_post}" class="h-post__time">${escapedCreatedAt}</a>
                                        </div>
                                    </div>
                                </div>

                                <div class="h-post__menu-wrap">
                                    <button onclick="toggleDropdown(event, ${post.id_post})" class="h-post__menu" type="button" aria-label="Menu postingan">
                                        <i data-lucide="more-horizontal"></i>
                                    </button>
                                    <div id="dropdown-${post.id_post}" class="dropdown hidden" style="width:144px;top:34px;">
                                        ${dropdownItems}
                                    </div>
                                </div>
                            </header>

                            ${mediaHTML}

                            <div class="h-post__body">
                                <p class="h-post__text">${linkifyContent(escapedContent)}</p>
                            </div>

                            <footer class="h-post__foot">
                                <button onclick="toggleLike(event, ${post.id_post}, this)" type="button" class="h-post__action h-post__like ${likeBtnClass}" style="color:${isLiked ? 'var(--color-danger)' : 'var(--text-subtle)'};" onmouseover="if(!this.classList.contains('is-liked'))this.style.color='var(--color-danger)'" onmouseout="if(!this.classList.contains('is-liked'))this.style.color='var(--text-subtle)'">
                                    <i data-lucide="heart" class="${isLiked ? 'fill-danger' : ''}" style="color:${isLiked ? 'var(--color-danger)' : 'var(--text-subtle)'};"></i>
                                    <span class="count-likes font-semibold">${post.likes_count}</span>
                                </button>
                                <a href="<?= base_url('post/'); ?>${userUrl}/${post.id_post}" class="h-post__action" style="color:var(--text-subtle);">
                                    <i data-lucide="message-square"></i>
                                    <span class="font-semibold">${post.comments_count}</span>
                                </a>
                                <button onclick="event.preventDefault(); event.stopPropagation(); openShareModal(JSON.parse(this.getAttribute('data-share')))" data-share="${sharePayload}" type="button" class="h-post__action h-post__share" aria-label="Bagikan postingan">
                                    <i data-lucide="share-2"></i>
                                    <span class="font-semibold">Bagikan</span>
                                </button>
                            </footer>
                        </article>
                    `;
                    
                    container.insertAdjacentHTML('beforeend', cardHTML);

                    if (isLiked && typeof lucide !== 'undefined') {
                        const icons = container.querySelectorAll('[data-lucide="heart"]');
                        const lastHeart = icons[icons.length - 1];
                        if (lastHeart) {
                            lastHeart.classList.add('fill-danger');
                            lastHeart.style.color = 'var(--color-danger)';
                        }
                    }
                });

                if (typeof PaddockPostMedia !== 'undefined') {
                    PaddockPostMedia.init(container);
                }
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
                        buttonElement.classList.add('is-liked', 'c-danger');
                        icon.classList.add('fill-danger', 'c-danger');
                        buttonElement.style.color = 'var(--color-danger)';
                        icon.style.color = 'var(--color-danger)';
                        if (window.playTeamLikeBurst) playTeamLikeBurst(buttonElement);
                    } else {
                        buttonElement.classList.remove('is-liked', 'c-danger');
                        icon.classList.remove('fill-danger', 'c-danger');
                        buttonElement.style.color = 'var(--text-subtle)';
                        icon.style.color = 'var(--text-subtle)';
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

    function escapeHtml(text) {
        return text
            .replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;").replace(/'/g, "&#039;");
    }
</script>
