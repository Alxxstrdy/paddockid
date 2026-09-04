<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? htmlspecialchars($title, ENT_QUOTES, 'UTF-8') : 'Admin Panel'; ?> — PaddockID</title>
    <link rel="icon" href="<?= assets_url('Icon.png'); ?>">
    <link rel="stylesheet" href="<?= assets_url('css/style.css'); ?>">
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <meta name="csrf-token-name" content="<?= $this->security->get_csrf_token_name(); ?>">
    <meta name="csrf-token-hash" content="<?= $this->security->get_csrf_hash(); ?>">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #0c0f1a; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.06); border-radius: 4px; }
        .line-clamp-3 { display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
        .notif-panel { transform: translateY(-8px); opacity: 0; pointer-events: none; transition: all 0.15s ease; }
        .notif-panel.open { transform: translateY(0); opacity: 1; pointer-events: auto; }
    </style>
</head>
<body class="antialiased" style="min-height:100vh;">

    <!-- SIDEBAR (Desktop) -->
    <aside id="admin-sidebar" class="fixed inset-y-0 left-0 z-40 transform transition-transform duration-200 -translate-x-full lg:translate-x-0" style="width:224px;background:var(--bg-surface);border-right:1px solid var(--border-subtle);" onclick="event.stopPropagation()">
        <div class="flex-col h-full" style="padding:20px 12px;display:flex;flex-direction:column;gap:4px;">
            <!-- Brand -->
            <div class="border-b" style="padding:20px;border-bottom-color:var(--border-subtle);">
                <a href="<?= base_url('admin'); ?>" class="flex-row gap-2-5">
                    <div class="rounded-lg flex items-center justify-center flex-shrink-0 relative" style="width:32px;height:32px;background:var(--color-primary-bg);">
                        <i data-lucide="shield" class="w-4 h-4 c-primary"></i>
                        <span id="sidebar-total-badge" class="absolute badge badge-danger hidden" style="top:-6px;right:-6px;" data-count="0">0</span>
                    </div>
                    <div class="min-w-0">
                        <p class="font-bold c-white" style="font-size:12px;letter-spacing:0.02em;">PaddockID</p>
                        <p class="text-micro">Admin Panel</p>
                    </div>
                </a>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 overflow-y-auto no-scrollbar" style="padding:16px 8px;display:flex;flex-direction:column;gap:4px;">
                <a href="<?= base_url('admin'); ?>" class="admin-nav-item <?= $admin_page === 'dashboard' ? 'is-active' : '' ?>">
                    <i data-lucide="layout-dashboard" class="w-4 h-4"></i> Dashboard
                </a>
                <a href="<?= base_url('admin/post_reports'); ?>" class="admin-nav-item <?= $admin_page === 'post_reports' ? 'is-active' : '' ?> relative">
                    <i data-lucide="flag" class="w-4 h-4"></i> Post Reports
                    <span id="badge-post-reports" class="badge badge-danger ml-auto hidden" data-count="0"></span>
                </a>
                <a href="<?= base_url('admin/user_reports'); ?>" class="admin-nav-item <?= $admin_page === 'user_reports' ? 'is-active' : '' ?> relative">
                    <i data-lucide="shield-alert" class="w-4 h-4"></i> User Reports
                    <span id="badge-user-reports" class="badge badge-warning ml-auto hidden" data-count="0"></span>
                </a>
                <a href="<?= base_url('admin/users'); ?>" class="admin-nav-item <?= $admin_page === 'users' ? 'is-active' : '' ?>">
                    <i data-lucide="users" class="w-4 h-4"></i> User List
                </a>
                <a href="<?= base_url('admin/login_attempts'); ?>" class="admin-nav-item <?= $admin_page === 'login_attempts' ? 'is-active' : '' ?> relative">
                    <i data-lucide="log-in" class="w-4 h-4"></i> Login Attempts
                    <span id="badge-failed-logins" class="badge badge-info ml-auto hidden" data-count="0"></span>
                </a>
                <a href="<?= base_url('admin/errors'); ?>" class="admin-nav-item <?= $admin_page === 'errors' ? 'is-active' : '' ?> relative">
                    <i data-lucide="terminal" class="w-4 h-4"></i> Error Logs
                    <span id="badge-errors" class="badge badge-danger ml-auto hidden" data-count="0"></span>
                </a>
                <a href="<?= base_url('admin/error_codes'); ?>" class="admin-nav-item <?= $admin_page === 'error_codes' ? 'is-active' : '' ?>">
                    <i data-lucide="book-open" class="w-4 h-4"></i> Error Codes
                </a>
                <a href="<?= base_url('admin/activity_logs'); ?>" class="admin-nav-item <?= $admin_page === 'activity_logs' ? 'is-active' : '' ?>">
                    <i data-lucide="activity" class="w-4 h-4"></i> Activity Log
                </a>

                <div class="pt-3 mt-3 border-t" style="border-color:var(--border-subtle);">
                    <p class="text-section-title px-3 mb-2">Race</p>
                    <a href="<?= base_url('admin/race_sessions'); ?>" class="admin-nav-item <?= $admin_page === 'race_sessions' ? 'is-active' : '' ?>">
                        <i data-lucide="timer" class="w-4 h-4"></i> Race Sessions
                    </a>
                </div>

                <div class="pt-3 mt-3 border-t" style="border-color:var(--border-subtle);">
                    <p class="text-section-title px-3 mb-2">Monetisasi</p>
                    <a href="<?= base_url('admin/ads'); ?>" class="admin-nav-item <?= in_array($admin_page, ['ads', 'ad_form']) ? 'is-active' : '' ?>">
                        <i data-lucide="megaphone" class="w-4 h-4"></i> Ads Management
                    </a>
                </div>

                <div class="pt-3 mt-3 border-t" style="border-color:var(--border-subtle);">
                    <p class="text-section-title px-3 mb-2">Navigation</p>
                    <a href="<?= base_url('home'); ?>" class="admin-nav-item c-subtle">
                        <i data-lucide="arrow-left" class="w-3-5 h-3-5"></i> Kembali ke Site
                    </a>
                </div>
            </nav>

            <!-- Admin Info -->
            <div class="border-t" style="padding:12px 16px;border-color:var(--border-subtle);">
                <div class="flex-row gap-2-5">
                    <div class="rounded-full flex items-center justify-center flex-shrink-0" style="width:28px;height:28px;background:var(--color-primary-bg);">
                        <i data-lucide="user" class="w-3-5 h-3-5 c-primary"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="truncate" style="font-size:11px;font-weight:600;color:var(--text-secondary);"><?= htmlspecialchars($this->session->userdata('user_logged_in')['username'] ?? 'Admin', ENT_QUOTES, 'UTF-8'); ?></p>
                        <p class="text-micro">Super Admin</p>
                    </div>
                </div>
            </div>
        </div>
    </aside>

    <!-- Sidebar Overlay (Mobile) -->
    <div id="sidebar-overlay" class="fixed inset-0 z-30 hidden lg:hidden" style="background:rgba(0,0,0,0.5);backdrop-filter:blur(4px);" onclick="toggleSidebar()"></div>

    <!-- MAIN AREA -->
    <div class="admin-content" style="min-height:100vh;display:flex;flex-direction:column;">
        <style>@media(min-width:1024px){.admin-content{margin-left:224px;}}</style>

        <!-- Top Bar (Mobile) -->
        <header class="admin-mobile-header sticky top-0 z-20">
            <button onclick="toggleSidebar()" class="p-2 rounded-lg transition-colors" style="color:var(--text-subtle);" onmouseover="this.style.color='var(--text-primary)';this.style.background='var(--bg-surface-hover)'" onmouseout="this.style.color='var(--text-subtle)';this.style.background='transparent'">
                <i data-lucide="menu" class="w-5 h-5"></i>
            </button>
            <div class="flex-row gap-2">
                <div class="rounded flex items-center justify-center" style="width:24px;height:24px;background:var(--color-primary-bg);">
                    <i data-lucide="shield" class="w-3 h-3 c-primary"></i>
                </div>
                <span class="font-bold c-white" style="font-size:12px;">Admin Panel</span>
            </div>
            <!-- Mobile notification bell -->
            <button onclick="toggleNotifPanel()" class="relative p-2 rounded-lg transition-colors" style="color:var(--text-subtle);" onmouseover="this.style.color='var(--text-primary)';this.style.background='var(--bg-surface-hover)'" onmouseout="this.style.color='var(--text-subtle)';this.style.background='transparent'">
                <i data-lucide="bell" class="w-5 h-5"></i>
                <span id="mobile-notif-badge" class="absolute flex items-center justify-center hidden" style="top:-2px;right:-2px;min-width:16px;height:16px;background:var(--color-primary);color:#fff;font-size:8px;font-weight:700;border-radius:var(--radius-pill);padding:0 4px;"></span>
            </button>
        </header>

        <!-- Desktop Top Bar -->
        <header class="hidden lg:flex items-center justify-between border-b sticky top-0 z-10" style="padding:16px 32px;background:rgba(12,15,26,0.6);backdrop-filter:blur(24px);border-color:var(--border-subtle);">
            <div>
                <h1 class="font-bold c-white" style="font-size:14px;letter-spacing:0.02em;"><?= htmlspecialchars($title ?? 'Admin', ENT_QUOTES, 'UTF-8'); ?></h1>
            </div>
            <div class="flex-row gap-3">
                <div class="flex-row gap-1-5 text-micro">
                    <span id="live-dot" class="rounded-full transition-colors" style="width:6px;height:6px;background:var(--text-subtle);transition-duration:500ms;"></span>
                    Live
                </div>
                <!-- Notification Bell -->
                <div class="relative" id="notif-wrapper">
                    <button onclick="toggleNotifPanel()" class="relative p-2 rounded-lg transition-colors" style="color:var(--text-subtle);" onmouseover="this.style.color='var(--text-primary)';this.style.background='var(--bg-surface-hover)'" onmouseout="this.style.color='var(--text-subtle)';this.style.background='transparent'">
                        <i data-lucide="bell" class="w-4-5 h-4-5"></i>
                        <span id="desktop-notif-badge" class="absolute flex items-center justify-center hidden" style="top:-2px;right:-2px;min-width:18px;height:18px;background:var(--color-primary);color:#fff;font-size:9px;font-weight:700;border-radius:var(--radius-pill);padding:0 4px;box-shadow:0 2px 8px rgba(220,38,38,0.3);"></span>
                    </button>
                    <!-- Notification Panel -->
                    <div id="notif-panel" class="notif-panel absolute right-0 top-full z-dropdown overflow-hidden" style="margin-top:8px;width:288px;background:var(--bg-surface);border:1px solid var(--border-strong);border-radius:var(--radius-lg);box-shadow:var(--shadow-xl);">
                        <div class="flex-row justify-between border-b" style="padding:12px 16px;border-color:var(--border-subtle);">
                            <p class="text-label">Notifikasi</p>
                            <span id="notif-panel-total" class="text-micro"></span>
                        </div>
                        <div id="notif-list" class="overflow-y-auto no-scrollbar" style="max-height:288px;">
                            <div class="text-center" style="padding:24px 16px;color:var(--text-subtle);font-size:10px;">Memuat...</div>
                        </div>
                        <div class="border-t" style="padding:10px 16px;border-color:var(--border-subtle);background:var(--bg-surface-subtle);">
                            <div class="flex-row justify-between">
                                <span id="notif-last-check" class="text-micro">-</span>
                                <button onclick="fetchCounts(); renderNotifPanel()" class="font-semibold transition-colors" style="font-size:9px;color:var(--color-primary);">Refresh</button>
                            </div>
                        </div>
                    </div>
                </div>
                <a href="<?= base_url('home'); ?>" class="flex-row gap-1-5 rounded-lg transition-colors" style="font-size:10px;color:var(--text-subtle);padding:6px 12px;border:1px solid var(--border-subtle);" onmouseover="this.style.color='var(--text-secondary)';this.style.borderColor='var(--border-strong)'" onmouseout="this.style.color='var(--text-subtle)';this.style.borderColor='var(--border-subtle)'">
                    <i data-lucide="external-link" class="w-3 h-3"></i> Lihat Site
                </a>
            </div>
        </header>

        <!-- Content -->
        <main class="flex-1" style="padding:24px;">
