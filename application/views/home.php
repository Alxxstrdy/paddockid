<header class="h-masthead">
    <div class="h-tabs" role="tablist" aria-label="Pilih feed">
        <button id="tab-for-you" onclick="switchTab('for_you')" class="tab <?= ($active_tab ?? 'for_you') === 'for_you' ? 'is-active' : '' ?>">For You</button>
        <button id="tab-following" onclick="switchTab('following')" class="tab <?= ($active_tab ?? 'for_you') === 'following' ? 'is-active' : '' ?>">Following</button>
    </div>
</header>

<div id="post-container" class="h-feed" data-sk="feed">
    <div id="tab-empty-following" class="hidden h-empty">
        <span>Belum ada postingan dari pengguna yang kamu ikuti.</span>
    </div>
    <div id="tab-empty-for-you" class="hidden h-empty">
        <span>Belum ada postingan terbaru.</span>
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
                    ?>
                    <article class="h-post h-ad relative">
                        <a href="<?= base_url('ads/track_click/' . $fa['id_ad']); ?>" target="_blank" rel="noopener noreferrer sponsored" class="h-post__link" aria-label="Iklan: <?= htmlspecialchars($fa['title'], ENT_QUOTES, 'UTF-8'); ?>"></a>
                        <div class="h-post__body">
                            <?php if (!empty($fa['image_url'])): ?>
                                <div class="h-ad__img">
                                    <img src="<?= base_url($fa['image_url']); ?>" alt="<?= htmlspecialchars($fa['title'], ENT_QUOTES, 'UTF-8'); ?>" style="max-height:256px;object-fit:cover;" loading="lazy">
                                </div>
                            <?php endif; ?>
                            <p class="h-ad__tag">Sponsored</p>
                            <p class="h-ad__title"><?= htmlspecialchars($fa['title'], ENT_QUOTES, 'UTF-8'); ?></p>
                            <?php if (!empty($fa['description'])): ?>
                                <p class="h-ad__desc"><?= htmlspecialchars($fa['description'], ENT_QUOTES, 'UTF-8'); ?></p>
                            <?php endif; ?>
                        </div>
                    </article>
                    <?php
                }
                $posts_since_ad++;
            ?>
            <?php
                $is_liked = isset($post['is_liked']) && $post['is_liked'] == true;
                $like_btn_class = $is_liked ? 'is-liked' : '';
                $post_username_url = rawurlencode($post['username']);
                $post_avatar_attr = htmlspecialchars($post['avatar'], ENT_QUOTES, 'UTF-8');
                $post_border_attr = htmlspecialchars($post['border'] ?? '', ENT_QUOTES, 'UTF-8');
                $post_team_color_attr = htmlspecialchars($post['team_color'] ?? '#666', ENT_QUOTES, 'UTF-8');
                $post_team_logo_attr = htmlspecialchars(assets_url($post['team_logo']), ENT_QUOTES, 'UTF-8');
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
                                <a href="<?= base_url('post/' . $post_username_url . '/' . $post['id_post']); ?>" class="h-post__time"><?= htmlspecialchars($post['created_at'], ENT_QUOTES, 'UTF-8'); ?></a>
                            </div>
                        </div>
                    </div>

                    <div class="h-post__menu-wrap">
                        <button onclick="toggleDropdown(event, <?= $post['id_post']; ?>)" class="h-post__menu" type="button" aria-label="Menu postingan">
                            <i data-lucide="more-horizontal"></i>
                        </button>

                        <div id="dropdown-<?= $post['id_post']; ?>" class="dropdown hidden" style="width:144px;top:34px;">
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
                                <button
                                    onclick="event.preventDefault(); event.stopPropagation(); openReportPost(<?= $post['id_post']; ?>)"
                                    class="w-full flex-row gap-2 transition-colors" style="text-align:left;padding:8px 12px;font-size:12px;color:var(--text-subtle);"
                                    onmouseover="this.style.background='var(--color-danger-bg)';this.style.color='var(--color-danger)'"
                                    onmouseout="this.style.background='';this.style.color='var(--text-subtle)'"
                                >
                                    <i data-lucide="flag" style="width:14px;height:14px;"></i>
                                    <span>Report Post</span>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </header>

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
                    <div class="h-post__media">
                        <div class="post-images <?= $grid_class; ?>">
                            <?php foreach ($images_to_show as $index => $img_url): ?>
                                <?php
                                    $item_class = ($total_images === 3 && $index === 0) ? 'row-span-2 h-full' : 'h-full';
                                ?>
                                <div class="relative <?= $item_class; ?> overflow-hidden">
                                    <img src="<?= htmlspecialchars(trim($img_url), ENT_QUOTES, 'UTF-8'); ?>" alt="Media postingan <?= $index + 1; ?>" loading="lazy">
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
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
        <div class="h-empty">
            <span>Belum ada postingan terbaru.</span>
        </div>
    <?php endif; ?>

    <?php if (isset($is_guest) && $is_guest): ?>
        <div id="guest-prompt" class="h-guest">
            <div class="h-guest__icon">
                <i data-lucide="users"></i>
            </div>
            <div class="h-guest__text">
                <h3 class="h-guest__title">Masuk ke paddock</h3>
                <p class="h-guest__desc">Ikut berdiskusi, beri reaksi, dan terhubung dengan fans F1 Indonesia.</p>
            </div>
            <a href="<?= base_url('auth'); ?>" class="btn btn-primary btn-sm" style="flex-shrink:0;">Masuk / Daftar</a>
        </div>
    <?php endif; ?>
</div>

<div id="loading-badge" class="hidden h-loading">
    <div class="spinner spinner--sm"></div>
    <span>Memuat postingan lainnya…</span>
</div>

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
            const adImage = ad.image_url_full
                ? `<div class="h-ad__img"><img src="${escapeHtml(ad.image_url_full)}" alt="${escapeHtml(ad.title)}" style="max-height:256px;object-fit:cover;" loading="lazy"></div>`
                : '';
            const adHTML = `
                <article class="h-post h-ad relative">
                    <a href="<?= base_url("ads/track_click/"); ?>${ad.id_ad}" target="_blank" rel="noopener noreferrer sponsored" class="h-post__link" aria-label="Iklan: ${escapeHtml(ad.title)}"></a>
                    <div class="h-post__body">
                        ${adImage}
                        <p class="h-ad__tag">Sponsored</p>
                        <p class="h-ad__title">${escapeHtml(ad.title)}</p>
                        ${ad.description ? '<p class="h-ad__desc">' + escapeHtml(ad.description) + '</p>' : ''}
                    </div>
                </article>
            `;
            container.insertAdjacentHTML('beforeend', adHTML);
        }

        const avatarBorderHTML = post.border
            ? `<span class="avatar-border"><img src="${escapeHtml(post.border)}" alt=""></span>`
            : '';

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
                    <div class="relative ${itemClass} overflow-hidden">
                        <img src="${escapeHtml(url)}" alt="Media postingan ${index + 1}" loading="lazy">
                    </div>
                `;
            });

            mediaHTML = `
                <div class="h-post__media">
                    <div class="post-images ${gridClass}">
                        ${imagesTemplate}
                    </div>
                </div>
            `;
        }

        const isOwner = CURRENT_USER_ID > 0 && post.user_id == CURRENT_USER_ID;
        const safeContent = escapeHtml(post.content);
        const safeUsername = escapeHtml(post.username);
        const safeUserUrl = encodeURIComponent(post.username);
        const safeUserJs = escapeJsString(encodeURIComponent(post.username));
        const safeTeamName = escapeHtml(post.team_name || '');
        const safeTeamColor = escapeHtml(post.team_color || '#666');
        const safeTeamLogo = escapeHtml(post.team_logo || '');
        const safeAvatar = escapeHtml(post.avatar);
        const isLiked = post.is_liked == true;
        const likeBtnClass = isLiked ? 'is-liked' : '';

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
                <a href="<?= base_url('post/'); ?>${safeUserUrl}/${post.id_post}" class="h-post__link" aria-label="Lihat detail postingan"></a>

                <header class="h-post__head">
                    <div class="h-post__author">
                        <div class="relative flex-shrink-0 select-none" style="width:40px;height:40px;">
                            <div class="h-post__avatar">
                                <a href="<?= base_url('user/'); ?>${safeUserUrl}"><img src="${safeAvatar}" alt="Avatar ${safeUsername}" loading="lazy" onerror="this.src='<?= assets_url('default.jpg'); ?>';"></a>
                            </div>
${avatarBorderHTML}

                </div>

                        <div class="min-w-0">
                            <div class="h-post__who">
                                <a href="<?= base_url('user/'); ?>${safeUserUrl}" class="h-post__name">${safeUsername}</a>
                                ${post.team_name ? '<span class="h-post__team" style="--tc:' + safeTeamColor + ';"><img src="' + safeTeamLogo + '" alt="' + safeTeamName + '"> ' + safeTeamName + '</span>' : ''}
                            </div>
                            <div class="h-post__meta">
                                <a href="<?= base_url('post/'); ?>${safeUserUrl}/${post.id_post}" class="h-post__time">${escapeHtml(post.created_at)}</a>
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
                    <p class="h-post__text">${linkifyContent(safeContent)}</p>
                </div>

                <footer class="h-post__foot">
                    <button onclick="toggleLike(event, ${post.id_post}, this)" type="button" class="h-post__action h-post__like ${likeBtnClass}" style="color:${isLiked ? 'var(--color-danger)' : 'var(--text-subtle)'};" onmouseover="if(!this.classList.contains('is-liked'))this.style.color='var(--color-danger)'" onmouseout="if(!this.classList.contains('is-liked'))this.style.color='var(--text-subtle)'">
                        <i data-lucide="heart" class="${isLiked ? 'fill-danger' : ''}" style="color:${isLiked ? 'var(--color-danger)' : 'var(--text-subtle)'};"></i>
                        <span class="count-likes font-semibold">${post.likes_count}</span>
                    </button>
                    <a href="<?= base_url('post/'); ?>${safeUserUrl}/${post.id_post}" class="h-post__action" style="color:var(--text-subtle);">
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

    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.length === 0) {
                hasMoreData = false;
                loadingBadge.innerHTML = '<span>Kamu telah mencapai batas akhir postingan.</span>';
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
</script>