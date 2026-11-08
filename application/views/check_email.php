<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email | PaddockID</title>
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
        if (window.lucide) lucide.createIcons();
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
                    var msg = document.querySelector('.cev-status');
                    if (msg) msg.innerHTML = '<span class="dot"></span>Email sudah terverifikasi, mengalihkan…';
                    window.setTimeout(function () { location.href = BASE; }, 1000);
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
            // Browser HP membekukan timer saat tab ditinggal (buka aplikasi email).
            // Cek ulang seketika saat tab kembali terlihat / disentuh.
            document.addEventListener('visibilitychange', function () {
                if (!document.hidden) poll();
            });
            window.addEventListener('focus', function () { poll(); });
        })();
    </script>
</body>
</html>