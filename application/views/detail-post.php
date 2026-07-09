<div class="space-y-4 max-w-2xl mx-auto">
    <?php 
        $is_liked = isset($post['is_liked']) && $post['is_liked'] == true; 
        $like_btn_class = $is_liked ? 'text-red-500' : 'hover:text-red-500';
        $like_icon_class = $is_liked ? 'fill-red-500 text-red-500' : '';
        $post_content_attr = htmlspecialchars($post['content'], ENT_QUOTES, 'UTF-8');
        $post_category_attr = htmlspecialchars($post['post_category'] ?? '', ENT_QUOTES, 'UTF-8');
    ?>
    
    <article class="glass-card overflow-hidden group transition-all relative" data-post-id="<?= $post['id_post']; ?>">
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
                <button onclick="toggleDropdown(event, 'post-<?= $post['id_post']; ?>')" class="text-slate-500 hover:text-slate-300 transition-colors p-1 rounded-md hover:bg-white/[0.05]">
                    <i data-lucide="more-horizontal" class="w-4 h-4"></i>
                </button>
                <div id="dropdown-post-<?= $post['id_post']; ?>" class="hidden absolute right-0 top-8 w-36 bg-slate-900/95 backdrop-blur-md border border-white/[0.08] rounded-lg shadow-xl overflow-hidden py-1 text-xs text-slate-300">
                    <button onclick="copyPostLink(event, '<?= base_url('post/' . $post['username'] . '/' . $post['id_post']); ?>', this)" class="w-full text-left px-3 py-2 hover:bg-white/[0.05] hover:text-white flex items-center gap-2 transition-colors">
                        <i data-lucide="link" class="w-3.5 h-3.5"></i>
                        <span>Copy Link</span>
                    </button>
                    <?php if (isset($current_user_id) && $current_user_id === (string)$post['user_id']): ?>
                        <button onclick="event.stopPropagation(); openEditPostModal('<?= $post['id_post']; ?>', '<?= $post_content_attr; ?>', '<?= $post_category_attr; ?>')" class="w-full text-left px-3 py-2 hover:bg-white/[0.05] hover:text-white flex items-center gap-2 transition-colors border-t border-white/[0.03]">
                            <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                            <span>Edit</span>
                        </button>
                        <button onclick="event.stopPropagation(); deletePost(<?= $post['id_post']; ?>)" class="w-full text-left px-3 py-2 hover:bg-red-500/10 hover:text-red-400 flex items-center gap-2 transition-colors border-t border-white/[0.03]">
                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                            <span>Hapus</span>
                        </button>
                    <?php else: ?>
                        <button onclick="event.stopPropagation(); openReportPost(<?= $post['id_post']; ?>)" class="w-full text-left px-3 py-2 hover:bg-red-500/10 hover:text-red-400 flex items-center gap-2 transition-colors border-t border-white/[0.03]">
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
                if ($total_images === 1) { $grid_class = 'grid-cols-1 aspect-[4/3]'; }
                elseif ($total_images === 2) { $grid_class = 'grid-cols-2 aspect-[4/3] gap-1'; }
                elseif ($total_images === 3) { $grid_class = 'grid-cols-2 aspect-[4/3] gap-1'; }
                else { $grid_class = 'grid-cols-2 grid-rows-2 aspect-[4/3] gap-1'; }
                $images_to_show = array_slice($images, 0, 4);
            ?>
            <div class="px-4 sm:px-5 mb-1 relative z-20"> 
                <div class="grid <?= $grid_class; ?> bg-slate-900 border border-white/[0.03] rounded-lg overflow-hidden">
                    <?php foreach ($images_to_show as $index => $img_url): ?>
                        <?php $item_class = ($total_images === 3 && $index === 0) ? 'row-span-2 h-full' : 'h-full'; ?>
                        <div class="relative w-full <?= $item_class; ?> overflow-hidden bg-slate-950">
                            <img src="<?= trim($img_url); ?>" 
                                 alt="Post Media" loading="lazy"
                                 class="w-full h-full object-cover cursor-pointer hover:scale-[1.02] transition-transform duration-300"
                                 onclick="openLightbox(<?= $index; ?>)">
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="p-4 sm:p-5 pt-2 space-y-3">
            <p class="text-xs sm:text-sm text-slate-300 leading-relaxed">
                <?= htmlspecialchars($post['content'], ENT_QUOTES, 'UTF-8'); ?>
            </p>
            
            <div class="flex items-center gap-4 pt-2 border-t border-white/[0.03] text-slate-400 text-[11px] sm:text-xs relative z-20">
                <button onclick="toggleLike(event, <?= $post['id_post']; ?>, this)" class="flex items-center gap-1.5 transition-colors group/btn <?= $like_btn_class; ?>">
                    <i data-lucide="heart" class="w-4 h-4 group-hover/btn:scale-110 transition-transform <?= $like_icon_class; ?>"></i>
                    <span class="font-semibold count-likes"><?= $post['likes_count']; ?></span>
                </button>
                <div class="flex items-center gap-1.5 text-slate-400">
                    <i data-lucide="message-square" class="w-4 h-4"></i>
                    <span class="font-semibold" id="comment-count-header"><?= count($comments); ?></span>
                </div>
            </div>
        </div>
    </article>

    <div class="glass-card p-4 flex gap-3 items-start">
        <div class="w-9 h-9 rounded-full overflow-hidden bg-slate-800 shrink-0">
            <img src="<?= $current_user_avatar; ?>" alt="My Avatar" class="w-full h-full object-cover">
        </div>
        <div class="flex-1 space-y-2">
            <div id="reply-target-badge" class="hidden flex items-center justify-between bg-white/[0.03] border border-white/[0.05] rounded-md px-2.5 py-1 text-[10px] text-slate-400">
                <span>Membalas <strong class="text-red-400" id="reply-username">@username</strong></span>
                <button onclick="cancelReplyMode()" class="text-slate-500 hover:text-slate-300 transition-colors">
                    <i data-lucide="x" class="w-3 h-3"></i>
                </button>
            </div>

            <form id="comment-form" onsubmit="submitComment(event, <?= $post['id_post']; ?>)">
                <input type="hidden" id="parent-comment-id" value="0">
                <textarea 
                    id="comment-input"
                    rows="2" 
                    placeholder="Balas postingan <?= htmlspecialchars($post['username'], ENT_QUOTES, 'UTF-8'); ?>..." 
                    class="w-full bg-transparent text-xs sm:text-sm text-slate-200 placeholder-slate-500 focus:outline-none resize-none border-b border-white/[0.03] pb-2 focus:border-red-500/50 transition-colors"
                    required
                ></textarea>
                <div class="flex justify-end pt-1">
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-semibold text-xs px-4 py-1.5 rounded-full transition-colors shadow-lg shadow-red-600/10">
                        Reply
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="glass-card p-4 sm:p-5 space-y-4">
        <div class="flex items-center justify-between border-b border-white/[0.04] pb-3">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Komentar</h3>
            <span class="text-[10px] text-slate-500 font-medium px-2 py-0.5 bg-white/[0.03] rounded-full border border-white/[0.05]" id="comment-count-badge"><?= count($comments); ?> Respon</span>
        </div>
        
        <div id="comments-container" class="space-y-4">
            <?php if (!empty($comments)): ?>
                <?php
                $main_comments = [];
                $replies = [];

                foreach ($comments as $comment) {
                    if (empty($comment['parent_id'])) {
                        $main_comments[$comment['id_comment']] = $comment;
                    } else {
                        $replies[$comment['parent_id']][] = $comment;
                    }
                }
                ?>

                <?php foreach ($main_comments as $main_id => $main_comment): ?>
                    <?php 
                        $m_liked = isset($main_comment['is_liked_comment']) && $main_comment['is_liked_comment'] == true;
                        $m_like_btn_class = $m_liked ? 'text-red-500' : 'hover:text-red-500';
                        $m_like_icon_class = $m_liked ? 'fill-red-500 text-red-500' : '';
                        $has_replies = isset($replies[$main_id]);
                        $reply_count = $has_replies ? count($replies[$main_id]) : 0;
                    ?>
                    
                    <div class="space-y-2" id="comment-thread-<?= $main_id; ?>">
                        
                        <div class="bg-white/[0.01] hover:bg-white/[0.02] border border-white/[0.03] rounded-xl p-4 flex gap-3 items-start transition-all duration-300 relative group/comment">
                            <div class="w-8 h-8 rounded-full overflow-hidden bg-slate-800 shrink-0">
                                <img src="<?= $main_comment['avatar']; ?>" alt="User Avatar" class="w-full h-full object-cover">
                            </div>
                            <div class="flex-1 min-w-0 space-y-1">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="font-semibold text-xs text-slate-200"><?= $main_comment['username']; ?></span>
                                        <span class="text-slate-600 text-[10px]">•</span>
                                        <span class="text-[10px] text-slate-500"><?= $main_comment['created_at']; ?></span>
                                    </div>
                                    
                                    <div class="relative z-30 invisible group-hover/comment:visible transition-all">
                                        <button onclick="toggleDropdown(event, 'comment-<?= $main_id; ?>')" class="text-slate-500 hover:text-slate-300 p-0.5 rounded hover:bg-white/[0.05]">
                                            <i data-lucide="more-horizontal" class="w-3.5 h-3.5"></i>
                                        </button>
                                        <div id="dropdown-comment-<?= $main_id; ?>" class="hidden absolute right-0 top-6 w-32 bg-slate-900 border border-white/[0.08] rounded-md shadow-xl overflow-hidden py-1 text-[11px] text-slate-300">
                                            <?php if (isset($current_user_id) && $current_user_id === (string)$main_comment['user_id']): ?>
                                                <button onclick="event.stopPropagation(); editComment(<?= $main_id; ?>)" class="block w-full text-left px-3 py-1.5 hover:bg-white/[0.05] hover:text-white flex items-center gap-1.5 transition-colors">
                                                    <i data-lucide="pencil" class="w-3 h-3"></i>
                                                    <span>Edit</span>
                                                </button>
                                                <button onclick="event.stopPropagation(); deleteComment(<?= $main_id; ?>)" class="block w-full text-left px-3 py-1.5 hover:bg-red-500/10 hover:text-red-400 flex items-center gap-1.5 transition-colors border-t border-white/[0.03]">
                                                    <i data-lucide="trash-2" class="w-3 h-3"></i>
                                                    <span>Hapus</span>
                                                </button>
                                            <?php else: ?>
                                                <button onclick="event.stopPropagation(); openReportComment(<?= $main_id; ?>)" class="block w-full text-left px-3 py-1.5 hover:bg-red-500/10 hover:text-red-400 flex items-center gap-1.5 transition-colors">
                                                    <i data-lucide="flag" class="w-3 h-3"></i>
                                                    <span>Laporkan</span>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <p id="comment-text-<?= $main_id; ?>" class="text-xs sm:text-sm text-slate-300 leading-relaxed pt-0.5">
                                    <?= htmlspecialchars($main_comment['comment_text'], ENT_QUOTES, 'UTF-8'); ?>
                                </p>
                                <div class="flex items-center gap-4 pt-2 border-t border-white/[0.02] text-[10px] sm:text-xs text-slate-500">
                                    <button onclick="toggleLikeComment(event, <?= $main_id; ?>, this)" class="flex items-center gap-1 transition-colors <?= $m_like_btn_class; ?>">
                                        <i data-lucide="heart" class="w-3.5 h-3.5 <?= $m_like_icon_class; ?>"></i>
                                        <span class="font-medium count-comment-likes"><?= $main_comment['likes_count'] ?? 0; ?></span>
                                    </button>
                                    <button onclick="setReplyTarget('<?= addslashes($main_comment['username']); ?>', <?= $main_id; ?>)" class="flex items-center gap-1 hover:text-blue-400 transition-colors">
                                        <i data-lucide="corner-up-left" class="w-3.5 h-3.5"></i>
                                        <span class="font-medium">Reply</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <?php if ($has_replies): ?>
                            <div class="pl-6 sm:pl-10 ml-4 sm:ml-5">
                                <button onclick="toggleReplies(<?= $main_id; ?>)" id="btn-toggle-replies-<?= $main_id; ?>" class="flex items-center gap-1.5 text-[11px] text-red-400 hover:text-red-300 font-medium transition-colors py-1">
                                    <i data-lucide="chevron-down" class="w-3.5 h-3.5 icon-toggle"></i>
                                    <span>Lihat Balasan (<?= $reply_count; ?>)</span>
                                </button>
                            </div>
                        <?php endif; ?>

                        <div id="replies-wrapper-<?= $main_id; ?>" class="<?= $has_replies ? 'hidden' : ''; ?> replies-container pl-6 sm:pl-10 border-l border-slate-800/60 space-y-2 ml-4 sm:ml-5">
                            <?php if ($has_replies): ?>
                                <?php foreach ($replies[$main_id] as $reply): ?>
                                    <?php 
                                        $r_liked = isset($reply['is_liked_comment']) && $reply['is_liked_comment'] == true;
                                        $r_like_btn_class = $r_liked ? 'text-red-500' : 'hover:text-red-500';
                                        $r_like_icon_class = $r_liked ? 'fill-red-500 text-red-500' : '';
                                        $reply_id = $reply['id_comment'] ?? 0;
                                    ?>
                                                    <div class="bg-white/[0.005] border border-white/[0.02] rounded-xl p-3.5 flex gap-3 items-start transition-all duration-300 relative group/reply">
                                                        <div class="w-7 h-7 rounded-full overflow-hidden bg-slate-800 shrink-0">
                                                            <img src="<?= $reply['avatar']; ?>" alt="User Avatar" class="w-full h-full object-cover">
                                                        </div>
                                                        <div class="flex-1 min-w-0 space-y-1">
                                                            <div class="flex items-center justify-between">
                                                                <div class="flex items-center gap-2">
                                                                    <span class="font-semibold text-xs text-slate-200"><?= $reply['username']; ?></span>
                                                                    <span class="text-slate-600 text-[10px]">•</span>
                                                                    <span class="text-[10px] text-slate-500"><?= $reply['created_at']; ?></span>
                                                                </div>
                                                                
                                                                <div class="relative z-30 invisible group-hover/reply:visible transition-all">
                                                                    <button onclick="toggleDropdown(event, 'reply-<?= $reply_id; ?>')" class="text-slate-500 hover:text-slate-300 p-0.5 rounded hover:bg-white/[0.05]">
                                                                        <i data-lucide="more-horizontal" class="w-3.5 h-3.5"></i>
                                                                    </button>
                                                                    <div id="dropdown-reply-<?= $reply_id; ?>" class="hidden absolute right-0 top-6 w-32 bg-slate-900 border border-white/[0.08] rounded-md shadow-xl overflow-hidden py-1 text-[11px] text-slate-300">
                                                                        <?php if (isset($current_user_id) && $current_user_id === (string)$reply['user_id']): ?>
                                                                            <button onclick="event.stopPropagation(); editComment(<?= $reply_id; ?>)" class="block w-full text-left px-3 py-1.5 hover:bg-white/[0.05] hover:text-white flex items-center gap-1.5 transition-colors">
                                                                                <i data-lucide="pencil" class="w-3 h-3"></i>
                                                                                <span>Edit</span>
                                                                            </button>
                                                                            <button onclick="event.stopPropagation(); deleteComment(<?= $reply_id; ?>)" class="block w-full text-left px-3 py-1.5 hover:bg-red-500/10 hover:text-red-400 flex items-center gap-1.5 transition-colors border-t border-white/[0.03]">
                                                                                <i data-lucide="trash-2" class="w-3 h-3"></i>
                                                                                <span>Hapus</span>
                                                                            </button>
                                                                        <?php else: ?>
                                                                            <button onclick="event.stopPropagation(); openReportComment(<?= $reply_id; ?>)" class="block w-full text-left px-3 py-1.5 hover:bg-red-500/10 hover:text-red-400 flex items-center gap-1.5 transition-colors">
                                                                                <i data-lucide="flag" class="w-3 h-3"></i>
                                                                                <span>Laporkan</span>
                                                                            </button>
                                                                        <?php endif; ?>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <p class="text-[10px] text-slate-500">Membalas <span class="text-blue-400/90">@<?= $reply['parent_username']; ?></span></p>
                                                            <p id="comment-text-<?= $reply_id; ?>" class="text-xs text-slate-300 leading-relaxed pt-0.5">
                                                                <?= htmlspecialchars($reply['comment_text'], ENT_QUOTES, 'UTF-8'); ?>
                                                            </p>
                                            <div class="flex items-center gap-4 pt-1.5 border-t border-white/[0.01] text-[10px] text-slate-500">
                                                <button onclick="toggleLikeComment(event, <?= $reply_id; ?>, this)" class="flex items-center gap-1 transition-colors <?= $r_like_btn_class; ?>">
                                                    <i data-lucide="heart" class="w-3.5 h-3.5 <?= $r_like_icon_class; ?>"></i>
                                                    <span class="font-medium count-comment-likes"><?= $reply['likes_count'] ?? 0; ?></span>
                                                </button>
                                                <button onclick="setReplyTarget('<?= addslashes($reply['username']); ?>', <?= $main_id; ?>)" class="flex items-center gap-1 hover:text-blue-400 transition-colors">
                                                    <i data-lucide="corner-up-left" class="w-3.5 h-3.5"></i>
                                                    <span class="font-medium">Reply</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>

                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div id="no-comment-placeholder" class="p-6 text-center text-slate-500 text-xs tracking-wide">
                    Belum ada komentar. Jadilah yang pertama membalas!
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<div id="lightbox-modal" class="hidden fixed inset-0 z-[999] bg-black/95 backdrop-blur-sm flex flex-col justify-between p-4 select-none animate-fade-in">
    <div class="flex items-center justify-between text-white w-full max-w-6xl mx-auto h-12">
        <span id="lightbox-counter" class="text-xs font-semibold tracking-wide text-slate-400">1 / 1</span>
        <button onclick="closeLightbox()" class="text-slate-400 hover:text-white p-2 rounded-full hover:bg-white/10 transition-colors">
            <i data-lucide="x" class="w-6 h-6"></i>
        </button>
    </div>
    <div class="relative flex-1 flex items-center justify-center w-full max-w-5xl mx-auto group">
        <button id="lightbox-prev-btn" onclick="changeImage(-1)" class="absolute left-2 sm:left-4 z-50 text-white bg-black/40 hover:bg-black/60 p-3 rounded-full border border-white/10 transition-all opacity-0 group-hover:opacity-100 focus:opacity-100">
            <i data-lucide="chevron-left" class="w-5 h-5"></i>
        </button>
        <div class="w-full h-full max-h-[75vh] flex items-center justify-center p-2">
            <img id="lightbox-active-img" src="" alt="Lightbox Media" class="max-w-full max-h-full object-contain rounded shadow-2xl animate-scale-up">
        </div>
        <button id="lightbox-next-btn" onclick="changeImage(1)" class="absolute right-2 sm:right-4 z-50 text-white bg-black/40 hover:bg-black/60 p-3 rounded-full border border-white/10 transition-all opacity-0 group-hover:opacity-100 focus:opacity-100">
            <i data-lucide="chevron-right" class="w-5 h-5"></i>
        </button>
    </div>
    <div class="h-8"></div>
</div>

</main>

<script>
// Toggle Dropdown Global (Untuk Post, Komentar Utama, dan Replies)
function toggleDropdown(event, id) {
    event.stopPropagation();
    document.querySelectorAll('[id^="dropdown-"]').forEach(dropdown => {
        if (dropdown.id !== `dropdown-${id}`) dropdown.classList.add('hidden');
    });
    const targetDropdown = document.getElementById(`dropdown-${id}`);
    if (targetDropdown) targetDropdown.classList.toggle('hidden');
}

document.addEventListener('click', function() {
    document.querySelectorAll('[id^="dropdown-"]').forEach(dropdown => dropdown.classList.add('hidden'));
});

function toggleReplies(mainId) {
    const wrapper = document.getElementById(`replies-wrapper-${mainId}`);
    const button = document.getElementById(`btn-toggle-replies-${mainId}`);
    const icon = button.querySelector('.icon-toggle');
    const label = button.querySelector('span');
    const isHidden = wrapper.classList.contains('hidden');
    
    if (isHidden) {
        wrapper.classList.remove('hidden');
        icon.style.transform = 'rotate(180deg)';
        label.innerText = 'Sembunyikan Balasan';
    } else {
        wrapper.classList.add('hidden');
        icon.style.transform = 'rotate(0deg)';
        const count = wrapper.children.length;
        label.innerText = `Lihat Balasan (${count})`;
    }
}

function setReplyTarget(username, commentId) {
    document.getElementById('parent-comment-id').value = commentId;
    document.getElementById('reply-username').innerText = `@${username}`;
    document.getElementById('reply-target-badge').classList.remove('hidden');
    
    const inputField = document.getElementById('comment-input');
    inputField.placeholder = `Balas @${username}...`;
    inputField.focus();
}

function cancelReplyMode() {
    document.getElementById('parent-comment-id').value = "0";
    document.getElementById('reply-target-badge').classList.add('hidden');
    
    const inputField = document.getElementById('comment-input');
    inputField.placeholder = <?= json_encode('Balas postingan ' . $post['username'] . '...', JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
}

// Submit via AJAX
function submitComment(event, postId) {
    event.preventDefault();
    
    const inputElement = document.getElementById('comment-input');
    const parentId = document.getElementById('parent-comment-id').value;
    const commentText = inputElement.value.trim();
    if (!commentText) return;

    const formData = new FormData();
    formData.append('id_post', postId);
    formData.append('comment_text', commentText);
    formData.append('parent_id', parentId);
    formData.append(document.querySelector('meta[name="csrf-token-name"]').content, document.querySelector('meta[name="csrf-token-hash"]').content);

    fetch('<?= base_url("post/add_comment"); ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            const placeholder = document.getElementById('no-comment-placeholder');
            if (placeholder) placeholder.remove();

            const commentId = data.new_comment.id_comment || Date.now();
            let targetReplyHTML = '';
            
            if (parseInt(parentId) > 0) {
                const targetName = document.getElementById('reply-username').innerText;
                targetReplyHTML = `<p class="text-[10px] text-slate-500">Membalas <span class="text-red-400/90">${targetName}</span></p>`;
            }

            const commentHTML = `
                <div class="bg-white/[0.01] hover:bg-white/[0.02] border border-white/[0.03] rounded-xl p-4 flex gap-3 items-start transition-all duration-300 animate-fade-in relative group/comment">
                    <div class="w-8 h-8 rounded-full overflow-hidden bg-slate-800 shrink-0">
                        <img src="${data.new_comment.avatar}" alt="User Avatar" class="w-full h-full object-cover">
                    </div>
                    <div class="flex-1 min-w-0 space-y-1">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="font-semibold text-xs text-slate-200">${data.new_comment.username}</span>
                                <span class="text-slate-600 text-[10px]">•</span>
                                <span class="text-[10px] text-slate-500">Baru saja</span>
                            </div>
                            <div class="relative z-30 invisible group-hover/comment:visible transition-all">
                                <button onclick="toggleDropdown(event, 'comment-${commentId}')" class="text-slate-500 hover:text-slate-300 p-0.5 rounded hover:bg-white/[0.05]">
                                    <i data-lucide="more-horizontal" class="w-3.5 h-3.5"></i>
                                </button>
                                <div id="dropdown-comment-${commentId}" class="hidden absolute right-0 top-6 w-32 bg-slate-900 border border-white/[0.08] rounded-md shadow-xl overflow-hidden py-1 text-[11px] text-slate-300">
                                    <button onclick="event.stopPropagation(); editComment(${commentId})" class="block w-full text-left px-3 py-1.5 hover:bg-white/[0.05] hover:text-white flex items-center gap-1.5 transition-colors">
                                        <i data-lucide="pencil" class="w-3 h-3"></i>
                                        <span>Edit</span>
                                    </button>
                                    <button onclick="event.stopPropagation(); deleteComment(${commentId})" class="block w-full text-left px-3 py-1.5 hover:bg-red-500/10 hover:text-red-400 flex items-center gap-1.5 transition-colors border-t border-white/[0.03]">
                                        <i data-lucide="trash-2" class="w-3 h-3"></i>
                                        <span>Hapus</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        ${targetReplyHTML}
                        <p id="comment-text-${commentId}" class="text-xs sm:text-sm text-slate-300 leading-relaxed pt-0.5">
                            ${escapeHtml(commentText)}
                        </p>
                        <div class="flex items-center gap-4 pt-2 border-t border-white/[0.02] text-[10px] sm:text-xs text-slate-500">
                            <button onclick="toggleLikeComment(event, ${commentId}, this)" class="flex items-center gap-1 hover:text-red-400 transition-colors">
                                <i data-lucide="heart" class="w-3.5 h-3.5"></i>
                                <span class="font-medium count-comment-likes">0</span>
                            </button>
                            <button onclick="setReplyTarget('${data.new_comment.username.replace(/'/g, "\\'")}', ${parentId > 0 ? parentId : commentId})" class="flex items-center gap-1 hover:text-blue-400 transition-colors">
                                <i data-lucide="corner-up-left" class="w-3.5 h-3.5"></i>
                                <span class="font-medium">Reply</span>
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            if (parseInt(parentId) > 0) {
                const replyWrapper = document.getElementById(`replies-wrapper-${parentId}`);
                if(replyWrapper) {
                    replyWrapper.insertAdjacentHTML('beforeend', commentHTML);
                    replyWrapper.classList.remove('hidden');
                }
            } else {
                const container = document.getElementById('comments-container');
                container.insertAdjacentHTML('afterbegin', commentHTML);
            }
            
            inputElement.value = '';
            cancelReplyMode();

            const countHeader = document.getElementById('comment-count-header');
            const countBadge = document.getElementById('comment-count-badge');
            const currentTotal = parseInt(countHeader.innerText) + 1;
            
            if (countHeader) countHeader.innerText = currentTotal;
            if (countBadge) countBadge.innerText = `${currentTotal} Respon`;

            if (typeof lucide !== 'undefined') lucide.createIcons();
        } else {
            alert('Gagal mengirim komentar: ' + data.message);
        }
    })
    .catch(error => console.error('Error:', error));
}

// Fitur Like Comment Menggunakan Fetch API AJAX (Sama Persis seperti Like Post)
// Fitur Like Comment Menggunakan Fetch API AJAX (Sama Persis seperti Like Post)
function toggleLikeComment(event, idComment, buttonElement) {
    event.preventDefault();
    event.stopPropagation(); 

    // Cek login: jika guest, tampilkan modal login
    if (!IS_LOGGED_IN) {
        showLoginModal();
        return;
    }

    const icon = buttonElement.querySelector('[data-lucide="heart"]');
    const countSpan = buttonElement.querySelector('.count-comment-likes');

    const url = `<?= base_url('post/toggle_like_comment'); ?>/${idComment}`;

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
            console.error('Gagal memproses like komentar:', err);
            showToast('Gagal menyukai komentar. Coba lagi.', 'red');
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
        .catch(err => {
            console.error('Gagal memproses like post:', err);
            showToast('Gagal menyukai postingan. Coba lagi.', 'red');
        });
}

// Fungsi Edit Komentar (Inline)
function editComment(commentId) {
    const textEl = document.getElementById(`comment-text-${commentId}`);
    if (!textEl) return;
    const currentText = textEl.innerText;

    // Sembunyikan teks asli
    textEl.style.display = 'none';

    // Buat textarea + tombol simpan/batal
    const editContainer = document.createElement('div');
    editContainer.id = `comment-edit-container-${commentId}`;
    editContainer.className = 'space-y-2 pt-0.5';
    editContainer.innerHTML = `
        <textarea id="comment-edit-input-${commentId}" class="w-full bg-slate-800 text-xs text-slate-200 rounded-lg px-3 py-2 border border-white/[0.06] focus:outline-none focus:border-red-500/50 resize-none" rows="2">${escapeHtml(currentText)}</textarea>
        <div class="flex gap-2 justify-end">
            <button onclick="cancelEditComment(${commentId})" class="text-[10px] px-3 py-1 rounded-lg bg-white/[0.05] text-slate-300 hover:bg-white/[0.08] transition-colors">Batal</button>
            <button onclick="saveComment(${commentId})" class="text-[10px] px-3 py-1 rounded-lg bg-red-600 text-white hover:bg-red-500 transition-colors">Simpan</button>
        </div>
    `;

    textEl.parentNode.insertBefore(editContainer, textEl.nextSibling);
}

function cancelEditComment(commentId) {
    const textEl = document.getElementById(`comment-text-${commentId}`);
    const editContainer = document.getElementById(`comment-edit-container-${commentId}`);
    if (editContainer) editContainer.remove();
    if (textEl) textEl.style.display = '';
}

function saveComment(commentId) {
    const input = document.getElementById(`comment-edit-input-${commentId}`);
    const textEl = document.getElementById(`comment-text-${commentId}`);
    if (!input || !textEl) return;

    const newText = input.value.trim();
    if (!newText) return;

    fetch('<?= base_url("post/edit_comment"); ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: getCsrfField() + '&id_comment=' + commentId + '&content=' + encodeURIComponent(newText)
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            textEl.innerText = newText;
            cancelEditComment(commentId);
            showCommentToast('Komentar berhasil diedit', 'emerald');
        } else {
            alert(data.message);
        }
    })
    .catch(err => {
        console.error('Error:', err);
        alert('Terjadi kesalahan. Silakan coba lagi.');
    });
}

function deleteComment(commentId) {
    if (!confirm('Apakah kamu yakin ingin menghapus komentar ini?')) return;

    fetch('<?= base_url("post/delete_comment"); ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: getCsrfField() + '&id_comment=' + commentId
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            // Hapus elemen komentar + thread (jika main comment) atau hanya reply
            const thread = document.getElementById(`comment-thread-${commentId}`);
            if (thread) {
                thread.style.transition = 'all 0.3s';
                thread.style.opacity = '0';
                setTimeout(() => thread.remove(), 300);
            } else {
                // Cari reply dengan id comment-text-{id}
                const replyEl = document.getElementById(`comment-text-${commentId}`);
                if (replyEl) {
                    const replyContainer = replyEl.closest('.group\\/reply') || replyEl.parentElement?.parentElement;
                    if (replyContainer) {
                        replyContainer.style.transition = 'all 0.3s';
                        replyContainer.style.opacity = '0';
                        setTimeout(() => replyContainer.remove(), 300);
                    }
                }
            }
            showCommentToast('Komentar berhasil dihapus', 'emerald');
        } else {
            alert(data.message);
        }
    })
    .catch(err => {
        console.error('Error:', err);
        alert('Terjadi kesalahan. Silakan coba lagi.');
    });
}

function showCommentToast(message, color) {
    const toast = document.createElement('div');
    const bgColor = color === 'red' ? 'bg-red-600' : 'bg-emerald-600';
    toast.className = `fixed bottom-20 left-1/2 -translate-x-1/2 z-[9999] ${bgColor} text-white text-xs font-semibold px-4 py-2.5 rounded-xl shadow-lg`;
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}

function escapeHtml(text) {
    return text
        .replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;").replace(/'/g, "&#039;");
}

<?php $images_arr = !empty($post['file_url']) ? array_map('trim', explode(',', $post['file_url'])) : []; ?>
const postImages = <?php echo json_encode($images_arr); ?>;
let currentImageIndex = 0;

function openLightbox(index) {
    if (postImages.length === 0) return;
    currentImageIndex = index;
    updateLightboxContent();
    document.getElementById('lightbox-modal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeLightbox() {
    document.getElementById('lightbox-modal').classList.add('hidden');
    document.body.style.overflow = '';
}

function changeImage(direction) {
    currentImageIndex += direction;
    if (currentImageIndex >= postImages.length) currentImageIndex = 0;
    else if (currentImageIndex < 0) currentImageIndex = postImages.length - 1;
    updateLightboxContent();
}

function updateLightboxContent() {
    const activeImg = document.getElementById('lightbox-active-img');
    const counter = document.getElementById('lightbox-counter');
    const prevBtn = document.getElementById('lightbox-prev-btn');
    const nextBtn = document.getElementById('lightbox-next-btn');
    
    activeImg.src = postImages[currentImageIndex];
    counter.innerText = `${currentImageIndex + 1} / ${postImages.length}`;
    
    if (postImages.length <= 1) {
        prevBtn.classList.add('hidden');
        nextBtn.classList.add('hidden');
    } else {
        prevBtn.classList.remove('hidden');
        nextBtn.classList.remove('hidden');
    }
}

</script>