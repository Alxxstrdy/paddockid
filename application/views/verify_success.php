<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Terverifikasi | PaddockID</title>
    <link rel="icon" href="<?= assets_url('Icon.svg') ?>">
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Syne:wght@700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= assets_url('css/style.css'); ?>?v=<?= filemtime(FCPATH . 'uploads/css/style.css'); ?>">
    <link rel="stylesheet" href="<?= assets_url('css/auth.css'); ?>?v=<?= filemtime(FCPATH . 'uploads/css/auth.css'); ?>">
</head>
<body style="display: flex; flex-direction: column; min-height: 100vh; justify-content: space-between;">

    <header class="p-6" style="padding-left: 48px; padding-right: 48px; position: relative; z-index: 1;">
        <a href="<?= base_url(); ?>" class="inline-block">
            <span class="auth-logo"><img src="<?= assets_url('Logo.svg'); ?>" alt="PaddockID Logo" class="auth-logo__mark auth-logo__mark--dark"><img src="<?= assets_url('LogoHitam.svg'); ?>" alt="" aria-hidden="true" class="auth-logo__mark auth-logo__mark--light"></span>
        </a>
    </header>

    <main class="vsm-wrap">
        <div class="vsm-card">

            <div class="vsm-badge"><i data-lucide="check"></i></div>

            <h1 class="vsm-title">Email Terverifikasi</h1>
            <p class="vsm-text">
                Email telah diverifikasi.<br>
                Tab ini boleh ditutup. Kamu akan diarahkan ke halaman login.
            </p>

            <p class="vsm-checking"><i data-lucide="loader-circle"></i> Mengalihkan ke halaman login…</p>

            <div style="margin-top:20px;">
                <a href="<?= base_url('auth'); ?>" class="cev-btn cev-btn-primary" style="text-decoration:none;">
                    <i data-lucide="log-in"></i> Masuk Sekarang
                </a>
            </div>

        </div>
    </main>

    <footer class="p-6" style="padding-bottom: 28px; position: relative; z-index: 1;"></footer>

    <script>
        if (window.lucide) lucide.createIcons();
        (function () {
            var LOGIN = <?= json_encode(base_url('auth')); ?>;
            window.setTimeout(function () {
                // Coba tutup tab sendiri (hanya berhasil untuk tab yang dibuka via script).
                // Kalau ditolak browser, fallback: redirect ke login.
                try { window.close(); } catch (e) {}
                window.setTimeout(function () { location.href = LOGIN; }, 500);
            }, 3000);
        })();
    </script>
</body>
</html>