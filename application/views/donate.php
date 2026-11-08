<div class="pp">

    <!-- HERO -->
    <header class="pp-hero">
        <div class="pp-hero__top">
            <span class="pp-date">Opsional &amp; sukarela</span>
        </div>

        <h1 class="pp-title">Dukung PaddockID</h1>
        <p class="pp-lead">
            PaddockID bebas iklan dan dijalankan sukarela. Donasimu menutup biaya
            server, domain, dan bandwidth API &mdash; supaya komunitas ini terus hidup.
        </p>
    </header>

    <!-- KARTU QR -->
    <section class="pp-section" id="donasi">
        <div class="pp-head">
            <span class="pp-num">01</span>
            <h2>Scan &amp; donasi</h2>
        </div>

        <div class="pp-pass-wrap">
            <div class="pp-pass">
                <div class="pp-pass__head">
                    <span class="pp-pass__brand">PaddockID</span>
                    <span class="pp-pass__type">Supporter Pass</span>
                </div>

                <div class="pp-pass__body">
                    <div class="pp-qr">
                        <!-- Saat QR siap: hapus placeholder di bawah, lalu ganti dengan
                             <img src="<?= base_url('uploads/donate/qris.png'); ?>" alt="QR donasi PaddockID"> -->
                        <i data-lucide="qr-code"></i>
                        <span>QR segera hadir</span>
                    </div>
                    <p class="pp-pass__hint">Scan pakai aplikasi e-wallet atau m-banking.</p>
                </div>

                <div class="pp-pass__perf" aria-hidden="true"></div>

                <div class="pp-pass__foot">
                    <span>Donasi sukarela</span>
                    <span class="pp-pass__stub">PADDOCK / 01</span>
                </div>
            </div>

            <?php /* Aktifkan tombol ini saat metode donasi (Saweria/Trakteer/dll) ditentukan:
            <a class="pp-btn" href="#" target="_blank" rel="noopener">
                <i data-lucide="external-link"></i> Buka halaman donasi
            </a>
            */ ?>

            <p class="pp-help">
                Belum bisa scan? <a href="<?= base_url('contact'); ?>" class="pp-link">Hubungi kami</a>.
            </p>
        </div>
    </section>

    <!-- KE MANA DANA -->
    <section class="pp-section" id="penggunaan">
        <div class="pp-head">
            <span class="pp-num">02</span>
            <h2>Ke mana dana digunakan</h2>
        </div>
        <div class="pp-list">
            <div class="pp-item">
                <span class="pp-item__icon"><i data-lucide="server"></i></span>
                <div class="pp-item__body">
                    <h3>Server &amp; hosting</h3>
                    <p>Menjaga situs tetap online dan cepat diakses dari mana saja.</p>
                </div>
            </div>
            <div class="pp-item">
                <span class="pp-item__icon"><i data-lucide="globe"></i></span>
                <div class="pp-item__body">
                    <h3>Domain</h3>
                    <p>Biaya tahunan alamat paddockid.web.id.</p>
                </div>
            </div>
            <div class="pp-item">
                <span class="pp-item__icon"><i data-lucide="database"></i></span>
                <div class="pp-item__body">
                    <h3>Data &amp; API</h3>
                    <p>Kuota dan bandwidth untuk jadwal, klasemen, serta hasil F1 di Race Hub.</p>
                </div>
            </div>
        </div>

        <div class="pp-quiet">
            <i data-lucide="info"></i>
            <p>Donasi tidak wajib. Semua fitur tetap bisa dipakai gratis tanpa membayar sepeser pun.</p>
        </div>
    </section>

    <!-- YANG TIDAK KAMI LAKUKAN -->
    <section class="pp-section" id="tidak">
        <div class="pp-head">
            <span class="pp-num">03</span>
            <h2>Yang tidak kami lakukan</h2>
        </div>
        <div class="pp-list">
            <div class="pp-item pp-item--no">
                <span class="pp-item__icon"><i data-lucide="eye-off"></i></span>
                <div class="pp-item__body">
                    <h3>Tanpa iklan</h3>
                    <p>Tidak ada iklan atau jaringan pihak ketiga di situs ini.</p>
                </div>
            </div>
            <div class="pp-item pp-item--no">
                <span class="pp-item__icon"><i data-lucide="shield-off"></i></span>
                <div class="pp-item__body">
                    <h3>Tanpa pelacakan</h3>
                    <p>Kami tidak melacak atau memprofilkan pengunjung.</p>
                </div>
            </div>
            <div class="pp-item pp-item--no">
                <span class="pp-item__icon"><i data-lucide="lock"></i></span>
                <div class="pp-item__body">
                    <h3>Tanpa data bayar</h3>
                    <p>Transaksi diproses penyedia donasi; kami tidak menyimpan data pembayaran.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <div class="pp-cta">
        <div>
            <h2>Ada pertanyaan soal donasi?</h2>
            <p>Kirim pesan ke pengelola PaddockID, kami balas dalam 1&ndash;2 hari kerja.</p>
        </div>
        <a href="<?= base_url('contact'); ?>" class="pp-cta__btn">
            <i data-lucide="mail"></i> Hubungi Kami
        </a>
    </div>

</div>
