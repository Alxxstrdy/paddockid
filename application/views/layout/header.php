<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? $title : 'PaddockID | Indonesian F1 Social Community'; ?></title>
    <link rel="icon" href="<?=assets_url('Icon.png')?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Syne:wght@700;800&display=swap" rel="stylesheet" media="print" onload="this.media='all'">
    <noscript><link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Syne:wght@700;800&display=swap" rel="stylesheet"></noscript>

    <?php
        $meta_desc  = isset($meta_description) ? $meta_description : 'PaddockID adalah komunitas F1 Indonesia. Ikuti diskusi seru seputar Formula 1, bagikan momen balapan, dan terhubung dengan penggemar F1 lainnya.';
        $meta_image = isset($meta_image) ? $meta_image : assets_url('Logo_PaddockID.png');
        $page_url   = current_url();
        $site_name  = 'PaddockID';
        $page_title = isset($title) ? $title : $site_name;
    ?>
    <meta name="description" content="<?= htmlspecialchars($meta_desc, ENT_QUOTES, 'UTF-8'); ?>">
    <link rel="canonical" href="<?= $page_url; ?>">
    <meta property="og:title" content="<?= htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8'); ?>">
    <meta property="og:description" content="<?= htmlspecialchars($meta_desc, ENT_QUOTES, 'UTF-8'); ?>">
    <meta property="og:url" content="<?= $page_url; ?>">
    <meta property="og:image" content="<?= $meta_image; ?>">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="<?= $site_name; ?>">
    <meta property="og:locale" content="id_ID">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8'); ?>">
    <meta name="twitter:description" content="<?= htmlspecialchars($meta_desc, ENT_QUOTES, 'UTF-8'); ?>">
    <meta name="twitter:image" content="<?= $meta_image; ?>">
    <meta name="csrf-token-name" content="<?= $this->security->get_csrf_token_name(); ?>">
    <meta name="csrf-token-hash" content="<?= $this->security->get_csrf_hash(); ?>">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #05070c;
            background-image: 
                radial-gradient(circle at 50% 0%, rgba(255, 24, 24, 0.04) 0%, transparent 50%),
                radial-gradient(circle at 20% 80%, rgba(0, 255, 135, 0.02) 0%, transparent 40%);
        }
        .font-syne { font-family: 'Syne', sans-serif; }
        .glass-card {
            background: rgba(15, 22, 38, 0.4);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.04);
        }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="text-slate-100 antialiased selection:bg-red-500 selection:text-white pb-24 lg:pb-0">

 <header class="p-6 lg:px-12 flex items-center justify-between gap-4">
    <a href="<?= base_url(); ?>" class="inline-block flex-shrink-0">                
        <img src="<?= assets_url('Logo_PaddockID.png'); ?>" alt="PaddockID Logo" class="h-9 w-auto object-contain">
    </a>

    <form action="<?= base_url('search'); ?>" method="GET" class="hidden sm:block flex-1 max-w-md mx-auto">
        <div class="relative">
            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                <i data-lucide="search" class="w-3.5 h-3.5"></i>
            </span>
            <input
                type="text"
                name="q"
                placeholder="Cari postingan atau pengguna..."
                class="w-full bg-slate-950/60 border border-white/[0.06] focus:border-red-500/50 rounded-full pl-10 pr-4 py-2 text-xs text-slate-300 placeholder-slate-600 focus:outline-none focus:ring-1 focus:ring-red-500/20 transition-all"
            >
        </div>
    </form>

    <div>
        <?php if ($this->session->userdata('user_logged_in')): ?>
            <?php 
                $session_user = $this->session->userdata('user_logged_in');
                $CI =& get_instance();
                $CI->load->database();

                // Satu query JOIN untuk user + border
                $fresh_user = $CI->db->select('u.display_name, u.username, u.avatar, u.border_active, b.image_url as border_image')
                    ->from('users u')
                    ->join('borders b', 'b.id_border = u.border_active', 'left')
                    ->where('u.id_user', $session_user['user_id'])
                    ->get()
                    ->row_array();

                $display_name = $fresh_user ? $fresh_user['display_name'] : $session_user['fullname'];
                $username     = $fresh_user ? $fresh_user['username'] : $session_user['username'];
                $avatar_file  = $fresh_user ? $fresh_user['avatar'] : $session_user['profile_pic'];
                $border_url   = null;
                if (!empty($fresh_user['border_image'])) {
                    $border_url = strpos($fresh_user['border_image'], 'http') === 0 ? $fresh_user['border_image'] : assets_url($fresh_user['border_image']);
                }
                $avatar_url   = avatar_url($avatar_file);

                $notif_count = $CI->db->where('id_user', $session_user['user_id'])
                    ->where('is_read', 0)
                    ->count_all_results('notifications');
            ?>
            
            <div class="flex items-center gap-4">
                
                <button onclick="openCreatePostModal()" class="bg-white text-black hover:bg-slate-200 font-semibold text-xs px-4 py-2 rounded-full transition-all flex items-center gap-1.5 shadow-lg shadow-white/5 active:scale-[0.98]">
                    <i data-lucide="plus" class="w-3.5 h-3.5"></i> 
                    <span class="hidden sm:inline">Post</span>
                </button>

                <div class="relative" id="notification-bell-wrapper">
                    <button onclick="toggleNotificationDropdown()" class="relative p-2 text-slate-400 hover:text-white transition-colors focus:outline-none" title="Notifikasi">
                        <i data-lucide="bell" class="w-5 h-5"></i>
                        <span id="notif-badge" class="absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] flex items-center justify-center bg-red-600 text-white text-[10px] font-bold rounded-full px-1 leading-none shadow-lg shadow-red-600/30 <?= $notif_count > 0 ? '' : 'hidden' ?>">
                            <?= $notif_count > 9 ? '9+' : $notif_count ?>
                        </span>
                    </button>
                    <div id="notification-dropdown" class="hidden absolute right-0 top-full mt-2 w-[320px] sm:w-[360px] max-h-[480px] overflow-y-auto glass-card rounded-xl border border-white/[0.08] shadow-2xl shadow-black/50 z-[9999]">
                        <div class="sticky top-0 bg-[#0a0e1a]/95 backdrop-blur-xl border-b border-white/[0.04] px-4 py-3 flex items-center justify-between z-10">
                            <h4 class="text-xs font-bold text-white uppercase tracking-wide">Notifikasi</h4>
                            <button onclick="markAllNotificationsRead()" class="text-[10px] text-red-400 hover:text-red-300 font-semibold transition-colors">Tandai sudah dibaca</button>
                        </div>
                        <div id="notification-list" class="divide-y divide-white/[0.04]">
                            <div class="px-4 py-8 text-center text-slate-500 text-xs">Memuat notifikasi...</div>
                        </div>
                    </div>
                </div>

<a href="<?= base_url('profile'); ?>" class="group flex items-center gap-3 focus:outline-none" title="Lihat Profil">
    <span class="hidden sm:inline-block text-xs font-semibold text-slate-300 group-hover:text-red-400 transition-colors">
        <?= htmlspecialchars($display_name, ENT_QUOTES, 'UTF-8'); ?>
    </span>
    
    <div class="relative w-9 h-9 flex-shrink-0">
        <div class="w-full h-full rounded-full p-[1.5px] bg-gradient-to-b from-white/[0.08] to-transparent group-hover:from-red-500 transition-all duration-300 ring-1 ring-white/[0.04] overflow-hidden">
            <img src="<?= $avatar_url ?>" 
                 alt="Foto Profil <?= $username; ?>" 
                 class="w-full h-full object-cover rounded-full"
                 onerror="this.src='<?= assets_url('default.jpg'); ?>';">
        </div>
        
        <?php if ($border_url): ?>
            <div class="absolute inset-0 w-full h-full pointer-events-none scale-[1] transform origin-center z-20">
                <img src="<?= $border_url; ?>" alt="F1 Border Decoration" class="w-full h-full object-contain">
            </div>
        <?php endif; ?>
    </div>
</a>

            </div> <?php else: ?>
            <?php if ($this->uri->segment(2) !== 'login' && $this->uri->segment(1) !== 'auth'): ?>
                <a href="<?= base_url('auth'); ?>" 
                   class="bg-red-600 hover:bg-red-500 text-white font-semibold text-xs py-2 px-4 rounded-xl transition-all shadow-lg shadow-red-600/10 flex items-center gap-1.5 active:scale-[0.99]">
                    <span>Masuk</span>
                    <i data-lucide="log-in" class="w-3.5 h-3.5"></i>
                </a>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</header>