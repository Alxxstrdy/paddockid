<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password | PaddockID</title>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Syne:wght@700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= assets_url('css/style.css'); ?>?v=<?= filemtime(FCPATH . 'uploads/css/style.css'); ?>">
    <link rel="stylesheet" href="<?= assets_url('css/auth.css'); ?>?v=<?= filemtime(FCPATH . 'uploads/css/auth.css'); ?>">
</head>
<body class="auth-page">
    <div class="w-full max-w-sm">
        <a href="<?= base_url(); ?>" class="block text-center" style="margin-bottom: 32px;">
            <img src="<?= assets_url('Logo_PaddockID.png'); ?>" alt="PaddockID" style="height: 40px; width: auto; margin: 0 auto;">
        </a>

        <div class="auth-card">
            <div class="text-center">
                <div class="section-title justify-center" style="margin-bottom: 8px;">
                    <span class="text-micro c-primary" style="letter-spacing: 0.14em;">Pemulihan Akun</span>
                </div>
                <h1 class="text-heading c-white" style="font-size: 14px;">Lupa Password</h1>
                <p class="text-caption c-muted" style="margin-top: 4px;">Masukkan email terdaftar — kami kirimkan tautan reset password ke inbox kamu.</p>
            </div>

            <?php if ($error = $this->session->flashdata('error')): ?>
                <div style="background: var(--color-danger-bg); border: 1px solid var(--color-danger-border); color: var(--color-danger); font-size: 12px; padding: 10px 16px; border-radius: var(--radius-lg);"><?= $error; ?></div>
            <?php endif; ?>

            <?php if ($success = $this->session->flashdata('success')): ?>
                <div style="background: var(--color-success-bg); border: 1px solid var(--color-success-border); color: var(--color-success); font-size: 12px; padding: 10px 16px; border-radius: var(--radius-lg);"><?= $success; ?></div>
            <?php endif; ?>

            <?php if ($info = $this->session->flashdata('info')): ?>
                <div style="background: var(--color-info-bg); border: 1px solid var(--color-info-border); color: var(--color-info); font-size: 12px; padding: 10px 16px; border-radius: var(--radius-lg);"><?= $info; ?></div>
            <?php endif; ?>

            <form action="<?= base_url('auth/send_reset_link'); ?>" method="POST">
                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <div class="input-icon-wrapper">
                        <span class="input-icon">
                            <i data-lucide="mail" style="width: 14px; height: 14px;"></i>
                        </span>
                        <input type="email" name="email" required
                            class="input" style="padding-left: 36px;"
                            placeholder="nama@email.com">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-full" style="margin-top: 16px;">
                    <i data-lucide="arrow-right" style="width: 14px; height: 14px;"></i>
                    Lanjutkan
                </button>
            </form>

            <div class="text-center" style="padding-top: 8px;">
                <a href="<?= base_url('auth'); ?>" class="text-micro c-subtle transition-colors flex-row justify-center gap-1">
                    <i data-lucide="arrow-left" style="width: 12px; height: 12px;"></i>
                    Kembali ke Login
                </a>
            </div>
        </div>
    </div>
    <script>lucide.createIcons();</script>
</body>
</html>
