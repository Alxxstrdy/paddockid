<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? $title : 'Tentang | PaddockID'; ?></title>
    <meta name="description" content="<?= isset($meta_description) ? $meta_description : 'PaddockID adalah komunitas Formula 1 Indonesia.'; ?>">
    <?php if (isset($page_url)): ?>
    <link rel="canonical" href="<?= $page_url; ?>">
    <?php endif; ?>
    <meta property="og:title" content="Tentang PaddockID">
    <meta property="og:description" content="Komunitas Formula 1 Indonesia — berbagi momen, berdiskusi, dan mengikuti balapan bersama.">
    <meta property="og:url" content="<?= base_url('about'); ?>">
    <meta name="theme-color" content="#090c13">
    <link rel="icon" href="<?= assets_url('Icon.svg'); ?>">
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Syne:wght@700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-body: #090c13;
            --bg-surface: #10141e;
            --bg-surface-raised: #161b28;
            --bg-surface-subtle: rgba(255, 255, 255, 0.025);
            --border-default: #232a3a;
            --border-subtle: rgba(255, 255, 255, 0.06);
            --text-primary: #eceef4;
            --text-secondary: #c3c9d7;
            --text-muted: #8d95a7;
            --text-subtle: #677084;
            --text-faint: #474f61;
            --color-primary: #2f6bff;
            --color-primary-hover: #4d86ff;
            --color-primary-bg: rgba(47, 107, 255, 0.12);
            --color-primary-bg-hover: rgba(47, 107, 255, 0.18);
            --color-primary-border: rgba(47, 107, 255, 0.3);
            --color-danger: #ef4a55;
            --radius-lg: 16px;
            --radius-xl: 22px;
            --radius-md: 10px;
            --font-heading: "Syne", sans-serif;
            --transition-fast: 0.2s ease;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body {
            font-family: "Plus Jakarta Sans", system-ui, sans-serif;
            background: var(--bg-body);
            color: var(--text-secondary);
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            -webkit-font-smoothing: antialiased;
        }
        a { color: var(--color-primary); text-decoration: none; }

        /* ---------- NAV (standalone) ---------- */
        .anav {
            position: sticky;
            top: 0;
            z-index: 50;
            background: rgba(9, 12, 19, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border-subtle);
        }
        .anav__in {
            max-width: 1240px;
            margin: 0 auto;
            padding: 12px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }
        .anav__brand { display: flex; align-items: center; gap: 11px; text-decoration: none; }
        .anav__brand img { height: 32px; width: auto; object-fit: contain; }
        .anav__brand span {
            font-family: var(--font-heading);
            font-weight: 800;
            font-size: 15px;
            color: var(--text-primary);
            letter-spacing: -0.01em;
        }
        .anav__links { display: flex; align-items: center; gap: 22px; }
        .anav__links a {
            font-size: 12.5px;
            font-weight: 600;
            color: var(--text-muted);
            transition: color var(--transition-fast);
        }
        .anav__links a:hover { color: var(--text-primary); }
        .anav__cta {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: var(--radius-md);
            background: var(--color-primary);
            color: #fff;
            font-size: 12.5px;
            font-weight: 700;
            transition: background var(--transition-fast), transform var(--transition-fast);
        }
        .anav__cta:hover { background: var(--color-primary-hover); transform: translateY(-1px); }
        .anav__cta :is(i, svg) { width: 14px; height: 14px; }
        @media (max-width: 640px) {
            .anav__links { display: none; }
        }

        /* ---------- HERO ---------- */
        .ahero {
            position: relative;
            overflow: hidden;
            padding: 88px 24px 72px;
        }
        .ahero::before {
            content: "";
            position: absolute;
            top: -160px;
            right: -140px;
            width: 520px;
            height: 520px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(47, 107, 255, 0.16), transparent 68%);
            pointer-events: none;
        }
        .ahero::after {
            content: "";
            position: absolute;
            bottom: -140px;
            left: -160px;
            width: 480px;
            height: 480px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(239, 74, 85, 0.09), transparent 66%);
            pointer-events: none;
        }
        .ahero__in {
            position: relative;
            max-width: 820px;
            margin: 0 auto;
            text-align: center;
        }
        .ahero__badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 7px 14px;
            border-radius: 999px;
            background: var(--color-primary-bg);
            border: 1px solid var(--color-primary-border);
            color: var(--color-primary-hover);
            font-size: 11.5px;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }
        .ahero__badge :is(i, svg) { width: 13px; height: 13px; }
        .ahero h1 {
            margin-top: 24px;
            font-family: var(--font-heading);
            font-weight: 800;
            font-size: clamp(2.1rem, 6vw, 3.6rem);
            line-height: 1.05;
            letter-spacing: -0.02em;
            color: var(--text-primary);
        }
        .ahero h1 span { color: var(--color-primary); }
        .ahero p {
            margin-top: 20px;
            max-width: 620px;
            margin-left: auto;
            margin-right: auto;
            font-size: 15px;
            line-height: 1.75;
            color: var(--text-muted);
        }
        .ahero__acts {
            margin-top: 30px;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 12px;
        }
        .abtn {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            padding: 12px 22px;
            border-radius: var(--radius-md);
            font-size: 13.5px;
            font-weight: 700;
            transition: transform var(--transition-fast), background var(--transition-fast);
        }
        .abtn :is(i, svg) { width: 15px; height: 15px; }
        .abtn--primary { background: var(--color-primary); color: #fff; }
        .abtn--primary:hover { background: var(--color-primary-hover); transform: translateY(-1px); }
        .abtn--ghost {
            background: var(--bg-surface-raised);
            color: var(--text-secondary);
            border: 1px solid var(--border-default);
        }
        .abtn--ghost:hover { color: var(--text-primary); border-color: var(--color-primary-border); }

        /* ---------- SEKSI ---------- */
        .asec { padding: 74px 24px; }
        .asec--alt { background: var(--bg-surface); }
        .asec__in { max-width: 1140px; margin: 0 auto; }
        .asec__tag {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: var(--color-primary);
        }
        .asec__tag :is(i, svg) { width: 13px; height: 13px; }
        .asec__head { max-width: 720px; margin-bottom: 40px; }
        .asec__head h2 {
            margin-top: 14px;
            font-family: var(--font-heading);
            font-size: clamp(1.5rem, 3.4vw, 2.2rem);
            font-weight: 800;
            line-height: 1.15;
            letter-spacing: -0.015em;
            color: var(--text-primary);
        }
        .asec__head p {
            margin-top: 14px;
            font-size: 14px;
            line-height: 1.75;
            color: var(--text-muted);
        }

        /* ---------- KARTU FITUR ---------- */
        .agrid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 16px; }
        .acard {
            display: flex;
            flex-direction: column;
            gap: 14px;
            padding: 26px 22px;
            border-radius: var(--radius-xl);
            background: var(--bg-surface-raised);
            border: 1px solid var(--border-subtle);
            transition: transform var(--transition-fast), border-color var(--transition-fast);
        }
        .acard:hover { transform: translateY(-3px); border-color: var(--color-primary-border); }
        .acard__icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: var(--color-primary-bg);
            color: var(--color-primary);
        }
        .acard__icon :is(i, svg) { width: 20px; height: 20px; }
        .acard h3 { font-size: 15px; font-weight: 700; color: var(--text-primary); }
        .acard p { font-size: 13px; line-height: 1.7; color: var(--text-muted); }

        /* ---------- ANGKA / STATISTIK ---------- */
        .anums {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 18px;
            margin-top: 8px;
        }
        .anum { text-align: center; padding: 22px 12px; }
        .anum__val {
            font-family: var(--font-heading);
            font-size: clamp(1.8rem, 4vw, 2.6rem);
            font-weight: 800;
            color: var(--text-primary);
            line-height: 1;
        }
        .anum__val sup { color: var(--color-primary); font-size: 1rem; }
        .anum__lbl { margin-top: 8px; font-size: 12px; color: var(--text-subtle); }

        /* ---------- EDITORIAL / CERITA ---------- */
        .astory { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; align-items: start; }
        @media (max-width: 760px) { .astory { grid-template-columns: 1fr; } }
        .astory__lead {
            font-size: 17px;
            line-height: 1.75;
            color: var(--text-secondary);
        }
        .astory__lead strong { color: var(--text-primary); }
        .astory__body { font-size: 13.5px; line-height: 1.8; color: var(--text-muted); }
        .astory__body p + p { margin-top: 12px; }

        /* ---------- PRINSIP ---------- */
        .aprinc {
            display: flex;
            gap: 14px;
            padding: 20px 0;
            border-bottom: 1px solid var(--border-subtle);
        }
        .aprinc:last-of-type { border-bottom: none; }
        .aprinc__icon {
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 12px;
            color: var(--color-primary);
            background: var(--color-primary-bg);
        }
        .aprinc__icon :is(i, svg) { width: 18px; height: 18px; }
        .aprinc h3 { font-size: 14.5px; font-weight: 700; color: var(--text-primary); }
        .aprinc p { margin-top: 4px; font-size: 13px; line-height: 1.7; color: var(--text-muted); }

        /* ---------- QUOTE ---------- */
        .aquote {
            max-width: 860px;
            margin: 0 auto;
            text-align: center;
            padding: 20px 0;
        }
        .aquote blockquote {
            font-family: var(--font-heading);
            font-size: clamp(1.3rem, 3vw, 1.9rem);
            font-weight: 800;
            line-height: 1.35;
            letter-spacing: -0.015em;
            color: var(--text-primary);
        }
        .aquote blockquote span { color: var(--color-primary); }
        .aquote cite {
            display: block;
            margin-top: 18px;
            font-style: normal;
            font-size: 12.5px;
            color: var(--text-subtle);
        }

        /* ---------- CTA BESAR ---------- */
        .acta {
            position: relative;
            overflow: hidden;
            text-align: center;
            padding: 72px 24px;
            border-top: 1px solid var(--border-subtle);
        }
        .acta::before {
            content: "";
            position: absolute;
            top: -120px;
            left: 50%;
            transform: translateX(-50%);
            width: 600px;
            height: 300px;
            background: radial-gradient(ellipse, rgba(47, 107, 255, 0.16), transparent 70%);
            pointer-events: none;
        }
        .acta h2 {
            position: relative;
            font-family: var(--font-heading);
            font-size: clamp(1.7rem, 4vw, 2.6rem);
            font-weight: 800;
            line-height: 1.1;
            letter-spacing: -0.02em;
            color: var(--text-primary);
        }
        .acta p { position: relative; margin-top: 14px; font-size: 14px; color: var(--text-muted); }
        .acta .abtn { position: relative; margin-top: 26px; }

        /* ---------- FOOTER ---------- */
        .afoot { border-top: 1px solid var(--border-subtle); padding: 44px 24px 52px; background: var(--bg-surface); }
        .afoot__in { max-width: 1140px; margin: 0 auto; }
        .afoot__grid { display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 30px; }
        @media (max-width: 760px) { .afoot__grid { grid-template-columns: 1fr 1fr; } }
        .afoot__brand { display: flex; align-items: center; gap: 10px; text-decoration: none; }
        .afoot__brand img { height: 28px; width: auto; }
        .afoot__brand span { font-family: var(--font-heading); font-weight: 800; color: var(--text-primary); font-size: 13px; }
        .afoot__desc { margin-top: 12px; font-size: 12px; line-height: 1.7; color: var(--text-subtle); max-width: 280px; }
        .afoot h4 { font-size: 11.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-faint); margin-bottom: 14px; }
        .afoot__links { display: flex; flex-direction: column; gap: 10px; }
        .afoot__links a { font-size: 12.5px; color: var(--text-muted); transition: color var(--transition-fast); }
        .afoot__links a:hover { color: var(--color-primary); }
        .afoot__copy { margin-top: 40px; padding-top: 22px; border-top: 1px solid var(--border-subtle); font-size: 11px; color: var(--text-faint); text-align: center; }
    </style>
</head>
<body>

    <!-- NAV -->
    <nav class="anav">
        <div class="anav__in">
            <a class="anav__brand" href="<?= base_url(); ?>">
                <img src="<?= assets_url('Logo.svg'); ?>" alt="PaddockID">
            </a>
            <div class="anav__links">
                <a href="#tentang">Tentang</a>
                <a href="#fitur">Fitur</a>
                <a href="#prinsip">Prinsip</a>
                <a href="<?= base_url('contact'); ?>">Kontak</a>
            </div>
            <a class="anav__cta" href="<?= base_url('auth/register'); ?>">
                <i data-lucide="user-plus"></i> Daftar
            </a>
        </div>
    </nav>

    <!-- HERO -->
    <header class="ahero">
        <div class="ahero__in">
            <h1>Tempat para <span>Formula 1 Fans</span> Indonesia berkumpul.</h1>
            <p>
                PaddockID adalah wadah digital untuk fans F1 di Indonesiaopen berbagi momen, berdiskusi,
                dan mengikuti setiap seri balapan bersama. Bebas iklan, dikelola penggemar, untuk penggemar.
            </p>
            <div class="ahero__acts">
                <a class="abtn abtn--primary" href="<?= base_url('auth/register'); ?>">
                    <i data-lucide="user-plus"></i> Daftar Gratis
                </a>
                <a class="abtn abtn--ghost" href="#fitur">
                    <i data-lucide="sparkles"></i> Lihat Fitur
                </a>
            </div>
        </div>
    </header>

    <!-- APA YANG KAMI BANGUN -->
    <section class="asec asec--alt" id="tentang">
        <div class="asec__in">
            <div class="astory">
                <div>
                    <span class="asec__tag"><i data-lucide="flag"></i> Apa yang kami bangun</span>
                    <p class="astory__lead" style="margin-top:16px">
                        <strong>Seiring bertumbuhnya fans F1 di Indonesia, makin banyak yang mencari
                        tempat untuk berbagi.</strong> PaddockID hadir untuk menjadi rumah digital mereka —
                        dari yang baru menonton seri pertama sampai yang hafal catatan pit lap tiap pembalap.
                    </p>
                </div>
                <div class="astory__body">
                    <p>
                        Kami percaya pengalaman menonton Formula 1 terasa lebih seru ketika dibagikan.
                        Karena itu kami membangun "paddock" sungguhan di dunia maya: tempat kamu memposting
                        foto dan cerita, berkomentar tanpa drama, saling follow, berkirim pesan pribadi,
                        ikut chat room tiap sesi, sampai memantau jadwal, klasemen, dan hasil balapan lewat
                        data Simrace.
                    </p>
                    <p>
                        Seluruhnya dibangun sebagai social hub sederhana berbasis web — ringan, cepat, dan
                        tanpa iklan. Tidak ada algoritma yang menjual perhatianmu; yang ada cuma komunitas
                        yang benar-benar suka balapan.
                    </p>
                </div>
            </div>

            <div class="anums" style="margin-top:56px">
                <div class="anum">
                    <div class="anum__val">0<sup>%</sup></div>
                    <div class="anum__lbl">Uang untuk iklan</div>
                </div>
                <div class="anum">
                    <div class="anum__val">100<sup>%</sup></div>
                    <div class="anum__lbl">Gratis untuk semua</div>
                </div>
                <div class="anum">
                    <div class="anum__val">24<sup>/7</sup></div>
                    <div class="anum__lbl">Siap ria bareng fans</div>
                </div>
                <div class="anum">
                    <div class="anum__val">1</div>
                    <div class="anum__lbl">Komunitas, satu paddock</div>
                </div>
            </div>
        </div>
    </section>

    <!-- FITUR -->
    <section class="asec" id="fitur">
        <div class="asec__in">
            <div class="asec__head">
                <span class="asec__tag"><i data-lucide="zap"></i> Fitur</span>
                <h2>Semua yang kamu butuhkan untuk ngobrolin balapan.</h2>
                <p>Dari membagikan momen paddock sampai mengikuti setiap lap — semua ada di satu tempat.</p>
            </div>
            <div class="agrid">
                <div class="acard">
                    <span class="acard__icon"><i data-lucide="share-2"></i></span>
                    <h3>Posting &amp; interaksi</h3>
                    <p>Bagikan foto dan cerita, like, komentari, follow, block, dan lapor konten dengan satu klik.</p>
                </div>
                <div class="acard">
                    <span class="acard__icon"><i data-lucide="trophy"></i></span>
                    <h3>Race Hub &amp; Simrace</h3>
                    <p>Jadwal, klasemen, hasil balapan, dan data Simrace — dari sirkuit pertama sampai chequered flag.</p>
                </div>
                <div class="acard">
                    <span class="acard__icon"><i data-lucide="message-circle"></i></span>
                    <h3>Chat room &amp; DM</h3>
                    <p>Diskusi realtime saat sesi berlangsung, plus pesan pribadi antar penggemar.</p>
                </div>
                <div class="acard">
                    <span class="acard__icon"><i data-lucide="bell"></i></span>
                    <h3>Notifikasi realtime</h3>
                    <p>Kabar soal like, komentar, dan pesan masuk — tanpa harus terus mengecek halaman.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- QUOTE / MISI -->
    <section class="asec asec--alt" style="padding:64px 24px">
        <div class="aquote">
            <blockquote>
                "PaddockID bukan sekadar aplikasi. Ini ruang tempat tiap <span>chequered flag</span>
                terasa lebih berkesan karena dirayakan bersama."
            </blockquote>
            <cite>— Tim di balik PaddockID</cite>
        </div>
    </section>

    <!-- PRINSIP -->
    <section class="asec" id="prinsip">
        <div class="asec__in">
            <div class="asec__head">
                <span class="asec__tag"><i data-lucide="shield-check"></i> Prinsip kami</span>
                <h2>Cara kami menjaga paddock tetap nyaman.</h2>
                <p>Komunitas aman dan sehat adalah bahan bakar utama kami.</p>
            </div>
            <div style="max-width:760px">
                <div class="aprinc">
                    <span class="aprinc__icon"><i data-lucide="shield-check"></i></span>
                    <div>
                        <h3>Privasi pengguna dijaga</h3>
                        <p>Data dikumpulkan seminimal mungkin, hanya untuk menjalankan fitur. Tidak ada profil iklan, tidak ada penjualan data.</p>
                    </div>
                </div>
                <div class="aprinc">
                    <span class="aprinc__icon"><i data-lucide="eye-off"></i></span>
                    <div>
                        <h3>Tanpa iklan &amp; pelacakan</h3>
                        <p>Tidak ada jaringan iklan atau pelacak pihak ketiga. Keberlanjutan diupayakan lewat donasi sukarela.</p>
                    </div>
                </div>
                <div class="aprinc">
                    <span class="aprinc__icon"><i data-lucide="users"></i></span>
                    <div>
                        <h3>Komunitas yang aman</h3>
                        <p>Fitur laporan dan block memastikan setiap anggota — dari penonton baru sampai veteran — merasa nyaman bergabung.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="acta">
        <h2>Mau ikut meramaikan paddock?</h2>
        <p>Bergabung gratis dan bikin akun dalam hitungan menit.</p>
        <a class="abtn abtn--primary" href="<?= base_url('auth/register'); ?>">
            <i data-lucide="user-plus"></i> Daftar Sekarang
        </a>
    </section>

    <!-- FOOTER -->
    <footer class="afoot">
        <div class="afoot__in">
            <div class="afoot__grid">
                <div>
                    <a class="afoot__brand" href="<?= base_url(); ?>">
                        <img src="<?= assets_url('Logo.svg'); ?>" alt="PaddockID">
                        <span>PaddockID</span>
                    </a>
                    <p class="afoot__desc">Komunitas Formula 1 Indonesia. Bebas iklan, dikelola oleh penggemar, dan dibuat dengan penuh semangat untuk fans.</p>
                </div>
                <div>
                    <h4>Produk</h4>
                    <nav class="afoot__links">
                        <a href="<?= base_url(); ?>">Beranda</a>
                        <a href="<?= base_url('race-hub'); ?>">Race Hub</a>
                        <a href="<?= base_url('chat'); ?>">Chat Room</a>
                        <a href="<?= base_url('search'); ?>">Cari</a>
                    </nav>
                </div>
                <div>
                    <h4>Perusahaan</h4>
                    <nav class="afoot__links">
                        <a href="<?= base_url('about'); ?>">Tentang</a>
                        <a href="<?= base_url('donate'); ?>">Dukung Kami</a>
                        <a href="<?= base_url('privacy'); ?>">Kebijakan Privasi</a>
                        <a href="<?= base_url('contact'); ?>">Kontak</a>
                    </nav>
                </div>
                <div>
                    <h4>Bantuan</h4>
                    <nav class="afoot__links">
                        <a href="<?= base_url('contact'); ?>">Hubungi Kami</a>
                        <a href="<?= base_url('auth/forgot_password'); ?>">Lupa Password</a>
                        <a href="<?= base_url('auth/register'); ?>">Buat Akun</a>
                    </nav>
                </div>
            </div>
            <p class="afoot__copy">&copy; <?= date('Y'); ?> PaddockID. Dibuat untuk fans F1 Indonesia.</p>
        </div>
    </footer>

    <script>
        if (typeof lucide !== 'undefined') lucide.createIcons();
    </script>
</body>
</html>