<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password | PaddockID</title>
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
                    <i data-lucide="lock" class="w-5 h-5 text-red-500"></i>
                </div>
                <h1 class="font-syne text-sm uppercase tracking-tight text-white">Lupa Password</h1>
                <p class="text-[11px] text-slate-400 mt-1">Masukkan email terdaftar untuk tautan reset.</p>
            </div>

            <?php if ($error = $this->session->flashdata('error')): ?>
                <div class="bg-red-500/10 border border-red-500/20 text-red-400 text-xs px-4 py-2.5 rounded-lg"><?= $error; ?></div>
            <?php endif; ?>

            <?php if ($success = $this->session->flashdata('success')): ?>
                <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs px-4 py-2.5 rounded-lg"><?= $success; ?></div>
            <?php endif; ?>

            <?php if ($info = $this->session->flashdata('info')): ?>
                <div class="bg-blue-500/10 border border-blue-500/20 text-blue-400 text-xs px-4 py-2.5 rounded-lg"><?= $info; ?></div>
            <?php endif; ?>

            <?php if ($reset_url = $this->session->flashdata('reset_url')): ?>
                <div class="bg-slate-800 rounded-lg p-3 text-[10px] text-slate-300 break-all border border-white/[0.06]"><?= $reset_url; ?></div>
            <?php endif; ?>

            <form action="<?= base_url('auth/send_reset_link'); ?>" method="POST">
                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                <div class="space-y-1">
                    <label class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Email</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-500"><i data-lucide="mail" class="w-3.5 h-3.5"></i></span>
                        <input type="email" name="email" required
                            class="w-full bg-slate-950/60 border border-white/[0.06] focus:border-red-500/50 rounded-xl pl-9 pr-4 py-2.5 text-xs text-slate-200 placeholder-slate-600 focus:outline-none focus:ring-1 focus:ring-red-500/20 transition-all"
                            placeholder="nama@email.com">
                    </div>
                </div>

                <button type="submit" class="w-full mt-4 bg-red-600 hover:bg-red-500 text-white font-semibold text-xs py-2.5 rounded-xl transition-all shadow-lg shadow-red-600/10 active:scale-[0.99] flex items-center justify-center gap-2">
                    <i data-lucide="send" class="w-3.5 h-3.5"></i>
                    Kirim Tautan Reset
                </button>
            </form>

            <div class="text-center pt-2">
                <a href="<?= base_url('auth'); ?>" class="text-[11px] text-slate-500 hover:text-red-400 transition-colors flex items-center justify-center gap-1">
                    <i data-lucide="arrow-left" class="w-3 h-3"></i>
                    Kembali ke Login
                </a>
            </div>
        </div>
    </div>
    <script>lucide.createIcons();</script>
</body>
</html>
