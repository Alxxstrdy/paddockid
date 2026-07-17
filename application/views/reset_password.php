<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | PaddockID</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Syne:wght@700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #05070c;
            background-image: radial-gradient(circle at 50% 0%, rgba(255, 24, 24, 0.04) 0%, transparent 50%);
        }
        .font-syne { font-family: 'Syne', sans-serif; }
        .glass-card {
            background: rgba(15, 22, 38, 0.4);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.04);
        }
    </style>
</head>
<body class="text-slate-100 antialiased min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-sm">
        <a href="<?= base_url(); ?>" class="block text-center mb-8">
            <img src="<?= assets_url('Logo_PaddockID.png'); ?>" alt="PaddockID" class="h-10 mx-auto">
        </a>

        <div class="glass-card rounded-2xl p-6 space-y-5">
            <div class="text-center">
                <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-red-500/10 flex items-center justify-center">
                    <i data-lucide="key-round" class="w-5 h-5 text-red-500"></i>
                </div>
                <h1 class="font-syne text-sm uppercase tracking-tight text-white">Reset Password</h1>
                <p class="text-[11px] text-slate-400 mt-1">Buat password baru untuk akun kamu.</p>
            </div>

            <?php if (!($valid ?? false)): ?>
                <div class="bg-red-500/10 border border-red-500/20 text-red-400 text-xs px-4 py-4 rounded-lg text-center">
                    <p>Tautan reset tidak valid atau sudah kedaluwarsa.</p>
                    <a href="<?= base_url('auth/forgot_password'); ?>" class="text-red-300 hover:text-red-200 underline mt-2 inline-block">Minta tautan baru</a>
                </div>
            <?php else: ?>
                <?php if ($error = $this->session->flashdata('error')): ?>
                    <div class="bg-red-500/10 border border-red-500/20 text-red-400 text-xs px-4 py-2.5 rounded-lg"><?= $error; ?></div>
                <?php endif; ?>

                <?php if ($success = $this->session->flashdata('success')): ?>
                    <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs px-4 py-2.5 rounded-lg"><?= $success; ?></div>
                <?php endif; ?>

                <form action="<?= base_url('auth/update_password_process'); ?>" method="POST">
                    <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                    <input type="hidden" name="token" value="<?= $token; ?>">

                    <div class="space-y-3">
                        <div class="space-y-1">
                            <label class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Password Baru</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-500"><i data-lucide="lock" class="w-3.5 h-3.5"></i></span>
                                <input type="password" name="password" id="new-password" required oninput="checkStrength()"
                                    class="w-full bg-slate-950/60 border border-white/[0.06] focus:border-red-500/50 rounded-xl pl-9 pr-4 py-2.5 text-xs text-slate-200 placeholder-slate-600 focus:outline-none focus:ring-1 focus:ring-red-500/20 transition-all"
                                    placeholder="Min. 8 karakter">
                            </div>
                        </div>

                        <div class="space-y-1">
                            <label class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Konfirmasi Password</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-500"><i data-lucide="lock" class="w-3.5 h-3.5"></i></span>
                                <input type="password" name="confirm_password" id="confirm-password" required
                                    class="w-full bg-slate-950/60 border border-white/[0.06] focus:border-red-500/50 rounded-xl pl-9 pr-4 py-2.5 text-xs text-slate-200 placeholder-slate-600 focus:outline-none focus:ring-1 focus:ring-red-500/20 transition-all"
                                    placeholder="Ulangi password">
                            </div>
                        </div>

                        <div id="password-strength" class="hidden text-[10px] space-y-1">
                            <div class="flex items-center gap-1.5" data-req="length"><span class="w-1.5 h-1.5 rounded-full bg-slate-600"></span> Minimal 8 karakter</div>
                            <div class="flex items-center gap-1.5" data-req="upper"><span class="w-1.5 h-1.5 rounded-full bg-slate-600"></span> Huruf besar</div>
                            <div class="flex items-center gap-1.5" data-req="lower"><span class="w-1.5 h-1.5 rounded-full bg-slate-600"></span> Huruf kecil</div>
                            <div class="flex items-center gap-1.5" data-req="digit"><span class="w-1.5 h-1.5 rounded-full bg-slate-600"></span> Angka</div>
                            <div class="flex items-center gap-1.5" data-req="symbol"><span class="w-1.5 h-1.5 rounded-full bg-slate-600"></span> Simbol (@$!%*?&)</div>
                            <div class="flex items-center gap-1.5" data-req="match"><span class="w-1.5 h-1.5 rounded-full bg-slate-600"></span> Password cocok</div>
                        </div>

                        <button type="submit" class="w-full bg-red-600 hover:bg-red-500 text-white font-semibold text-xs py-2.5 rounded-xl transition-all shadow-lg shadow-red-600/10 active:scale-[0.99]">
                            Ubah Password
                        </button>
                    </div>
                </form>
            <?php endif; ?>

            <div class="text-center pt-2">
                <a href="<?= base_url('auth'); ?>" class="text-[11px] text-slate-500 hover:text-red-400 transition-colors flex items-center justify-center gap-1">
                    <i data-lucide="arrow-left" class="w-3 h-3"></i>
                    Kembali ke Login
                </a>
            </div>
        </div>
    </div>

    <script>
        function checkStrength() {
            const pw = document.getElementById('new-password').value;
            const confirm = document.getElementById('confirm-password').value;
            const container = document.getElementById('password-strength');
            container.classList.remove('hidden');

            const checks = {
                length: pw.length >= 8,
                upper: /[A-Z]/.test(pw),
                lower: /[a-z]/.test(pw),
                digit: /\d/.test(pw),
                symbol: /[@$!%*?&]/.test(pw),
                match: pw.length > 0 && pw === confirm
            };

            container.querySelectorAll('[data-req]').forEach(el => {
                const req = el.dataset.req;
                const dot = el.querySelector('span');
                if (checks[req]) {
                    dot.className = 'w-1.5 h-1.5 rounded-full bg-emerald-500';
                    el.classList.add('text-emerald-400');
                    el.classList.remove('text-slate-500');
                } else {
                    dot.className = 'w-1.5 h-1.5 rounded-full bg-slate-600';
                    el.classList.remove('text-emerald-400');
                    el.classList.add('text-slate-500');
                }
            });
        }

        document.getElementById('confirm-password').addEventListener('input', checkStrength);
    </script>
    <script>lucide.createIcons();</script>
</body>
</html>
