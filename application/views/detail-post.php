<div class="space-y-4 max-w-2xl mx-auto">
    <?php 
        $is_liked = isset($post['is_liked']) && $post['is_liked'] == true; 
        $like_btn_class = $is_liked ? 'c-primary' : '';
        $like_icon_class = $is_liked ? 'c-primary' : '';
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
    
    <article class="card overflow-hidden group transition relative" data-post-id="<?= $post['id_post']; ?>" data-user-id="<?= $post['user_id']; ?>">
        <div class="p-4 sm:p-5 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="relative" style="width:36px;height:36px;display:flex;align-items:center;justify-content:center;" class="select-none z-20">
                    <div class="w-full h-full rounded-full overflow-hidden" style="background:var(--bg-surface-raised)">
                        <a href="<?= base_url('user/' . $post_username_url); ?>">
                            <img src="<?= $post_avatar_attr; ?>" alt="User" class="w-full h-full rounded-full" style="object-fit:cover">
                        </a>
                    </div>
                    <?php if (!empty($post['border'])): ?>
                        <div class="absolute inset-0 w-full h-full" style="pointer-events:none;transform:scale(1.25);transform-origin:center">
                            <img src="<?= $post_border_attr; ?>" alt="F1 Border Decoration" class="w-full h-full" style="object-fit:contain">
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($post['is_online'])): ?>
                        <div class="online-indicator"></div>
                    <?php endif; ?>
                </div>
                
                <div class="flex flex-col justify-center">
                    <div class="flex items-center gap-2">
                        <a href="<?= base_url('user/' . $post_username_url); ?>" class="font-semibold text-xs sm:text-sm cursor-pointer transition-colors relative z-20" onmouseover="this.style.color='var(--color-primary)'" onmouseout="this.style.color=''"><?= htmlspecialchars($post['username'], ENT_QUOTES, 'UTF-8'); ?></a>
                        <?php if (!empty($post['team_name'])): ?>
                            <span class="inline-flex items-center gap-1 font-semibold rounded-full" style="font-size:9px;padding:2px 6px;background:<?= $post_team_color_attr ?>15;border:1px solid var(--border-subtle);text-transform:uppercase;letter-spacing:0.06em">
                                <img src="<?= $post_team_logo_attr ?>" alt="<?= htmlspecialchars($post['team_name']) ?>" class="w-3 h-3" style="object-fit:contain">
                                <?= htmlspecialchars($post['team_name']) ?>
                            </span>
                        <?php endif; ?>
                        <span class="c-faint" style="font-size:10px">•</span>
                        <span class="inline-flex items-center c-white rounded-full" style="font-size:8px;padding:2px 6px;font-weight:600;background:rgba(255,255,255,0.04);border:1px solid var(--border-strong);text-transform:uppercase;letter-spacing:0.06em"><?= $post_category_html; ?></span>
                    </div>
                    <span class="c-subtle" style="font-size:10px;margin-top:2px"><?= $post_created_at_attr; ?></span>
                </div>
            </div>

            <div class="relative z-30 flex items-center">
                <button onclick="toggleDropdown(event, 'post-<?= $post['id_post']; ?>')" class="c-muted transition-colors p-1 rounded-md" onmouseover="this.style.color='var(--text-secondary)';this.style.background='rgba(255,255,255,0.05)'" onmouseout="this.style.color='';this.style.background=''">
                    <i data-lucide="more-horizontal" class="w-4 h-4"></i>
                </button>
                <div id="dropdown-post-<?= $post['id_post']; ?>" class="hidden absolute right-0 top-8 w-36 rounded-lg shadow-xl overflow-hidden py-1 text-xs c-secondary" style="background:var(--bg-surface);border:1px solid var(--border-subtle);backdrop-filter:blur(12px)">
                    <button onclick="copyPostLink(event, '<?= base_url('post/' . $post_username_url . '/' . $post['id_post']); ?>', this)" class="w-full text-left px-3 py-2 flex items-center gap-2 transition-colors c-secondary" onmouseover="this.style.background='rgba(255,255,255,0.05)';this.style.color='var(--text-primary)'" onmouseout="this.style.background='';this.style.color=''">
                        <i data-lucide="link" class="w-3.5 h-3.5"></i>
                        <span>Copy Link</span>
                    </button>
                    <?php if (isset($current_user_id) && (string)$current_user_id === (string)$post['user_id']): ?>
                        <a href="<?= base_url('post/edit/' . $post['id_post']); ?>" class="w-full text-left px-3 py-2 flex items-center gap-2 transition-colors c-secondary" style="border-top:1px solid var(--border-subtle)" onmouseover="this.style.background='rgba(255,255,255,0.05)';this.style.color='var(--text-primary)'" onmouseout="this.style.background='';this.style.color=''">
                            <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                            <span>Edit</span>
                        </a>
                        <button onclick="event.stopPropagation(); deletePost(<?= $post['id_post']; ?>)" class="w-full text-left px-3 py-2 flex items-center gap-2 transition-colors c-primary" style="border-top:1px solid var(--border-subtle)" onmouseover="this.style.background='var(--color-primary-bg)'" onmouseout="this.style.background=''">
                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                            <span>Hapus</span>
                        </button>
                    <?php else: ?>
                        <button onclick="event.stopPropagation(); openReportPost(<?= $post['id_post']; ?>)" class="w-full text-left px-3 py-2 flex items-center gap-2 transition-colors c-primary" style="border-top:1px solid var(--border-subtle)" onmouseover="this.style.background='var(--color-primary-bg)'" onmouseout="this.style.background=''">
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
                if ($total_images === 1) { $grid_style = 'grid-template-columns:1fr;aspect-ratio:4/3'; }
                elseif ($total_images === 2) { $grid_style = 'grid-template-columns:1fr 1fr;aspect-ratio:4/3;gap:4px'; }
                elseif ($total_images === 3) { $grid_style = 'grid-template-columns:1fr 1fr;aspect-ratio:4/3;gap:4px'; }
                else { $grid_style = 'grid-template-columns:1fr 1fr;grid-template-rows:1fr 1fr;aspect-ratio:4/3;gap:4px'; }
                $images_to_show = array_slice($images, 0, 4);
            ?>
            <div class="px-4 sm:px-5 relative z-20" style="margin-bottom:4px"> 
                <div class="grid rounded-lg overflow-hidden" style="<?= $grid_style ?>;background:var(--bg-surface);border:1px solid var(--border-subtle)">
                    <?php foreach ($images_to_show as $index => $img_url): ?>
                        <?php $item_style = ($total_images === 3 && $index === 0) ? 'grid-row:span 2;height:100%' : 'height:100%'; ?>
                        <div class="relative w-full overflow-hidden" style="<?= $item_style ?>;background:var(--bg-surface)">
                            <img src="<?= htmlspecialchars(trim($img_url), ENT_QUOTES, 'UTF-8'); ?>" 
                                 alt="Post Media" loading="lazy"
                                 class="w-full h-full cursor-pointer transition-transform duration-300"
                                 style="object-fit:cover"
                                 onmouseover="this.style.transform='scale(1.02)'" onmouseout="this.style.transform=''"
                                 onclick="openLightbox(<?= $index; ?>)">
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="p-4 sm:p-5 space-y-3" style="padding-top:8px">
            <p class="text-xs sm:text-sm c-secondary leading-relaxed">
                <?= htmlspecialchars($post['content'], ENT_QUOTES, 'UTF-8'); ?>
            </p>
            
            <div class="flex items-center gap-4 c-muted text-[11px] sm:text-xs relative z-20" style="padding-top:8px;border-top:1px solid var(--border-subtle)">
                <button onclick="toggleLike(event, <?= $post['id_post']; ?>, this)" class="flex items-center gap-1.5 transition-colors group/btn <?= $like_btn_class; ?>" onmouseover="if(!this.classList.contains('c-primary'))this.style.color='var(--color-primary)'" onmouseout="if(!this.classList.contains('c-primary'))this.style.color=''">
                    <i data-lucide="heart" class="w-4 h-4 transition-transform <?= $like_icon_class; ?>" style="<?=$is_liked ? 'fill:var(--color-primary)' : ''?>"></i>
                    <span class="font-semibold count-likes"><?= $post['likes_count']; ?></span>
                </button>
                <div class="flex items-center gap-1.5 c-muted">
                    <i data-lucide="message-square" class="w-4 h-4"></i>
                    <span class="font-semibold" id="comment-count-header"><?= count($comments); ?></span>
                </div>
            </div>
        </div>
    </article>

    <div class="card p-4 flex gap-3 items-start">
        <div class="relative shrink-0" style="width:36px;height:36px" data-user-id="<?= $current_user_id; ?>">
            <div class="w-full h-full rounded-full overflow-hidden" style="background:var(--bg-surface-raised)">
                <img src="<?= $current_user_avatar; ?>" alt="My Avatar" class="w-full h-full" style="object-fit:cover">
            </div>
            <div class="online-indicator"></div>
        </div>
        <div class="flex-1 space-y-2">
            <div id="reply-target-badge" class="hidden flex items-center justify-between rounded-md c-muted" style="background:rgba(255,255,255,0.03);border:1px solid var(--border-default);padding:4px 10px;font-size:10px">
                <span>Membalas <strong class="c-primary" id="reply-username">@username</strong></span>
                <button onclick="cancelReplyMode()" class="c-muted transition-colors">
                    <i data-lucide="x" class="w-3 h-3"></i>
                </button>
            </div>

            <form id="comment-form" onsubmit="submitComment(event, <?= $post['id_post']; ?>)">
                <input type="hidden" id="parent-comment-id" value="0">
                <textarea 
                    id="comment-input"
                    rows="2" 
                    placeholder="Balas postingan <?= htmlspecialchars($post['username'], ENT_QUOTES, 'UTF-8'); ?>..." 
                    class="w-full bg-transparent text-xs sm:text-sm c-secondary resize-none pb-2 transition-colors"
                    style="border-bottom:1px solid var(--border-subtle);outline:none"
                    required
                ></textarea>
                <div class="flex justify-end" style="padding-top:4px">
                    <button type="submit" class="btn btn-primary btn-sm">
                        Reply
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card p-4 sm:p-5 space-y-4">
        <div class="flex items-center justify-between pb-3" style="border-bottom:1px solid var(--border-default)">
            <h3 class="text-xs font-bold c-muted" style="text-transform:uppercase;letter-spacing:0.06em">Komentar</h3>
            <span class="c-subtle font-medium rounded-full" style="font-size:10px;padding:2px 8px;background:rgba(255,255,255,0.03);border:1px solid var(--border-default)" id="comment-count-badge"><?= count($comments); ?> Respon</span>
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
                        $m_like_btn_class = $m_liked ? 'c-primary' : '';
                        $m_like_icon_class = $m_liked ? 'c-primary' : '';
                        $has_replies = isset($replies[$main_id]);
                        $reply_count = $has_replies ? count($replies[$main_id]) : 0;
                        $m_username_attr = htmlspecialchars(addslashes($main_comment['username']), ENT_QUOTES, 'UTF-8');
                        $m_username_html = htmlspecialchars($main_comment['username'], ENT_QUOTES, 'UTF-8');
                        $m_avatar_attr = htmlspecialchars($main_comment['avatar'], ENT_QUOTES, 'UTF-8');
                        $m_created_at_attr = htmlspecialchars($main_comment['created_at'], ENT_QUOTES, 'UTF-8');
                    ?>
                    
                    <div class="space-y-2" id="comment-thread-<?= $main_id; ?>">
                        
                        <div class="rounded-xl p-4 flex gap-3 items-start transition-all duration-300 relative group/comment" style="background:rgba(255,255,255,0.01);border:1px solid var(--border-subtle)" onmouseover="this.style.background='rgba(255,255,255,0.02)'" onmouseout="this.style.background='rgba(255,255,255,0.01)'">
                            <div class="relative shrink-0" style="width:32px;height:32px" data-user-id="<?= $main_comment['user_id']; ?>">
                                <div class="w-full h-full rounded-full overflow-hidden" style="background:var(--bg-surface-raised)">
                                    <img src="<?= $m_avatar_attr; ?>" alt="User Avatar" class="w-full h-full" style="object-fit:cover">
                                </div>
                                <?php if (!empty($main_comment['is_online'])): ?>
                                    <div class="online-indicator"></div>
                                <?php endif; ?>
                            </div>
                            <div class="flex-1 min-w-0 space-y-1">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="font-semibold text-xs c-secondary"><?= $m_username_html; ?></span>
                                        <span class="c-faint" style="font-size:10px">•</span>
                                        <span class="c-subtle" style="font-size:10px"><?= $m_created_at_attr; ?></span>
                                    </div>
                                    
                                    <div class="relative z-30 invisible group-hover/comment:visible transition-all">
                                        <button onclick="toggleDropdown(event, 'comment-<?= $main_id; ?>')" class="c-muted p-0.5 rounded" onmouseover="this.style.color='var(--text-secondary)';this.style.background='rgba(255,255,255,0.05)'" onmouseout="this.style.color='';this.style.background=''">
                                            <i data-lucide="more-horizontal" class="w-3.5 h-3.5"></i>
                                        </button>
                                        <div id="dropdown-comment-<?= $main_id; ?>" class="hidden absolute right-0 top-6 w-32 rounded-md shadow-xl overflow-hidden py-1 c-secondary" style="font-size:11px;background:var(--bg-surface);border:1px solid var(--border-subtle)">
                                            <?php if (isset($current_user_id) && $current_user_id === (string)$main_comment['user_id']): ?>
                                                <button onclick="event.stopPropagation(); editComment(<?= $main_id; ?>)" class="block w-full text-left px-3 py-1.5 flex items-center gap-1.5 transition-colors" onmouseover="this.style.background='rgba(255,255,255,0.05)';this.style.color='var(--text-primary)'" onmouseout="this.style.background='';this.style.color=''">
                                                    <i data-lucide="pencil" class="w-3 h-3"></i>
                                                    <span>Edit</span>
                                                </button>
                                                <button onclick="event.stopPropagation(); deleteComment(<?= $main_id; ?>)" class="block w-full text-left px-3 py-1.5 flex items-center gap-1.5 transition-colors c-primary" style="border-top:1px solid var(--border-subtle)" onmouseover="this.style.background='var(--color-primary-bg)'" onmouseout="this.style.background=''">
                                                    <i data-lucide="trash-2" class="w-3 h-3"></i>
                                                    <span>Hapus</span>
                                                </button>
                                            <?php else: ?>
                                                <button onclick="event.stopPropagation(); openReportComment(<?= $main_id; ?>)" class="block w-full text-left px-3 py-1.5 flex items-center gap-1.5 transition-colors c-primary" style="border-top:1px solid var(--border-subtle)" onmouseover="this.style.background='var(--color-primary-bg)'" onmouseout="this.style.background=''">
                                                    <i data-lucide="flag" class="w-3 h-3"></i>
                                                    <span>Laporkan</span>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <p id="comment-text-<?= $main_id; ?>" class="text-xs sm:text-sm c-secondary leading-relaxed" style="padding-top:2px">
                                    <?= htmlspecialchars($main_comment['comment_text'], ENT_QUOTES, 'UTF-8'); ?>
                                </p>
                                <div class="flex items-center gap-4 c-subtle text-[10px] sm:text-xs" style="padding-top:8px;border-top:1px solid rgba(255,255,255,0.02)">
                                    <button onclick="toggleLikeComment(event, <?= $main_id; ?>, this)" class="flex items-center gap-1 transition-colors <?= $m_like_btn_class; ?>" onmouseover="if(!this.classList.contains('c-primary'))this.style.color='var(--color-primary)'" onmouseout="if(!this.classList.contains('c-primary'))this.style.color=''">
                                        <i data-lucide="heart" class="w-3.5 h-3.5 <?= $m_like_icon_class; ?>" style="<?=$m_liked ? 'fill:var(--color-primary)' : ''?>"></i>
                                        <span class="font-medium count-comment-likes"><?= $main_comment['likes_count'] ?? 0; ?></span>
                                    </button>
                                    <button onclick="setReplyTarget('<?= $m_username_attr; ?>', <?= $main_id; ?>)" class="flex items-center gap-1 transition-colors" onmouseover="this.style.color='var(--color-info)'" onmouseout="this.style.color=''">
                                        <i data-lucide="corner-up-left" class="w-3.5 h-3.5"></i>
                                        <span class="font-medium">Reply</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <?php if ($has_replies): ?>
                            <div style="padding-left:24px;padding-left:40px;margin-left:16px;margin-left:20px">
                                <button onclick="toggleReplies(<?= $main_id; ?>)" id="btn-toggle-replies-<?= $main_id; ?>" class="flex items-center gap-1.5 font-medium transition-colors py-1" style="font-size:11px" onmouseover="this.style.color='var(--color-primary)'" onmouseout="this.style.color='var(--color-primary)'">
                                    <i data-lucide="chevron-down" class="w-3.5 h-3.5 icon-toggle"></i>
                                    <span>Lihat Balasan (<?= $reply_count; ?>)</span>
                                </button>
                            </div>
                        <?php endif; ?>

                        <div id="replies-wrapper-<?= $main_id; ?>" class="<?= $has_replies ? 'hidden' : ''; ?> replies-container space-y-2" style="padding-left:24px;padding-left:40px;border-left:1px solid rgba(255,255,255,0.06);margin-left:16px;margin-left:20px">
                            <?php if ($has_replies): ?>
                                <?php foreach ($replies[$main_id] as $reply): ?>
                                    <?php 
                                        $r_liked = isset($reply['is_liked_comment']) && $reply['is_liked_comment'] == true;
                                        $r_like_btn_class = $r_liked ? 'c-primary' : '';
                                        $r_like_icon_class = $r_liked ? 'c-primary' : '';
                                        $reply_id = $reply['id_comment'] ?? 0;
                                        $r_username_attr = htmlspecialchars(addslashes($reply['username']), ENT_QUOTES, 'UTF-8');
                                        $r_username_html = htmlspecialchars($reply['username'], ENT_QUOTES, 'UTF-8');
                                        $r_avatar_attr = htmlspecialchars($reply['avatar'], ENT_QUOTES, 'UTF-8');
                                        $r_created_at_attr = htmlspecialchars($reply['created_at'], ENT_QUOTES, 'UTF-8');
                                        $r_parent_username_html = htmlspecialchars($reply['parent_username'] ?? '', ENT_QUOTES, 'UTF-8');
                                    ?>
                                                    <div class="rounded-xl p-3.5 flex gap-3 items-start transition-all duration-300 relative group/reply" style="background:rgba(255,255,255,0.005);border:1px solid rgba(255,255,255,0.02)">
                                                        <div class="relative shrink-0" style="width:28px;height:28px" data-user-id="<?= $reply['user_id']; ?>">
                                                            <div class="w-full h-full rounded-full overflow-hidden" style="background:var(--bg-surface-raised)">
                                                                <img src="<?= $r_avatar_attr; ?>" alt="User Avatar" class="w-full h-full" style="object-fit:cover">
                                                            </div>
                                                            <?php if (!empty($reply['is_online'])): ?>
                                                                <div class="online-indicator"></div>
                                                            <?php endif; ?>
                                                        </div>
                                                        <div class="flex-1 min-w-0 space-y-1">
                                                            <div class="flex items-center justify-between">
                                                                <div class="flex items-center gap-2">
                                                                    <span class="font-semibold text-xs c-secondary"><?= $r_username_html; ?></span>
                                                                    <span class="c-faint" style="font-size:10px">•</span>
                                                                    <span class="c-subtle" style="font-size:10px"><?= $r_created_at_attr; ?></span>
                                                                </div>
                                                                
                                                                <div class="relative z-30 invisible group-hover/reply:visible transition-all">
                                                                    <button onclick="toggleDropdown(event, 'reply-<?= $reply_id; ?>')" class="c-muted p-0.5 rounded" onmouseover="this.style.color='var(--text-secondary)';this.style.background='rgba(255,255,255,0.05)'" onmouseout="this.style.color='';this.style.background=''">
                                                                        <i data-lucide="more-horizontal" class="w-3.5 h-3.5"></i>
                                                                    </button>
                                                                    <div id="dropdown-reply-<?= $reply_id; ?>" class="hidden absolute right-0 top-6 w-32 rounded-md shadow-xl overflow-hidden py-1 c-secondary" style="font-size:11px;background:var(--bg-surface);border:1px solid var(--border-subtle)">
                                                                        <?php if (isset($current_user_id) && (string)$current_user_id === (string)$reply['user_id']): ?>
                                                                            <button onclick="event.stopPropagation(); editComment(<?= $reply_id; ?>)" class="block w-full text-left px-3 py-1.5 flex items-center gap-1.5 transition-colors" onmouseover="this.style.background='rgba(255,255,255,0.05)';this.style.color='var(--text-primary)'" onmouseout="this.style.background='';this.style.color=''">
                                                                                <i data-lucide="pencil" class="w-3 h-3"></i>
                                                                                <span>Edit</span>
                                                                            </button>
                                                                            <button onclick="event.stopPropagation(); deleteComment(<?= $reply_id; ?>)" class="block w-full text-left px-3 py-1.5 flex items-center gap-1.5 transition-colors c-primary" style="border-top:1px solid var(--border-subtle)" onmouseover="this.style.background='var(--color-primary-bg)'" onmouseout="this.style.background=''">
                                                                                <i data-lucide="trash-2" class="w-3 h-3"></i>
                                                                                <span>Hapus</span>
                                                                            </button>
                                                                        <?php else: ?>
                                                                            <button onclick="event.stopPropagation(); openReportComment(<?= $reply_id; ?>)" class="block w-full text-left px-3 py-1.5 flex items-center gap-1.5 transition-colors c-primary" style="border-top:1px solid var(--border-subtle)" onmouseover="this.style.background='var(--color-primary-bg)'" onmouseout="this.style.background=''">
                                                                                <i data-lucide="flag" class="w-3 h-3"></i>
                                                                                <span>Laporkan</span>
                                                                            </button>
                                                                        <?php endif; ?>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <p class="c-subtle" style="font-size:10px">Membalas <span class="c-info">@<?= $r_parent_username_html; ?></span></p>
                                                            <p id="comment-text-<?= $reply_id; ?>" class="text-xs c-secondary leading-relaxed" style="padding-top:2px">
                                                                <?= htmlspecialchars($reply['comment_text'], ENT_QUOTES, 'UTF-8'); ?>
                                                            </p>
                                            <div class="flex items-center gap-4 c-subtle" style="font-size:10px;padding-top:6px;border-top:1px solid rgba(255,255,255,0.01)">
                                                <button onclick="toggleLikeComment(event, <?= $reply_id; ?>, this)" class="flex items-center gap-1 transition-colors <?= $r_like_btn_class; ?>" onmouseover="if(!this.classList.contains('c-primary'))this.style.color='var(--color-primary)'" onmouseout="if(!this.classList.contains('c-primary'))this.style.color=''">
                                                    <i data-lucide="heart" class="w-3.5 h-3.5 <?= $r_like_icon_class; ?>" style="<?=$r_liked ? 'fill:var(--color-primary)' : ''?>"></i>
                                                    <span class="font-medium count-comment-likes"><?= $reply['likes_count'] ?? 0; ?></span>
                                                </button>
                                                <button onclick="setReplyTarget('<?= $r_username_attr; ?>', <?= $main_id; ?>)" class="flex items-center gap-1 transition-colors" onmouseover="this.style.color='var(--color-info)'" onmouseout="this.style.color=''">
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
                <div id="no-comment-placeholder" class="p-6 text-center c-subtle text-xs" style="letter-spacing:0.04em">
                    Belum ada komentar. Jadilah yang pertama membalas!
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<div id="lightbox-modal" class="hidden fixed inset-0 flex flex-col justify-between p-4 select-none animate-fade-in" style="z-index:600;background:var(--bg-surface);backdrop-filter:blur(4px)">
    <div class="flex items-center justify-between c-white w-full mx-auto" style="max-width:72rem;height:48px">
        <span id="lightbox-counter" class="text-xs font-semibold c-muted" style="letter-spacing:0.04em">1 / 1</span>
        <button onclick="closeLightbox()" class="c-muted p-2 rounded-full transition-colors" onmouseover="this.style.color='var(--text-primary)';this.style.background='rgba(255,255,255,0.1)'" onmouseout="this.style.color='';this.style.background=''">
            <i data-lucide="x" class="w-6 h-6"></i>
        </button>
    </div>
    <div class="relative flex-1 flex items-center justify-center w-full mx-auto group" style="max-width:64rem">
        <button id="lightbox-prev-btn" onclick="changeImage(-1)" class="absolute z-50 c-white p-3 rounded-full transition-all opacity-0 group-hover:opacity-100" style="left:8px;left:16px;border:1px solid rgba(255,255,255,0.1);outline:none;background:rgba(0,0,0,0.4)" onmouseover="this.style.background='rgba(0,0,0,0.6)'" onmouseout="this.style.background='rgba(0,0,0,0.4)'">
            <i data-lucide="chevron-left" class="w-5 h-5"></i>
        </button>
        <div class="w-full h-full flex items-center justify-center p-2" style="max-height:75vh">
            <img id="lightbox-active-img" src="" alt="Lightbox Media" class="max-w-full max-h-full rounded shadow-xl animate-scale-up" style="object-fit:contain">
        </div>
        <button id="lightbox-next-btn" onclick="changeImage(1)" class="absolute z-50 c-white p-3 rounded-full transition-all opacity-0 group-hover:opacity-100" style="right:8px;right:16px;border:1px solid rgba(255,255,255,0.1);outline:none;background:rgba(0,0,0,0.4)" onmouseover="this.style.background='rgba(0,0,0,0.6)'" onmouseout="this.style.background='rgba(0,0,0,0.4)'">
            <i data-lucide="chevron-right" class="w-5 h-5"></i>
        </button>
    </div>
    <div style="height:32px"></div>
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
                targetReplyHTML = `<p class="c-subtle" style="font-size:10px">Membalas <span class="c-primary">${escapeHtml(targetName)}</span></p>`;
            }

            const commentHTML = `
                <div class="rounded-xl p-4 flex gap-3 items-start transition-all duration-300 animate-fade-in relative group/comment" style="background:rgba(255,255,255,0.01);border:1px solid var(--border-subtle)">
                    <div class="rounded-full overflow-hidden shrink-0" style="width:32px;height:32px;background:var(--bg-surface-raised)">
                        <img src="${escapeHtml(data.new_comment.avatar)}" alt="User Avatar" class="w-full h-full" style="object-fit:cover">
                    </div>
                    <div class="flex-1 min-w-0 space-y-1">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="font-semibold text-xs c-secondary">${escapeHtml(data.new_comment.username)}</span>
                                <span class="c-faint" style="font-size:10px">•</span>
                                <span class="c-subtle" style="font-size:10px">Baru saja</span>
                            </div>
                            <div class="relative z-30 invisible group-hover/comment:visible transition-all">
                                <button onclick="toggleDropdown(event, 'comment-${commentId}')" class="c-muted p-0.5 rounded" onmouseover="this.style.color='var(--text-secondary)';this.style.background='rgba(255,255,255,0.05)'" onmouseout="this.style.color='';this.style.background=''">
                                    <i data-lucide="more-horizontal" class="w-3.5 h-3.5"></i>
                                </button>
                                <div id="dropdown-comment-${commentId}" class="hidden absolute right-0 top-6 w-32 rounded-md shadow-xl overflow-hidden py-1 c-secondary" style="font-size:11px;background:var(--bg-surface);border:1px solid var(--border-subtle)">
                                    <button onclick="event.stopPropagation(); editComment(${commentId})" class="block w-full text-left px-3 py-1.5 flex items-center gap-1.5 transition-colors" onmouseover="this.style.background='rgba(255,255,255,0.05)';this.style.color='var(--text-primary)'" onmouseout="this.style.background='';this.style.color=''">
                                        <i data-lucide="pencil" class="w-3 h-3"></i>
                                        <span>Edit</span>
                                    </button>
                                    <button onclick="event.stopPropagation(); deleteComment(${commentId})" class="block w-full text-left px-3 py-1.5 flex items-center gap-1.5 transition-colors c-primary" style="border-top:1px solid var(--border-subtle)" onmouseover="this.style.background='var(--color-primary-bg)'" onmouseout="this.style.background=''">
                                        <i data-lucide="trash-2" class="w-3 h-3"></i>
                                        <span>Hapus</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        ${targetReplyHTML}
                        <p id="comment-text-${commentId}" class="text-xs sm:text-sm c-secondary leading-relaxed" style="padding-top:2px">
                            ${escapeHtml(commentText)}
                        </p>
                        <div class="flex items-center gap-4 c-subtle text-[10px] sm:text-xs" style="padding-top:8px;border-top:1px solid rgba(255,255,255,0.02)">
                            <button onclick="toggleLikeComment(event, ${commentId}, this)" class="flex items-center gap-1 transition-colors" onmouseover="this.style.color='var(--color-primary)'" onmouseout="this.style.color=''">
                                <i data-lucide="heart" class="w-3.5 h-3.5"></i>
                                <span class="font-medium count-comment-likes">0</span>
                            </button>
                            <button onclick="setReplyTarget('${escapeJsString(data.new_comment.username)}', ${parentId > 0 ? parentId : commentId})" class="flex items-center gap-1 transition-colors" onmouseover="this.style.color='var(--color-info)'" onmouseout="this.style.color=''">
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
                    buttonElement.classList.remove('c-subtle');
                    buttonElement.classList.add('c-primary');
                    icon.style.fill = 'var(--color-primary)';
                    icon.style.color = 'var(--color-primary)';
                } else {
                    buttonElement.classList.remove('c-primary');
                    buttonElement.classList.add('c-subtle');
                    icon.style.fill = '';
                    icon.style.color = '';
                }
                countSpan.innerText = data.likes_count;
            }
        })
        .catch(err => {
            console.error('Gagal memproses like komentar:', err);
            showToast('Gagal menyukai komentar. Coba lagi.', 'error');
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
                    buttonElement.classList.remove('c-subtle');
                    buttonElement.classList.add('c-primary');
                    icon.style.fill = 'var(--color-primary)';
                    icon.style.color = 'var(--color-primary)';
                } else {
                    buttonElement.classList.remove('c-primary');
                    buttonElement.classList.add('c-subtle');
                    icon.style.fill = '';
                    icon.style.color = '';
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
    editContainer.className = 'space-y-2';
    editContainer.style.paddingTop = '2px';
    editContainer.innerHTML = `
        <textarea id="comment-edit-input-${commentId}" class="w-full text-xs c-secondary rounded-lg resize-none" rows="2" style="background:var(--bg-surface-raised);padding:8px 12px;border:1px solid var(--border-strong);outline:none">${escapeHtml(currentText)}</textarea>
        <div class="flex gap-2 justify-end">
            <button onclick="cancelEditComment(${commentId})" class="text-[10px] px-3 py-1 rounded-lg c-secondary transition-colors" style="background:rgba(255,255,255,0.05)" onmouseover="this.style.background='rgba(255,255,255,0.08)'" onmouseout="this.style.background='rgba(255,255,255,0.05)'">Batal</button>
            <button onclick="saveComment(${commentId})" class="text-[10px] px-3 py-1 rounded-lg c-white transition-colors" style="background:var(--color-primary)" onmouseover="this.style.background='var(--color-primary)'" onmouseout="this.style.background='var(--color-primary)'">Simpan</button>
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
    const bgColor = color === 'red' ? 'var(--color-primary)' : 'var(--color-success)';
    toast.className = `fixed left-1/2 z-[9999] c-white text-xs font-semibold rounded-xl shadow-lg`;
    toast.style.cssText = `bottom:80px;transform:translateX(-50%);background:${bgColor};padding:10px 16px`;
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
