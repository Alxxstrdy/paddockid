<div class="space-y-4 sm:space-y-6">
    <!-- Search Bar -->
    <div class="glass-card rounded-2xl p-4 sm:p-5 border border-white/[0.06]">
        <form id="search-form" action="<?= base_url('search'); ?>" method="GET" class="flex items-center gap-2">
            <div class="relative flex-1">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                    <i data-lucide="search" class="w-4 h-4"></i>
                </span>
                <input
                    type="text"
                    name="q"
                    id="search-input"
                    value="<?= htmlspecialchars($keyword ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                    placeholder="Cari postingan atau pengguna..."
                    autocomplete="off"
                    class="w-full bg-slate-950/60 border border-white/[0.08] focus:border-red-500/50 rounded-xl pl-10 pr-4 py-2.5 text-xs sm:text-sm text-slate-200 placeholder-slate-600 focus:outline-none focus:ring-1 focus:ring-red-500/30 transition-all"
                >
            </div>
            <button type="submit" class="bg-red-600 hover:bg-red-500 text-white font-semibold text-xs px-4 py-2.5 rounded-xl transition-all shadow-lg shadow-red-600/10 whitespace-nowrap">
                Cari
            </button>
        </form>
    </div>

    <?php if (!empty($keyword)): ?>
        <!-- Search Results -->
        <div class="glass-card rounded-2xl border border-white/[0.06] overflow-hidden">
            <!-- Tabs -->
            <div class="flex border-b border-white/[0.06]">
                <button
                    id="tab-posts"
                    class="tab-btn flex-1 py-3 text-xs font-semibold transition-colors relative"
                    data-type="posts"
                    onclick="switchTab('posts')"
                >
                    Posts (<?= $posts_count; ?>)
                    <span class="tab-indicator absolute bottom-0 left-0 right-0 h-0.5 bg-red-500 hidden"></span>
                </button>
                <button
                    id="tab-users"
                    class="tab-btn flex-1 py-3 text-xs font-semibold text-slate-500 hover:text-slate-300 transition-colors relative"
                    data-type="users"
                    onclick="switchTab('users')"
                >
                    Users (<?= $users_count; ?>)
                    <span class="tab-indicator absolute bottom-0 left-0 right-0 h-0.5 bg-red-500 hidden"></span>
                </button>
            </div>

            <!-- Posts Tab Content -->
            <div id="tab-content-posts" class="tab-content">
                <?php if (!empty($posts)): ?>
                    <?php foreach ($posts as $post):
                        $is_liked = isset($post['is_liked']) && $post['is_liked'] == true;
                        $like_btn_class = $is_liked ? 'text-red-500' : 'hover:text-red-500';
                        $like_icon_class = $is_liked ? 'fill-red-500 text-red-500' : '';
                        $post_content_attr = addslashes($post['content']);
                        $post_category_attr = addslashes($post['post_category'] ?? '');
                    ?>
                    <article class="glass-card overflow-hidden group transition-all relative hover:bg-white/[0.02] border-0 border-b border-white/[0.04] last:border-b-0" data-post-id="<?= $post['id_post']; ?>" data-user-id="<?= $post['user_id']; ?>">
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
                                            <img src="<?= $post['border']; ?>" alt="F1 Border" class="w-full h-full object-contain">
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
                                    <button onclick="copyPostLink(event, '<?= base_url('post/' . $post['username'] . '/' . $post['id_post']); ?>', this)" class="w-full text-left px-3 py-2 hover:bg-white/[0.05] hover:text-white flex items-center gap-2 transition-colors">
                                        <i data-lucide="link" class="w-3.5 h-3.5"></i>
                                        <span>Copy Link</span>
                                    </button>
                                    <?php if (isset($current_user_id) && $current_user_id === (string)$post['user_id']): ?>
                                        <button onclick="event.preventDefault(); event.stopPropagation(); openEditPostModal('<?= $post['id_post']; ?>', '<?= $post_content_attr; ?>', '<?= $post_category_attr; ?>')" class="w-full text-left px-3 py-2 hover:bg-white/[0.05] hover:text-white flex items-center gap-2 transition-colors border-t border-white/[0.03]">
                                            <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                                            <span>Edit</span>
                                        </button>
                                        <button onclick="event.preventDefault(); event.stopPropagation(); deletePost(<?= $post['id_post']; ?>)" class="w-full text-left px-3 py-2 hover:bg-red-500/10 hover:text-red-400 flex items-center gap-2 transition-colors border-t border-white/[0.03]">
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
                                $images = explode(',', $post['file_url']);
                                $total_images = count($images);
                                if ($total_images === 1) {
                                    $grid_class = 'grid-cols-1 aspect-[4/3]';
                                } elseif ($total_images === 2) {
                                    $grid_class = 'grid-cols-2 aspect-[4/3] gap-1';
                                } elseif ($total_images === 3) {
                                    $grid_class = 'grid-cols-2 aspect-[4/3] gap-1';
                                } else {
                                    $grid_class = 'grid-cols-2 grid-rows-2 aspect-[4/3] gap-1';
                                }
                                $images_to_show = array_slice($images, 0, 4);
                            ?>
                            <div class="px-4 sm:px-5 mb-1">
                                <div class="grid <?= $grid_class; ?> bg-slate-900 border border-white/[0.03] rounded-lg overflow-hidden">
                                    <?php foreach ($images_to_show as $index => $img_url):
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
                                <button onclick="toggleLike(event, <?= $post['id_post']; ?>, this)" class="flex items-center gap-1.5 transition-colors group/btn <?= $like_btn_class; ?>">
                                    <i data-lucide="heart" class="w-4 h-4 group-hover/btn:scale-110 transition-transform <?= $like_icon_class; ?>"></i>
                                    <span class="font-semibold count-likes"><?= $post['likes_count']; ?></span>
                                </button>
                                <a href="<?= base_url('post/' . $post['username'] . '/' . $post['id_post']); ?>" class="flex items-center gap-1.5 hover:text-blue-400 transition-colors group/btn">
                                    <i data-lucide="message-square" class="w-4 h-4 group-hover/btn:scale-110 transition-transform"></i>
                                    <span class="font-semibold"><?= $post['comments_count']; ?></span>
                                </a>
                            </div>
                        </div>
                    </article>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="p-8 text-center text-slate-500 text-xs" id="posts-empty">Tidak ada postingan ditemukan.</div>
                <?php endif; ?>
            </div>

            <!-- Users Tab Content -->
            <div id="tab-content-users" class="tab-content hidden">
                <?php if (!empty($users)): ?>
                    <?php foreach ($users as $user): ?>
                    <div class="flex items-center justify-between p-4 sm:p-5 border-b border-white/[0.04] last:border-b-0 hover:bg-white/[0.02] transition-colors" data-user-id="<?= $user['id_user']; ?>">
                        <a href="<?= base_url('user/' . $user['username']); ?>" class="flex items-center gap-3 flex-1 min-w-0">
                            <div class="relative w-10 h-10 flex-shrink-0 flex items-center justify-center">
                                <div class="<?= !empty($user['border']) ? 'w-[84%] h-[84%]' : 'w-full h-full'; ?> rounded-full overflow-hidden bg-slate-800">
                                    <img src="<?= $user['avatar']; ?>" alt="<?= $user['username']; ?>" class="w-full h-full object-cover rounded-full">
                                </div>
                                <?php if (!empty($user['border'])): ?>
                                    <div class="absolute inset-0 w-full h-full pointer-events-none scale-[1.15] transform origin-center">
                                        <img src="<?= $user['border']; ?>" alt="Border" class="w-full h-full object-contain">
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($user['is_online'])): ?>
                                    <div class="online-indicator"></div>
                                <?php endif; ?>
                            </div>
                            <div class="min-w-0">
                                <div class="flex items-center gap-1.5">
                                    <span class="font-semibold text-xs sm:text-sm text-slate-200 truncate"><?= $user['display_name']; ?></span>
                                    <?php if ($user['verified']): ?>
                                        <i data-lucide="badge-check" class="w-3.5 h-3.5 text-blue-400 flex-shrink-0"></i>
                                    <?php endif; ?>
                                </div>
                                <span class="text-[11px] text-slate-500">@<?= $user['username']; ?></span>
                                <span class="text-[10px] text-slate-600 ml-2">• <?= $user['followers_count']; ?> followers</span>
                            </div>
                        </a>
                        <?php if (isset($current_user_id) && $current_user_id && $current_user_id !== $user['id_user']): ?>
                            <button
                                onclick="event.preventDefault(); event.stopPropagation(); toggleFollowUser('<?= $user['id_user']; ?>', this)"
                                class="follow-btn flex-shrink-0 text-xs font-semibold px-4 py-1.5 rounded-full transition-all border <?= $user['is_followed'] ? 'bg-white/[0.05] text-slate-300 border-white/[0.08] hover:border-red-500/30 hover:text-red-400' : 'bg-red-600 text-white border-red-600 hover:bg-red-500'; ?>"
                            >
                                <?= $user['is_followed'] ? 'Following' : 'Follow'; ?>
                            </button>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="p-8 text-center text-slate-500 text-xs" id="users-empty">Tidak ada pengguna ditemukan.</div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Loading Badge -->
        <div id="loading-badge" class="hidden text-center py-6 text-xs text-slate-500 tracking-wide">
            <div class="inline-block animate-spin rounded-full h-4 w-4 border-b-2 border-red-500 mr-2 align-middle"></div>
            Memuat lebih banyak...
        </div>
    <?php else: ?>
        <!-- Empty State -->
        <div class="glass-card rounded-2xl p-8 text-center border border-white/[0.06]">
            <div class="w-14 h-14 mx-auto mb-4 rounded-full bg-white/[0.03] flex items-center justify-center">
                <i data-lucide="search" class="w-6 h-6 text-slate-500"></i>
            </div>
            <h3 class="font-syne text-sm uppercase tracking-tight text-white mb-2">Cari di PaddockID</h3>
            <p class="text-xs text-slate-400 leading-relaxed max-w-sm mx-auto">Temukan postingan menarik dan pengguna baru di komunitas Formula 1 Indonesia.</p>
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

function switchTab(type) {
    activeTab = type;

    document.querySelectorAll('.tab-btn').forEach(btn => {
        const isActive = btn.dataset.type === type;
        btn.classList.toggle('text-white', isActive);
        btn.classList.toggle('text-slate-500', !isActive);
        btn.querySelector('.tab-indicator')?.classList.toggle('hidden', !isActive);
    });

    document.querySelectorAll('.tab-content').forEach(el => {
        el.classList.toggle('hidden', el.id !== `tab-content-${type}`);
    });
}

// Initialize active tab on page load
document.addEventListener('DOMContentLoaded', function() {
    switchTab('posts');
});

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
                    loadingBadge.innerHTML = "<span class='text-slate-600 uppercase tracking-wider text-[10px]'>Tidak ada hasil lagi.</span>";
                    setTimeout(() => loadingBadge.classList.add('hidden'), 2000);
                }
                return;
            }

            const container = document.getElementById(`tab-content-${activeTab}`);

            if (activeTab === 'posts') {
                data.forEach(post => {
                    const avatarClass = post.border ? 'w-[84%] h-[84%]' : 'w-full h-full';
                    const onlineHTML = post.is_online ? '<div class="online-indicator"></div>' : '';
                    const avatarBorderHTML = post.border
                        ? `<div class="absolute inset-0 w-full h-full pointer-events-none scale-[1] transform origin-center">
                               <img src="${post.border}" alt="F1 Border" class="w-full h-full object-contain">
                           </div>`
                        : '';

                    let mediaHTML = '';
                    if (post.file_url) {
                        const images = post.file_url.split(',').map(img => img.trim());
                        const totalImages = images.length;
                        let gridClass = '';
                        if (totalImages === 1) gridClass = 'grid-cols-1 aspect-[4/3]';
                        else if (totalImages === 2) gridClass = 'grid-cols-2 aspect-[4/3] gap-1';
                        else if (totalImages === 3) gridClass = 'grid-cols-2 aspect-[4/3] gap-1';
                        else gridClass = 'grid-cols-2 grid-rows-2 aspect-[4/3] gap-1';

                        const imagesToShow = images.slice(0, 4);
                        let imagesTemplate = '';
                        imagesToShow.forEach((url, idx) => {
                            const itemClass = (totalImages === 3 && idx === 0) ? 'row-span-2 h-full' : 'h-full';
                            imagesTemplate += `
                                <div class="relative w-full ${itemClass} overflow-hidden bg-slate-950">
                                    <img src="${url}" alt="Post Media" loading="lazy" class="w-full h-full object-cover group-hover:scale-[1.01] transition-transform duration-500">
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

                    const isOwner = <?= isset($current_user_id) && $current_user_id ? 'CURRENT_USER_ID' : '0'; ?> > 0 && post.user_id == <?= isset($current_user_id) && $current_user_id ? 'CURRENT_USER_ID' : '0'; ?>;
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
                            </button>`
                        : `
                            <button onclick="copyPostLink(event, '<?= base_url('post/'); ?>${post.username}/${post.id_post}', this)" class="w-full text-left px-3 py-2 hover:bg-white/[0.05] hover:text-white flex items-center gap-2 transition-colors">
                                <i data-lucide="link" class="w-3.5 h-3.5"></i><span>Copy Link</span>
                            </button>
                            <button onclick="event.stopPropagation(); openReportPost(${post.id_post})" class="block w-full text-left px-3 py-2 hover:bg-red-500/10 hover:text-red-400 flex items-center gap-2 transition-colors border-t border-white/[0.03]">
                                <i data-lucide="flag" class="w-3.5 h-3.5"></i><span>Report Post</span>
                            </button>`;

                    const cardHTML = `
                        <article class="glass-card overflow-hidden group transition-all relative hover:bg-white/[0.02] border-0 border-b border-white/[0.04]" data-post-id="${post.id_post}" data-user-id="${post.user_id}">
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
                                            <a href="<?= base_url('user/'); ?>${post.username}" class="font-semibold text-xs sm:text-sm hover:text-red-400 transition-colors relative z-20">${post.username}</a>
                                            ${post.team_name ? '<span class="inline-flex items-center gap-1 text-[9px] font-semibold uppercase tracking-wider px-1.5 py-0.5 rounded-full border border-white/[0.08]" style="background:' + (post.team_color || '#666') + '15;"><img src="<?= assets_url(''); ?>' + post.team_logo + '" alt="' + post.team_name + '" class="w-3 h-3 object-contain"> ' + post.team_name + '</span>' : ''}
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
                postOffset += data.length;
                if (data.length < limit) hasMorePosts = false;
            } else {
                data.forEach(user => {
                    const avatarClass = user.border ? 'w-[84%] h-[84%]' : 'w-full h-full';
                    const borderHTML = user.border
                        ? `<div class="absolute inset-0 w-full h-full pointer-events-none scale-[1.15] transform origin-center">
                               <img src="${user.border}" alt="Border" class="w-full h-full object-contain">
                           </div>`
                        : '';
                    const verifiedHTML = user.verified
                        ? `<i data-lucide="badge-check" class="w-3.5 h-3.5 text-blue-400 flex-shrink-0"></i>`
                        : '';
                    const onlineHTML = user.is_online ? '<div class="online-indicator"></div>' : '';

                    const isOwnProfile = <?= isset($current_user_id) && $current_user_id ? 'CURRENT_USER_ID' : 'null'; ?> && user.id_user == <?= isset($current_user_id) && $current_user_id ? 'CURRENT_USER_ID' : 'null'; ?>;
                    const followBtn = (!isOwnProfile && <?= isset($current_user_id) && $current_user_id ? 'CURRENT_USER_ID' : 'null'; ?>)
                        ? `<button onclick="event.preventDefault(); event.stopPropagation(); toggleFollowUser('${user.id_user}', this)" class="follow-btn flex-shrink-0 text-xs font-semibold px-4 py-1.5 rounded-full transition-all border ${user.is_followed ? 'bg-white/[0.05] text-slate-300 border-white/[0.08] hover:border-red-500/30 hover:text-red-400' : 'bg-red-600 text-white border-red-600 hover:bg-red-500'}">${user.is_followed ? 'Following' : 'Follow'}</button>`
                        : '';

                    const cardHTML = `
                        <div class="flex items-center justify-between p-4 sm:p-5 border-b border-white/[0.04] hover:bg-white/[0.02] transition-colors" data-user-id="${user.id_user}">
                            <a href="<?= base_url('user/'); ?>${user.username}" class="flex items-center gap-3 flex-1 min-w-0">
                                <div class="relative w-10 h-10 flex-shrink-0 flex items-center justify-center">
                                    <div class="${avatarClass} rounded-full overflow-hidden bg-slate-800">
                                        <img src="${user.avatar}" alt="${user.username}" class="w-full h-full object-cover rounded-full">
                                    </div>
                                    ${borderHTML}
                                    ${onlineHTML}
                                </div>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-1.5">
                                        <span class="font-semibold text-xs sm:text-sm text-slate-200 truncate">${user.display_name}</span>
                                        ${verifiedHTML}
                                    </div>
                                    <span class="text-[11px] text-slate-500">@${user.username}</span>
                                    <span class="text-[10px] text-slate-600 ml-2">• ${user.followers_count} followers</span>
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
</main>
