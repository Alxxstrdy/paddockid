<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Paddock Lost | PaddockID</title>

    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600&family=Syne:wght@700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css'); ?>">
</head>
<body style="min-height: 100vh; display: flex; flex-direction: column; justify-content: space-between;">

    <header style="padding: 24px; border-bottom: 1px solid var(--border-subtle); background: rgba(5, 7, 12, 0.1); backdrop-filter: blur(4px); width: 100%;">
        <div class="max-w-xl mx-auto flex-row justify-between items-center" style="max-width: 1152px;">
            <a href="#">
                <img src="Logo_PaddockID.png" alt="PaddockID Logo" style="height: 28px; width: auto; object-fit: contain;">
            </a>
        </div>
    </header>

    <main class="flex-1 flex-col items-center justify-center px-4 py-12 text-center relative z-10">

        <div class="absolute inset-0 flex items-center justify-center" style="pointer-events: none; opacity: 0.2; z-index: 0;">
            <div style="width: 384px; height: 384px; background: rgba(220, 38, 38, 0.1); border-radius: 50%; filter: blur(120px);"></div>
        </div>

        <div class="card rounded-2xl p-8 relative z-10" style="max-width: 448px; width: 100%; padding: 32px; box-shadow: var(--shadow-xl);">
            <div style="width: 64px; height: 64px; background: var(--color-primary-bg); border: 1px solid var(--color-primary-border); color: var(--color-primary); border-radius: var(--radius-xl); display: flex; align-items: center; justify-content: center; margin: 0 auto 24px; transform: rotate(-6deg); transition: transform 0.3s; box-shadow: 0 8px 24px rgba(220, 38, 38, 0.05);">
                <i data-lucide="triangle-alert" style="width: 32px; height: 32px;"></i>
            </div>

            <h1 style="font-family: var(--font-heading); font-size: 4rem; font-weight: 800; text-transform: uppercase; letter-spacing: -0.02em; background: linear-gradient(to bottom, #ffffff, #cbd5e1, #64748b); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; line-height: 1; margin-bottom: 8px;">
                404
            </h1>

            <h2 class="text-xs font-semibold c-primary" style="font-family: var(--font-mono); text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 16px;">
                [ BOX BOX BOX • PADDOCK LOST ]
            </h2>

            <p class="text-xs c-muted mb-8" style="line-height: 1.625;">
                Waduh, sepertinya kamu keluar lintasan. Halaman yang kamu cari tidak ditemukan atau telah dipindahkan.
            </p>

            <div class="flex-col justify-center gap-3" style="display: flex;">
                <a href="#" class="btn btn-primary" style="padding: 12px 20px;">
                    <i data-lucide="home" style="width: 16px; height: 16px;"></i>
                    <span>Kembali ke Beranda</span>
                </a>

                <button onclick="window.history.back()" class="btn btn-secondary" style="padding: 12px 20px;">
                    <i data-lucide="arrow-left" style="width: 16px; height: 16px;"></i>
                    <span>Kembali Sebelumnya</span>
                </button>
            </div>

        </div>

    </main>

    <footer class="text-center py-6" style="font-size: 10px; color: var(--text-faint); letter-spacing: 0.05em; border-top: 1px solid var(--border-subtle); background: rgba(5, 7, 12, 0.1);">
        &copy; 2026 PaddockID. Race Control Error System.
    </footer>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
