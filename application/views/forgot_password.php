<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password | PaddockID</title>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Syne:wght@700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= assets_url('css/style.css'); ?>">
</head>
<body class="auth-page">
    <div class="w-full max-w-sm">
        <a href="<?= base_url(); ?>" class="block text-center" style="margin-bottom: 32px;">
            <img src="<?= assets_url('Logo_PaddockID.png'); ?>" alt="PaddockID" style="height: 40px; width: auto; margin: 0 auto;">
        </a>

        <div class="auth-card">
            <div class="text-center">
                <div class="mx-auto mb-3 flex-row justify-center" style="width: 48px; height: 48px; background: var(--color-primary-bg); border-radius: var(--radius-pill);">
                    <i data-lucide="lock" class="c-primary" style="width: 20px; height: 20px;"></i>
                </div>
                <h1 class="text-heading c-white" style="font-size: 14px;">Lupa Password</h1>
                <p class="text-caption c-muted" style="margin-top: 4px;">Masukkan email terdaftar untuk tautan reset.</p>
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

            <?php if ($reset_url = $this->session->flashdata('reset_url')): ?>
                <div style="background: var(--bg-surface-raised); border-radius: var(--radius-lg); padding: 12px; font-size: 10px; color: var(--text-secondary); word-break: break-all; border: 1px solid var(--border-default);"><?= $reset_url; ?></div>
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
                            class="input" style="border-radius: var(--radius-xl); padding-left: 36px;"
                            placeholder="nama@email.com">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-full" style="margin-top: 16px; box-shadow: var(--shadow-glow-red);">
                    <i data-lucide="send" style="width: 14px; height: 14px;"></i>
                    Kirim Tautan Reset
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
