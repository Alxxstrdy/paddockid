<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? $title : 'Login | PaddockID'; ?></title>
    <link rel="icon" href="<?= assets_url('Icon.png') ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Syne:wght@700;800&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #05070c;
            background-image: 
                radial-gradient(circle at 50% 0%, rgba(255, 24, 24, 0.05) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(0, 255, 135, 0.02) 0%, transparent 40%);
        }
        .font-syne { font-family: 'Syne', sans-serif; }
        .glass-card {
            background: rgba(15, 22, 38, 0.4);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.04);
        }
    </style>
</head>
<body class="text-slate-100 antialiased selection:bg-red-500 selection:text-white min-h-screen flex flex-col justify-between">

    <header class="p-6 lg:px-12">
        <a href="<?= base_url(); ?>" class="inline-block">                
            <img src="<?= assets_url('Logo_PaddockID.png'); ?>" alt="PaddockID Logo" class="h-9 w-auto object-contain">
        </a>
    </header>

    <main class="flex-1 flex items-center justify-center px-4 sm:px-6 mb-12">
        <div class="w-full max-w-md glass-card rounded-2xl p-6 sm:p-8 relative overflow-hidden shadow-2xl border border-white/[0.06]">
            
            <div class="absolute top-0 inset-x-0 h-[3px] bg-gradient-to-r from-transparent via-red-500 to-transparent"></div>
            
            <div class="text-center mb-8">
                <h1 class="font-syne text-2xl sm:text-3xl uppercase tracking-tight text-white mb-2">
                    Welcome <span class="text-red-500">Back</span>
                </h1>
            </div>

            <?php if($this->session->flashdata('error')): ?>
                <div class="mb-4 bg-red-500/10 border border-red-500/20 text-red-400 text-xs rounded-lg p-3 flex items-center gap-2">
                    <i data-lucide="alert-circle" class="w-4 h-4 flex-shrink-0"></i>
                    <span><?= $this->session->flashdata('error'); ?></span>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('auth/login_process'); ?>" method="POST" class="space-y-4">
                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                
                <div>
                    <label class="block text-slate-400 text-[11px] uppercase tracking-wider font-semibold mb-1.5">Username / Email</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                            <i data-lucide="user" class="w-4 h-4"></i>
                        </span>
                        <input type="text" name="identity" required placeholder="Masukkan username atau email"
                            class="w-full bg-slate-950/60 border border-white/[0.08] focus:border-red-500/50 rounded-xl pl-10 pr-4 py-2.5 text-sm text-slate-200 placeholder-slate-600 focus:outline-none focus:ring-1 focus:ring-red-500/30 transition-all">
                    </div>
                </div>

                <div>
                    <div class="flex justify-between items-center mb-1.5">
                        <label class="block text-slate-400 text-[11px] uppercase tracking-wider font-semibold">Password</label>
                        <a href="<?= base_url('auth/forgot_password'); ?>" class="text-[11px] text-slate-500 hover:text-red-400 transition-colors">Lupa Password?</a>
                    </div>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                            <i data-lucide="lock" class="w-4 h-4"></i>
                        </span>
                        <input type="password" id="password-field" name="password" required placeholder="••••••••"
                            class="w-full bg-slate-950/60 border border-white/[0.08] focus:border-red-500/50 rounded-xl pl-10 pr-10 py-2.5 text-sm text-slate-200 placeholder-slate-600 focus:outline-none focus:ring-1 focus:ring-red-500/30 transition-all">
                        <button type="button" onclick="togglePasswordVisibility()" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-500 hover:text-slate-300">
                            <i id="password-toggle-icon" data-lucide="eye" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>

                <div class="flex items-center pt-1">
                    <label class="flex items-center cursor-pointer select-none group">
                        <input type="checkbox" name="remember" value="1" 
                            class="rounded border-white/[0.12] bg-slate-950/80 text-red-600 focus:ring-red-500/30 focus:ring-offset-0 focus:ring-1 w-4 h-4 transition-all accent-red-600 cursor-pointer">
                        <span class="ml-2 text-[11px] text-slate-400 group-hover:text-slate-300 transition-colors font-medium">Ingat Saya (1 Bulan)</span>
                    </label>
                </div>

                <button type="submit" 
                    class="w-full bg-red-600 hover:bg-red-500 text-white font-semibold text-sm py-2.5 px-4 rounded-xl transition-all shadow-lg shadow-red-600/10 hover:shadow-red-600/20 active:scale-[0.99] flex items-center justify-center gap-2 mt-2">
                    <span>Masuk ke Paddock</span>
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </button>
            </form>

            <div class="relative my-6 flex items-center justify-center">
                <div class="absolute inset-x-0 h-[1px] bg-white/[0.06]"></div>
                <span class="relative bg-[#0b101d] px-3 text-[10px] uppercase font-bold tracking-widest text-slate-500">Atau</span>
            </div>

            <a href="<?= base_url('auth/google_login'); ?>" 
                class="w-full bg-white/[0.03] hover:bg-white/[0.08] text-slate-200 border border-white/[0.08] hover:border-white/[0.15] font-medium text-xs sm:text-sm py-2.5 px-4 rounded-xl transition-all flex items-center justify-center gap-2.5 active:scale-[0.99]">
                <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                    <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                    <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z" fill="#FBBC05"/>
                    <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z" fill="#EA4335"/>
                </svg>
                <span>Lanjutkan dengan Google</span>
            </a>

            <div class="mt-6 text-center text-xs text-slate-400 border-t border-white/[0.03] pt-4">
                Belum punya akun? 
                <a href="<?= base_url('auth/register'); ?>" class="text-red-400 font-semibold hover:underline">Daftar Sekarang</a>
            </div>

        </div>
    </main>

    <footer class="py-4 text-center text-[10px] text-slate-600 tracking-wide">
        &copy; 2026 PaddockID. Built for Indonesian Formula 1 Enthusiasts.
    </footer>

    <script>
        // Inisialisasi ikon Lucide
        lucide.createIcons();

        // Fungsi Show/Hide Password
        function togglePasswordVisibility() {
            const passwordField = document.getElementById('password-field');
            const toggleIcon = document.getElementById('password-toggle-icon');
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                toggleIcon.setAttribute('data-lucide', 'eye-off');
            } else {
                passwordField.type = 'password';
                toggleIcon.setAttribute('data-lucide', 'eye');
            }
            // Re-render hanya ikon toggle agar berubah bentuk
            lucide.createIcons();
        }
    </script>
</body>
</html>