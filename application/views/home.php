<div class="tabs" style="margin-bottom:16px;">
    <button id="tab-for-you" onclick="switchTab('for_you')" class="tab <?= ($active_tab ?? 'for_you') === 'for_you' ? 'is-active' : '' ?>">
        For You
    </button>
    <button id="tab-following" onclick="switchTab('following')" class="tab <?= ($active_tab ?? 'for_you') === 'following' ? 'is-active' : '' ?>">
        Following
    </button>
</div>

<div id="post-container" class="space-y-4">
    <div id="tab-empty-following" class="hidden card p-8 text-center">
        <span class="empty-state__text">Belum ada postingan dari pengguna yang kamu ikuti.</span>
    </div>
    <div id="tab-empty-for-you" class="hidden card p-8 text-center">
        <span class="empty-state__text">Belum ada postingan terbaru.</span>
    </div>
            <?php if (!empty($all_posts)): ?>
        <?php
            $ads_enabled = $this->config->item('ads_enabled');
            $ads_min_gap = $this->config->item('ads_feed_min_gap') ?: 5;
            $ads_chance = $this->config->item('ads_feed_chance') ?: 0;
            $posts_since_ad = $ads_min_gap;
            $ad_cycle = 0;
        ?>
        <?php foreach ($all_posts as $idx => $post): ?>
            <?php
                if ($ads_enabled && !empty($feed_ads) && $posts_since_ad >= $ads_min_gap && mt_rand(1, 100) <= $ads_chance) {
                    $fa = $feed_ads[$ad_cycle % count($feed_ads)];
                    $ad_cycle++;
                    $posts_since_ad = 0;
                    echo '<article class="post-card overflow-hidden group relative">';
                    echo '<a href="' . base_url('ads/track_click/' . $fa['id_ad']) . '" target="_blank" rel="noopener noreferrer sponsored" class="absolute inset-0 z-10" aria-label="Iklan: ' . htmlspecialchars($fa['title']) . '"></a>';
                    echo '<div class="relative">';
                    echo '<img src="' . base_url($fa['image_url']) . '" alt="' . htmlspecialchars($fa['title']) . '" style="width:100%;height:auto;max-height:256px;object-fit:cover;">';
                    echo '<span class="badge-muted absolute" style="top:12px;left:12px;font-size:8px;padding:2px 8px;border-radius:var(--radius-pill);backdrop-filter:blur(8px);">Sponsored</span>';
                    echo '</div>';
                    echo '<div class="p-4 sm:p-5">';
                    echo '<p class="text-xs sm:text-sm font-bold c-white group-hover:c-primary transition-colors relative z-20">' . htmlspecialchars($fa['title']) . '</p>';
                    if (!empty($fa['description'])) {
                        echo '<p class="text-caption mt-1 relative z-20">' . htmlspecialchars($fa['description']) . '</p>';
                    }
                    echo '</div></article>';
                }
                $posts_since_ad++;
            ?>
            <?php 
                $is_liked = isset($post['is_liked']) && $post['is_liked'] == true; 
                $like_btn_class = $is_liked ? 'c-danger' : '';
                $like_icon_class = $is_liked ? 'fill-danger c-danger' : '';
                $post_content_attr = addslashes($post['content']);
                $post_category_attr = addslashes($post['post_category'] ?? '');
                $post_username_url = rawurlencode($post['username']);
                $post_avatar_attr = htmlspecialchars($post['avatar'], ENT_QUOTES, 'UTF-8');
                $post_border_attr = htmlspecialchars($post['border'] ?? '', ENT_QUOTES, 'UTF-8');
                $post_category_html = htmlspecialchars($post['category'], ENT_QUOTES, 'UTF-8');
                $post_team_color_attr = htmlspecialchars($post['team_color'] ?? '#666', ENT_QUOTES, 'UTF-8');
                $post_team_logo_attr = htmlspecialchars(assets_url($post['team_logo']), ENT_QUOTES, 'UTF-8');
            ?>
            <article class="post-card overflow-hidden group relative" data-post-id="<?= $post['id_post']; ?>" data-user-id="<?= $post['user_id']; ?>">
                
                <a href="<?= base_url('post/' . $post_username_url . '/' . $post['id_post']); ?>" class="absolute inset-0 z-10" aria-label="Lihat detail postingan"></a>

                <div class="p-4 sm:p-5 flex-row justify-between">
                    <div class="flex-row gap-3">
                        <div class="relative flex-shrink-0 select-none z-20" style="width:36px;height:36px;">
                            <div style="width:100%;height:100%;border-radius:50%;overflow:hidden;background:var(--bg-surface-raised);">
                                <a href="<?= base_url('user/' . $post_username_url); ?>">
                <img src="<?= $post_avatar_attr; ?>" alt="User" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
                                </a>
                            </div>
                            
                            <?php if (!empty($post['border'])): ?>
                                <div class="avatar-border">
                                    <img src="<?= $post_border_attr; ?>" alt="F1 Border Decoration">
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($post['is_online'])): ?>
                                <div class="online-indicator"></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="flex-col" style="justify-content:center;">
                            <div class="flex-row gap-2">
                                <a href="<?= base_url('user/' . $post_username_url); ?>" class="font-semibold text-xs sm:text-sm transition-colors relative z-20" style="color:var(--text-primary);"><?= htmlspecialchars($post['username'], ENT_QUOTES, 'UTF-8'); ?></a>
                                <?php if (!empty($post['team_name'])): ?>
                                    <span class="badge-pill" style="font-size:9px;font-weight:600;text-transform:uppercase;letter-spacing:0.06em;padding:2px 6px;border:1px solid var(--border-strong);background:<?= $post_team_color_attr ?>15;">
                                        <img src="<?= $post_team_logo_attr ?>" alt="<?= htmlspecialchars($post['team_name']) ?>" style="width:12px;height:12px;object-fit:contain;display:inline-block;vertical-align:middle;">
                                        <?= htmlspecialchars($post['team_name']) ?>
                                    </span>
                                <?php endif; ?>
                                <span class="c-faint" style="font-size:10px;">•</span>
                                <span class="badge-muted" style="font-size:8px;padding:2px 6px;border-radius:var(--radius-pill);text-transform:uppercase;letter-spacing:0.06em;"><?= $post_category_html; ?></span>
                            </div>
                            <span class="text-caption" style="font-size:10px;margin-top:2px;"><?= htmlspecialchars($post['created_at'], ENT_QUOTES, 'UTF-8'); ?></span>
                        </div>
                    </div>

                    <div class="relative z-30 flex-row">
                        <button onclick="toggleDropdown(event, <?= $post['id_post']; ?>)" class="btn-icon-sm c-subtle transition-colors">
                            <i data-lucide="more-horizontal" style="width:16px;height:16px;"></i>
                        </button>
                        
                        <div id="dropdown-<?= $post['id_post']; ?>" class="dropdown hidden" style="width:144px;top:32px;">
                            <button 
                                onclick="copyPostLink(event, '<?= base_url('post/' . $post_username_url . '/' . $post['id_post']); ?>', this)"
                                class="w-full flex-row gap-2 transition-colors" style="text-align:left;padding:8px 12px;font-size:12px;color:var(--text-muted);"
                                onmouseover="this.style.background='var(--bg-surface-hover)';this.style.color='var(--text-primary)'"
                                onmouseout="this.style.background='';this.style.color='var(--text-muted)'"
                            >
                                <i data-lucide="link" style="width:14px;height:14px;"></i>
                                <span>Copy Link</span>
                            </button>

                            <?php if (isset($current_user_id) && $current_user_id === (string)$post['user_id']): ?>
                                <a 
                                    href="<?= base_url('post/edit/' . $post['id_post']); ?>"
                                    onclick="event.stopPropagation();"
                                    class="w-full flex-row gap-2 transition-colors" style="text-align:left;padding:8px 12px;font-size:12px;color:var(--text-muted);border-top:1px solid var(--border-subtle);"
                                    onmouseover="this.style.background='var(--bg-surface-hover)';this.style.color='var(--text-primary)'"
                                    onmouseout="this.style.background='';this.style.color='var(--text-muted)'"
                                >
                                    <i data-lucide="pencil" style="width:14px;height:14px;"></i>
                                    <span>Edit</span>
                                </a>
                                <button 
                                    onclick="event.preventDefault(); event.stopPropagation(); deletePost(<?= $post['id_post']; ?>)"
                                    class="w-full flex-row gap-2 transition-colors" style="text-align:left;padding:8px 12px;font-size:12px;color:var(--text-subtle);border-top:1px solid var(--border-subtle);"
                                    onmouseover="this.style.background='var(--color-danger-bg)';this.style.color='var(--color-danger)'"
                                    onmouseout="this.style.background='';this.style.color='var(--text-subtle)'"
                                >
                                    <i data-lucide="trash-2" style="width:14px;height:14px;"></i>
                                    <span>Hapus</span>
                                </button>
                            <?php else: ?>
                                <button onclick="event.preventDefault(); event.stopPropagation(); openReportPost(<?= $post['id_post']; ?>)" class="w-full flex-row gap-2 transition-colors" style="text-align:left;padding:8px 12px;font-size:12px;color:var(--text-subtle);border-top:1px solid var(--border-subtle);" onmouseover="this.style.background='var(--color-danger-bg)';this.style.color='var(--color-danger)'" onmouseout="this.style.background='';this.style.color='var(--text-subtle)'">
                                    <i data-lucide="flag" style="width:14px;height:14px;"></i>
                                    <span>Report Post</span>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                    <?php if (!empty($post['file_url'])): ?>
                        <?php 
                            $images = explode(',', $post['file_url']);
                            $total_images = count($images);
                            
                            if ($total_images === 1) {
                                $grid_class = 'post-images--1';
                            } elseif ($total_images === 2) {
                                $grid_class = 'post-images--2';
                            } elseif ($total_images === 3) {
                                $grid_class = 'post-images--3';
                            } else {
                                $grid_class = 'post-images--4';
                            }
                            
                            $images_to_show = array_slice($images, 0, 4);
                        ?>
                        <div style="padding:0 20px;margin-bottom:4px;">
                            <div class="post-images <?= $grid_class; ?>" style="aspect-ratio:4/3;background:var(--bg-surface);border:1px solid var(--border-subtle);">
                                <?php foreach ($images_to_show as $index => $img_url): ?>
                                    <?php 
                                        $item_class = ($total_images === 3 && $index === 0) ? 'row-span-2 h-full' : 'h-full';
                                    ?>
                                    <div class="relative <?= $item_class; ?> overflow-hidden" style="background:var(--bg-surface-raised);">
                                        <img src="<?= htmlspecialchars(trim($img_url), ENT_QUOTES, 'UTF-8'); ?>" alt="Post Media" loading="lazy" style="width:100%;height:100%;object-fit:cover;">
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                <div class="p-4 sm:p-5 space-y-3" style="padding-top:8px;">
                    <p class="text-xs sm:text-sm leading-relaxed whitespace-pre-wrap break-words" style="color:var(--text-secondary);"><?= htmlspecialchars($post['content'], ENT_QUOTES, 'UTF-8'); ?></p>
                    
                    <div class="flex-row gap-4" style="padding-top:8px;border-top:1px solid var(--border-subtle);color:var(--text-subtle);font-size:11px;position:relative;z-index:20;">
                        <button 
                            onclick="toggleLike(event, <?= $post['id_post']; ?>, this)" 
                            class="flex-row gap-1-5 transition-colors <?= $like_btn_class; ?>"
                        >
                            <i data-lucide="heart" style="width:16px;height:16px;transition:transform 0.15s ease;" class="<?= $like_icon_class; ?>"></i>
                            <span class="font-semibold count-likes"><?= $post['likes_count']; ?></span>
                        </button>

                        <a 
                            href="<?= base_url('post/' . $post['username'] . '/' . $post['id_post']); ?>" 
                            class="flex-row gap-1-5 transition-colors"
                            style="color:var(--text-subtle);"
                            onmouseover="this.style.color='var(--color-info)'"
                            onmouseout="this.style.color='var(--text-subtle)'"
                        >
                            <i data-lucide="message-square" style="width:16px;height:16px;"></i>
                            <span class="font-semibold"><?= $post['comments_count']; ?></span>
                        </a>
                    </div>
                </div>
            </article>

        <?php endforeach; ?>
    <?php else: ?>
        <div class="card p-8 text-center">
            <span class="empty-state__text">Belum ada postingan terbaru.</span>
        </div>
    <?php endif; ?>

    <?php if (isset($is_guest) && $is_guest): ?>
        <div id="guest-prompt" class="card p-6 text-center" style="border-radius:var(--radius-xl);">
            <div style="width:48px;height:48px;margin:0 auto 16px;border-radius:50%;background:var(--color-primary-bg);display:flex;align-items:center;justify-content:center;">
                <i data-lucide="log-in" style="width:24px;height:24px;" class="c-primary"></i>
            </div>
            <h3 class="text-heading text-sm" style="margin-bottom:8px;">Ingin Melihat Lebih Banyak?</h3>
            <p class="text-small" style="margin-bottom:20px;line-height:1.6;">Masuk atau daftar akun untuk menikmati seluruh postingan dan fitur interaktif PaddockID.</p>
            <a href="<?= base_url('auth'); ?>" class="btn btn-primary" style="display:inline-block;">Masuk / Daftar</a>
        </div>
    <?php endif; ?>
</div>

<div id="loading-badge" class="hidden text-center p-6">
    <div class="flex-row gap-2" style="justify-content:center;color:var(--text-subtle);font-size:12px;">
        <div class="spinner spinner--sm"></div>
        Memuat postingan lainnya...
    </div>
</div>
</main>

<script>
function toggleDropdown(event, postId) {
    event.preventDefault();
    event.stopPropagation(); 

    document.querySelectorAll('[id^="dropdown-"]').forEach(dropdown => {
        if (dropdown.id !== `dropdown-${postId}`) {
            dropdown.classList.add('hidden');
        }
    });

    const targetDropdown = document.getElementById(`dropdown-${postId}`);
    targetDropdown.classList.toggle('hidden');
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

document.addEventListener('click', function (e) {
    document.querySelectorAll('[id^="dropdown-"]').forEach(dropdown => {
        if (!dropdown.contains(e.target)) {
            dropdown.classList.add('hidden');
        }
    });
});
</script>

<script>
const limit = 5;
let offset = 5;
let isLoading = false;
let hasMoreData = true;

const categorySlug = '<?= isset($active_category_slug) ? $active_category_slug : ''; ?>';
const IS_GUEST = <?= (isset($is_guest) && $is_guest) ? 'true' : 'false'; ?>;
const INITIAL_TAB = '<?= $active_tab ?? 'for_you'; ?>';
const STORED_TAB = <?= json_encode(get_pref_cookie('feed_tab', '')); ?>;

let currentTab = (STORED_TAB === INITIAL_TAB) ? STORED_TAB : INITIAL_TAB;

if (IS_GUEST) {
    hasMoreData = false;
}

function switchTab(tab) {
    if (tab === currentTab) return;
    if (IS_GUEST && tab === 'following') return;

    currentTab = tab;
    if (typeof setPreference === 'function') setPreference('feed_tab', tab);
    try { localStorage.setItem('feed_tab', tab); } catch (e) {}

    document.getElementById('tab-for-you').className = tab === 'for_you' ? 'tab is-active' : 'tab';
    document.getElementById('tab-following').className = tab === 'following' ? 'tab is-active' : 'tab';

    offset = 0;
    hasMoreData = true;
    isLoading = false;
    window._postsSinceAd = <?= $this->config->item('ads_feed_min_gap') ?: 5; ?>;
    window._adCycle = 0;
    document.getElementById('post-container').querySelectorAll('article').forEach(el => el.remove());
    document.getElementById('loading-badge').classList.add('hidden');

    loadMoreFresh();
}

function loadMoreFresh() {
    isLoading = true;
    const loadingBadge = document.getElementById('loading-badge');
    loadingBadge.classList.remove('hidden');

    let url = `<?= base_url('home/load_more_posts'); ?>?offset=${offset}&tab=${currentTab}`;
    if (categorySlug) url += `&category=${categorySlug}`;

    fetch(url)
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('post-container');
            document.getElementById('tab-empty-for-you').classList.add('hidden');
            document.getElementById('tab-empty-following').classList.add('hidden');

            if (data.length === 0) {
                hasMoreData = false;
                loadingBadge.classList.add('hidden');
                if (currentTab === 'following') {
                    document.getElementById('tab-empty-following').classList.remove('hidden');
                } else {
                    document.getElementById('tab-empty-for-you').classList.remove('hidden');
                }
                return;
            }

            renderPosts(data, container);
            offset += limit;
            isLoading = false;
            loadingBadge.classList.add('hidden');
        })
        .catch(err => {
            console.error('Gagal memuat postingan:', err);
            isLoading = false;
            loadingBadge.classList.add('hidden');
        });
}

window.addEventListener('scroll', () => {
    if (isLoading || !hasMoreData) return;
    if (window.innerHeight + window.scrollY >= document.documentElement.scrollHeight - 150) {
        loadMorePosts();
    }
});

function renderPosts(posts, container) {
    if (typeof window._feedAdsCache === 'undefined') {
        window._feedAdsCache = null;
        window._feedAdsFetched = false;
    }
    if (!window._feedAdsFetched) {
        window._feedAdsFetched = true;
        fetch('<?= base_url("ads/get_active"); ?>?position=feed&limit=10')
            .then(r => r.json())
            .then(ads => { window._feedAdsCache = ads || []; })
            .catch(() => { window._feedAdsCache = []; });
    }

    const adsMinGap = <?= $this->config->item('ads_feed_min_gap') ?: 5; ?>;
    const adsChance = <?= $this->config->item('ads_feed_chance') ?: 0; ?>;
    const feedAds = window._feedAdsCache || [];

    if (typeof window._postsSinceAd === 'undefined') window._postsSinceAd = adsMinGap;
    if (typeof window._adCycle === 'undefined') window._adCycle = 0;

    posts.forEach((post, idx) => {
        window._postsSinceAd++;

        if (adsChance > 0 && feedAds.length > 0 && window._postsSinceAd >= adsMinGap && Math.random() * 100 <= adsChance) {
            const ad = feedAds[window._adCycle % feedAds.length];
            window._adCycle++;
            window._postsSinceAd = 0;
            const adHTML = `
                <article class="post-card overflow-hidden group relative">
                    <a href="<?= base_url("ads/track_click/"); ?>${ad.id_ad}" target="_blank" rel="noopener noreferrer sponsored" class="absolute inset-0 z-10" aria-label="Iklan: ${escapeHtml(ad.title)}"></a>
                    <div class="relative">
                        <img src="${escapeHtml(ad.image_url_full)}" alt="${escapeHtml(ad.title)}" style="width:100%;height:auto;max-height:256px;object-fit:cover;">
                        <span class="badge-muted absolute" style="top:12px;left:12px;font-size:8px;padding:2px 8px;border-radius:var(--radius-pill);backdrop-filter:blur(8px);">Sponsored</span>
                    </div>
                    <div class="p-4 sm:p-5">
                        <p class="text-xs sm:text-sm font-bold c-white transition-colors relative z-20" onmouseover="this.style.color='var(--color-primary)'" onmouseout="this.style.color='var(--text-primary)'">${escapeHtml(ad.title)}</p>
                        ${ad.description ? '<p class="text-caption mt-1 relative z-20">' + escapeHtml(ad.description) + '</p>' : ''}
                    </div>
                </article>
            `;
            container.insertAdjacentHTML('beforeend', adHTML);
        }

        const avatarBorderHTML = post.border 
            ? `<div class="avatar-border"><img src="${escapeHtml(post.border)}" alt="F1 Border Decoration"></div>` 
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
                    <div class="relative ${itemClass} overflow-hidden" style="background:var(--bg-surface-raised);">
                        <img src="${escapeHtml(url)}" alt="Post Media ${index + 1}" loading="lazy" style="width:100%;height:100%;object-fit:cover;">
                    </div>
                `;
            });

            mediaHTML = `
                <div style="padding:0 20px;margin-bottom:4px;">
                    <div class="post-images ${gridClass}" style="aspect-ratio:4/3;background:var(--bg-surface);border:1px solid var(--border-subtle);">
                        ${imagesTemplate}
                    </div>
                </div>
            `;
        }

        const isOwner = CURRENT_USER_ID > 0 && post.user_id == CURRENT_USER_ID;
        const dynamicLikeBtnClass = post.is_liked ? 'c-danger' : '';
        const dynamicLikeIconClass = post.is_liked ? 'fill-danger c-danger' : '';
        const safeContent = escapeHtml(post.content);
        const safeUsername = escapeHtml(post.username);
        const safeUserUrl = encodeURIComponent(post.username);
        const safeUserJs = escapeJsString(encodeURIComponent(post.username));
        const safeCategory = escapeHtml(post.category);
        const safeTeamName = escapeHtml(post.team_name || '');
        const safeTeamColor = escapeHtml(post.team_color || '#666');
        const safeTeamLogo = escapeHtml(post.team_logo || '');
        const safeAvatar = escapeHtml(post.avatar);

        const dropdownItems = isOwner
            ? `
                <button onclick="copyPostLink(event, '<?= base_url('post/'); ?>${safeUserJs}/${post.id_post}', this)" style="width:100%;text-align:left;padding:8px 12px;font-size:12px;color:var(--text-muted);display:flex;align-items:center;gap:8px;" onmouseover="this.style.background='var(--bg-surface-hover)';this.style.color='var(--text-primary)'" onmouseout="this.style.background='';this.style.color='var(--text-muted)'">
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
                <button onclick="copyPostLink(event, '<?= base_url('post/'); ?>${safeUserJs}/${post.id_post}', this)" style="width:100%;text-align:left;padding:8px 12px;font-size:12px;color:var(--text-muted);display:flex;align-items:center;gap:8px;" onmouseover="this.style.background='var(--bg-surface-hover)';this.style.color='var(--text-primary)'" onmouseout="this.style.background='';this.style.color='var(--text-muted)'">
                    <i data-lucide="link" style="width:14px;height:14px;"></i><span>Copy Link</span>
                </button>
                <button onclick="event.stopPropagation(); openReportPost(${post.id_post})" style="width:100%;text-align:left;padding:8px 12px;font-size:12px;color:var(--text-subtle);display:flex;align-items:center;gap:8px;border-top:1px solid var(--border-subtle);" onmouseover="this.style.background='var(--color-danger-bg)';this.style.color='var(--color-danger)'" onmouseout="this.style.background='';this.style.color='var(--text-subtle)'">
                    <i data-lucide="flag" style="width:14px;height:14px;"></i><span>Report Post</span>
                </button>
            `;

        const cardHTML = `
            <article class="post-card overflow-hidden group relative" data-post-id="${post.id_post}" data-user-id="${post.user_id}">
                <a href="<?= base_url('post/'); ?>${safeUserUrl}/${post.id_post}" class="absolute inset-0 z-10" aria-label="Lihat detail postingan"></a>
                <div class="p-4 sm:p-5 flex-row justify-between">
                    <div class="flex-row gap-3">
                        <div class="relative flex-shrink-0 select-none z-20" style="width:36px;height:36px;">
                            <div style="width:100%;height:100%;border-radius:50%;overflow:hidden;background:var(--bg-surface-raised);">
                                <a href="<?= base_url('user/'); ?>${safeUserUrl}"><img src="${safeAvatar}" alt="User" style="width:100%;height:100%;object-fit:cover;border-radius:50%;"></a>
                            </div>
                            ${avatarBorderHTML}
                            ${onlineHTML}
                        </div>
                        <div class="flex-col" style="justify-content:center;">
                            <div class="flex-row gap-2">
                                <a href="<?= base_url('user/'); ?>${safeUserUrl}" class="font-semibold text-xs sm:text-sm transition-colors relative z-20" style="color:var(--text-primary);">${safeUsername}</a>
                                ${post.team_name ? '<span class="badge-pill" style="font-size:9px;font-weight:600;text-transform:uppercase;letter-spacing:0.06em;padding:2px 6px;border:1px solid var(--border-strong);background:' + safeTeamColor + '15;"><img src="' + safeTeamLogo + '" alt="' + safeTeamName + '" style="width:12px;height:12px;object-fit:contain;display:inline-block;vertical-align:middle;"> ' + safeTeamName + '</span>' : ''}
                                <span class="c-faint" style="font-size:10px;">•</span>
                                <span class="badge-muted" style="font-size:8px;padding:2px 6px;border-radius:var(--radius-pill);text-transform:uppercase;letter-spacing:0.06em;">${safeCategory}</span>
                            </div>
                            <span class="text-caption" style="font-size:10px;margin-top:2px;">${escapeHtml(post.created_at)}</span>
                        </div>
                    </div>
                    <div class="relative z-30 flex-row">
                        <button onclick="toggleDropdown(event, ${post.id_post})" class="btn-icon-sm c-subtle transition-colors">
                            <i data-lucide="more-horizontal" style="width:16px;height:16px;"></i>
                        </button>
                        <div id="dropdown-${post.id_post}" class="dropdown hidden" style="width:144px;top:32px;">
                            ${dropdownItems}
                        </div>
                    </div>
                </div>
                ${mediaHTML}
                <div class="p-4 sm:p-5 space-y-3" style="padding-top:8px;">
                    <p class="text-xs sm:text-sm leading-relaxed whitespace-pre-wrap break-words" style="color:var(--text-secondary);">${safeContent}</p>
                    <div class="flex-row gap-4" style="padding-top:8px;border-top:1px solid var(--border-subtle);color:var(--text-subtle);font-size:11px;position:relative;z-index:20;">
                        <button onclick="toggleLike(event, ${post.id_post}, this)" class="flex-row gap-1-5 transition-colors ${dynamicLikeBtnClass}">
                            <i data-lucide="heart" style="width:16px;height:16px;" class="${dynamicLikeIconClass}"></i>
                            <span class="font-semibold count-likes">${post.likes_count}</span>
                        </button>
                        <a href="<?= base_url('post/'); ?>${safeUserUrl}/${post.id_post}" class="flex-row gap-1-5 transition-colors" style="color:var(--text-subtle);" onmouseover="this.style.color='var(--color-info)'" onmouseout="this.style.color='var(--text-subtle)'">
                            <i data-lucide="message-square" style="width:16px;height:16px;"></i>
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
}

function loadMorePosts() {
    if (IS_GUEST) {
        isLoading = false;
        return;
    }

    isLoading = true;
    const loadingBadge = document.getElementById('loading-badge');
    loadingBadge.classList.remove('hidden'); 
    
    let url = `<?= base_url('home/load_more_posts'); ?>?offset=${offset}&tab=${currentTab}`;
    if (categorySlug) url += `&category=${categorySlug}`;
    
    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.length === 0) {
                hasMoreData = false;
                loadingBadge.innerHTML = '<span style="color:var(--text-faint);text-transform:uppercase;font-size:10px;letter-spacing:0.06em;">Kamu telah mencapai batas akhir postingan.</span>';
                return;
            }
            
            renderPosts(data, document.getElementById('post-container'));
            offset += limit;               
            isLoading = false;             
            loadingBadge.classList.add('hidden'); 
        })
        .catch(err => {
            console.error('Gagal memproses lazy-load posts:', err);
            isLoading = false;
            loadingBadge.classList.add('hidden');
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
                    buttonElement.classList.add('c-danger');
                    icon.classList.add('fill-danger', 'c-danger');
                } else {
                    buttonElement.classList.remove('c-danger');
                    icon.classList.remove('fill-danger', 'c-danger');
                }
                countSpan.innerText = data.likes_count;
            }
        })
        .catch(err => {
            console.error('Gagal memproses like:', err);
            showToast('Gagal menyukai postingan. Coba lagi.', 'error');
        });
}
</script>
