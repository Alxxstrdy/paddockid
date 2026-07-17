<div class="flex items-center border-b border-white/[0.04] mb-4">
    <button id="tab-for-you" onclick="switchTab('for_you')" class="flex-1 pb-3 text-xs font-semibold text-center transition-colors border-b-2 <?= ($active_tab ?? 'for_you') === 'for_you' ? 'text-white border-red-500' : 'text-slate-500 border-transparent hover:text-slate-300' ?>">
        For You
    </button>
    <button id="tab-following" onclick="switchTab('following')" class="flex-1 pb-3 text-xs font-semibold text-center transition-colors border-b-2 <?= ($active_tab ?? 'for_you') === 'following' ? 'text-white border-red-500' : 'text-slate-500 border-transparent hover:text-slate-300' ?>">
        Following
    </button>
</div>

<div id="post-container" class="space-y-4">
    <div id="tab-empty-following" class="hidden glass-card p-8 text-center text-slate-500 text-xs">
        Belum ada postingan dari pengguna yang kamu ikuti.
    </div>
    <div id="tab-empty-for-you" class="hidden glass-card p-8 text-center text-slate-500 text-xs">
        Belum ada postingan terbaru.
    </div>
            <?php if (!empty($all_posts)): ?>
        <?php
            $ads_enabled = $this->config->item('ads_enabled');
            $ads_min_gap = $this->config->item('ads_feed_min_gap') ?: 5;
            $ads_chance = $this->config->item('ads_feed_chance') ?: 0;
            $posts_since_ad = $ads_min_gap; // Start at min_gap so first ad can appear immediately
            $ad_cycle = 0;
        ?>
        <?php foreach ($all_posts as $idx => $post): ?>
            <?php
                // Random ad injection
                if ($ads_enabled && !empty($feed_ads) && $posts_since_ad >= $ads_min_gap && mt_rand(1, 100) <= $ads_chance) {
                    $fa = $feed_ads[$ad_cycle % count($feed_ads)];
                    $ad_cycle++;
                    $posts_since_ad = 0;
                    echo '<article class="glass-card overflow-hidden group transition-all relative hover:bg-white/[0.02]">';
                    echo '<a href="' . base_url('ads/track_click/' . $fa['id_ad']) . '" target="_blank" rel="noopener noreferrer sponsored" class="absolute inset-0 z-10" aria-label="Iklan: ' . htmlspecialchars($fa['title']) . '"></a>';
                    echo '<div class="relative">';
                    echo '<img src="' . base_url($fa['image_url']) . '" alt="' . htmlspecialchars($fa['title']) . '" class="w-full h-auto max-h-64 object-cover">';
                    echo '<span class="absolute top-3 left-3 text-[8px] font-bold uppercase tracking-widest px-2 py-0.5 rounded-full bg-black/60 backdrop-blur-sm text-slate-400 border border-white/[0.08]">Sponsored</span>';
                    echo '</div>';
                    echo '<div class="p-4 sm:p-5">';
                    echo '<p class="text-xs sm:text-sm font-bold text-white group-hover:text-red-400 transition-colors relative z-20">' . htmlspecialchars($fa['title']) . '</p>';
                    if (!empty($fa['description'])) {
                        echo '<p class="text-[11px] text-slate-500 mt-1 leading-relaxed relative z-20">' . htmlspecialchars($fa['description']) . '</p>';
                    }
                    echo '</div></article>';
                }
                $posts_since_ad++;
            ?>
            <?php 
                $is_liked = isset($post['is_liked']) && $post['is_liked'] == true; 
                $like_btn_class = $is_liked ? 'text-red-500' : 'hover:text-red-500';
                $like_icon_class = $is_liked ? 'fill-red-500 text-red-500' : '';
                $post_content_attr = addslashes($post['content']);
                $post_category_attr = addslashes($post['post_category'] ?? '');
            ?>
            <article class="glass-card overflow-hidden group transition-all relative hover:bg-white/[0.02]" data-post-id="<?= $post['id_post']; ?>" data-user-id="<?= $post['user_id']; ?>">
                
                <a href="<?= base_url('post/' . $post['username'] . '/' . $post['id_post']); ?>" class="absolute inset-0 z-10" aria-label="Lihat detail postingan"></a>

                <div class="p-4 sm:p-5 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="relative w-9 h-9 flex items-center justify-center select-none z-20">
                            <div class="<?= !empty($post['border']) ? 'w-[84%] h-[84%]' : 'w-full h-full'; ?> rounded-full overflow-hidden bg-slate-800">
                                <a href="<?= base_url('user/' . $post['username']); ?>">
                <img src="<?= $post['avatar']; ?>" alt="User" class="w-full h-full object-cover rounded-full">
                                </a>
                            </div>
                            
                            <?php if (!empty($post['border'])): ?>
                                <div class="absolute inset-0 w-full h-full pointer-events-none scale-[1] transform origin-center">
                                    <img src="<?= $post['border']; ?>" alt="F1 Border Decoration" class="w-full h-full object-contain">
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($post['is_online'])): ?>
                                <div class="online-indicator"></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="flex flex-col justify-center">
                            <div class="flex items-center gap-2">
                                <a href="<?= base_url('user/' . $post['username']); ?>" class="font-semibold text-xs sm:text-sm hover:text-red-400 cursor-pointer transition-colors relative z-20"><?= htmlspecialchars($post['username'], ENT_QUOTES, 'UTF-8'); ?></a>
                                <?php if (!empty($post['team_name'])): ?>
                                    <span class="inline-flex items-center gap-1 text-[9px] font-semibold uppercase tracking-wider px-1.5 py-0.5 rounded-full border border-white/[0.08]" style="background:<?= $post['team_color'] ?? '#666' ?>15;">
                                        <img src="<?= assets_url($post['team_logo']) ?>" alt="<?= htmlspecialchars($post['team_name']) ?>" class="w-3 h-3 object-contain">
                                        <?= htmlspecialchars($post['team_name']) ?>
                                    </span>
                                <?php endif; ?>
                                <span class="text-slate-600 text-[10px]">•</span>
                                <span class="inline-flex items-center text-[8px] px-1.5 py-0.5 font-semibold text-white bg-white/[0.04] border border-white/[0.06] rounded-full uppercase tracking-wider"><?= $post['category']; ?></span>
                            </div>
                            <span class="text-[10px] text-slate-500 mt-0.5"><?= $post['created_at']; ?></span>
                        </div>
                    </div>

                    <div class="relative z-30 flex items-center">
                        <button onclick="toggleDropdown(event, <?= $post['id_post']; ?>)" class="text-slate-500 hover:text-slate-300 transition-colors p-1 rounded-md hover:bg-white/[0.05]">
                            <i data-lucide="more-horizontal" class="w-4 h-4"></i>
                        </button>
                        
                        <div id="dropdown-<?= $post['id_post']; ?>" class="hidden absolute right-0 top-8 w-36 bg-slate-900/95 backdrop-blur-md border border-white/[0.08] rounded-lg shadow-xl overflow-hidden py-1 text-xs text-slate-300">
                            <button 
                                onclick="copyPostLink(event, '<?= base_url('post/' . $post['username'] . '/' . $post['id_post']); ?>', this)"
                                class="w-full text-left px-3 py-2 hover:bg-white/[0.05] hover:text-white flex items-center gap-2 transition-colors"
                            >
                                <i data-lucide="link" class="w-3.5 h-3.5"></i>
                                <span>Copy Link</span>
                            </button>

                            <?php if (isset($current_user_id) && $current_user_id === (string)$post['user_id']): ?>
                                <button 
                                    onclick="event.preventDefault(); event.stopPropagation(); openEditPostModal('<?= $post['id_post']; ?>', '<?= $post_content_attr; ?>', '<?= $post_category_attr; ?>')"
                                    class="w-full text-left px-3 py-2 hover:bg-white/[0.05] hover:text-white flex items-center gap-2 transition-colors border-t border-white/[0.03]"
                                >
                                    <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                                    <span>Edit</span>
                                </button>
                                <button 
                                    onclick="event.preventDefault(); event.stopPropagation(); deletePost(<?= $post['id_post']; ?>)"
                                    class="w-full text-left px-3 py-2 hover:bg-red-500/10 hover:text-red-400 flex items-center gap-2 transition-colors border-t border-white/[0.03]"
                                >
                                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                    <span>Hapus</span>
                                </button>
                            <?php else: ?>
                                <button onclick="event.preventDefault(); event.stopPropagation(); openReportPost(<?= $post['id_post']; ?>)" class="w-full text-left px-3 py-2 hover:bg-red-500/10 hover:text-red-400 flex items-center gap-2 transition-colors border-t border-white/[0.03]">
                                    <i data-lucide="flag" class="w-3.5 h-3.5"></i>
                                    <span>Report Post</span>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                    <?php if (!empty($post['file_url'])): ?>
                        <?php 
                            // Pecah string gambar berdasarkan koma menjadi array
                            $images = explode(',', $post['file_url']);
                            $total_images = count($images);
                            
                            // Tentukan layout grid berdasarkan jumlah gambar
                            if ($total_images === 1) {
                                $grid_class = 'grid-cols-1 aspect-[4/3]';
                            } elseif ($total_images === 2) {
                                $grid_class = 'grid-cols-2 aspect-[4/3] gap-1';
                            } elseif ($total_images === 3) {
                                $grid_class = 'grid-cols-2 aspect-[4/3] gap-1'; // Nanti gambar pertama di-span full vertical
                            } else {
                                $grid_class = 'grid-cols-2 grid-rows-2 aspect-[4/3] gap-1';
                            }
                            
                            // Batasi maksimal 4 gambar yang dirender di preview
                            $images_to_show = array_slice($images, 0, 4);
                        ?>
                        <div class="px-4 sm:px-5 mb-1">
                            <div class="grid <?= $grid_class; ?> bg-slate-900 border border-white/[0.03] rounded-lg overflow-hidden">
                                <?php foreach ($images_to_show as $index => $img_url): ?>
                                    <?php 
                                        // Trik CSS khusus jika gambarnya ada 3, biar gambar pertama memanjang dari atas ke bawah di kolom kiri
                                        $item_class = ($total_images === 3 && $index === 0) ? 'row-span-2 h-full' : 'h-full';
                                    ?>
                                    <div class="relative w-full <?= $item_class; ?> overflow-hidden bg-slate-950">
                                        <img src="<?= trim($img_url); ?>" alt="Post Media" loading="lazy" class="w-full h-full object-cover group-hover:scale-[1.01] transition-transform duration-500">
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                <div class="p-4 sm:p-5 pt-2 space-y-3">
                    <p class="text-xs sm:text-sm text-slate-300 leading-relaxed"><?= htmlspecialchars($post['content'], ENT_QUOTES, 'UTF-8'); ?></p>
                    
                    <div class="flex items-center gap-4 pt-2 border-t border-white/[0.03] text-slate-400 text-[11px] sm:text-xs relative z-20">
                        <button 
                            onclick="toggleLike(event, <?= $post['id_post']; ?>, this)" 
                            class="flex items-center gap-1.5 transition-colors group/btn <?= $like_btn_class; ?>"
                        >
                            <i data-lucide="heart" class="w-4 h-4 group-hover/btn:scale-110 transition-transform <?= $like_icon_class; ?>"></i>
                            <span class="font-semibold count-likes"><?= $post['likes_count']; ?></span>
                        </button>

                        <a 
                            href="<?= base_url('post/' . $post['username'] . '/' . $post['id_post']); ?>" 
                            class="flex items-center gap-1.5 hover:text-blue-400 transition-colors group/btn"
                        >
                            <i data-lucide="message-square" class="w-4 h-4 group-hover/btn:scale-110 transition-transform"></i>
                            <span class="font-semibold"><?= $post['comments_count']; ?></span>
                        </a>
                    </div>
                </div>
            </article>

        <?php endforeach; ?>
    <?php else: ?>
        <div class="glass-card p-8 text-center text-slate-500 text-xs">Belum ada postingan terbaru.</div>
    <?php endif; ?>

    <!-- Login prompt untuk guest (setelah post habis) -->
    <?php if (isset($is_guest) && $is_guest): ?>
        <div id="guest-prompt" class="glass-card rounded-2xl p-6 text-center border border-white/[0.06]">
            <div class="w-12 h-12 mx-auto mb-4 rounded-full bg-red-500/10 flex items-center justify-center">
                <i data-lucide="log-in" class="w-6 h-6 text-red-500"></i>
            </div>
            <h3 class="font-syne text-sm uppercase tracking-tight text-white mb-2">Ingin Melihat Lebih Banyak?</h3>
            <p class="text-xs text-slate-400 leading-relaxed mb-5">Masuk atau daftar akun untuk menikmati seluruh postingan dan fitur interaktif PaddockID.</p>
            <a href="<?= base_url('auth'); ?>" class="inline-block px-6 py-2.5 text-xs font-semibold text-white bg-red-600 hover:bg-red-500 rounded-xl transition-colors shadow-lg shadow-red-600/10">
                Masuk / Daftar
            </a>
        </div>
    <?php endif; ?>
</div>

<div id="loading-badge" class="hidden text-center py-6 text-xs text-slate-500 tracking-wide">
    <div class="inline-block animate-spin rounded-full h-4 w-4 border-b-2 border-red-500 mr-2 align-middle"></div>
    Memuat postingan lainnya...
</div>
</main>

<script>
// 1. Fungsi Toggle Dropdown
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

// 2. Fungsi Copy Link Menggunakan Clipboard API
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
    }).catch(err => {
        console.error('Gagal menyalin link: ', err);
    });
}

// 3. Auto-close dropdown jika user klik sembarang tempat di luar menu
document.addEventListener('click', function (e) {
    document.querySelectorAll('[id^="dropdown-"]').forEach(dropdown => {
        if (!dropdown.contains(e.target)) {
            dropdown.classList.add('hidden');
        }
    });
});
</script>

<script>
// Konfigurasi Awal Infinite Scroll
const limit = 5;
let offset = 5;
let isLoading = false;
let hasMoreData = true;

const categorySlug = '<?= isset($active_category_slug) ? $active_category_slug : ''; ?>';
const IS_GUEST = <?= (isset($is_guest) && $is_guest) ? 'true' : 'false'; ?>;
const INITIAL_TAB = '<?= $active_tab ?? 'for_you'; ?>';

let currentTab = localStorage.getItem('feed_tab') || INITIAL_TAB;
// Pastikan currentTab sinkron dengan yang dirender server
if (currentTab !== INITIAL_TAB) {
    currentTab = INITIAL_TAB;
}

// Nonaktifkan infinite scroll untuk guest & following tab jika belum login
if (IS_GUEST) {
    hasMoreData = false;
}

// Fungsi switch tab
function switchTab(tab) {
    if (tab === currentTab) return;

    // Guest: following tab tidak tersedia
    if (IS_GUEST && tab === 'following') return;

    currentTab = tab;
    localStorage.setItem('feed_tab', tab);

    // Update UI tabs
    document.getElementById('tab-for-you').className = tab === 'for_you'
        ? 'flex-1 pb-3 text-xs font-semibold text-center transition-colors border-b-2 text-white border-red-500'
        : 'flex-1 pb-3 text-xs font-semibold text-center transition-colors border-b-2 text-slate-500 border-transparent hover:text-slate-300';
    document.getElementById('tab-following').className = tab === 'following'
        ? 'flex-1 pb-3 text-xs font-semibold text-center transition-colors border-b-2 text-white border-red-500'
        : 'flex-1 pb-3 text-xs font-semibold text-center transition-colors border-b-2 text-slate-500 border-transparent hover:text-slate-300';

    // Reset state
    offset = 0;
    hasMoreData = true;
    isLoading = false;
    window._postsSinceAd = <?= $this->config->item('ads_feed_min_gap') ?: 5; ?>;
    window._adCycle = 0;
    document.getElementById('post-container').querySelectorAll('article').forEach(el => el.remove());
    document.getElementById('loading-badge').classList.add('hidden');

    // Load ulang dari awal
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
            // Sembunyikan empty state dulu
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
    // Fetch feed ads for AJAX injection (cache after first fetch)
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
                <article class="glass-card overflow-hidden group transition-all relative hover:bg-white/[0.02]">
                    <a href="<?= base_url("ads/track_click/"); ?>${ad.id_ad}" target="_blank" rel="noopener noreferrer sponsored" class="absolute inset-0 z-10" aria-label="Iklan: ${ad.title}"></a>
                    <div class="relative">
                        <img src="${ad.image_url_full}" alt="${ad.title}" class="w-full h-auto max-h-64 object-cover">
                        <span class="absolute top-3 left-3 text-[8px] font-bold uppercase tracking-widest px-2 py-0.5 rounded-full bg-black/60 backdrop-blur-sm text-slate-400 border border-white/[0.08]">Sponsored</span>
                    </div>
                    <div class="p-4 sm:p-5">
                        <p class="text-xs sm:text-sm font-bold text-white group-hover:text-red-400 transition-colors relative z-20">${ad.title}</p>
                        ${ad.description ? '<p class="text-[11px] text-slate-500 mt-1 leading-relaxed relative z-20">' + ad.description + '</p>' : ''}
                    </div>
                </article>
            `;
            container.insertAdjacentHTML('beforeend', adHTML);
        }
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
                        <img src="${url}" alt="Post Media ${index + 1}" loading="lazy" class="w-full h-full object-cover group-hover:scale-[1.01] transition-transform duration-500">
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

        const isOwner = CURRENT_USER_ID > 0 && post.user_id == CURRENT_USER_ID;
        const dynamicLikeBtnClass = post.is_liked ? 'text-red-500' : 'hover:text-red-500';
        const dynamicLikeIconClass = post.is_liked ? 'fill-red-500 text-red-500' : '';
        const escapedContent = escapeJsString(post.content);

        const dropdownItems = isOwner
            ? `
                <button onclick="copyPostLink(event, '<?= base_url('post/'); ?>${post.username}/${post.id_post}', this)" class="w-full text-left px-3 py-2 hover:bg-white/[0.05] hover:text-white flex items-center gap-2 transition-colors">
                    <i data-lucide="link" class="w-3.5 h-3.5"></i><span>Copy Link</span>
                </button>
                <button onclick="event.stopPropagation(); openEditPostModal(${post.id_post}, '${escapedContent}', '${post.post_category || ''}')" class="w-full text-left px-3 py-2 hover:bg-white/[0.05] hover:text-white flex items-center gap-2 transition-colors border-t border-white/[0.03]">
                    <i data-lucide="pencil" class="w-3.5 h-3.5"></i><span>Edit</span>
                </button>
                <button onclick="event.stopPropagation(); deletePost(${post.id_post})" class="w-full text-left px-3 py-2 hover:bg-red-500/10 hover:text-red-400 flex items-center gap-2 transition-colors border-t border-white/[0.03]">
                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i><span>Hapus</span>
                </button>
            `
            : `
                <button onclick="copyPostLink(event, '<?= base_url('post/'); ?>${post.username}/${post.id_post}', this)" class="w-full text-left px-3 py-2 hover:bg-white/[0.05] hover:text-white flex items-center gap-2 transition-colors">
                    <i data-lucide="link" class="w-3.5 h-3.5"></i><span>Copy Link</span>
                </button>
                <button onclick="event.stopPropagation(); openReportPost(${post.id_post})" class="block w-full text-left px-3 py-2 hover:bg-red-500/10 hover:text-red-400 flex items-center gap-2 transition-colors border-t border-white/[0.03]">
                    <i data-lucide="flag" class="w-3.5 h-3.5"></i><span>Report Post</span>
                </button>
            `;

        const cardHTML = `
            <article class="glass-card overflow-hidden group transition-all relative hover:bg-white/[0.02]" data-post-id="${post.id_post}" data-user-id="${post.user_id}">
                <a href="<?= base_url('post/'); ?>${post.username}/${post.id_post}" class="absolute inset-0 z-10" aria-label="Lihat detail postingan"></a>
                <div class="p-4 sm:p-5 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="relative w-9 h-9 flex items-center justify-center select-none z-20">
                            <div class="${avatarClass} rounded-full overflow-hidden bg-slate-800">
                                <a href="<?= base_url('user/'); ?>${post.username}"><img src="${post.avatar}" alt="User" class="w-full h-full object-cover rounded-full"></a>
                            </div>
                            ${avatarBorderHTML}
                            ${onlineHTML}
                        </div>
                        <div class="flex flex-col justify-center">
                            <div class="flex items-center gap-2">
                                <a href="<?= base_url('user/'); ?>${post.username}" class="font-semibold text-xs sm:text-sm hover:text-red-400 cursor-pointer transition-colors relative z-20">${post.username}</a>
                                ${post.team_name ? '<span class="inline-flex items-center gap-1 text-[9px] font-semibold uppercase tracking-wider px-1.5 py-0.5 rounded-full border border-white/[0.08]" style="background:' + (post.team_color || '#666') + '15;"><img src="' + post.team_logo + '" alt="' + post.team_name + '" class="w-3 h-3 object-contain"> ' + post.team_name + '</span>' : ''}
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
                            ${dropdownItems}
                        </div>
                    </div>
                </div>
                ${mediaHTML}
                <div class="p-4 sm:p-5 pt-2 space-y-3">
                    <p class="text-xs sm:text-sm text-slate-300 leading-relaxed">${post.content}</p>
                    <div class="flex items-center gap-4 pt-2 border-t border-white/[0.03] text-slate-400 text-[11px] sm:text-xs relative z-20">
                        <button onclick="toggleLike(event, ${post.id_post}, this)" class="flex items-center gap-1.5 transition-colors group/btn ${dynamicLikeBtnClass}">
                            <i data-lucide="heart" class="w-4 h-4 group-hover/btn:scale-110 transition-transform ${dynamicLikeIconClass}"></i>
                            <span class="font-semibold count-likes">${post.likes_count}</span>
                        </button>
                        <a href="<?= base_url('post/'); ?>${post.username}/${post.id_post}" class="flex items-center gap-1.5 hover:text-blue-400 transition-colors group/btn">
                            <i data-lucide="message-square" class="w-4 h-4 group-hover/btn:scale-110 transition-transform"></i>
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
                loadingBadge.innerHTML = "<span class='text-slate-600 uppercase tracking-wider text-[10px]'>Kamu telah mencapai batas akhir postingan.</span>";
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

// Fungsi AJAX Like Postingan
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
        .catch(err => {
            console.error('Gagal memproses like:', err);
            showToast('Gagal menyukai postingan. Coba lagi.', 'red');
        });
}
</script>