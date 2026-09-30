<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Terverifikasi | PaddockID</title>
    <link rel="icon" href="<?= assets_url('Icon.png') ?>">
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Syne:wght@700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= assets_url('css/style.css'); ?>?v=<?= filemtime(FCPATH . 'uploads/css/style.css'); ?>">
    <link rel="stylesheet" href="<?= assets_url('css/auth.css'); ?>?v=<?= filemtime(FCPATH . 'uploads/css/auth.css'); ?>">
    <style>
        body {
            background:
                radial-gradient(560px 340px at 50% -60px, rgba(47,191,127,0.12), transparent 70%),
                #090c13;
        }

        .vsm-wrap { position: relative; z-index: 1; width: 100%; max-width: 400px; margin: 0 auto; }

        .vsm-card {
            background: #10141e;
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 16px;
            padding: 30px 30px 26px;
            box-shadow: 0 24px 48px -24px rgba(3,6,11,0.85);
            text-align: center;
        }

        .vsm-badge {
            width: 52px;
            height: 52px;
            margin: 0 auto 18px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(47,191,127,0.12);
            border: 1px solid rgba(47,191,127,0.28);
            color: #2fbf7f;
        }
        .vsm-badge i { width: 26px; height: 26px; }

        .vsm-title {
            font-family: var(--font-heading);
            font-size: 18px;
            font-weight: 800;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            color: var(--text-primary);
            margin: 0 0 10px;
        }
        .vsm-text {
            font-size: 13px;
            line-height: 1.65;
            color: var(--text-subtle);
            margin: 0 0 24px;
        }
        .vsm-text b { color: var(--text-primary); font-weight: 600; }

        .vsm-actions { display: flex; flex-direction: column; gap: 10px; }
        .vsm-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            padding: 11px 16px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            border: 1px solid transparent;
            cursor: pointer;
            transition: background-color var(--transition-fast), color var(--transition-fast);
        }
        .vsm-btn i { width: 16px; height: 16px; }
        .vsm-btn-primary { background: #2f6bff; color: #fff; }
        .vsm-btn-primary:hover { background: #4d86ff; }
        .vsm-btn-ghost {
            background: rgba(255,255,255,0.03);
            color: var(--text-muted);
            border-color: rgba(255,255,255,0.08);
        }
        .vsm-btn-ghost:hover { background: rgba(255,255,255,0.06); color: var(--text-secondary); }

        .vsm-checking {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            font-size: 11px;
            color: var(--text-faint);
            margin: 14px 0 0;
        }
        .vsm-checking i { width: 13px; height: 13px; animation: vsm-spin 1.2s linear infinite; }
        @keyframes vsm-spin { to { transform: rotate(360deg); } }
    </style>
</head>
<body style="display: flex; flex-direction: column; min-height: 100vh; justify-content: space-between;">

    <header class="p-6" style="padding-left: 48px; padding-right: 48px; position: relative; z-index: 1;">
        <a href="<?= base_url(); ?>" class="inline-block">
            <img src="<?= assets_url('Logo_PaddockID.png'); ?>" alt="PaddockID Logo" style="height: 36px; width: auto; object-fit: contain;">
        </a>
    </header>

    <main class="vsm-wrap">
        <div class="vsm-card">

            <div class="vsm-badge"><i data-lucide="check"></i></div>

            <h1 class="vsm-title">Email Terverifikasi</h1>
            <p class="vsm-text">
                Email telah diverifikasi.<br>
                Kembali ke <b>halaman utama</b> untuk melakukan login.
            </p>

            <p class="vsm-checking"><i data-lucide="loader-circle"></i> Mengalihkan ke halaman utama…</p>

        </div>
    </main>

    <footer class="p-6" style="padding-bottom: 28px; position: relative; z-index: 1;"></footer>

    <script>
        lucide.createIcons();
        (function () {
            var HOME = <?= json_encode(base_url()); ?>;
            window.setTimeout(function () { location.href = HOME; }, 6000);
        })();
    </script>
</body>
</html>