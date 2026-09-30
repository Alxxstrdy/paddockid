<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 · OFF TRACK | PaddockID</title>

    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Syne:wght@700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/uploads/css/style.css?v=<?= filemtime(FCPATH . 'uploads/css/style.css'); ?>">
    <link rel="stylesheet" href="/uploads/css/error.css?v=<?= filemtime(FCPATH . 'uploads/css/error.css'); ?>">
</head>
<body class="p404">

    <div class="p404__bg" aria-hidden="true">
        <div class="p404__glow p404__glow--blue"></div>
        <div class="p404__glow p404__glow--red"></div>
        <div class="p404__floor"></div>
        <div class="p404__scan"></div>
        <div class="p404__checker"></div>
    </div>

    <div class="p404__hud p404__hud--tr">
        <div>OFF-LINE</div>
        <div class="p404__live">
            <span class="p404__dot"></span>
            <span>SIGNAL LOST</span>
        </div>
    </div>

    <main class="p404__main">

        <h1 class="p404__code" data-text="404">404</h1>

        <div class="p404__sub">Road To Nowhere</div>

        <p class="p404__msg">
            Waduh, kamu keluar lintasan &mdash; tim pit berhenti memacar. Halaman yang kamu cari nggak ketemu:
            mungkin sudah dipindah, atau memang nggak pernah lahir di garasi ini.
        </p>

        <div class="p404__actions">
            <a href="/" class="p404__btn p404__btn--primary">
                <i data-lucide="home"></i>
                <span>Kembali ke Beranda</span>
            </a>
            <button type="button" class="p404__btn" onclick="window.history.back()">
                <i data-lucide="arrow-left"></i>
                <span>Kembali Sebelumnya</span>
            </button>
        </div>
    </main>

    <div class="p404__tele" aria-hidden="true">
        <span>LAT<span class="pr">&ndash;6.20</span></span>
        <span>SPD<span class="pr">000</span></span>
        <span>ENG<span class="pr">404.0&deg;</span></span>
        <span>TIME<span class="pr" id="p404-clock">--:--:--</span></span>
        <span class="blink">REC</span>
    </div>

    <footer class="p404__foot">
        &copy; 2026 PaddockID · Race Control Error System
    </footer>

    <script>
        (function () {
            if (typeof lucide !== 'undefined') lucide.createIcons();

            var clock = document.getElementById('p404-clock');
            function tick() {
                var d = new Date();
                var p = function (n) { return (n < 10 ? '0' : '') + n; };
                if (clock) clock.textContent = p(d.getHours()) + ':' + p(d.getMinutes()) + ':' + p(d.getSeconds());
            }
            tick();
            setInterval(tick, 1000);

            var code = document.querySelector('.p404__code');
            if (code && window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                code.textContent = '404';
            }
        })();
    </script>
</body>
</html>