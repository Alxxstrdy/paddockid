<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? $title : 'Login | PaddockID'; ?></title>
    <link rel="icon" href="<?= assets_url('Icon.png') ?>">
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Syne:wght@700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= assets_url('css/style.css'); ?>?v=<?= filemtime(FCPATH . 'uploads/css/style.css'); ?>">
</head>
<body style="display: flex; flex-direction: column; min-height: 100vh; justify-content: space-between;">

    <header class="p-6" style="padding-left: 48px; padding-right: 48px;">
        <a href="<?= base_url(); ?>" class="inline-block">                
            <img src="<?= assets_url('Logo_PaddockID.png'); ?>" alt="PaddockID Logo" style="height: 36px; width: auto; object-fit: contain;">
        </a>
    </header>

    <main class="flex-1 flex-row justify-center px-4" style="margin-bottom: 48px;">
        <div class="auth-card" style="max-width: 448px; border-radius: var(--radius-2xl); padding: 24px; box-shadow: var(--shadow-xl); overflow: hidden; position: relative;">
            
            <div style="position: absolute; top: 0; left: 0; right: 0; height: 3px; background: linear-gradient(to right, transparent, var(--color-primary), transparent);"></div>
            
            <div class="text-center" style="margin-bottom: 32px;">
                <h1 class="auth-card__title">
                    Welcome <span class="c-primary">Back</span>
                </h1>
            </div>

            <?php if($this->session->flashdata('error')): ?>
                <div class="mb-4 flex-row gap-2" style="background: var(--color-danger-bg); border: 1px solid var(--color-danger-border); color: var(--color-danger); font-size: 12px; border-radius: var(--radius-lg); padding: 12px;">
                    <i data-lucide="alert-circle" style="width: 16px; height: 16px;" class="flex-shrink-0"></i>
                    <span><?= $this->session->flashdata('error'); ?></span>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('auth/login_process'); ?>" method="POST" class="space-y-4">
                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                
                <div class="form-group">
                    <label class="form-label">Username / Email</label>
                    <div class="input-icon-wrapper">
                        <span class="input-icon">
                            <i data-lucide="user" style="width: 16px; height: 16px;"></i>
                        </span>
                        <input type="text" name="identity" required placeholder="Masukkan username atau email"
                            class="input" style="border-radius: var(--radius-xl); padding-left: 40px;">
                    </div>
                </div>

                <div class="form-group">
                    <div class="flex-row justify-between" style="margin-bottom: 4px;">
                        <label class="form-label" style="margin-bottom: 0;">Password</label>
                        <a href="<?= base_url('auth/forgot_password'); ?>" class="text-micro c-subtle transition-colors">Lupa Password?</a>
                    </div>
                    <div class="input-icon-wrapper">
                        <span class="input-icon">
                            <i data-lucide="lock" style="width: 16px; height: 16px;"></i>
                        </span>
                        <input type="password" id="password-field" name="password" required placeholder="••••••••"
                            class="input" style="border-radius: var(--radius-xl); padding-left: 40px; padding-right: 40px;">
                        <button type="button" onclick="togglePasswordVisibility()" style="position: absolute; top: 0; right: 0; bottom: 0; padding-right: 12px; display: flex; align-items: center; color: var(--text-subtle);">
                            <i id="password-toggle-icon" data-lucide="eye" style="width: 16px; height: 16px;"></i>
                        </button>
                    </div>
                </div>

                <div class="flex-row" style="padding-top: 4px;">
                    <label class="flex-row select-none cursor-pointer">
                        <input type="checkbox" name="remember" value="1" 
                            style="width: 16px; height: 16px; accent-color: var(--color-primary); cursor: pointer; border-radius: 4px;">
                        <span class="text-micro c-muted" style="margin-left: 8px; font-weight: 500;">Ingat Saya (1 Bulan)</span>
                    </label>
                </div>

                <button type="submit" 
                    class="btn btn-primary w-full" style="padding: 10px 16px; box-shadow: var(--shadow-glow-red); margin-top: 8px;">
                    <span>Masuk ke Paddock</span>
                    <i data-lucide="arrow-right" style="width: 16px; height: 16px;"></i>
                </button>
            </form>

            <div class="auth-divider">
                <span>Atau</span>
            </div>

            <a href="<?= base_url('auth/google_login'); ?>" class="btn-google">
                <svg style="width: 16px; height: 16px;" class="flex-shrink-0" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                    <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                    <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z" fill="#FBBC05"/>
                    <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z" fill="#EA4335"/>
                </svg>
                <span>Lanjutkan dengan Google</span>
            </a>

            <div class="text-center text-small c-muted border-t" style="margin-top: 24px; padding-top: 16px;">
                Belum punya akun? 
                <a href="<?= base_url('auth/register'); ?>" class="c-primary font-semibold" style="text-decoration: underline;">Daftar Sekarang</a>
            </div>

        </div>
    </main>

    <footer class="py-4 text-center text-micro c-faint" style="letter-spacing: 0.05em;">
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
