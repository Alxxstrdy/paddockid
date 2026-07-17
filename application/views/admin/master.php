<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? htmlspecialchars($title, ENT_QUOTES, 'UTF-8') : 'Admin Panel'; ?> — PaddockID</title>
    <link rel="icon" href="<?= assets_url('Icon.png'); ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <meta name="csrf-token-name" content="<?= $this->security->get_csrf_token_name(); ?>">
    <meta name="csrf-token-hash" content="<?= $this->security->get_csrf_hash(); ?>">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #0c0f1a; }
        .admin-card { background: rgba(17, 21, 36, 0.8); border: 1px solid rgba(255,255,255,0.04); }
        .nav-link { display: flex; align-items: center; gap: 0.625rem; padding: 0.625rem 0.875rem; border-radius: 0.5rem; font-size: 0.8125rem; font-weight: 500; color: #64748b; transition: all 0.15s; }
        .nav-link:hover { color: #cbd5e1; background: rgba(255,255,255,0.03); }
        .nav-link.active { color: #f87171; background: rgba(239, 68, 68, 0.08); }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.06); border-radius: 4px; }
        .line-clamp-3 { display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
        .badge { min-width: 18px; height: 18px; display: inline-flex; align-items: center; justify-content: center; padding: 0 5px; font-size: 9px; font-weight: 700; border-radius: 9999px; line-height: 1; }
        .badge-red { background: #ef4444; color: #fff; }
        .badge-amber { background: #f59e0b; color: #fff; }
        .badge-blue { background: #3b82f6; color: #fff; }
        .badge-pulse { animation: badge-pulse 2s ease-in-out infinite; }
        @keyframes badge-pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.6; } }
        .notif-panel { transform: translateY(-8px); opacity: 0; pointer-events: none; transition: all 0.15s ease; }
        .notif-panel.open { transform: translateY(0); opacity: 1; pointer-events: auto; }
    </style>
</head>
<body class="text-slate-200 antialiased min-h-screen">

    <!-- SIDEBAR (Desktop) -->
    <aside id="admin-sidebar" class="fixed inset-y-0 left-0 z-40 w-56 bg-[#0a0d16] border-r border-white/[0.04] transform transition-transform duration-200 lg:translate-x-0 -translate-x-full" onclick="event.stopPropagation()">
        <div class="flex flex-col h-full">
            <!-- Brand -->
            <div class="px-5 py-5 border-b border-white/[0.04]">
                <a href="<?= base_url('admin'); ?>" class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-red-500/10 flex items-center justify-center flex-shrink-0 relative">
                        <i data-lucide="shield" class="w-4 h-4 text-red-400"></i>
                        <span id="sidebar-total-badge" class="absolute -top-1.5 -right-1.5 badge badge-red badge-pulse hidden" data-count="0">0</span>
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-bold text-white tracking-wide">PaddockID</p>
                        <p class="text-[9px] text-slate-600 uppercase tracking-widest font-semibold">Admin Panel</p>
                    </div>
                </a>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto no-scrollbar">
                <a href="<?= base_url('admin'); ?>" class="nav-link <?= $admin_page === 'dashboard' ? 'active' : '' ?>">
                    <i data-lucide="layout-dashboard" class="w-4 h-4"></i> Dashboard
                </a>
                <a href="<?= base_url('admin/post_reports'); ?>" class="nav-link <?= $admin_page === 'post_reports' ? 'active' : '' ?> relative">
                    <i data-lucide="flag" class="w-4 h-4"></i> Post Reports
                    <span id="badge-post-reports" class="badge badge-red ml-auto hidden" data-count="0"></span>
                </a>
                <a href="<?= base_url('admin/user_reports'); ?>" class="nav-link <?= $admin_page === 'user_reports' ? 'active' : '' ?> relative">
                    <i data-lucide="shield-alert" class="w-4 h-4"></i> User Reports
                    <span id="badge-user-reports" class="badge badge-amber ml-auto hidden" data-count="0"></span>
                </a>
                <a href="<?= base_url('admin/users'); ?>" class="nav-link <?= $admin_page === 'users' ? 'active' : '' ?>">
                    <i data-lucide="users" class="w-4 h-4"></i> User List
                </a>
                <a href="<?= base_url('admin/login_attempts'); ?>" class="nav-link <?= $admin_page === 'login_attempts' ? 'active' : '' ?> relative">
                    <i data-lucide="log-in" class="w-4 h-4"></i> Login Attempts
                    <span id="badge-failed-logins" class="badge badge-blue ml-auto hidden" data-count="0"></span>
                </a>
                <a href="<?= base_url('admin/errors'); ?>" class="nav-link <?= $admin_page === 'errors' ? 'active' : '' ?> relative">
                    <i data-lucide="terminal" class="w-4 h-4"></i> Error Logs
                    <span id="badge-errors" class="badge badge-red ml-auto hidden" data-count="0"></span>
                </a>

                <div class="pt-3 mt-3 border-t border-white/[0.04]">
                    <p class="text-[9px] text-slate-600 uppercase tracking-widest font-semibold px-3 mb-2">Monetisasi</p>
                    <a href="<?= base_url('admin/ads'); ?>" class="nav-link <?= in_array($admin_page, ['ads', 'ad_form']) ? 'active' : '' ?>">
                        <i data-lucide="megaphone" class="w-4 h-4"></i> Ads Management
                    </a>
                </div>

                <div class="pt-3 mt-3 border-t border-white/[0.04]">
                    <p class="text-[9px] text-slate-600 uppercase tracking-widest font-semibold px-3 mb-2">Navigation</p>
                    <a href="<?= base_url('home'); ?>" class="nav-link text-slate-600 hover:text-slate-400">
                        <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i> Kembali ke Site
                    </a>
                </div>
            </nav>

            <!-- Admin Info -->
            <div class="px-4 py-3 border-t border-white/[0.04]">
                <div class="flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-full bg-red-500/10 flex items-center justify-center flex-shrink-0">
                        <i data-lucide="user" class="w-3.5 h-3.5 text-red-400"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[11px] font-semibold text-slate-300 truncate"><?= htmlspecialchars($this->session->userdata('user_logged_in')['username'] ?? 'Admin', ENT_QUOTES, 'UTF-8'); ?></p>
                        <p class="text-[9px] text-slate-600">Super Admin</p>
                    </div>
                </div>
            </div>
        </div>
    </aside>

    <!-- Sidebar Overlay (Mobile) -->
    <div id="sidebar-overlay" class="fixed inset-0 z-30 bg-black/50 backdrop-blur-sm lg:hidden hidden" onclick="toggleSidebar()"></div>

    <!-- MAIN AREA -->
    <div class="lg:ml-56 min-h-screen flex flex-col">

        <!-- Top Bar (Mobile) -->
        <header class="sticky top-0 z-20 flex items-center justify-between px-4 py-3 bg-[#0c0f1a]/80 backdrop-blur-xl border-b border-white/[0.04] lg:hidden">
            <button onclick="toggleSidebar()" class="p-2 text-slate-400 hover:text-white transition-colors rounded-lg hover:bg-white/[0.04]">
                <i data-lucide="menu" class="w-5 h-5"></i>
            </button>
            <div class="flex items-center gap-2">
                <div class="w-6 h-6 rounded bg-red-500/10 flex items-center justify-center">
                    <i data-lucide="shield" class="w-3 h-3 text-red-400"></i>
                </div>
                <span class="text-xs font-bold text-white">Admin Panel</span>
            </div>
            <!-- Mobile notification bell -->
            <button onclick="toggleNotifPanel()" class="relative p-2 text-slate-400 hover:text-white transition-colors rounded-lg hover:bg-white/[0.04]">
                <i data-lucide="bell" class="w-5 h-5"></i>
                <span id="mobile-notif-badge" class="absolute -top-0.5 -right-0.5 min-w-[16px] h-[16px] flex items-center justify-center bg-red-500 text-white text-[8px] font-bold rounded-full px-1 hidden"></span>
            </button>
        </header>

        <!-- Desktop Top Bar -->
        <header class="hidden lg:flex items-center justify-between px-8 py-4 border-b border-white/[0.04] bg-[#0c0f1a]/60 backdrop-blur-xl sticky top-0 z-10">
            <div>
                <h1 class="text-sm font-bold text-white tracking-wide"><?= htmlspecialchars($title ?? 'Admin', ENT_QUOTES, 'UTF-8'); ?></h1>
            </div>
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-1.5 text-[9px] text-slate-600">
                    <span id="live-dot" class="w-1.5 h-1.5 rounded-full bg-slate-600 transition-colors duration-500"></span>
                    Live
                </div>
                <!-- Notification Bell -->
                <div class="relative" id="notif-wrapper">
                    <button onclick="toggleNotifPanel()" class="relative p-2 text-slate-400 hover:text-white transition-colors rounded-lg hover:bg-white/[0.04]">
                        <i data-lucide="bell" class="w-4.5 h-4.5"></i>
                        <span id="desktop-notif-badge" class="absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] flex items-center justify-center bg-red-500 text-white text-[9px] font-bold rounded-full px-1 shadow-lg shadow-red-500/30 hidden"></span>
                    </button>
                    <!-- Notification Panel -->
                    <div id="notif-panel" class="notif-panel absolute right-0 top-full mt-2 w-72 bg-[#0f1220] rounded-xl border border-white/[0.06] shadow-2xl shadow-black/50 z-50 overflow-hidden">
                        <div class="px-4 py-3 border-b border-white/[0.04] flex items-center justify-between">
                            <p class="text-[11px] font-bold text-white uppercase tracking-wide">Notifikasi</p>
                            <span id="notif-panel-total" class="text-[9px] text-slate-500 font-mono"></span>
                        </div>
                        <div id="notif-list" class="max-h-72 overflow-y-auto no-scrollbar divide-y divide-white/[0.03]">
                            <div class="px-4 py-6 text-center text-slate-600 text-[10px]">Memuat...</div>
                        </div>
                        <div class="px-4 py-2.5 border-t border-white/[0.04] bg-white/[0.01]">
                            <div class="flex items-center justify-between">
                                <span id="notif-last-check" class="text-[9px] text-slate-600 font-mono">-</span>
                                <button onclick="fetchCounts(); renderNotifPanel()" class="text-[9px] text-red-400 hover:text-red-300 font-semibold transition-colors">Refresh</button>
                            </div>
                        </div>
                    </div>
                </div>
                <a href="<?= base_url('home'); ?>" class="text-[10px] text-slate-500 hover:text-slate-300 transition-colors flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-white/[0.04] hover:border-white/[0.08]">
                    <i data-lucide="external-link" class="w-3 h-3"></i> Lihat Site
                </a>
            </div>
        </header>

        <!-- Content -->
        <main class="flex-1 px-4 sm:px-6 lg:px-8 py-6 lg:py-8 space-y-6">
