<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? $title : 'Daftar Akun | PaddockID'; ?></title>
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
            
            <div class="text-center" style="margin-bottom: 24px;">
                <h1 class="auth-card__title" style="margin-bottom: 8px;">
                    Create <span class="c-primary">Account</span>
                </h1>
                <p class="text-small c-muted">Bergabunglah dengan komunitas Formula 1 Indonesia</p>
            </div>

            <?php if($this->session->flashdata('error')): ?>
                <div class="mb-4 flex-row gap-2" style="background: var(--color-danger-bg); border: 1px solid var(--color-danger-border); color: var(--color-danger); font-size: 12px; border-radius: var(--radius-lg); padding: 12px;">
                    <i data-lucide="alert-circle" style="width: 16px; height: 16px;" class="flex-shrink-0"></i>
                    <span><?= $this->session->flashdata('error'); ?></span>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('auth/register_process'); ?>" method="POST" id="registerForm" class="space-y-4">
                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
    
                <!-- 1. Username -->
                <div class="form-group">
                    <label class="form-label">Username</label>
                    <div class="input-icon-wrapper">
                        <span class="input-icon">
                            <i data-lucide="at-sign" style="width: 16px; height: 16px;"></i>
                        </span>
                        <input type="text" name="username" required placeholder="Contoh: sennaspeed"
                            class="input" style="border-radius: var(--radius-xl); padding-left: 40px;">
                    </div>
                </div>

                <!-- 2. Email -->
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <div class="input-icon-wrapper">
                        <span class="input-icon">
                            <i data-lucide="mail" style="width: 16px; height: 16px;"></i>
                        </span>
                        <input type="email" name="email" required placeholder="nama@email.com"
                            class="input" style="border-radius: var(--radius-xl); padding-left: 40px;">
                    </div>
                </div>

                <!-- 3. Password -->
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <div class="input-icon-wrapper">
                        <span class="input-icon">
                            <i data-lucide="lock" style="width: 16px; height: 16px;"></i>
                        </span>
                        <input type="password" id="password-field" name="password" required placeholder="••••••••"
                            class="input" style="border-radius: var(--radius-xl); padding-left: 40px; padding-right: 40px;">
                        <button type="button" onclick="toggleVisibility('password-field', 'password-toggle-icon')" style="position: absolute; top: 0; right: 0; bottom: 0; padding-right: 12px; display: flex; align-items: center; color: var(--text-subtle);">
                            <i id="password-toggle-icon" data-lucide="eye" style="width: 16px; height: 16px;"></i>
                        </button>
                    </div>
                    
                    <!-- Indikator Kekuatan Password (Real-time) -->
                    <div class="grid-2" style="margin-top: 8px; gap: 8px 8px; font-size: 10px; padding-left: 4px;">
                        <div id="req-length" class="flex-row gap-1 transition-colors c-subtle"><i data-lucide="circle" style="width: 10px; height: 10px;"></i> Min. 8 Karakter</div>
                        <div id="req-upper" class="flex-row gap-1 transition-colors c-subtle"><i data-lucide="circle" style="width: 10px; height: 10px;"></i> Huruf Besar (A-Z)</div>
                        <div id="req-lower" class="flex-row gap-1 transition-colors c-subtle"><i data-lucide="circle" style="width: 10px; height: 10px;"></i> Huruf Kecil (a-z)</div>
                        <div id="req-number" class="flex-row gap-1 transition-colors c-subtle"><i data-lucide="circle" style="width: 10px; height: 10px;"></i> Angka (0-9)</div>
                        <div id="req-symbol" class="flex-row gap-1 transition-colors c-subtle"><i data-lucide="circle" style="width: 10px; height: 10px;"></i> Simbol (@$!%*?&)</div>
                    </div>
                </div>

                <!-- 4. Verifikasi Password -->
                <div class="form-group">
                    <label class="form-label">Verifikasi Password</label>
                    <div class="input-icon-wrapper">
                        <span class="input-icon">
                            <i data-lucide="shield-check" style="width: 16px; height: 16px;"></i>
                        </span>
                        <input type="password" id="confirm-password-field" name="confirm_password" required placeholder="••••••••"
                            class="input" style="border-radius: var(--radius-xl); padding-left: 40px; padding-right: 40px;">
                        <button type="button" onclick="toggleVisibility('confirm-password-field', 'confirm-toggle-icon')" style="position: absolute; top: 0; right: 0; bottom: 0; padding-right: 12px; display: flex; align-items: center; color: var(--text-subtle);">
                            <i id="confirm-toggle-icon" data-lucide="eye" style="width: 16px; height: 16px;"></i>
                        </button>
                    </div>
                    <p id="match-error" class="text-micro c-danger mt-1 hidden">Password tidak cocok!</p>
                </div>

                <button type="submit" id="submitBtn"
                    class="btn btn-primary w-full" style="padding: 10px 16px; box-shadow: var(--shadow-glow-red); margin-top: 16px;">
                    <span>Daftar Akun</span>
                    <i data-lucide="user-plus" style="width: 16px; height: 16px;"></i>
                </button>
            </form>

            <div class="text-center text-small c-muted border-t" style="margin-top: 24px; padding-top: 16px;">
                Sudah punya akun? 
                <a href="<?= base_url('auth'); ?>" class="c-primary font-semibold" style="text-decoration: underline;">Masuk Sekarang</a>
            </div>

        </div>
    </main>

    <footer class="py-4 text-center text-micro c-faint" style="letter-spacing: 0.05em;">
        &copy; 2026 PaddockID. Built for Indonesian Formula 1 Enthusiasts.
    </footer>

    <script>
        lucide.createIcons();

        // 1. Fungsi Toggle Show/Hide Password Universal
        function toggleVisibility(fieldId, iconId) {
            const field = document.getElementById(fieldId);
            const icon = document.getElementById(iconId);
            if (field.type === 'password') {
                field.type = 'text';
                icon.setAttribute('data-lucide', 'eye-off');
            } else {
                field.type = 'password';
                icon.setAttribute('data-lucide', 'eye');
            }
            lucide.createIcons();
        }

        // 2. Integrasi Interaktif Validasi Password Real-Time
        const passwordInput = document.getElementById('password-field');
        const confirmInput = document.getElementById('confirm-password-field');
        const matchError = document.getElementById('match-error');
        const form = document.getElementById('registerForm');

        const rules = {
            length: { regex: /.{8,}/, element: document.getElementById('req-length') },
            upper:  { regex: /[A-Z]/,   element: document.getElementById('req-upper') },
            lower:  { regex: /[a-z]/,   element: document.getElementById('req-lower') },
            number: { regex: /[0-9]/,   element: document.getElementById('req-number') },
            symbol: { regex: /[@$!%*?&]/, element: document.getElementById('req-symbol') }
        };

        function validatePassword() {
            const val = passwordInput.value;
            let allValid = true;

            for (let key in rules) {
                const isValid = rules[key].regex.test(val);
                if (isValid) {
                    rules[key].element.classList.remove('c-subtle', 'c-danger');
                    rules[key].element.classList.add('c-success');
                } else {
                    rules[key].element.classList.remove('c-success');
                    rules[key].element.classList.add('c-subtle');
                    allValid = false;
                }
            }
            return allValid;
        }

        function validateMatch() {
            if (confirmInput.value === '') {
                matchError.classList.add('hidden');
                return false;
            }
            if (passwordInput.value !== confirmInput.value) {
                matchError.classList.remove('hidden');
                return false;
            } else {
                matchError.classList.add('hidden');
                return true;
            }
        }

        passwordInput.addEventListener('input', () => { validatePassword(); validateMatch(); });
        confirmInput.addEventListener('input', validateMatch);

        // Cek total sebelum dikirim ke backend
        form.addEventListener('submit', function(e) {
            const passValid = validatePassword();
            const matchValid = validateMatch();
            
            if (!passValid || !matchValid) {
                e.preventDefault();
                alert('Mohon penuhi seluruh kriteria keamanan password dan pastikan verifikasi cocok.');
            }
        });
    </script>
</body>
</html>
