<div id="post-container" class="space-y-4">
    <?php if (!empty($all_posts)): ?>
        <?php foreach ($all_posts as $post): ?>
            <?php 
                $is_liked = isset($post['is_liked']) && $post['is_liked'] == true; 
                $like_btn_class = $is_liked ? 'text-red-500' : 'hover:text-red-500';
                $like_icon_class = $is_liked ? 'fill-red-500 text-red-500' : '';
            ?>
            <article class="glass-card overflow-hidden group transition-all relative hover:bg-white/[0.02]">
                
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
                        </div>
                        
                        <div class="flex flex-col justify-center">
                            <div class="flex items-center gap-2">
                                <a href="<?= base_url('user/' . $post['username']); ?>" class="font-semibold text-xs sm:text-sm hover:text-red-400 cursor-pointer transition-colors relative z-20"><?= $post['username']; ?></a>                                    
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
                            
                            <a href="<?= base_url('post/report/' . $post['id_post']); ?>" class="block text-left px-3 py-2 hover:bg-red-500/10 hover:text-red-400 flex items-center gap-2 transition-colors border-t border-white/[0.03]">
                                <i data-lucide="flag" class="w-3.5 h-3.5"></i>
                                <span>Report Post</span>
                            </a>
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
                                        <img src="<?= trim($img_url); ?>" alt="Post Media" class="w-full h-full object-cover group-hover:scale-[1.01] transition-transform duration-500">
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                <div class="p-4 sm:p-5 pt-2 space-y-3">
                    <p class="text-xs sm:text-sm text-slate-300 leading-relaxed">
                        <?= $post['content']; ?>
                    </p>
                    
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
let offset = 5;       
const limit = 5;      
let isLoading = false;
let hasMoreData = true;

const categorySlug = '<?= isset($active_category_slug) ? $active_category_slug : ''; ?>';
const IS_GUEST = <?= (isset($is_guest) && $is_guest) ? 'true' : 'false'; ?>;

// Nonaktifkan infinite scroll untuk guest
if (IS_GUEST) {
    hasMoreData = false;
}

window.addEventListener('scroll', () => {
    if (isLoading || !hasMoreData) return;
    if (window.innerHeight + window.scrollY >= document.documentElement.scrollHeight - 150) {
        loadMorePosts();
    }
});

function loadMorePosts() {
    // Safety guard: jangan load more untuk guest
    if (IS_GUEST) {
        isLoading = false;
        return;
    }

    isLoading = true;
    const loadingBadge = document.getElementById('loading-badge');
    loadingBadge.classList.remove('hidden'); 
    
    let url = `<?= base_url('home/load_more_posts'); ?>?offset=${offset}`;
    if (categorySlug) url += `&category=${categorySlug}`;
    
    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.length === 0) {
                hasMoreData = false;
                loadingBadge.innerHTML = "<span class='text-slate-600 uppercase tracking-wider text-[10px]'>Kamu telah mencapai batas akhir postingan.</span>";
                return;
            }
            
            const container = document.getElementById('post-container');
            
            data.forEach(post => {
                const avatarClass = post.border ? 'w-[84%] h-[84%]' : 'w-full h-full';
                const avatarBorderHTML = post.border 
                    ? `<div class="absolute inset-0 w-full h-full pointer-events-none scale-[1.25] transform origin-center">
                        <img src="${post.border}" alt="F1 Border Decoration" class="w-full h-full object-contain">
                       </div>` 
                    : '';

                let mediaHTML = '';
                if (post.file_url) {
                    // Pecah string koma menjadi array di JavaScript
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

                    // Ambil maksimal 4 gambar saja
                    const imagesToShow = images.slice(0, 4);

                    imagesToShow.forEach((url, index) => {
                        // Trik layout jika 3 gambar: gambar index 0 ditarik vertical penuh (row-span-2)
                        const itemClass = (totalImages === 3 && index === 0) ? 'row-span-2 h-full' : 'h-full';

                        imagesTemplate += `
                            <div class="relative w-full ${itemClass} overflow-hidden bg-slate-950">
                                <img src="${url}" alt="Post Media ${index + 1}" class="w-full h-full object-cover group-hover:scale-[1.01] transition-transform duration-500">
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

                // Render tombol aksi dynamic infinite-scroll berdasarkan status liked
                const dynamicLikeBtnClass = post.is_liked ? 'text-red-500' : 'hover:text-red-500';
                const dynamicLikeIconClass = post.is_liked ? 'fill-red-500 text-red-500' : '';

                const cardHTML = `
                    <article class="glass-card overflow-hidden group transition-all relative hover:bg-white/[0.02]">
                        <a href="<?= base_url('post/'); ?>${post.username}/${post.id_post}" class="absolute inset-0 z-10" aria-label="Lihat detail postingan"></a>
                        
                        <div class="p-4 sm:p-5 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="relative w-9 h-9 flex items-center justify-center select-none z-20">
                                    <div class="${avatarClass} rounded-full overflow-hidden bg-slate-800">
                                        <a href="<?= base_url('user/'); ?>${post.username}">
                                            <img src="${post.avatar}" alt="User" class="w-full h-full object-cover rounded-full">
                                        </a>
                                    </div>
                                    ${avatarBorderHTML}
                                </div>
                                
                                <div class="flex flex-col justify-center">
                                    <div class="flex items-center gap-2">
                                        <a href="<?= base_url('user/'); ?>${post.username}" class="font-semibold text-xs sm:text-sm hover:text-red-400 cursor-pointer transition-colors relative z-20">${post.username}</a>                                    
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
                                    
                                    <a href="<?= base_url('post/report/'); ?>${post.id_post}" class="block text-left px-3 py-2 hover:bg-red-500/10 hover:text-red-400 flex items-center gap-2 transition-colors border-t border-white/[0.03]">
                                        <i data-lucide="flag" class="w-3.5 h-3.5"></i>
                                        <span>Report Post</span>
                                    </a>
                                </div>
                            </div>
                        </div>

                        ${mediaHTML}

                        <div class="p-4 sm:p-5 pt-2 space-y-3">
                            <p class="text-xs sm:text-sm text-slate-300 leading-relaxed">
                                ${post.content}
                            </p>
                            
                            <div class="flex items-center gap-4 pt-2 border-t border-white/[0.03] text-slate-400 text-[11px] sm:text-xs relative z-20">
                                <button 
                                    onclick="toggleLike(event, ${post.id_post}, this)" 
                                    class="flex items-center gap-1.5 transition-colors group/btn ${dynamicLikeBtnClass}"
                                >
                                    <i data-lucide="heart" class="w-4 h-4 group-hover/btn:scale-110 transition-transform ${dynamicLikeIconClass}"></i>
                                    <span class="font-semibold count-likes">${post.likes_count}</span>
                                </button>
                                
                                <a 
                                    href="<?= base_url('post/'); ?>${post.username}/${post.id_post}" 
                                    class="flex items-center gap-1.5 hover:text-blue-400 transition-colors group/btn"
                                >
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

    fetch(url, { method: 'POST' })
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
</script>