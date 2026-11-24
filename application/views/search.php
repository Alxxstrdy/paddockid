<div class="space-y-4">
    <!-- Search Bar -->
    <div class="card rounded-2xl p-5" style="border:1px solid var(--border-default)">
        <form id="search-form" action="<?= base_url('search'); ?>" method="GET" class="flex-row items-center gap-2">
            <div class="relative flex-1">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none c-subtle">
                    <i data-lucide="search" style="width:16px;height:16px"></i>
                </span>
                <input
                    type="text"
                    name="q"
                    id="search-input"
                    value="<?= htmlspecialchars($keyword ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                    placeholder="Cari postingan atau pengguna..."
                    autocomplete="off"
                    class="input w-full"
                    style="background:rgba(0,0,0,0.4);border:1px solid var(--border-strong);border-radius:0.75rem;padding-left:2.5rem;padding-right:1rem;padding-top:0.625rem;padding-bottom:0.625rem;font-size:12px;color:var(--text-secondary);transition:all 0.2s"
                >
                <div id="search-dropdown" class="search-menu hidden"></div>
            </div>
            <button type="submit" class="btn-primary" style="padding:0.625rem 1rem;border-radius:0.75rem;font-weight:600;font-size:12px;white-space:nowrap">
                Cari
            </button>
        </form>
    </div>

    <?php if (!empty($keyword)): ?>
        <!-- Search Results -->
        <div class="card rounded-2xl" style="border:1px solid var(--border-default);overflow:hidden">
            <!-- Tabs -->
            <div class="flex" style="border-bottom:1px solid var(--border-default)">
                <button
                    id="tab-posts"
                    class="tab tab-btn flex-1 py-3 text-small font-semibold transition-colors relative"
                    style="border-radius:0"
                    data-type="posts"
                    onclick="switchTab('posts')"
                >
                    Posts (<?= $posts_count; ?>)
                    <span class="tab-indicator absolute bottom-0 left-0 right-0 h-0.5 hidden" style="background:var(--color-primary)"></span>
                </button>
                <button
                    id="tab-users"
                    class="tab-btn flex-1 py-3 text-small font-semibold transition-colors relative"
                    style="color:var(--text-subtle);border-radius:0"
                    data-type="users"
                    onclick="switchTab('users')"
                >
                    Users (<?= $users_count; ?>)
                    <span class="tab-indicator absolute bottom-0 left-0 right-0 h-0.5 hidden" style="background:var(--color-primary)"></span>
                </button>
            </div>

            <!-- Posts Tab Content -->
            <div id="tab-content-posts" class="tab-content h-feed" data-sk="feed">
                <?php if (!empty($posts)): ?>
                    <?php foreach ($posts as $post):
                        $is_liked = isset($post['is_liked']) && $post['is_liked'] == true;
                        $like_btn_class = $is_liked ? 'is-liked' : '';
                        $post_username_url = rawurlencode($post['username']);
                        $post_avatar_attr = htmlspecialchars($post['avatar'], ENT_QUOTES, 'UTF-8');
                        $post_border_attr = htmlspecialchars($post['border'] ?? '', ENT_QUOTES, 'UTF-8');
                        $post_team_color_attr = htmlspecialchars($post['team_color'] ?? '#666', ENT_QUOTES, 'UTF-8');
                        $post_team_logo_attr = htmlspecialchars(assets_url($post['team_logo']), ENT_QUOTES, 'UTF-8');
                        $post_created_at_attr = htmlspecialchars($post['created_at'], ENT_QUOTES, 'UTF-8');
                    ?>
                    <article class="h-post relative" data-post-id="<?= $post['id_post']; ?>" data-user-id="<?= $post['user_id']; ?>">
                        <a href="<?= base_url('post/' . $post_username_url . '/' . $post['id_post']); ?>" class="h-post__link" aria-label="Lihat detail postingan"></a>

                        <header class="h-post__head">
                            <div class="h-post__author">
                                <div class="relative flex-shrink-0 select-none" style="width:40px;height:40px;">
                                    <div class="h-post__avatar">
                                        <a href="<?= base_url('user/' . $post_username_url); ?>">
                                            <img src="<?= $post_avatar_attr; ?>" alt="Avatar <?= htmlspecialchars($post['username'], ENT_QUOTES, 'UTF-8'); ?>" loading="lazy" onerror="this.src='<?= assets_url('default.jpg'); ?>';">
                                        </a>
                                    </div>
                                    <?php if (!empty($post['border'])): ?>
                                        <span class="avatar-border"><img src="<?= $post_border_attr; ?>" alt=""></span>
                                    <?php endif; ?>
                                </div>

                                <div class="min-w-0">
                                    <div class="h-post__who">
                                        <a href="<?= base_url('user/' . $post_username_url); ?>" class="h-post__name"><?= htmlspecialchars($post['username'], ENT_QUOTES, 'UTF-8'); ?></a>
                                        <?php if (!empty($post['team_name'])): ?>
                                            <span class="h-post__team" style="--tc:<?= $post_team_color_attr; ?>;">
                                                <img src="<?= $post_team_logo_attr ?>" alt="<?= htmlspecialchars($post['team_name'], ENT_QUOTES, 'UTF-8'); ?>">
                                                <?= htmlspecialchars($post['team_name'], ENT_QUOTES, 'UTF-8'); ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="h-post__meta">
                                        <a href="<?= base_url('post/' . $post_username_url . '/' . $post['id_post']); ?>" class="h-post__time"><?= $post_created_at_attr; ?></a>
                                    </div>
                                </div>
                            </div>

                            <div class="h-post__menu-wrap">
                                <button onclick="toggleDropdown(event, <?= $post['id_post']; ?>)" class="h-post__menu" type="button" aria-label="Menu postingan">
                                    <i data-lucide="more-horizontal"></i>
                                </button>
                                <div id="dropdown-<?= $post['id_post']; ?>" class="dropdown hidden" style="width:144px;top:34px;">
                                    <button onclick="copyPostLink(event, '<?= base_url('post/' . $post_username_url . '/' . $post['id_post']); ?>', this)" style="width:100%;text-align:left;padding:8px 12px;font-size:12px;color:var(--text-muted);display:flex;align-items:center;gap:8px;" onmouseover="this.style.background='var(--bg-surface-hover)';this.style.color='var(--text-primary)'" onmouseout="this.style.background='';this.style.color='var(--text-muted)'">
                                        <i data-lucide="link" style="width:14px;height:14px"></i>
                                        <span>Copy Link</span>
                                    </button>
                                    <?php if (isset($current_user_id) && (int) $current_user_id === (int) $post['user_id']): ?>
                                        <a href="<?= base_url('post/edit/' . $post['id_post']); ?>" onclick="event.stopPropagation();" style="width:100%;text-align:left;padding:8px 12px;font-size:12px;color:var(--text-muted);display:flex;align-items:center;gap:8px;border-top:1px solid var(--border-subtle);" onmouseover="this.style.background='var(--bg-surface-hover)';this.style.color='var(--text-primary)'" onmouseout="this.style.background='';this.style.color='var(--text-muted)'">
                                            <i data-lucide="pencil" style="width:14px;height:14px"></i>
                                            <span>Edit</span>
                                        </a>
                                        <button onclick="event.preventDefault(); event.stopPropagation(); deletePost(<?= $post['id_post']; ?>)" style="width:100%;text-align:left;padding:8px 12px;font-size:12px;color:var(--text-subtle);display:flex;align-items:center;gap:8px;border-top:1px solid var(--border-subtle);" onmouseover="this.style.background='var(--color-danger-bg)';this.style.color='var(--color-danger)'" onmouseout="this.style.background='';this.style.color='var(--text-subtle)'">
                                            <i data-lucide="trash-2" style="width:14px;height:14px"></i>
                                            <span>Hapus</span>
                                        </button>
                                    <?php else: ?>
                                        <button onclick="event.preventDefault(); event.stopPropagation(); openReportPost(<?= $post['id_post']; ?>)" style="width:100%;text-align:left;padding:8px 12px;font-size:12px;color:var(--text-subtle);display:flex;align-items:center;gap:8px;border-top:1px solid var(--border-subtle);" onmouseover="this.style.background='var(--color-danger-bg)';this.style.color='var(--color-danger)'" onmouseout="this.style.background='';this.style.color='var(--text-subtle)'">
                                            <i data-lucide="flag" style="width:14px;height:14px"></i>
                                            <span>Report Post</span>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </header>

                        <?php if (!empty($post['file_url'])): ?>
                            <?= post_media_html($post['file_url']); ?>
                        <?php endif; ?>

                        <div class="h-post__body">
                            <p class="h-post__text"><?= linkify_content(htmlspecialchars($post['content'], ENT_QUOTES, 'UTF-8')); ?></p>
                        </div>

                        <footer class="h-post__foot">
                            <button
                                onclick="toggleLike(event, <?= $post['id_post']; ?>, this)"
                                type="button"
                                class="h-post__action h-post__like <?= $like_btn_class; ?>"
                                style="color:<?= $is_liked ? 'var(--color-danger)' : 'var(--text-subtle)'; ?>;"
                                onmouseover="if(!this.classList.contains('is-liked'))this.style.color='var(--color-danger)'"
                                onmouseout="if(!this.classList.contains('is-liked'))this.style.color='var(--text-subtle)'"
                            >
                                <i data-lucide="heart" class="<?= $is_liked ? 'fill-danger' : ''; ?>" style="color:<?= $is_liked ? 'var(--color-danger)' : 'var(--text-subtle)'; ?>;"></i>
                                <span class="count-likes font-semibold"><?= $post['likes_count']; ?></span>
                            </button>

                            <a
                                href="<?= base_url('post/' . $post_username_url . '/' . $post['id_post']); ?>"
                                class="h-post__action"
                                style="color:var(--text-subtle);"
                            >
                                <i data-lucide="message-square"></i>
                                <span class="font-semibold"><?= $post['comments_count']; ?></span>
                            </a>

                            <button
                                data-share='<?= htmlspecialchars(
                                    json_encode([
                                        'id'       => $post['id_post'],
                                        'username' => $post['username'],
                                        'text'     => mb_substr($post['content'], 0, 160)
                                    ]),
                                    ENT_QUOTES, 'UTF-8'
                                ); ?>'
                                onclick="event.preventDefault(); event.stopPropagation(); openShareModal(JSON.parse(this.getAttribute('data-share')))"
                                type="button"
                                class="h-post__action h-post__share"
                                aria-label="Bagikan postingan"
                            >
                                <i data-lucide="share-2"></i>
                                <span class="font-semibold">Bagikan</span>
                            </button>
                        </footer>
                    </article>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="p-8 text-center c-subtle text-small" id="posts-empty">Tidak ada postingan ditemukan.</div>
                <?php endif; ?>
            </div>

            <!-- Users Tab Content -->
            <div id="tab-content-users" class="tab-content hidden">
                <?php if (!empty($users)): ?>
                    <?php foreach ($users as $user):
                        $user_username_url = rawurlencode($user['username']);
                        $user_avatar_attr = htmlspecialchars($user['avatar'], ENT_QUOTES, 'UTF-8');
                        $user_border_attr = htmlspecialchars($user['border'] ?? '', ENT_QUOTES, 'UTF-8');
                        $user_display_name_html = htmlspecialchars($user['display_name'], ENT_QUOTES, 'UTF-8');
                    ?>
                    <div class="flex-row items-center justify-between p-4 user-row" data-user-id="<?= $user['id_user']; ?>">
                        <a href="<?= base_url('user/' . $user_username_url); ?>" class="flex-row items-center gap-3 flex-1 min-w-0">
                            <div class="relative" style="width:40px;height:40px;flex-shrink:0;display:flex;align-items:center;justify-content:center">
                                <div class="rounded-full overflow-hidden" style="width:100%;height:100%;background:var(--bg-surface-raised)">
                                    <img src="<?= $user_avatar_attr; ?>" alt="<?= htmlspecialchars($user['username']); ?>" class="rounded-full" style="width:100%;height:100%;object-fit:cover">
                                </div>
                                <?php if (!empty($user['border'])): ?>
                                    <div class="absolute inset-0" style="width:100%;height:100%;pointer-events:none;transform:scale(1.25);transform-origin:center">
                                        <img src="<?= $user_border_attr; ?>" alt="Border" style="width:100%;height:100%;object-fit:contain">
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="min-w-0">
                                <div class="flex-row items-center gap-1-5">
                                    <span class="font-semibold text-small c-secondary truncate"><?= $user_display_name_html; ?></span>
                                    <?php if ($user['verified']): ?>
                                        <i data-lucide="badge-check" class="c-info flex-shrink-0" style="width:14px;height:14px"></i>
                                    <?php endif; ?>
                                </div>
                                <span class="text-caption c-subtle">@<?= htmlspecialchars($user['username']); ?></span>
                                <span class="text-micro c-faint" style="margin-left:8px">• <?= $user['followers_count']; ?> followers</span>
                            </div>
                        </a>
                        <?php if (isset($current_user_id) && $current_user_id && $current_user_id !== $user['id_user']): ?>
                            <button
                                onclick="event.preventDefault(); event.stopPropagation(); toggleFollowUser('<?= $user['id_user']; ?>', this)"
                                class="follow-btn flex-shrink-0 text-small font-semibold rounded-full transition-all <?= $user['is_followed'] ? '' : 'btn-primary' ?>"
                                style="<?= $user['is_followed'] ? 'padding:4px 1rem;background:var(--bg-surface-hover);color:var(--text-secondary);border:1px solid var(--border-strong);transition:border-color 0.2s, color 0.2s' : 'padding:4px 1rem;border-color:var(--color-primary)' ?>"
                                <?= $user['is_followed'] ? 'onmouseover="this.style.borderColor=\'var(--color-primary-border)\';this.style.color=\'var(--color-primary)\'" onmouseout="this.style.borderColor=\'var(--border-strong)\';this.style.color=\'var(--text-secondary)\'"' : '' ?>
                            >
                                <?= $user['is_followed'] ? 'Following' : 'Follow'; ?>
                            </button>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="p-8 text-center c-subtle text-small" id="users-empty">Tidak ada pengguna ditemukan.</div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Loading Badge -->
        <div id="loading-badge" class="hidden text-center py-6 text-small c-subtle" style="letter-spacing:0.05em">
            <div class="spinner inline-block align-middle" style="width:16px;height:16px;margin-right:8px;border-width:2px;border-bottom-color:var(--color-primary)"></div>
            Memuat lebih banyak...
        </div>
    <?php else: ?>
        <!-- Empty State -->
        <div class="card rounded-2xl p-8 text-center" style="border:1px solid var(--border-default)">
            <div class="rounded-full mx-auto mb-4 flex items-center justify-center" style="width:56px;height:56px;background:var(--bg-surface-hover)">
                <i data-lucide="search" class="c-subtle" style="width:24px;height:24px"></i>
            </div>
            <h3 class="text-heading text-small uppercase" style="letter-spacing:-0.025em;color:var(--text-primary);margin-bottom:8px">Cari di PaddockID</h3>
            <p class="text-small c-muted leading-relaxed" style="max-width:24rem;margin:0 auto">Temukan postingan menarik dan pengguna baru di komunitas Formula 1 Indonesia.</p>
        </div>
    <?php endif; ?>
</div>

<script>
const KEYWORD = '<?= htmlspecialchars($keyword ?? '', ENT_QUOTES, 'UTF-8'); ?>';
let activeTab = 'posts';
let postOffset = <?= !empty($posts) ? count($posts) : 0; ?>;
let userOffset = <?= !empty($users) ? count($users) : 0; ?>;
const limit = 5;
let isLoading = false;
let hasMorePosts = <?= ($posts_count > count($posts)) ? 'true' : 'false'; ?>;
let hasMoreUsers = <?= ($users_count > count($users)) ? 'true' : 'false'; ?>;

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
        .catch(err => {
            console.error('Gagal memproses like:', err);
            showToast('Gagal menyukai postingan. Coba lagi.', 'error');
        });
}

function toggleDropdown(event, postId) {
    event.preventDefault();
    event.stopPropagation();

    document.querySelectorAll('[id^="dropdown-"]').forEach(dropdown => {
        if (dropdown.id !== `dropdown-${postId}`) {
            dropdown.classList.add('hidden');
        }
    });

    const target = document.getElementById(`dropdown-${postId}`);
    if (target) target.classList.toggle('hidden');
}

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
    }).catch(err => {
        console.error('Gagal menyalin link: ', err);
    });
}

document.addEventListener('DOMContentLoaded', function() {
    switchTab('posts');
});


function switchTab(type) {
    activeTab = type;

    document.querySelectorAll('.tab-btn').forEach(btn => {
        const isActive = btn.dataset.type === type;
        btn.classList.toggle('c-white', isActive);
        btn.classList.toggle('c-subtle', !isActive);
        btn.querySelector('.tab-indicator')?.classList.toggle('hidden', !isActive);
    });

    document.querySelectorAll('.tab-content').forEach(el => {
        el.classList.toggle('hidden', el.id !== `tab-content-${type}`);
    });
}

// Infinite scroll
window.addEventListener('scroll', () => {
    if (isLoading) return;
    const hasMore = activeTab === 'posts' ? hasMorePosts : hasMoreUsers;
    if (!hasMore) return;
    if (window.innerHeight + window.scrollY >= document.documentElement.scrollHeight - 200) {
        loadMoreResults();
    }
});

function loadMoreResults() {
    if (isLoading) return;
    const hasMore = activeTab === 'posts' ? hasMorePosts : hasMoreUsers;
    if (!hasMore || !KEYWORD) return;

    isLoading = true;
    const loadingBadge = document.getElementById('loading-badge');
    if (loadingBadge) loadingBadge.classList.remove('hidden');

    const offset = activeTab === 'posts' ? postOffset : userOffset;
    const url = `<?= base_url('search/search_ajax'); ?>?q=${encodeURIComponent(KEYWORD)}&type=${activeTab}&offset=${offset}`;

    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.length === 0) {
                if (activeTab === 'posts') hasMorePosts = false;
                else hasMoreUsers = false;
                if (loadingBadge) {
                    loadingBadge.innerHTML = "<span class='c-faint' style='text-transform:uppercase;letter-spacing:0.05em;font-size:10px'>Tidak ada hasil lagi.</span>";
                    setTimeout(() => loadingBadge.classList.add('hidden'), 2000);
                }
                return;
            }

            const container = document.getElementById(`tab-content-${activeTab}`);

            if (activeTab === 'posts') {
                data.forEach(post => {
                    const avatarBorderHTML = post.border
                        ? `<span class="avatar-border"><img src="${escapeHtml(post.border)}" alt=""></span>`
                        : '';

                    let mediaHTML = '';
                    if (post.file_url && typeof PaddockPostMedia !== 'undefined') {
                        mediaHTML = PaddockPostMedia.render(post.file_url);
                    }

                    const isOwner = <?= isset($current_user_id) && $current_user_id ? 'CURRENT_USER_ID' : '0'; ?> > 0 && post.user_id == <?= isset($current_user_id) && $current_user_id ? 'CURRENT_USER_ID' : '0'; ?>;
                    const isLiked = post.is_liked == true;
                    const likeBtnClass = isLiked ? 'is-liked' : '';
                    const sharePayload = escapeAttr(JSON.stringify({
                        id: post.id_post,
                        username: post.username,
                        text: String(post.content || '').slice(0, 160)
                    }));
                    const escapedContent = escapeHtml(post.content);
                    const escapedUsername = escapeHtml(post.username);
                    const userUrl = encodeURIComponent(post.username);
                    const userJs = escapeJsString(encodeURIComponent(post.username));
                    const escapedTeamName = escapeHtml(post.team_name || '');
                    const escapedTeamColor = escapeHtml(post.team_color || '#666');
                    const escapedTeamLogo = escapeHtml(post.team_logo || '');
                    const escapedAvatar = escapeHtml(post.avatar);
                    const escapedCreatedAt = escapeHtml(post.created_at);

                    const dropdownItems = isOwner
                        ? `
                            <button onclick="copyPostLink(event, '<?= base_url('post/'); ?>${userJs}/${post.id_post}', this)" style="width:100%;text-align:left;padding:8px 12px;font-size:12px;color:var(--text-muted);display:flex;align-items:center;gap:8px;" onmouseover="this.style.background='var(--bg-surface-hover)';this.style.color='var(--text-primary)'" onmouseout="this.style.background='';this.style.color='var(--text-muted)'">
                                <i data-lucide="link" style="width:14px;height:14px;"></i><span>Copy Link</span>
                            </button>
                            <a href="<?= base_url('post/edit/'); ?>${post.id_post}" onclick="event.stopPropagation();" style="width:100%;text-align:left;padding:8px 12px;font-size:12px;color:var(--text-muted);display:flex;align-items:center;gap:8px;border-top:1px solid var(--border-subtle);" onmouseover="this.style.background='var(--bg-surface-hover)';this.style.color='var(--text-primary)'" onmouseout="this.style.background='';this.style.color='var(--text-muted)'">
                                <i data-lucide="pencil" style="width:14px;height:14px;"></i><span>Edit</span>
                            </a>
                            <button onclick="event.stopPropagation(); deletePost(${post.id_post})" style="width:100%;text-align:left;padding:8px 12px;font-size:12px;color:var(--text-subtle);display:flex;align-items:center;gap:8px;border-top:1px solid var(--border-subtle);" onmouseover="this.style.background='var(--color-danger-bg)';this.style.color='var(--color-danger)'" onmouseout="this.style.background='';this.style.color='var(--text-subtle)'">
                                <i data-lucide="trash-2" style="width:14px;height:14px;"></i><span>Hapus</span>
                            </button>
                        `
                        : `
                            <button onclick="copyPostLink(event, '<?= base_url('post/'); ?>${userJs}/${post.id_post}', this)" style="width:100%;text-align:left;padding:8px 12px;font-size:12px;color:var(--text-muted);display:flex;align-items:center;gap:8px;" onmouseover="this.style.background='var(--bg-surface-hover)';this.style.color='var(--text-primary)'" onmouseout="this.style.background='';this.style.color='var(--text-muted)'">
                                <i data-lucide="link" style="width:14px;height:14px;"></i><span>Copy Link</span>
                            </button>
                            <button onclick="event.stopPropagation(); openReportPost(${post.id_post})" style="width:100%;text-align:left;padding:8px 12px;font-size:12px;color:var(--text-subtle);display:flex;align-items:center;gap:8px;border-top:1px solid var(--border-subtle);" onmouseover="this.style.background='var(--color-danger-bg)';this.style.color='var(--color-danger)'" onmouseout="this.style.background='';this.style.color='var(--text-subtle)'">
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
                postOffset += data.length;
                if (data.length < limit) hasMorePosts = false;
            } else {
                data.forEach(user => {
                    const avatarStyle = 'width:100%;height:100%';
                    const borderHTML = user.border
                        ? `<div class="absolute inset-0" style="width:100%;height:100%;pointer-events:none;transform:scale(1.25);transform-origin:center">
                               <img src="${escapeHtml(user.border)}" alt="Border" style="width:100%;height:100%;object-fit:contain">
                           </div>`
                        : '';
                    const verifiedHTML = user.verified
                        ? `<i data-lucide="badge-check" class="c-info flex-shrink-0" style="width:14px;height:14px"></i>`
                        : '';
                    
                    const isOwnProfile = <?= isset($current_user_id) && $current_user_id ? 'CURRENT_USER_ID' : 'null'; ?> && user.id_user == <?= isset($current_user_id) && $current_user_id ? 'CURRENT_USER_ID' : 'null'; ?>;
                        const followBtn = (!isOwnProfile && <?= isset($current_user_id) && $current_user_id ? 'CURRENT_USER_ID' : 'null'; ?>)
                        ? `<button onclick="event.preventDefault(); event.stopPropagation(); toggleFollowUser('${escapeJsString(user.id_user)}', this)" class="follow-btn flex-shrink-0 text-xs font-semibold rounded-full transition-all ${user.is_followed ? '' : 'btn-primary'}" style="${user.is_followed ? 'padding:4px 1rem;background:var(--bg-surface-hover);color:var(--text-secondary);border:1px solid var(--border-strong);transition:border-color 0.2s,color 0.2s' : 'padding:4px 1rem;border-color:var(--color-primary)'}"${user.is_followed ? " onmouseover=\"this.style.borderColor='var(--color-primary-border)';this.style.color='var(--color-primary)'\" onmouseout=\"this.style.borderColor='var(--border-strong)';this.style.color='var(--text-secondary)'\"" : ''}>${user.is_followed ? 'Following' : 'Follow'}</button>`
                        : '';

                    const escapedUserUrl = encodeURIComponent(user.username);
                    const escapedDisplayName = escapeHtml(user.display_name);
                    const escapedUsername = escapeHtml(user.username);
                    const escapedAvatar = escapeHtml(user.avatar);

                    const cardHTML = `
                        <div class="flex-row items-center justify-between p-4 user-row" data-user-id="${user.id_user}">
                            <a href="<?= base_url('user/'); ?>${escapedUserUrl}" class="flex-row items-center gap-3 flex-1 min-w-0">
                                <div class="relative w-10 h-10 flex-shrink-0 flex items-center justify-center">
                                    <div class="rounded-full overflow-hidden" style="${avatarStyle};background:var(--bg-surface-raised)">
                                        <img src="${escapedAvatar}" alt="${escapedUsername}" class="rounded-full" style="width:100%;height:100%;object-fit:cover">
                                    </div>
                                    ${borderHTML}

                                </div>
                                <div class="min-w-0">
                                    <div class="flex-row items-center gap-1-5">
                                        <span class="font-semibold text-xs truncate" style="color:var(--text-secondary)">${escapedDisplayName}</span>
                                        ${verifiedHTML}
                                    </div>
                                    <span class="text-caption" style="color:var(--text-subtle)">@${escapedUsername}</span>
                                    <span class="text-micro" style="color:var(--text-faint);margin-left:8px">• ${user.followers_count} followers</span>
                                </div>
                            </a>
                            ${followBtn}
                        </div>
                    `;
                    container.insertAdjacentHTML('beforeend', cardHTML);
                });
                userOffset += data.length;
                if (data.length < limit) hasMoreUsers = false;
            }

            var postsContainer = document.getElementById('tab-content-posts');
            if (postsContainer && typeof PaddockPostMedia !== 'undefined') {
                PaddockPostMedia.init(postsContainer);
            }
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }

            isLoading = false;
            if (loadingBadge) loadingBadge.classList.add('hidden');
        })
        .catch(err => {
            console.error('Search load error:', err);
            isLoading = false;
            if (loadingBadge) loadingBadge.classList.add('hidden');
        });
}
</script>