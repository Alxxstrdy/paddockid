<style>
    body {
        font-family: 'Plus Jakarta Sans', sans-serif;
        background-color: #05070c;
        background-image: radial-gradient(circle at 50% 0%, rgba(255, 24, 24, 0.03) 0%, transparent 40%);
    }
    .font-syne { font-family: 'Syne', sans-serif; }
    .glass-card {
        background: rgba(15, 22, 38, 0.4);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.04);
    }
</style>

<div class="flex-1 max-w-2xl w-full mx-auto px-4 py-6">

<?php if (!empty($user['is_blocked_by'])): ?>
<!-- BLOCKED PROFILE -->
<div class="glass-card rounded-2xl overflow-hidden shadow-2xl relative border border-white/[0.06] mb-8">
    <div class="h-36 sm:h-48 w-full relative bg-gradient-to-r from-red-950/40 to-slate-900 overflow-hidden">
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-red-900/20 via-slate-950 to-slate-950"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-[#05070c]/90 via-[#05070c]/30 to-transparent"></div>
    </div>
    <div class="px-5 pb-5 relative -mt-14 sm:-mt-16">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-4">
            <div class="relative w-24 h-24 sm:w-28 sm:h-28 flex-shrink-0 mx-auto sm:mx-0">
                <div class="w-full h-full rounded-full p-[2.5px] bg-slate-950 ring-2 ring-white/[0.08] overflow-hidden">
                    <img src="<?= assets_url('default.jpg'); ?>" alt="Avatar" class="w-full h-full object-cover rounded-full">
                </div>
            </div>
            <div class="text-center sm:text-right flex flex-col items-center sm:items-end gap-2">
                <button onclick="blockedFollowAlert()" class="text-[11px] font-semibold px-5 py-2 rounded-xl border bg-red-600 hover:bg-red-500 text-white border-red-600 shadow-lg shadow-red-600/10">Ikuti</button>
            </div>
        </div>
        <div class="text-center sm:text-left space-y-2">
            <div class="flex items-center justify-center sm:justify-start gap-2">
                <h2 class="font-syne text-lg sm:text-xl uppercase tracking-tight text-white"><?= htmlspecialchars($user['username'] ?? 'Pengguna'); ?></h2>
            </div>
            <p class="text-xs text-slate-400 -mt-1">@<?= htmlspecialchars($user['username']); ?></p>
            <div class="flex items-center justify-center sm:justify-start gap-5 text-xs pt-1">
                <span class="flex gap-1"><span class="font-bold text-white">--</span> <span class="text-slate-400">Following</span></span>
                <span class="flex gap-1"><span class="font-bold text-white">--</span> <span class="text-slate-400">Followers</span></span>
            </div>
        </div>
    </div>
</div>
</div>
</main>

<?php elseif ($is_banned): ?>
<!-- BANNED PROFILE -->
<div class="glass-card rounded-2xl overflow-hidden shadow-2xl relative border border-white/[0.06] mb-8">
    <div class="h-36 sm:h-48 w-full relative bg-gradient-to-r from-slate-950/80 to-slate-900 overflow-hidden">
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-slate-800/20 via-slate-950 to-slate-950"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-[#05070c]/90 via-[#05070c]/30 to-transparent"></div>
    </div>
    <div class="px-5 pb-5 relative -mt-14 sm:-mt-16">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-4">
            <div class="relative w-24 h-24 sm:w-28 sm:h-28 flex-shrink-0 mx-auto sm:mx-0">
                <div class="w-full h-full rounded-full p-[2.5px] bg-slate-950 ring-2 ring-white/[0.08] overflow-hidden">
                    <img src="<?= assets_url('default.jpg'); ?>" alt="Avatar" class="w-full h-full object-cover rounded-full">
                </div>
            </div>
            <div class="text-center sm:text-right flex flex-col items-center sm:items-end gap-2">
                <span class="text-[11px] font-semibold px-5 py-2 rounded-xl border bg-slate-600/10 text-slate-500 border-slate-600/20 cursor-not-allowed">Ikuti</span>
            </div>
        </div>
        <div class="text-center sm:text-left space-y-3">
            <div class="flex items-center justify-center sm:justify-start gap-2">
                <h2 class="font-syne text-lg sm:text-xl uppercase tracking-tight text-slate-500"><?= htmlspecialchars($user['username'] ?? 'Pengguna'); ?></h2>
            </div>
            <p class="text-xs text-slate-500 -mt-1">@<?= htmlspecialchars($user['username']); ?></p>
            <div class="flex items-center justify-center sm:justify-start gap-5 text-xs pt-1">
                <span class="flex gap-1"><span class="font-bold text-slate-500">--</span> <span class="text-slate-600">Following</span></span>
                <span class="flex gap-1"><span class="font-bold text-slate-500">--</span> <span class="text-slate-600">Followers</span></span>
            </div>
            <div class="flex items-center justify-center sm:justify-start gap-3 pt-4 border-t border-white/[0.03]">
                <div class="w-10 h-10 rounded-full bg-slate-800/50 flex items-center justify-center flex-shrink-0">
                    <i data-lucide="shield-off" class="w-5 h-5 text-slate-600"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-500">Akun telah ditangguhkan</p>
                    <p class="text-[10px] text-slate-600">Akun ini telah dinonaktifkan oleh administrator.</p>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
</main>

<?php else: ?>
<!-- NORMAL PROFILE -->
    
    <div class="glass-card rounded-2xl overflow-hidden shadow-2xl relative border border-white/[0.06] mb-8">
    
    <div class="h-36 sm:h-48 w-full relative bg-gradient-to-r from-red-950/40 to-slate-900 overflow-hidden">
        <?php if (!empty($user['banner'])): ?>
            <img src="<?= base_url($user['banner']); ?>" alt="User Banner" class="w-full h-full object-cover">
        <?php else: ?>
            <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-red-900/20 via-slate-950 to-slate-950"></div>
        <?php endif; ?>
        <div class="absolute inset-0 bg-gradient-to-t from-[#05070c]/90 via-[#05070c]/30 to-transparent"></div>
    </div>

    <div class="px-5 pb-5 relative -mt-14 sm:-mt-16">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-4">
            
            <div class="relative w-24 h-24 sm:w-28 sm:h-28 flex-shrink-0 mx-auto sm:mx-0" data-user-id="<?= $user['id_user']; ?>">
                <div class="w-full h-full rounded-full p-[2.5px] bg-slate-950 ring-2 ring-white/[0.08] overflow-hidden">
                    <img src="<?= avatar_url($user['avatar']); ?>" 
                         alt="Avatar" class="w-full h-full object-cover rounded-full"
                         onerror="this.src='<?= assets_url('default.jpg'); ?>';">
                </div>
                <?php if (!empty($user['border_image'])): ?>
                    <div class="absolute inset-0 w-full h-full pointer-events-none scale-[1.25] transform origin-center z-20">
                        <img src="<?= assets_url($user['border_image']); ?>" alt="F1 Border" class="w-full h-full object-contain">
                    </div>
                <?php endif; ?>
                <?php if (!empty($user['is_online'])): ?>
                    <div class="online-indicator"></div>
                <?php endif; ?>
            </div>

            <div class="text-center sm:text-right flex flex-col items-center sm:items-end gap-2">
                <?php if ($current_user_id && $current_user_id === $profile_user_id): ?>
                    <a href="<?= base_url('profile'); ?>" class="bg-white/[0.05] hover:bg-red-600 text-slate-200 hover:text-white text-[11px] font-semibold px-4 py-2 rounded-xl border border-white/[0.06] transition-all duration-300">
                        Ke Profil Saya
                    </a>
                <?php elseif ($current_user_id && !empty($user['is_blocked'])): ?>
                    <div class="flex items-center gap-2">
                        <button onclick="unblockUser()" class="text-[11px] font-semibold px-5 py-2 rounded-xl border border-red-600/30 bg-red-600/10 hover:bg-red-600/20 text-red-400 hover:text-red-300 transition-all duration-300">
                            <i data-lucide="ban" class="w-3.5 h-3.5 inline-block mr-1"></i> Buka Blokir
                        </button>
                        <div class="relative" id="user-menu-container">
                            <button onclick="toggleUserMenu()" class="bg-white/[0.05] hover:bg-white/[0.08] text-slate-400 hover:text-white text-[11px] p-2 rounded-xl border border-white/[0.06] transition-all duration-300">
                                <i data-lucide="ellipsis-vertical" class="w-4 h-4"></i>
                            </button>
                            <div id="user-dropdown-menu" class="absolute right-0 mt-2 w-44 bg-slate-900 border border-white/[0.06] rounded-xl shadow-2xl py-1.5 hidden z-50">
                                <button onclick="openUserReportModal()" class="flex items-center gap-2.5 w-full px-4 py-2.5 text-xs text-slate-300 hover:bg-white/[0.04] hover:text-white transition-colors text-left">
                                    <i data-lucide="flag" class="w-3.5 h-3.5 text-slate-500"></i>
                                    Laporkan Pengguna
                                </button>
                            </div>
                        </div>
                    </div>
                <?php elseif ($current_user_id): ?>
                    <div class="flex items-center gap-2">
                        <button id="follow-btn" onclick="toggleFollow()" class="text-[11px] font-semibold px-5 py-2 rounded-xl transition-all duration-300 border <?= $user['is_following'] ? 'bg-white/[0.05] hover:bg-red-600 text-slate-200 hover:text-white border-white/[0.06]' : 'bg-red-600 hover:bg-red-500 text-white border-red-600 shadow-lg shadow-red-600/10'; ?>">
                            <?= $user['is_following'] ? 'Mengikuti' : 'Ikuti'; ?>
                        </button>
                        <div class="relative" id="user-menu-container">
                            <button onclick="toggleUserMenu()" class="bg-white/[0.05] hover:bg-white/[0.08] text-slate-400 hover:text-white text-[11px] p-2 rounded-xl border border-white/[0.06] transition-all duration-300">
                                <i data-lucide="ellipsis-vertical" class="w-4 h-4"></i>
                            </button>
                            <div id="user-dropdown-menu" class="absolute right-0 mt-2 w-44 bg-slate-900 border border-white/[0.06] rounded-xl shadow-2xl py-1.5 hidden z-50">
                                <button onclick="openUserReportModal()" class="flex items-center gap-2.5 w-full px-4 py-2.5 text-xs text-slate-300 hover:bg-white/[0.04] hover:text-white transition-colors text-left">
                                    <i data-lucide="flag" class="w-3.5 h-3.5 text-slate-500"></i>
                                    Laporkan Pengguna
                                </button>
                                <button onclick="openBlockModal()" class="flex items-center gap-2.5 w-full px-4 py-2.5 text-xs text-red-400 hover:bg-white/[0.04] hover:text-red-300 transition-colors text-left">
                                    <i data-lucide="ban" class="w-3.5 h-3.5"></i>
                                    Blokir Pengguna
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            
        </div>

        <div class="text-center sm:text-left space-y-2">
            <div class="flex items-center justify-center sm:justify-start gap-2">
                <h2 class="font-syne text-lg sm:text-xl uppercase tracking-tight text-white">
                    <?= htmlspecialchars(!empty($user['display_name']) ? $user['display_name'] : $user['username'], ENT_QUOTES, 'UTF-8'); ?>
                </h2>
                <?php if ($user['verified'] == 1): ?>
                    <span class="text-red-500" title="Verified Driver"><i data-lucide="badge-check" class="w-4 h-4 fill-red-500/10"></i></span>
                <?php endif; ?>
            </div>
            <p class="text-xs text-slate-400 -mt-1">@<?= $user['username']; ?></p>
            
            <div class="flex items-center justify-center sm:justify-start gap-5 text-xs pt-1">
                <button onclick="openFollowModal('following')" class="hover:text-red-400 transition-colors duration-200 flex gap-1 cursor-pointer">
                    <span class="font-bold text-white"><?= number_format($user['total_following']); ?></span>
                    <span class="text-slate-400">Following</span>
                </button>
                <button onclick="openFollowModal('followers')" class="hover:text-red-400 transition-colors duration-200 flex gap-1 cursor-pointer">
                    <span class="font-bold text-white"><?= number_format($user['total_followers']); ?></span>
                    <span class="text-slate-400">Followers</span>
                </button>
            </div>
            
            <p class="text-xs sm:text-sm text-slate-300 leading-relaxed pt-3 border-t border-white/[0.03]">
                <?= !empty($user['bio']) ? nl2br(htmlentities($user['bio'])) : '<span class="text-slate-500 italic">Belum ada biografi yang ditulis.</span>'; ?>
            </p>

            <?php if (!empty($user['team_name'])): ?>
                <div class="flex items-center justify-center sm:justify-start gap-2 pt-2">
                    <span class="inline-flex items-center gap-1.5 text-[10px] font-semibold uppercase tracking-wider px-2 py-0.5 rounded-full border border-white/[0.08]" style="background:<?= $user['team_color'] ?? '#666' ?>15;">
                        <img src="<?= assets_url($user['team_logo']) ?>" alt="<?= htmlspecialchars($user['team_name']) ?>" class="w-4 h-4 object-contain">
                        <?= htmlspecialchars($user['team_name']) ?>
                    </span>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

    <div class="flex border-b border-white/[0.04] mb-6 text-xs sm:text-sm font-semibold text-slate-400">
        <div class="flex-1 py-3 text-center border-b-2 border-red-500 text-white transition-all duration-300 flex items-center justify-center gap-2">
            <i data-lucide="grid" class="w-4 h-4"></i> Postingan
        </div>
    </div>

    <div id="post-container" class="space-y-4">
    </div>

    <div id="loading-badge" class="py-8 text-center flex justify-center items-center hidden">
        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-slate-950 border border-white/[0.05]">
            <div class="w-3 h-3 border-2 border-red-500 border-t-transparent rounded-full animate-spin"></div>
            <span class="text-xs text-slate-400 font-medium">Memuat data paddock...</span>
        </div>
    </div>

</div>
</main>
<?php endif; ?>

<!-- REPORT USER MODAL -->
<div id="user-report-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeUserReportModal()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="glass-card rounded-2xl w-full max-w-md border border-white/[0.06] shadow-2xl p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-syne text-sm uppercase tracking-tight text-white">Laporkan Pengguna</h3>
                <button onclick="closeUserReportModal()" class="text-slate-400 hover:text-white transition-colors">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <form id="user-report-form" onsubmit="submitUserReport(event)">
                <div class="mb-4">
                    <label class="text-[10px] text-slate-500 uppercase tracking-wider mb-1.5 block">Alasan Laporan</label>
                    <textarea id="user-report-reason" rows="4" required
                        class="w-full bg-slate-800 text-xs sm:text-sm text-slate-200 placeholder-slate-500 focus:outline-none border border-white/[0.06] rounded-lg px-3 py-2.5 focus:border-red-500/50 transition-colors resize-none"
                        placeholder="Jelaskan alasan kamu melaporkan pengguna ini..."></textarea>
                </div>
                <div class="flex justify-end gap-3 pt-4 border-t border-white/[0.04]">
                    <button type="button" onclick="closeUserReportModal()" class="px-4 py-2.5 text-xs font-semibold text-slate-300 bg-white/[0.05] hover:bg-white/[0.08] rounded-xl transition-colors border border-white/[0.06]">
                        Batal
                    </button>
                    <button type="submit" id="user-report-submit-btn" class="px-4 py-2.5 text-xs font-semibold text-white bg-red-600 hover:bg-red-500 rounded-xl transition-colors shadow-lg shadow-red-600/10">
                        Kirim Laporan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- BLOCK USER CONFIRMATION MODAL -->
<div id="block-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeBlockModal()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="glass-card rounded-2xl w-full max-w-sm border border-white/[0.06] shadow-2xl p-6 text-center">
            <div class="w-12 h-12 mx-auto mb-4 rounded-full bg-red-500/10 flex items-center justify-center">
                <i data-lucide="ban" class="w-6 h-6 text-red-500"></i>
            </div>
            <h3 class="font-syne text-sm uppercase tracking-tight text-white mb-2">Blokir Pengguna</h3>
            <p class="text-xs text-slate-400 leading-relaxed mb-6">Apakah kamu yakin ingin memblokir <span class="text-white font-semibold">@<?= $user['username']; ?></span>? Pengguna yang diblokir tidak akan bisa melihat postinganmu atau mengikutimu.</p>
            <div class="flex gap-3">
                <button onclick="closeBlockModal()" class="flex-1 px-4 py-2.5 text-xs font-semibold text-slate-300 bg-white/[0.05] hover:bg-white/[0.08] rounded-xl transition-colors border border-white/[0.06]">
                    Batal
                </button>
                <button id="block-confirm-btn" onclick="submitBlock()" class="flex-1 px-4 py-2.5 text-xs font-semibold text-white bg-red-600 hover:bg-red-500 rounded-xl transition-colors shadow-lg shadow-red-600/10">
                    Blokir
                </button>
            </div>
        </div>
    </div>
</div>

<!-- FOLLOW MODAL -->
<div id="follow-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeFollowModal()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="glass-card rounded-2xl w-full max-w-sm max-h-[70vh] flex flex-col border border-white/[0.06] shadow-2xl">
            <div class="flex items-center justify-between px-5 py-4 border-b border-white/[0.04]">
                <h3 id="follow-modal-title" class="font-syne text-sm uppercase tracking-tight text-white">Following</h3>
                <button onclick="closeFollowModal()" class="text-slate-400 hover:text-white transition-colors">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
            <div id="follow-modal-body" class="overflow-y-auto no-scrollbar flex-1">
                <div class="flex items-center justify-center py-8">
                    <div class="w-5 h-5 border-2 border-red-500 border-t-transparent rounded-full animate-spin"></div>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
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

    const userId = '<?= $profile_user_id; ?>';

    function openFollowModal(type) {
        const modal = document.getElementById('follow-modal');
        const title = document.getElementById('follow-modal-title');
        const body = document.getElementById('follow-modal-body');

        title.textContent = type === 'following' ? 'Following' : 'Followers';
        body.innerHTML = `
            <div class="flex items-center justify-center py-8">
                <div class="w-5 h-5 border-2 border-red-500 border-t-transparent rounded-full animate-spin"></div>
            </div>
        `;
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';

        fetch(`<?= base_url('user/get_follows_ajax'); ?>?type=${type}&user_id=${userId}`)
            .then(r => {
                if (!r.ok) return r.json().then(e => { throw new Error(e.error || 'Server error'); });
                return r.json();
            })
            .then(data => {
                if (data.length === 0) {
                    body.innerHTML = `<div class="text-center py-10 text-slate-500 text-xs uppercase tracking-wider">Tidak ada ${type}</div>`;
                    return;
                }
                body.innerHTML = data.map(user => {
                    const borderClass = 'w-full h-full';
                    const borderHTML = user.border_image
                        ? `<div class="absolute inset-0 w-full h-full pointer-events-none scale-[1.25] transform origin-center">
                               <img src="${escapeHtml(user.border_image)}" alt="" class="w-full h-full object-contain">
                           </div>`
                        : '';
                    const verifiedHTML = user.verified == 1
                        ? `<span class="text-red-500 inline-flex"><i data-lucide="badge-check" class="w-3 h-3 fill-red-500/10"></i></span>`
                        : '';
                    const onlineHTML = user.is_online ? '<div class="online-indicator"></div>' : '';
                    const followUsername = escapeHtml(user.username);
                    const followUserUrl = encodeURIComponent(user.username);
                    const followAvatar = escapeHtml(user.avatar);
                    const followDisplayName = escapeHtml(user.display_name || user.username);
                    return `
                        <a href="<?= base_url('user/'); ?>${followUserUrl}" class="flex items-center gap-3 px-5 py-3 hover:bg-white/[0.02] transition-colors border-b border-white/[0.02] last:border-0">
                            <div class="relative w-9 h-9 flex items-center justify-center flex-shrink-0">
                                <div class="${borderClass} rounded-full overflow-hidden bg-slate-800">
                                    <img src="${followAvatar}" alt="" class="w-full h-full object-cover rounded-full" onerror="this.src='<?= assets_url('default.jpg'); ?>';">
                                </div>
                                ${borderHTML}
                                ${onlineHTML}
                            </div>
                            <div class="flex flex-col min-w-0">
                                <div class="flex items-center gap-1.5">
                                    <span class="font-semibold text-xs text-white truncate">${followDisplayName}</span>
                                    ${verifiedHTML}
                                </div>
                                <span class="text-[10px] text-slate-500 truncate">@${followUsername}</span>
                            </div>
                        </a>
                    `;
                }).join('');
                if (typeof lucide !== 'undefined') lucide.createIcons();
            })
            .catch(err => {
                body.innerHTML = `<div class="text-center py-10 text-red-400 text-xs">${err.message || 'Gagal memuat data'}</div>`;
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
        
        const url = `<?= base_url('user/get_user_posts_ajax'); ?>?user_id=${userId}&offset=${offset}&limit=${limit}`;
        
        fetch(url)
            .then(r => {
                if (!r.ok) return r.json().then(e => { throw new Error(e.error || 'Server error'); });
                return r.json();
            })
            .then(data => {
                const container = document.getElementById('post-container');

                if (data.length === 0) {
                    if (offset === 0) {
                        container.innerHTML = '<div class="glass-card p-8 text-center text-slate-500 text-xs">Belum ada postingan.</div>';
                    }
                    hasMoreData = false;
                    isLoading = false;
                    loadingBadge.classList.add('hidden');
                    return;
                }
                
                data.forEach(post => {
                    const avatarClass = 'w-full h-full';
                    const avatarBorderHTML = post.border 
                        ? `<div class="absolute inset-0 w-full h-full pointer-events-none scale-[1.25] transform origin-center">
                            <img src="${escapeHtml(post.border)}" alt="F1 Border Decoration" class="w-full h-full object-contain">
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
                                    <img src="${escapeHtml(url)}" alt="Post Media" loading="lazy" class="w-full h-full object-cover">
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

                    const dynamicLikeBtnClass = post.is_liked ? 'text-red-500' : 'hover:text-red-500';
                    const dynamicLikeIconClass = post.is_liked ? 'fill-red-500 text-red-500' : '';
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

                    const cardHTML = `
                        <article class="glass-card rounded-xl overflow-hidden group transition-all duration-300 relative hover:bg-white/[0.02]" data-post-id="${post.id_post}" data-user-id="${post.user_id}">
                            <a href="<?= base_url('post/'); ?>${userUrl}/${post.id_post}" class="absolute inset-0 z-10"></a>
                            
                            <div class="p-4 sm:p-5 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="relative w-9 h-9 flex items-center justify-center select-none z-20">
                                        <div class="${avatarClass} rounded-full overflow-hidden bg-slate-800">
                                            <a href="<?= base_url('user/'); ?>${userUrl}">
                                                <img src="${escapedAvatar}" alt="User" class="w-full h-full object-cover rounded-full">
                                            </a>
                                        </div>
                                        ${avatarBorderHTML}
                                        ${onlineHTML}
                                    </div>
                                    
                                    <div class="flex flex-col justify-center">
                                        <div class="flex items-center gap-2">
                                            <a href="<?= base_url('user/'); ?>${userUrl}" class="font-semibold text-xs sm:text-sm hover:text-red-400 cursor-pointer transition-colors relative z-20">${escapedUsername}</a>
                                            ${post.team_name ? '<span class="inline-flex items-center gap-1 text-[9px] font-semibold uppercase tracking-wider px-1.5 py-0.5 rounded-full border border-white/[0.08]" style="background:' + escapedTeamColor + '15;"><img src="<?= base_url(''); ?>' + escapedTeamLogo + '" alt="' + escapedTeamName + '" class="w-3 h-3 object-contain"> ' + escapedTeamName + '</span>' : ''}
                                            <span class="text-slate-600 text-[10px]">•</span>
                                            <span class="inline-flex items-center text-[8px] px-1.5 py-0.5 font-semibold text-white bg-white/[0.04] border border-white/[0.06] rounded-full uppercase tracking-wider">${escapedCategory}</span>
                                        </div>
                                        <span class="text-[10px] text-slate-500 mt-0.5">${escapedCreatedAt}</span>
                                    </div>
                                </div>
                                
                                <div class="relative z-30 flex items-center">
                                    <button onclick="toggleDropdown(event, ${post.id_post})" class="text-slate-500 hover:text-slate-300 transition-colors p-1 rounded-md hover:bg-white/[0.05]">
                                        <i data-lucide="more-horizontal" class="w-4 h-4"></i>
                                    </button>
                                    <div id="dropdown-${post.id_post}" class="hidden absolute right-0 top-8 w-36 bg-slate-900/95 backdrop-blur-md border border-white/[0.08] rounded-lg shadow-xl overflow-hidden py-1 text-xs text-slate-300">
                                        <button 
                                            onclick="copyPostLink(event, '<?= base_url('post/'); ?>${userJs}/${post.id_post}', this)"
                                            class="w-full text-left px-3 py-2 hover:bg-white/[0.05] hover:text-white flex items-center gap-2 transition-colors"
                                        >
                                            <i data-lucide="link" class="w-3.5 h-3.5"></i>
                                            <span>Copy Link</span>
                                        </button>
                                        <button 
                                            onclick="event.stopPropagation(); openReportPost(${post.id_post})"
                                            class="block w-full text-left px-3 py-2 hover:bg-red-500/10 hover:text-red-400 flex items-center gap-2 transition-colors border-t border-white/[0.03]"
                                        >
                                            <i data-lucide="flag" class="w-3.5 h-3.5"></i>
                                            <span>Report Post</span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            ${mediaHTML}

                            <div class="p-4 sm:p-5 pt-2 space-y-3">
                                <p class="text-xs sm:text-sm text-slate-300 leading-relaxed">${escapedContent}</p>
                                
                                <div class="flex items-center gap-4 pt-2 border-t border-white/[0.03] text-slate-400 text-[11px] sm:text-xs relative z-20">
                                    <button onclick="toggleLike(event, ${post.id_post}, this)" class="flex items-center gap-1.5 transition-colors group/btn ${dynamicLikeBtnClass}">
                                        <i data-lucide="heart" class="w-4 h-4 ${dynamicLikeIconClass}"></i>
                                        <span class="count-likes font-semibold">${post.likes_count}</span>
                                    </button>
                                    <a href="<?= base_url('post/'); ?>${userUrl}/${post.id_post}" class="flex items-center gap-1.5 hover:text-blue-400 transition-colors">
                                        <i data-lucide="message-square" class="w-4 h-4"></i>
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
                if (offset === 0) {
                    document.getElementById('post-container').innerHTML = '<div class="glass-card p-8 text-center text-slate-500 text-xs">Gagal memuat postingan. Silakan muat ulang halaman.</div>';
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
        }).catch(err => console.error('Gagal menyalin link: ', err));
    }

    // User dropdown menu
    document.addEventListener('click', function(e) {
        const container = document.getElementById('user-menu-container');
        const menu = document.getElementById('user-dropdown-menu');
        if (container && !container.contains(e.target)) {
            menu.classList.add('hidden');
        }
    });

    function toggleUserMenu() {
        const menu = document.getElementById('user-dropdown-menu');
        menu.classList.toggle('hidden');
    }

    // Follow / Unfollow
    function toggleFollow() {
        if (!IS_LOGGED_IN) {
            showLoginModal();
            return;
        }

        const btn = document.getElementById('follow-btn');

        fetch('<?= base_url('user/toggle_follow'); ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: getCsrfField() + '&user_id=<?= $profile_user_id; ?>'
        })
        .then(r => r.json())
        .then(data => {
            if (data.status === 'success') {
                if (data.action === 'followed') {
                    btn.textContent = 'Mengikuti';
                    btn.className = 'text-[11px] font-semibold px-5 py-2 rounded-xl transition-all duration-300 border bg-white/[0.05] hover:bg-red-600 text-slate-200 hover:text-white border-white/[0.06]';
                } else {
                    btn.textContent = 'Ikuti';
                    btn.className = 'text-[11px] font-semibold px-5 py-2 rounded-xl transition-all duration-300 border bg-red-600 hover:bg-red-500 text-white border-red-600 shadow-lg shadow-red-600/10';
                }
                const followerBtn = document.querySelector('button[onclick="openFollowModal(\'followers\')"] .font-bold');
                if (followerBtn) {
                    followerBtn.textContent = data.followers_count.toLocaleString();
                }
            } else {
                showToast(data.message || 'Terjadi kesalahan', 'red');
            }
        })
        .catch(err => {
            showToast('Terjadi kesalahan jaringan', 'red');
            console.error('Gagal follow/unfollow:', err);
        });
    }

    // Report User
    function openUserReportModal() {
        document.getElementById('user-dropdown-menu').classList.add('hidden');
        document.getElementById('user-report-modal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeUserReportModal() {
        document.getElementById('user-report-modal').classList.add('hidden');
        document.body.style.overflow = '';
    }

    function submitUserReport(e) {
        e.preventDefault();
        const reason = document.getElementById('user-report-reason').value.trim();
        if (!reason) return;

        const btn = document.getElementById('user-report-submit-btn');
        btn.disabled = true;
        btn.textContent = 'Mengirim...';

        fetch('<?= base_url('user/report_user'); ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: getCsrfField() + '&user_id=<?= $profile_user_id; ?>&reason=' + encodeURIComponent(reason)
        })
        .then(r => r.json())
        .then(data => {
            if (data.status === 'success') {
                closeUserReportModal();
                document.getElementById('user-report-reason').value = '';
                showToast(data.message, 'emerald');
            } else {
                alert(data.message || 'Gagal mengirim laporan.');
            }
        })
        .catch(err => {
            console.error('Error:', err);
            alert('Terjadi kesalahan. Silakan coba lagi.');
        })
        .finally(() => {
            btn.disabled = false;
            btn.textContent = 'Kirim Laporan';
        });
    }

    // Block User
    function openBlockModal() {
        document.getElementById('user-dropdown-menu').classList.add('hidden');
        document.getElementById('block-modal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeBlockModal() {
        document.getElementById('block-modal').classList.add('hidden');
        document.body.style.overflow = '';
    }

    function submitBlock() {
        const btn = document.getElementById('block-confirm-btn');
        btn.disabled = true;
        btn.textContent = 'Memblokir...';

        fetch('<?= base_url('user/block_user'); ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: getCsrfField() + '&user_id=<?= $profile_user_id; ?>'
        })
        .then(r => r.json())
        .then(data => {
            if (data.status === 'success') {
                closeBlockModal();
                showToast(data.message, 'red');
                setTimeout(() => window.location.reload(), 1500);
            } else {
                alert(data.message || 'Gagal memblokir pengguna.');
            }
        })
        .catch(err => {
            console.error('Error:', err);
            alert('Terjadi kesalahan. Silakan coba lagi.');
        })
        .finally(() => {
            btn.disabled = false;
            btn.textContent = 'Blokir';
        });
    }

    function unblockUser() {
        fetch('<?= base_url('user/unblock_user'); ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: getCsrfField() + '&user_id=<?= $profile_user_id; ?>'
        })
        .then(r => r.json())
        .then(data => {
            if (data.status === 'success') {
                showToast('Blokir berhasil dibatalkan', 'emerald');
                setTimeout(() => window.location.reload(), 1000);
            } else {
                showToast(data.message || 'Gagal membatalkan blokir', 'red');
            }
        })
        .catch(err => {
            showToast('Terjadi kesalahan jaringan', 'red');
            console.error('Error:', err);
        });
    }

    // Blocked profile follow alert
    function blockedFollowAlert() {
        showToast('Tidak dapat mengikuti pengguna ini', 'red');
    }

    // Toast helper
    function showToast(message, color) {
        const toast = document.createElement('div');
        const bgColor = color === 'red' ? 'bg-red-600' : 'bg-emerald-600';
        toast.className = `fixed bottom-20 left-1/2 -translate-x-1/2 z-[9999] ${bgColor} text-white text-xs font-semibold px-4 py-2.5 rounded-xl shadow-lg`;
        toast.textContent = message;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
    }
</script>
