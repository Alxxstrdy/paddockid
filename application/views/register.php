<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? $title : 'Daftar Akun | PaddockID'; ?></title>
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
            
            <div class="text-center mb-6">
                <h1 class="font-syne text-2xl sm:text-3xl uppercase tracking-tight text-white mb-2">
                    Create <span class="text-red-500">Account</span>
                </h1>
                <p class="text-xs text-slate-400">Bergabunglah dengan komunitas Formula 1 Indonesia</p>
            </div>

            <?php if($this->session->flashdata('error')): ?>
                <div class="mb-4 bg-red-500/10 border border-red-500/20 text-red-400 text-xs rounded-lg p-3 flex items-center gap-2">
                    <i data-lucide="alert-circle" class="w-4 h-4 flex-shrink-0"></i>
                    <span><?= $this->session->flashdata('error'); ?></span>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('auth/register_process'); ?>" method="POST" id="registerForm" class="space-y-4">
                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
    
                <!-- 1. Username -->
                <div>
                    <label class="block text-slate-400 text-[11px] uppercase tracking-wider font-semibold mb-1.5">Username</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                            <i data-lucide="at-sign" class="w-4 h-4"></i>
                        </span>
                        <input type="text" name="username" required placeholder="Contoh: sennaspeed"
                            class="w-full bg-slate-950/60 border border-white/[0.08] focus:border-red-500/50 rounded-xl pl-10 pr-4 py-2.5 text-sm text-slate-200 placeholder-slate-600 focus:outline-none focus:ring-1 focus:ring-red-500/30 transition-all">
                    </div>
                </div>

                <!-- 2. Email -->
                <div>
                    <label class="block text-slate-400 text-[11px] uppercase tracking-wider font-semibold mb-1.5">Email</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                            <i data-lucide="mail" class="w-4 h-4"></i>
                        </span>
                        <input type="email" name="email" required placeholder="nama@email.com"
                            class="w-full bg-slate-950/60 border border-white/[0.08] focus:border-red-500/50 rounded-xl pl-10 pr-4 py-2.5 text-sm text-slate-200 placeholder-slate-600 focus:outline-none focus:ring-1 focus:ring-red-500/30 transition-all">
                    </div>
                </div>

                <!-- 3. Password -->
                <div>
                    <label class="block text-slate-400 text-[11px] uppercase tracking-wider font-semibold mb-1.5">Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                            <i data-lucide="lock" class="w-4 h-4"></i>
                        </span>
                        <input type="password" id="password-field" name="password" required placeholder="••••••••"
                            class="w-full bg-slate-950/60 border border-white/[0.08] focus:border-red-500/50 rounded-xl pl-10 pr-10 py-2.5 text-sm text-slate-200 placeholder-slate-600 focus:outline-none focus:ring-1 focus:ring-red-500/30 transition-all">
                        <button type="button" onclick="toggleVisibility('password-field', 'password-toggle-icon')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-500 hover:text-slate-300">
                            <i id="password-toggle-icon" data-lucide="eye" class="w-4 h-4"></i>
                        </button>
                    </div>
                    
                    <!-- Indikator Kekuatan Password (Real-time) -->
                    <div class="mt-2 grid grid-cols-2 gap-x-2 gap-y-1 text-[10px] text-slate-500 pl-1">
                        <div id="req-length" class="flex items-center gap-1 transition-colors"><i data-lucide="circle" class="w-2.5 h-2.5"></i> Min. 8 Karakter</div>
                        <div id="req-upper" class="flex items-center gap-1 transition-colors"><i data-lucide="circle" class="w-2.5 h-2.5"></i> Huruf Besar (A-Z)</div>
                        <div id="req-lower" class="flex items-center gap-1 transition-colors"><i data-lucide="circle" class="w-2.5 h-2.5"></i> Huruf Kecil (a-z)</div>
                        <div id="req-number" class="flex items-center gap-1 transition-colors"><i data-lucide="circle" class="w-2.5 h-2.5"></i> Angka (0-9)</div>
                        <div id="req-symbol" class="flex items-center gap-1 transition-colors"><i data-lucide="circle" class="w-2.5 h-2.5"></i> Simbol (@$!%*?&)</div>
                    </div>
                </div>

                <!-- 4. Verifikasi Password -->
                <div>
                    <label class="block text-slate-400 text-[11px] uppercase tracking-wider font-semibold mb-1.5">Verifikasi Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                            <i data-lucide="shield-check" class="w-4 h-4"></i>
                        </span>
                        <input type="password" id="confirm-password-field" name="confirm_password" required placeholder="••••••••"
                            class="w-full bg-slate-950/60 border border-white/[0.08] focus:border-red-500/50 rounded-xl pl-10 pr-10 py-2.5 text-sm text-slate-200 placeholder-slate-600 focus:outline-none focus:ring-1 focus:ring-red-500/30 transition-all">
                        <button type="button" onclick="toggleVisibility('confirm-password-field', 'confirm-toggle-icon')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-500 hover:text-slate-300">
                            <i id="confirm-toggle-icon" data-lucide="eye" class="w-4 h-4"></i>
                        </button>
                    </div>
                    <p id="match-error" class="text-[10px] text-red-400 mt-1 hidden">Password tidak cocok!</p>
                </div>

                <button type="submit" id="submitBtn"
                    class="w-full bg-red-600 hover:bg-red-500 text-white font-semibold text-sm py-2.5 px-4 rounded-xl transition-all shadow-lg shadow-red-600/10 hover:shadow-red-600/20 active:scale-[0.99] flex items-center justify-center gap-2 mt-4">
                    <span>Daftar Akun</span>
                    <i data-lucide="user-plus" class="w-4 h-4"></i>
                </button>
            </form>

            <div class="mt-6 text-center text-xs text-slate-400 border-t border-white/[0.03] pt-4">
                Sudah punya akun? 
                <a href="<?= base_url('auth'); ?>" class="text-red-400 font-semibold hover:underline">Masuk Sekarang</a>
            </div>

        </div>
    </main>

    <footer class="py-4 text-center text-[10px] text-slate-600 tracking-wide">
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
                    rules[key].element.classList.remove('text-slate-500', 'text-red-400');
                    rules[key].element.classList.add('text-green-400');
                } else {
                    rules[key].element.classList.remove('text-green-400');
                    rules[key].element.classList.add('text-slate-500');
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