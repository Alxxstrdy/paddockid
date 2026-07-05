<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? $title : 'PaddockID | Indonesian F1 Social Community'; ?></title>
    <!-- Tailwind CSS -->
     <link rel="icon" href="<?=assets_url('Icon.png')?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Syne:wght@700;800&display=swap" rel="stylesheet">
    
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

 <header class="p-6 lg:px-12 flex items-center justify-between">
    <a href="<?= base_url(); ?>" class="inline-block">                
        <img src="<?= assets_url('Logo_PaddockID.png'); ?>" alt="PaddockID Logo" class="h-9 w-auto object-contain">
    </a>

    <div>
        <?php if ($this->session->userdata('user_logged_in')): ?>
            <?php 
                $session_user = $this->session->userdata('user_logged_in');
                // Tentukan base URL untuk avatar dan bingkai border
                $profile_pic = $session_user['profile_pic'];
                $avatar_url = (strpos($profile_pic, 'http') === 0) 
                    ? $profile_pic 
                    : assets_url($profile_pic);
                $border_url = !empty($session_user['border']) 
                    ? (strpos($session_user['border'], 'http') === 0 ? $session_user['border'] : assets_url($session_user['border']))
                    : null;
            ?>
            
            <div class="flex items-center gap-4">
                
                <button class="bg-white text-black hover:bg-slate-200 font-semibold text-xs px-4 py-2 rounded-full transition-all flex items-center gap-1.5 shadow-lg shadow-white/5 active:scale-[0.98]">
                    <i data-lucide="plus" class="w-3.5 h-3.5"></i> 
                    <span class="hidden sm:inline">Post</span>
                </button>

<a href="<?= base_url('profile'); ?>" class="group flex items-center gap-3 focus:outline-none" title="Lihat Profil">
    <span class="hidden sm:inline-block text-xs font-semibold text-slate-300 group-hover:text-red-400 transition-colors">
        <?= $session_user['fullname']; ?>
    </span>
    
    <div class="relative w-9 h-9 flex-shrink-0">
        <div class="w-full h-full rounded-full p-[1.5px] bg-gradient-to-b from-white/[0.08] to-transparent group-hover:from-red-500 transition-all duration-300 ring-1 ring-white/[0.04] overflow-hidden">
            <img src="<?= $avatar_url ?>" 
                 alt="Foto Profil <?= $session_user['username']; ?>" 
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