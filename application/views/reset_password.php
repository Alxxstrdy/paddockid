<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | PaddockID</title>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Syne:wght@700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= assets_url('css/style.css'); ?>?v=<?= filemtime(FCPATH . 'uploads/css/style.css'); ?>">
</head>
<body class="auth-page">
    <div class="w-full max-w-sm">
        <a href="<?= base_url(); ?>" class="block text-center" style="margin-bottom: 32px;">
            <img src="<?= assets_url('Logo_PaddockID.png'); ?>" alt="PaddockID" style="height: 40px; width: auto; margin: 0 auto;">
        </a>

        <div class="auth-card">
            <div class="text-center">
                <div class="mx-auto mb-3 flex-row justify-center" style="width: 48px; height: 48px; background: var(--color-primary-bg); border-radius: var(--radius-pill);">
                    <i data-lucide="key-round" class="c-primary" style="width: 20px; height: 20px;"></i>
                </div>
                <h1 class="text-heading c-white" style="font-size: 14px;">Reset Password</h1>
                <p class="text-caption c-muted" style="margin-top: 4px;">Buat password baru untuk akun kamu.</p>
            </div>

            <?php if (!($valid ?? false)): ?>
                <div class="text-center" style="background: var(--color-danger-bg); border: 1px solid var(--color-danger-border); color: var(--color-danger); font-size: 12px; padding: 16px; border-radius: var(--radius-lg);">
                    <p>Tautan reset tidak valid atau sudah kedaluwarsa.</p>
                    <a href="<?= base_url('auth/forgot_password'); ?>" class="c-primary" style="text-decoration: underline; margin-top: 8px;">Minta tautan baru</a>
                </div>
            <?php else: ?>
                <?php if ($error = $this->session->flashdata('error')): ?>
                    <div style="background: var(--color-danger-bg); border: 1px solid var(--color-danger-border); color: var(--color-danger); font-size: 12px; padding: 10px 16px; border-radius: var(--radius-lg);"><?= $error; ?></div>
                <?php endif; ?>

                <?php if ($success = $this->session->flashdata('success')): ?>
                    <div style="background: var(--color-success-bg); border: 1px solid var(--color-success-border); color: var(--color-success); font-size: 12px; padding: 10px 16px; border-radius: var(--radius-lg);"><?= $success; ?></div>
                <?php endif; ?>

                <form action="<?= base_url('auth/update_password_process'); ?>" method="POST">
                    <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                    <input type="hidden" name="token" value="<?= $token; ?>">

                    <div class="space-y-3">
                        <div class="form-group">
                            <label class="form-label">Password Baru</label>
                            <div class="input-icon-wrapper">
                                <span class="input-icon">
                                    <i data-lucide="lock" style="width: 14px; height: 14px;"></i>
                                </span>
                                <input type="password" name="password" id="new-password" required oninput="checkStrength()"
                                    class="input" style="border-radius: var(--radius-xl); padding-left: 36px;"
                                    placeholder="Min. 8 karakter">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Konfirmasi Password</label>
                            <div class="input-icon-wrapper">
                                <span class="input-icon">
                                    <i data-lucide="lock" style="width: 14px; height: 14px;"></i>
                                </span>
                                <input type="password" name="confirm_password" id="confirm-password" required
                                    class="input" style="border-radius: var(--radius-xl); padding-left: 36px;"
                                    placeholder="Ulangi password">
                            </div>
                        </div>

                        <div id="password-strength" class="hidden text-micro space-y-1">
                            <div class="flex-row gap-1" data-req="length"><span style="width: 6px; height: 6px; border-radius: 50%; background: var(--text-faint);"></span> Minimal 8 karakter</div>
                            <div class="flex-row gap-1" data-req="upper"><span style="width: 6px; height: 6px; border-radius: 50%; background: var(--text-faint);"></span> Huruf besar</div>
                            <div class="flex-row gap-1" data-req="lower"><span style="width: 6px; height: 6px; border-radius: 50%; background: var(--text-faint);"></span> Huruf kecil</div>
                            <div class="flex-row gap-1" data-req="digit"><span style="width: 6px; height: 6px; border-radius: 50%; background: var(--text-faint);"></span> Angka</div>
                            <div class="flex-row gap-1" data-req="symbol"><span style="width: 6px; height: 6px; border-radius: 50%; background: var(--text-faint);"></span> Simbol (@$!%*?&)</div>
                            <div class="flex-row gap-1" data-req="match"><span style="width: 6px; height: 6px; border-radius: 50%; background: var(--text-faint);"></span> Password cocok</div>
                        </div>

                        <button type="submit" class="btn btn-primary w-full" style="box-shadow: var(--shadow-glow-red);">
                            Ubah Password
                        </button>
                    </div>
                </form>
            <?php endif; ?>

            <div class="text-center" style="padding-top: 8px;">
                <a href="<?= base_url('auth'); ?>" class="text-micro c-subtle transition-colors flex-row justify-center gap-1">
                    <i data-lucide="arrow-left" style="width: 12px; height: 12px;"></i>
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
                    dot.style.background = 'var(--color-success)';
                    el.classList.add('c-success');
                    el.classList.remove('c-subtle');
                } else {
                    dot.style.background = 'var(--text-faint)';
                    el.classList.remove('c-success');
                    el.classList.add('c-subtle');
                }
            });
        }

        document.getElementById('confirm-password').addEventListener('input', checkStrength);
    </script>
    <script>lucide.createIcons();</script>
</body>
</html>
