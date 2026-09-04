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
                <div id="search-history" class="hidden absolute top-full left-0 right-0 mt-2 z-50" style="background:var(--bg-surface);backdrop-filter:blur(12px);border:1px solid var(--border-strong);border-radius:0.75rem;box-shadow:0 25px 50px -12px rgba(0,0,0,0.5);overflow:hidden">
                    <div class="flex-row items-center justify-between px-4 py-2.5" style="border-bottom:1px solid var(--border-default)">
                        <span class="text-micro" style="font-weight:600;text-transform:uppercase;letter-spacing:0.05em;color:var(--text-muted)">Riwayat Pencarian</span>
                        <button type="button" onclick="clearSearchHistory()" class="text-micro c-primary" style="font-weight:600;transition:color 0.2s">Hapus riwayat</button>
                    </div>
                    <ul id="search-history-list"></ul>
                </div>
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
            <div id="tab-content-posts" class="tab-content">
                <?php if (!empty($posts)): ?>
                    <?php foreach ($posts as $post):
                        $is_liked = isset($post['is_liked']) && $post['is_liked'] == true;
                        $like_btn_class = $is_liked ? 'c-primary' : '';
                        $like_btn_style = $is_liked ? '' : 'transition:color 0.2s';
                        $like_icon_class = $is_liked ? 'fill-red-500' : '';
                        $like_icon_style = $is_liked ? 'fill:var(--color-primary);color:var(--color-primary)' : '';
                        $post_content_attr = addslashes($post['content']);
                        $post_category_attr = addslashes($post['post_category'] ?? '');
                        $post_username_url = rawurlencode($post['username']);
                        $post_avatar_attr = htmlspecialchars($post['avatar'], ENT_QUOTES, 'UTF-8');
                        $post_border_attr = htmlspecialchars($post['border'] ?? '', ENT_QUOTES, 'UTF-8');
                        $post_category_html = htmlspecialchars($post['category'], ENT_QUOTES, 'UTF-8');
                        $post_team_color_attr = htmlspecialchars($post['team_color'] ?? '#666', ENT_QUOTES, 'UTF-8');
                        $post_team_logo_attr = htmlspecialchars(assets_url($post['team_logo']), ENT_QUOTES, 'UTF-8');
                        $post_created_at_attr = htmlspecialchars($post['created_at'], ENT_QUOTES, 'UTF-8');
                    ?>
                    <article class="card overflow-hidden group relative" style="transition:all 0.2s;transition-property:background-color,border-color" data-post-id="<?= $post['id_post']; ?>" data-user-id="<?= $post['user_id']; ?>">
                        <a href="<?= base_url('post/' . $post_username_url . '/' . $post['id_post']); ?>" class="absolute inset-0 z-10" aria-label="Lihat detail postingan"></a>

                        <div class="p-5 flex-row items-center justify-between">
                            <div class="flex-row items-center gap-3">
                                <div class="relative select-none z-20" style="width:36px;height:36px;display:flex;align-items:center;justify-content:center">
                                    <div class="rounded-full overflow-hidden" style="width:100%;height:100%;background:var(--bg-surface-raised)">
                                        <a href="<?= base_url('user/' . $post_username_url); ?>">
                                            <img src="<?= $post_avatar_attr; ?>" alt="User" class="rounded-full" style="width:100%;height:100%;object-fit:cover">
                                        </a>
                                    </div>
                                    <?php if (!empty($post['border'])): ?>
                                        <div class="absolute inset-0" style="width:100%;height:100%;pointer-events:none;transform:scale(1.25);transform-origin:center">
                                            <img src="<?= $post_border_attr; ?>" alt="F1 Border" style="width:100%;height:100%;object-fit:contain">
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($post['is_online'])): ?>
                                        <div class="online-indicator"></div>
                                    <?php endif; ?>
                                </div>
                                <div class="flex-col justify-center">
                                    <div class="flex-row items-center gap-2">
                                        <a href="<?= base_url('user/' . $post_username_url); ?>" class="font-semibold text-small" style="transition:color 0.2s;cursor:pointer;position:relative;z-index:20" onmouseover="this.style.color='var(--color-primary)'" onmouseout="this.style.color=''"><?= htmlspecialchars($post['username'], ENT_QUOTES, 'UTF-8'); ?></a>
                                        <?php if (!empty($post['team_name'])): ?>
                                            <span class="inline-flex items-center gap-1" style="font-size:9px;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;padding:2px 6px;border-radius:9999px;border:1px solid var(--border-strong);background:<?= $post_team_color_attr ?>15">
                                                <img src="<?= $post_team_logo_attr ?>" alt="<?= htmlspecialchars($post['team_name']) ?>" style="width:12px;height:12px;object-fit:contain">
                                                <?= htmlspecialchars($post['team_name']) ?>
                                            </span>
                                        <?php endif; ?>
                                        <span class="c-faint" style="font-size:10px">•</span>
                                        <span class="inline-flex items-center c-white" style="font-size:8px;padding:2px 6px;font-weight:600;background:rgba(255,255,255,0.04);border:1px solid var(--border-default);border-radius:9999px;text-transform:uppercase;letter-spacing:0.05em"><?= $post_category_html; ?></span>
                                    </div>
                                    <span class="text-micro c-subtle" style="margin-top:2px"><?= $post_created_at_attr; ?></span>
                                </div>
                            </div>

                            <div class="relative z-30 flex items-center">
                                <button onclick="toggleDropdown(event, <?= $post['id_post']; ?>)" class="c-subtle transition-colors p-1 rounded-md" style="transition:background-color 0.2s" onmouseover="this.style.background='rgba(255,255,255,0.05)';this.style.color='var(--text-secondary)'" onmouseout="this.style.background='';this.style.color=''">
                                    <i data-lucide="more-horizontal" style="width:16px;height:16px"></i>
                                </button>
                                <div id="dropdown-<?= $post['id_post']; ?>" class="hidden absolute right-0 top-8" style="width:9rem;background:var(--bg-surface);backdrop-filter:blur(12px);border:1px solid var(--border-strong);border-radius:0.5rem;box-shadow:0 20px 25px -5px rgba(0,0,0,0.3);overflow:hidden;padding-top:4px;padding-bottom:4px;font-size:12px;color:var(--text-secondary)">
                                    <button onclick="copyPostLink(event, '<?= base_url('post/' . $post_username_url . '/' . $post['id_post']); ?>', this)" class="w-full text-left px-3 py-2 flex items-center gap-2 transition-colors" style="transition:background-color 0.2s" onmouseover="this.style.background='rgba(255,255,255,0.05)';this.style.color='var(--text-primary)'" onmouseout="this.style.background='';this.style.color=''">
                                        <i data-lucide="link" style="width:14px;height:14px"></i>
                                        <span>Copy Link</span>
                                    </button>
                                    <?php if (isset($current_user_id) && $current_user_id === (string)$post['user_id']): ?>
                                        <a href="<?= base_url('post/edit/' . $post['id_post']); ?>" onclick="event.stopPropagation();" class="w-full text-left px-3 py-2 flex items-center gap-2 transition-colors" style="border-top:1px solid rgba(255,255,255,0.03);transition:background-color 0.2s" onmouseover="this.style.background='rgba(255,255,255,0.05)';this.style.color='var(--text-primary)'" onmouseout="this.style.background='';this.style.color=''">
                                            <i data-lucide="pencil" style="width:14px;height:14px"></i>
                                            <span>Edit</span>
                                        </a>
                                        <button onclick="event.preventDefault(); event.stopPropagation(); deletePost(<?= $post['id_post']; ?>)" class="w-full text-left px-3 py-2 flex items-center gap-2 transition-colors" style="border-top:1px solid rgba(255,255,255,0.03);transition:background-color 0.2s" onmouseover="this.style.background='var(--color-primary-bg)';this.style.color='var(--color-primary)'" onmouseout="this.style.background='';this.style.color=''">
                                            <i data-lucide="trash-2" style="width:14px;height:14px"></i>
                                            <span>Hapus</span>
                                        </button>
                                    <?php else: ?>
                                        <button onclick="event.preventDefault(); event.stopPropagation(); openReportPost(<?= $post['id_post']; ?>)" class="w-full text-left px-3 py-2 flex items-center gap-2 transition-colors" style="border-top:1px solid rgba(255,255,255,0.03);transition:background-color 0.2s" onmouseover="this.style.background='var(--color-primary-bg)';this.style.color='var(--color-primary)'" onmouseout="this.style.background='';this.style.color=''">
                                            <i data-lucide="flag" style="width:14px;height:14px"></i>
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
                            <div class="post-images" style="padding:0 1.25rem;margin-bottom:4px">
                                <div class="grid <?= $grid_class; ?> rounded-lg overflow-hidden" style="background:var(--bg-surface);border:1px solid rgba(255,255,255,0.03)">
                                    <?php foreach ($images_to_show as $index => $img_url):
                                        $item_class = ($total_images === 3 && $index === 0) ? 'row-span-2' : '';
                                        $item_style = 'width:100%;overflow:hidden;background:var(--bg-body)' . ($total_images === 3 && $index === 0 ? ';height:100%' : ';height:100%');
                                    ?>
                                        <div class="relative <?= $item_class; ?>" style="<?= $item_style ?>">
                                            <img src="<?= htmlspecialchars(trim($img_url), ENT_QUOTES, 'UTF-8'); ?>" alt="Post Media" loading="lazy" class="w-full h-full transition-transform duration-500" style="object-fit:cover;width:100%;height:100%">
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="p-4 pt-2 space-y-3">
                            <p class="text-small c-secondary leading-relaxed"><?= htmlspecialchars($post['content'], ENT_QUOTES, 'UTF-8'); ?></p>
                            <div class="flex-row items-center gap-4 pt-2 c-muted relative z-20" style="font-size:11px;border-top:1px solid rgba(255,255,255,0.03)">
                                <button onclick="toggleLike(event, <?= $post['id_post']; ?>, this)" class="flex-row items-center gap-1.5 group/btn <?= $like_btn_class; ?>" style="<?= $like_btn_style ?>">
                                    <i data-lucide="heart" class="transition-transform <?= $like_icon_class; ?>" style="width:16px;height:16px;transition:transform 0.2s;<?= $like_icon_style ?>"></i>
                                    <span class="font-semibold count-likes"><?= $post['likes_count']; ?></span>
                                </button>
                                <a href="<?= base_url('post/' . $post_username_url . '/' . $post['id_post']); ?>" class="flex-row items-center gap-1.5" style="transition:color 0.2s" onmouseover="this.style.color='var(--color-info)'" onmouseout="this.style.color=''">
                                    <i data-lucide="message-square" class="transition-transform" style="width:16px;height:16px"></i>
                                    <span class="font-semibold"><?= $post['comments_count']; ?></span>
                                </a>
                            </div>
                        </div>
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
                    <div class="flex-row items-center justify-between p-4" style="border-bottom:1px solid rgba(255,255,255,0.04);transition:background-color 0.2s" data-user-id="<?= $user['id_user']; ?>" onmouseover="this.style.background='rgba(255,255,255,0.02)'" onmouseout="this.style.background=''">
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
                                <?php if (!empty($user['is_online'])): ?>
                                    <div class="online-indicator"></div>
                                <?php endif; ?>
                            </div>
                            <div class="min-w-0">
                                <div class="flex-row items-center gap-1.5">
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
                                style="<?= $user['is_followed'] ? 'padding:4px 1rem;background:rgba(255,255,255,0.05);color:var(--text-secondary);border:1px solid var(--border-strong);transition:border-color 0.2s, color 0.2s' : 'padding:4px 1rem;border-color:var(--color-primary)' ?>"
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
            <div class="rounded-full mx-auto mb-4 flex items-center justify-center" style="width:56px;height:56px;background:rgba(255,255,255,0.03)">
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

// --- Search History ---
const IS_LOGGED_IN = <?= !empty($is_logged_in) ? 'true' : 'false'; ?>;

function renderSearchHistory() {
    const dropdown = document.getElementById('search-history');
    const list = document.getElementById('search-history-list');
    if (!dropdown || !list || !IS_LOGGED_IN) return;

    fetch('<?= base_url('search/history_ajax'); ?>')
        .then(r => r.json())
        .then(items => {
            if (!Array.isArray(items) || !items.length) {
                dropdown.classList.add('hidden');
                return;
            }
            list.innerHTML = items.map(item => `
                <li class="flex-row items-center group/row">
                    <button type="button" onclick="runSearch('${escapeJsString(item.keyword)}')" class="flex-1 min-w-0 flex-row items-center gap-2.5 px-4 py-2.5 text-small c-secondary transition-colors text-left" style="transition:background-color 0.2s" onmouseover="this.style.background='rgba(255,255,255,0.04)';this.style.color='var(--text-primary)'" onmouseout="this.style.background='';this.style.color=''">
                        <i data-lucide="history" class="c-subtle flex-shrink-0" style="width:14px;height:14px"></i>
                        <span class="truncate">${escapeJsString(item.keyword)}</span>
                    </button>
                    <button type="button" onclick="deleteHistoryItem(${parseInt(item.id, 10)})" class="px-3 py-2.5 c-faint flex-shrink-0" style="transition:color 0.2s" onmouseover="this.style.color='var(--color-primary)'" onmouseout="this.style.color=''">
                        <i data-lucide="x" style="width:14px;height:14px"></i>
                    </button>
                </li>
            `).join('');
            dropdown.classList.remove('hidden');
            if (typeof lucide !== 'undefined') lucide.createIcons();
        })
        .catch(() => dropdown.classList.add('hidden'));
}

function clearSearchHistory() {
    if (!IS_LOGGED_IN) return;
    fetch('<?= base_url('search/clear_history'); ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: getCsrfField()
    }).then(() => {
        document.getElementById('search-history').classList.add('hidden');
    });
}

function deleteHistoryItem(id) {
    if (!IS_LOGGED_IN) return;
    fetch('<?= base_url('search/delete_history'); ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: getCsrfField() + '&id=' + id
    }).then(() => renderSearchHistory());
}

function runSearch(keyword) {
    const input = document.getElementById('search-input');
    if (input) input.value = keyword;
    document.getElementById('search-form').submit();
}

document.addEventListener('DOMContentLoaded', function() {
    switchTab('posts');

    const searchInput = document.getElementById('search-input');
    const historyDropdown = document.getElementById('search-history');
    if (searchInput && historyDropdown) {
        searchInput.addEventListener('focus', function() {
            renderSearchHistory();
        });

        document.addEventListener('click', function(e) {
            if (!historyDropdown.classList.contains('hidden')) {
                const isInside = historyDropdown.contains(e.target) || searchInput.contains(e.target);
                if (!isInside) historyDropdown.classList.add('hidden');
            }
        });
    }
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
                    const avatarStyle = 'width:100%;height:100%';
                    const onlineHTML = post.is_online ? '<div class="online-indicator"></div>' : '';
                    const avatarBorderHTML = post.border
                        ? `<div class="absolute inset-0" style="width:100%;height:100%;pointer-events:none;transform:scale(1.25);transform-origin:center">
                               <img src="${escapeHtml(post.border)}" alt="F1 Border" style="width:100%;height:100%;object-fit:contain">
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
                            const itemStyle = (totalImages === 3 && idx === 0)
                                ? 'relative w-full row-span-2 overflow:hidden;background:var(--bg-body)'
                                : 'relative w-full h-full overflow:hidden;background:var(--bg-body)';
                            imagesTemplate += `
                                <div class="${itemStyle}">
                                    <img src="${escapeHtml(url)}" alt="Post Media" loading="lazy" class="w-full h-full transition-transform duration-500" style="object-fit:cover;width:100%;height:100%">
                                </div>
                            `;
                        });
                        mediaHTML = `
                            <div class="px-5 mb-1">
                                <div class="grid ${gridClass} rounded-lg overflow-hidden" style="background:var(--bg-surface);border:1px solid rgba(255,255,255,0.03)">
                                    ${imagesTemplate}
                                </div>
                            </div>
                        `;
                    }

                    const isOwner = <?= isset($current_user_id) && $current_user_id ? 'CURRENT_USER_ID' : '0'; ?> > 0 && post.user_id == <?= isset($current_user_id) && $current_user_id ? 'CURRENT_USER_ID' : '0'; ?>;
                    const dynamicLikeBtnClass = post.is_liked ? 'c-primary' : '';
                    const dynamicLikeBtnStyle = post.is_liked ? '' : 'transition:color 0.2s';
                    const dynamicLikeIconClass = post.is_liked ? 'fill-red-500' : '';
                    const dynamicLikeIconStyle = post.is_liked ? 'fill:var(--color-primary);color:var(--color-primary)' : '';
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

                    const dropdownItems = isOwner
                        ? `
                            <button onclick="copyPostLink(event, '<?= base_url('post/'); ?>${userJs}/${post.id_post}', this)" class="w-full text-left px-3 py-2 flex items-center gap-2 transition-colors" style="transition:background-color 0.2s" onmouseover="this.style.background='rgba(255,255,255,0.05)';this.style.color='var(--text-primary)'" onmouseout="this.style.background='';this.style.color=''">
                                <i data-lucide="link" style="width:14px;height:14px"></i><span>Copy Link</span>
                            </button>
                            <a href="<?= base_url('post/edit/'); ?>${post.id_post}" onclick="event.stopPropagation();" class="w-full text-left px-3 py-2 flex items-center gap-2 transition-colors" style="border-top:1px solid rgba(255,255,255,0.03);transition:background-color 0.2s" onmouseover="this.style.background='rgba(255,255,255,0.05)';this.style.color='var(--text-primary)'" onmouseout="this.style.background='';this.style.color=''">
                                <i data-lucide="pencil" style="width:14px;height:14px"></i><span>Edit</span>
                            </a>
                            <button onclick="event.stopPropagation(); deletePost(${post.id_post})" class="w-full text-left px-3 py-2 flex items-center gap-2 transition-colors" style="border-top:1px solid rgba(255,255,255,0.03);transition:background-color 0.2s" onmouseover="this.style.background='var(--color-primary-bg)';this.style.color='var(--color-primary)'" onmouseout="this.style.background='';this.style.color=''">
                                <i data-lucide="trash-2" style="width:14px;height:14px"></i><span>Hapus</span>
                            </button>`
                        : `
                            <button onclick="copyPostLink(event, '<?= base_url('post/'); ?>${userJs}/${post.id_post}', this)" class="w-full text-left px-3 py-2 flex items-center gap-2 transition-colors" style="transition:background-color 0.2s" onmouseover="this.style.background='rgba(255,255,255,0.05)';this.style.color='var(--text-primary)'" onmouseout="this.style.background='';this.style.color=''">
                                <i data-lucide="link" style="width:14px;height:14px"></i><span>Copy Link</span>
                            </button>
                            <button onclick="event.stopPropagation(); openReportPost(${post.id_post})" class="block w-full text-left px-3 py-2 flex items-center gap-2 transition-colors" style="border-top:1px solid rgba(255,255,255,0.03);transition:background-color 0.2s" onmouseover="this.style.background='var(--color-primary-bg)';this.style.color='var(--color-primary)'" onmouseout="this.style.background='';this.style.color=''">
                                <i data-lucide="flag" style="width:14px;height:14px"></i><span>Report Post</span>
                            </button>`;

                    const cardHTML = `
                        <article class="card overflow-hidden group relative" style="transition:all 0.2s;transition-property:background-color,border-color" data-post-id="${post.id_post}" data-user-id="${post.user_id}">
                            <a href="<?= base_url('post/'); ?>${userUrl}/${post.id_post}" class="absolute inset-0 z-10" aria-label="Lihat detail postingan"></a>
                            <div class="p-5 flex-row items-center justify-between">
                                <div class="flex-row items-center gap-3">
                                    <div class="relative w-9 h-9 flex items-center justify-center select-none z-20">
                                        <div class="rounded-full overflow-hidden" style="${avatarStyle};background:var(--bg-surface-raised)">
                                            <a href="<?= base_url('user/'); ?>${userUrl}"><img src="${escapedAvatar}" alt="User" class="rounded-full" style="width:100%;height:100%;object-fit:cover"></a>
                                        </div>
                                        ${avatarBorderHTML}
                                        ${onlineHTML}
                                    </div>
                                    <div class="flex-col justify-center">
                                        <div class="flex-row items-center gap-2">
                                            <a href="<?= base_url('user/'); ?>${userUrl}" class="font-semibold text-small transition-colors relative z-20" style="transition:color 0.2s" onmouseover="this.style.color='var(--color-primary)'" onmouseout="this.style.color=''">${escapedUsername}</a>
                                            ${post.team_name ? '<span class="inline-flex items-center gap-1" style="font-size:9px;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;padding:2px 6px;border-radius:9999px;border:1px solid var(--border-strong);background:' + escapedTeamColor + '15;"><img src="<?= assets_url(''); ?>' + escapedTeamLogo + '" alt="' + escapedTeamName + '" style="width:12px;height:12px;object-fit:contain"> ' + escapedTeamName + '</span>' : ''}
                                            <span class="c-faint" style="font-size:10px">•</span>
                                            <span class="inline-flex items-center c-white" style="font-size:8px;padding:2px 6px;font-weight:600;background:rgba(255,255,255,0.04);border:1px solid var(--border-default);border-radius:9999px;text-transform:uppercase;letter-spacing:0.05em">${escapedCategory}</span>
                                        </div>
                                        <span class="text-micro c-subtle" style="margin-top:2px">${escapedCreatedAt}</span>
                                    </div>
                                </div>
                                <div class="relative z-30 flex items-center">
                                    <button onclick="toggleDropdown(event, ${post.id_post})" class="c-subtle transition-colors p-1 rounded-md" style="transition:background-color 0.2s" onmouseover="this.style.background='rgba(255,255,255,0.05)';this.style.color='var(--text-secondary)'" onmouseout="this.style.background='';this.style.color=''">
                                        <i data-lucide="more-horizontal" style="width:16px;height:16px"></i>
                                    </button>
                                    <div id="dropdown-${post.id_post}" class="hidden absolute right-0 top-8" style="width:9rem;background:var(--bg-surface);backdrop-filter:blur(12px);border:1px solid var(--border-strong);border-radius:0.5rem;box-shadow:0 20px 25px -5px rgba(0,0,0,0.3);overflow:hidden;padding-top:4px;padding-bottom:4px;font-size:12px;color:var(--text-secondary)">
                                        ${dropdownItems}
                                    </div>
                                </div>
                            </div>
                            ${mediaHTML}
                            <div class="p-4 pt-2 space-y-3">
                                <p class="text-small c-secondary leading-relaxed">${escapedContent}</p>
                                <div class="flex-row items-center gap-4 pt-2 c-muted relative z-20" style="font-size:11px;border-top:1px solid rgba(255,255,255,0.03)">
                                    <button onclick="toggleLike(event, ${post.id_post}, this)" class="flex-row items-center gap-1.5 transition-colors group/btn ${dynamicLikeBtnClass}" style="${dynamicLikeBtnStyle}">
                                        <i data-lucide="heart" class="transition-transform ${dynamicLikeIconClass}" style="width:16px;height:16px;transition:transform 0.2s;${dynamicLikeIconStyle}"></i>
                                        <span class="font-semibold count-likes">${post.likes_count}</span>
                                    </button>
                                    <a href="<?= base_url('post/'); ?>${userUrl}/${post.id_post}" class="flex-row items-center gap-1.5 transition-colors" style="transition:color 0.2s" onmouseover="this.style.color='var(--color-info)'" onmouseout="this.style.color=''">
                                        <i data-lucide="message-square" class="transition-transform" style="width:16px;height:16px"></i>
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
                    const avatarStyle = 'width:100%;height:100%';
                    const borderHTML = user.border
                        ? `<div class="absolute inset-0" style="width:100%;height:100%;pointer-events:none;transform:scale(1.25);transform-origin:center">
                               <img src="${escapeHtml(user.border)}" alt="Border" style="width:100%;height:100%;object-fit:contain">
                           </div>`
                        : '';
                    const verifiedHTML = user.verified
                        ? `<i data-lucide="badge-check" class="c-info flex-shrink-0" style="width:14px;height:14px"></i>`
                        : '';
                    const onlineHTML = user.is_online ? '<div class="online-indicator"></div>' : '';

                    const isOwnProfile = <?= isset($current_user_id) && $current_user_id ? 'CURRENT_USER_ID' : 'null'; ?> && user.id_user == <?= isset($current_user_id) && $current_user_id ? 'CURRENT_USER_ID' : 'null'; ?>;
                        const followBtn = (!isOwnProfile && <?= isset($current_user_id) && $current_user_id ? 'CURRENT_USER_ID' : 'null'; ?>)
                        ? `<button onclick="event.preventDefault(); event.stopPropagation(); toggleFollowUser('${escapeJsString(user.id_user)}', this)" class="follow-btn flex-shrink-0 text-xs font-semibold rounded-full transition-all ${user.is_followed ? '' : 'btn-primary'}" style="${user.is_followed ? 'padding:4px 1rem;background:rgba(255,255,255,0.05);color:var(--text-secondary);border:1px solid var(--border-strong);transition:border-color 0.2s,color 0.2s' : 'padding:4px 1rem;border-color:var(--color-primary)'}"${user.is_followed ? ' onmouseover="this.style.borderColor=\\'var(--color-primary-border)\\';this.style.color=\\'var(--color-primary)\\'" onmouseout="this.style.borderColor=\\'var(--border-strong)\\';this.style.color=\\'var(--text-secondary)\\'"' : ''}>${user.is_followed ? 'Following' : 'Follow'}</button>`
                        : '';

                    const escapedUserUrl = encodeURIComponent(user.username);
                    const escapedDisplayName = escapeHtml(user.display_name);
                    const escapedUsername = escapeHtml(user.username);
                    const escapedAvatar = escapeHtml(user.avatar);

                    const cardHTML = `
                        <div class="flex-row items-center justify-between p-4" style="border-bottom:1px solid rgba(255,255,255,0.04);transition:background-color 0.2s" data-user-id="${user.id_user}" onmouseover="this.style.background='rgba(255,255,255,0.02)'" onmouseout="this.style.background=''">
                            <a href="<?= base_url('user/'); ?>${escapedUserUrl}" class="flex-row items-center gap-3 flex-1 min-w-0">
                                <div class="relative w-10 h-10 flex-shrink-0 flex items-center justify-center">
                                    <div class="rounded-full overflow-hidden" style="${avatarStyle};background:var(--bg-surface-raised)">
                                        <img src="${escapedAvatar}" alt="${escapedUsername}" class="rounded-full" style="width:100%;height:100%;object-fit:cover">
                                    </div>
                                    ${borderHTML}
                                    ${onlineHTML}
                                </div>
                                <div class="min-w-0">
                                    <div class="flex-row items-center gap-1.5">
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