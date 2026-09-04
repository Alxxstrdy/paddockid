<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? $title : 'PaddockID | Indonesian F1 Social Community'; ?></title>
    <link rel="icon" href="<?=assets_url('Icon.png')?>">
    <link rel="stylesheet" href="<?= assets_url('css/style.css'); ?>">
    <script src="https://unpkg.com/lucide@latest"></script>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Syne:wght@700;800&display=swap" rel="stylesheet" media="print" onload="this.media='all'">
    <noscript><link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Syne:wght@700;800&display=swap" rel="stylesheet"></noscript>

    <?php
        $this->config->load('ads');
        $this->load->helper('cookie_pref_helper');
        $ads_allowed = (get_pref_cookie('ads_consent', '0') === '1');
        if ($this->config->item('adsense_enabled') && $ads_allowed):
    ?>
    <!-- Google AdSense -->
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=<?= $this->config->item('adsense_pub_id'); ?>" crossorigin="anonymous"></script>
    <?php endif; ?>

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
</head>
<body class="<?= get_pref_cookie('theme', 'dark') === 'light' ? 'light' : '' ?>" style="padding-bottom: 80px;">
    <?php if (get_pref_cookie('theme', 'dark') !== 'light'): ?>
    <style>
        body {
            background-image: 
                radial-gradient(circle at 50% 0%, rgba(255, 24, 24, 0.04) 0%, transparent 50%),
                radial-gradient(circle at 20% 80%, rgba(0, 255, 135, 0.02) 0%, transparent 40%);
        }
    </style>
    <?php endif; ?>

 <header class="site-header">
    <a href="<?= base_url(); ?>" class="site-header__logo">                
        <img src="<?= assets_url('Logo_PaddockID.png'); ?>" alt="PaddockID Logo">
    </a>

    <form action="<?= base_url('search'); ?>" method="GET" class="search-bar hide-mobile">
        <span class="search-icon">
            <i data-lucide="search" style="width:14px;height:14px;"></i>
        </span>
        <input
            type="text"
            name="q"
            placeholder="Cari postingan atau pengguna..."
            class="input input--pill"
        >
    </form>

    <div>
        <?php if ($this->session->userdata('user_logged_in')): ?>
            <?php 
                $session_user = $this->session->userdata('user_logged_in');
                $CI =& get_instance();
                $CI->load->database();

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
            
            <div class="flex-row gap-4">
                
                <a href="<?= base_url('post/create'); ?>" class="btn btn-white btn-sm">
                    <i data-lucide="plus" style="width:14px;height:14px;"></i> 
                    <span class="hide-mobile">Post</span>
                </a>

                <div class="relative" id="notification-bell-wrapper">
                    <button onclick="toggleNotificationDropdown()" class="btn-icon relative text-muted transition-colors" title="Notifikasi">
                        <i data-lucide="bell" style="width:20px;height:20px;"></i>
                        <span id="notif-badge" class="absolute badge-count <?= $notif_count > 0 ? '' : 'hidden' ?>">
                            <?= $notif_count > 9 ? '9+' : $notif_count ?>
                        </span>
                    </button>
                    <div id="notification-dropdown" class="dropdown dropdown--wide hidden">
                        <div class="dropdown-header">
                            <h4 class="dropdown-header__title">Notifikasi</h4>
                            <button onclick="markAllNotificationsRead()" class="text-xs font-semibold c-primary">Tandai sudah dibaca</button>
                        </div>
                        <div id="notification-list" class="dropdown-list">
                            <div class="empty-state p-6">
                                <span class="empty-state__text">Memuat notifikasi...</span>
                            </div>
                        </div>
                    </div>
                </div>

                <a href="<?= base_url('profile'); ?>" class="flex-row gap-3" title="Lihat Profil" style="text-decoration:none;">
                    <span class="hide-mobile text-xs font-semibold c-muted transition-colors" style="color:var(--text-muted);">
                        <?= htmlspecialchars($display_name, ENT_QUOTES, 'UTF-8'); ?>
                    </span>
                    
                    <div class="avatar" data-user-id="<?= $session_user['user_id']; ?>">
                        <div class="avatar-ring">
                            <img src="<?= $avatar_url ?>" 
                                 alt="Foto Profil <?= $username; ?>"
                                 onerror="this.src='<?= assets_url('default.jpg'); ?>';">
                        </div>
                        
                        <?php if ($border_url): ?>
                            <div class="avatar-border">
                                <img src="<?= $border_url; ?>" alt="F1 Border Decoration">
                            </div>
                        <?php endif; ?>

                        <div class="online-indicator"></div>
                    </div>
                </a>

            </div> <?php else: ?>
            <?php if ($this->uri->segment(2) !== 'login' && $this->uri->segment(1) !== 'auth'): ?>
                <a href="<?= base_url('auth'); ?>" class="btn btn-primary btn-sm">
                    <span>Masuk</span>
                    <i data-lucide="log-in" style="width:14px;height:14px;"></i>
                </a>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</header>
