<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email | PaddockID</title>
    <link rel="icon" href="<?= assets_url('Icon.png') ?>">
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Syne:wght@700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= assets_url('css/style.css'); ?>?v=<?= filemtime(FCPATH . 'uploads/css/style.css'); ?>">
    <link rel="stylesheet" href="<?= assets_url('css/auth.css'); ?>?v=<?= filemtime(FCPATH . 'uploads/css/auth.css'); ?>">
    <style>
        body {
            background:
                radial-gradient(560px 340px at 50% -60px, rgba(47,107,255,0.14), transparent 70%),
                #090c13;
        }

        .cev-main {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 400px;
            margin: 0 auto;
        }

        .cev-card {
            background: #10141e;
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 16px;
            padding: 30px 30px 26px;
            box-shadow: 0 24px 48px -24px rgba(3,6,11,0.85);
        }

        .cev-badge {
            width: 52px;
            height: 52px;
            margin: 0 auto 18px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(47,107,255,0.12);
            border: 1px solid rgba(47,107,255,0.25);
            color: #6ea2ff;
        }
        .cev-badge i { width: 24px; height: 24px; }

        .cev-title {
            font-family: var(--font-heading);
            font-size: 18px;
            font-weight: 800;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            text-align: center;
            color: var(--text-primary);
            margin: 0 0 6px;
        }
        .cev-sub {
            font-size: 13px;
            color: var(--text-subtle);
            text-align: center;
            margin: 0 0 14px;
        }

        .cev-email-chip {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 6px 12px;
            border-radius: 999px;
            background: rgba(47,107,255,0.10);
            border: 1px solid rgba(47,107,255,0.28);
            font-family: var(--font-mono);
            font-size: 12px;
            color: #8fb4ff;
        }

        .cev-status {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 11.5px;
            color: var(--text-muted);
            margin: 14px 0 22px;
        }
        .cev-status .dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #2fbf7f;
            animation: cev-pulse 1.8s ease-out infinite;
        }
        @keyframes cev-pulse {
            0% { box-shadow: 0 0 0 0 rgba(47,191,127,0.5); }
            100% { box-shadow: 0 0 0 7px rgba(47,191,127,0); }
        }

        .cev-list { margin: 0 0 18px; padding: 0; list-style: none; }
        .cev-list li {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 0;
            border-bottom: 1px dashed rgba(255,255,255,0.06);
        }
        .cev-list li:last-child { border-bottom: 0; }
        .cev-num {
            flex-shrink: 0;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 7px;
            background: rgba(47,107,255,0.14);
            color: #6ea2ff;
            font-size: 11px;
            font-weight: 700;
            font-family: var(--font-mono);
        }
        .cev-list-text { font-size: 13px; color: var(--text-secondary); line-height: 1.5; }
        .cev-list-text b { color: var(--text-primary); font-weight: 600; }

        .cev-hint {
            text-align: center;
            font-size: 11.5px;
            color: var(--text-faint);
            line-height: 1.6;
            margin: 0 0 20px;
        }

        .cev-actions { display: flex; flex-direction: column; gap: 10px; }
        .cev-btn {
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
            transition: opacity var(--transition-fast), background-color var(--transition-fast);
        }
        .cev-btn i { width: 16px; height: 16px; }
        .cev-btn-primary {
            background: #2f6bff;
            color: #fff;
        }
        .cev-btn-primary:hover:not(:disabled) { background: #4d86ff; }
        .cev-btn-primary:disabled { opacity: 0.45; cursor: not-allowed; }
        .cev-btn-ghost {
            background: rgba(255,255,255,0.03);
            color: var(--text-muted);
            border-color: rgba(255,255,255,0.08);
        }
        .cev-btn-ghost:hover { background: rgba(255,255,255,0.06); color: var(--text-secondary); }

        .cev-resend-note {
            font-size: 11px;
            color: var(--text-faint);
            text-align: center;
            margin: 12px 0 0;
        }
        .cev-checking {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            font-size: 11px;
            color: var(--text-faint);
            margin: 14px 0 0;
        }
        .cev-checking i { width: 13px; height: 13px; animation: cev-spin 1.2s linear infinite; }
        @keyframes cev-spin { to { transform: rotate(360deg); } }
    </style>
</head>
<body style="display: flex; flex-direction: column; min-height: 100vh; justify-content: space-between;">

    <header class="p-6" style="padding-left: 48px; padding-right: 48px; position: relative; z-index: 1;">
        <a href="<?= base_url(); ?>" class="inline-block">
            <img src="<?= assets_url('Logo_PaddockID.png'); ?>" alt="PaddockID Logo" style="height: 36px; width: auto; object-fit: contain;">
        </a>
    </header>

    <main class="cev-main">
        <div class="cev-card">

            <?php if ($this->session->flashdata('success')): ?>
                <div class="mb-4 flex-row gap-2" style="background: var(--color-success-bg); border: 1px solid var(--color-success-border); color: var(--color-success); font-size: 12px; border-radius: var(--radius-lg); padding: 12px;">
                    <i data-lucide="check-circle" style="width: 16px; height: 16px;" class="flex-shrink-0"></i>
                    <span><?= $this->session->flashdata('success'); ?></span>
                </div>
            <?php endif; ?>
            <?php if ($this->session->flashdata('error')): ?>
                <div class="mb-4 flex-row gap-2" style="background: var(--color-danger-bg); border: 1px solid var(--color-danger-border); color: var(--color-danger); font-size: 12px; border-radius: var(--radius-lg); padding: 12px;">
                    <i data-lucide="alert-circle" style="width: 16px; height: 16px;" class="flex-shrink-0"></i>
                    <span><?= $this->session->flashdata('error'); ?></span>
                </div>
            <?php endif; ?>

            <h1 class="cev-title">Cek Inbox Kamu</h1>
            <p class="cev-sub">Tautan verifikasi sudah kami kirim ke</p>

            <div class="text-center">
                <span class="cev-email-chip"><i data-lucide="mail" style="width: 13px; height: 13px;"></i><span><?= htmlspecialchars($email_masked, ENT_QUOTES, 'UTF-8'); ?></span></span>
            </div>

            <div class="cev-status"><span class="dot"></span>menunggu verifikasi</div>

            <ul class="cev-list">
                <li><span class="cev-num">1</span><span class="cev-list-text">Buka <b>inbox</b>, atau folder <b>Spam/Junk</b> kalau belum ada.</span></li>
                <li><span class="cev-num">2</span><span class="cev-list-text">Klik tombol <b>Verifikasi Email</b> di dalam pesan.</span></li>
                <li><span class="cev-num">3</span><span class="cev-list-text">Kembali ke sini lalu <b>refresh</b>, kamu akan <b>diarahkan ke login</b>.</span></li>
            </ul>

            <p class="cev-hint">Tautan berlaku 24 jam &amp; sekali pakai. Posting &amp; komentar terbuka setelah email diverifikasi.</p>

            <div class="cev-actions">
                <?php echo form_open('auth/resend_verification'); ?>
                    <button type="submit" class="cev-btn cev-btn-primary" id="btn-resend" <?= $resend_remaining === 0 ? 'disabled' : ''; ?>>
                        <i data-lucide="refresh-cw"></i> Kirim Ulang Email
                    </button>
                <?php echo form_close(); ?>
                <a href="<?= base_url('auth'); ?>" class="cev-btn cev-btn-ghost">
                    <i data-lucide="log-in"></i> Sudah Verifikasi? Masuk
                </a>
            </div>

            <p class="cev-resend-note" id="resend-note" <?= $resend_remaining > 0 ? 'style="visibility:hidden;"' : ''; ?>>
                Batas kirim ulang tercapai.
                Coba lagi <?= $resend_available_at ? 'pukul <b>' . date('H:i', strtotime($resend_available_at)) . '</b>' : 'nanti'; ?>.
            </p>

            <p class="cev-checking"><i data-lucide="loader-circle"></i> Memeriksa status otomatis.</p>

        </div>
    </main>

    <footer class="p-6" style="padding-bottom: 28px; position: relative; z-index: 1;"></footer>

    <script>
        lucide.createIcons();
        (function () {
            var BASE = <?= json_encode(base_url('auth')); ?>;
            var STATUS = <?= json_encode(base_url('auth/check_email_status')); ?>;
            var btn = document.getElementById('btn-resend');
            var note = document.getElementById('resend-note');

            function fmt(av) {
                if (!av) return 'nanti';
                return 'pukul <b>' + av.slice(11, 16) + '</b>';
            }

            function render(d) {
                if (d.verified === 1) {
                    location.href = BASE;
                    return;
                }
                if (d.can_resend === 1) {
                    btn.disabled = false;
                    note.style.visibility = 'hidden';
                } else {
                    btn.disabled = true;
                    note.style.visibility = 'visible';
                    note.innerHTML = 'Batas kirim ulang tercapai. Coba lagi ' + fmt(d.next_available) + '.';
                }
            }

            function poll() {
                fetch(STATUS, { credentials: 'same-origin' })
                    .then(function (r) { return r.json(); })
                    .then(render)
                    .catch(function () {});
                window.setTimeout(poll, 4000);
            }
            window.setTimeout(poll, 4000);
        })();
    </script>
</body>
</html>