<div class="pp">

    <!-- HERO -->
    <header class="pp-hero">
        <div class="pp-hero__top">            
            <span class="pp-date">Diperbarui 30 Sep 2026</span>
        </div>

        <h1 class="pp-title">Kebijakan Privasi</h1>
        <p class="pp-lead">
            PaddockID adalah komunitas Formula 1 Indonesia. Kami mengumpulkan data sesedikit
            mungkin &mdash; hanya yang diperlukan agar fitur di situs ini bisa berjalan.
        </p>
    
    </header>

    <!-- DATA -->
    <section class="pp-section" id="data">
        <div class="pp-head">
            <span class="pp-num">01</span>
            <h2>Data yang kami kumpulkan</h2>
        </div>
        <div class="pp-list">
            <div class="pp-item">
                <span class="pp-item__icon"><i data-lucide="user-round"></i></span>
                <div class="pp-item__body">
                    <h3>Data akun</h3>
                    <p>Nama pengguna, alamat email, dan kata sandi yang disimpan dalam bentuk terenkripsi. Email dipakai untuk verifikasi akun dan pemberitahuan penting.</p>
                </div>
            </div>
            <div class="pp-item">
                <span class="pp-item__icon"><i data-lucide="image"></i></span>
                <div class="pp-item__body">
                    <h3>Konten yang kamu buat</h3>
                    <p>Postingan, komentar, foto, dan pesan. Konten ini terlihat oleh pengguna lain sesuai pengaturan privasi tiap unggahan.</p>
                </div>
            </div>
            <div class="pp-item">
                <span class="pp-item__icon"><i data-lucide="activity"></i></span>
                <div class="pp-item__body">
                    <h3>Data teknis</h3>
                    <p>Alamat IP dan jenis browser saat masuk. Ini hanya dipakai untuk keamanan dan pembatasan penyalahgunaan.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- TIDAK DILAKUKAN -->
    <section class="pp-section" id="tidak">
        <div class="pp-head">
            <span class="pp-num">02</span>
            <h2>Yang tidak kami lakukan</h2>
        </div>
        <div class="pp-list">
            <div class="pp-item pp-item--no">
                <span class="pp-item__icon"><i data-lucide="x"></i></span>
                <div class="pp-item__body">
                    <h3>Tidak ada iklan pihak ketiga</h3>
                    <p>Situs ini tidak memuat Google AdSense atau jaringan iklan apa pun. Karena itu kami tidak pernah meminta persetujuan cookie iklan.</p>
                </div>
            </div>
            <div class="pp-item pp-item--no">
                <span class="pp-item__icon"><i data-lucide="x"></i></span>
                <div class="pp-item__body">
                    <h3>Tidak menjual atau menyewakan data kamu</h3>
                    <p>Data kamu tidak pernah kami jual, sewa, atau bagikan ke pihak mana pun untuk keuntungan.</p>
                </div>
            </div>
            <div class="pp-item pp-item--no">
                <span class="pp-item__icon"><i data-lucide="x"></i></span>
                <div class="pp-item__body">
                    <h3>Tidak memakai cookie pelacakan</h3>
                    <p>Kami tidak memprofilkan kamu atau mengikuti aktivitasmu di situs lain.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- COOKIE -->
    <section class="pp-section" id="cookie">
        <div class="pp-head">
            <span class="pp-num">03</span>
            <h2>Cookie yang kami pakai</h2>
        </div>
        <p class="pp-text" style="margin-bottom:14px;">
            Hanya cookie yang diperlukan agar situs berfungsi. Tidak ada cookie iklan atau
            pelacakan pihak ketiga, jadi tidak ada tombol persetujuan yang perlu kamu klik.
        </p>
        <div class="pp-table-wrap">
            <div class="overflow-x-auto">
                <table class="pp-table">
                    <thead>
                        <tr>
                            <th>Cookie</th>
                            <th>Kegunaan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><code>paddock_session</code></td>
                            <td>Menjaga kamu tetap masuk. Berisi pengenal sesi acak, bukan data pribadi.</td>
                        </tr>
                        <tr>
                            <td><code>paddock_csrf</code></td>
                            <td>Melindungi formulir dari pengiriman palsu. Berisi token acak.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- HAK -->
    <section class="pp-section" id="hak">
        <div class="pp-head">
            <span class="pp-num">04</span>
            <h2>Hak kamu</h2>
        </div>
        <div class="pp-list">
            <div class="pp-check">
                <i data-lucide="check"></i>
                <p>Kamu bisa mengubah email, nama pengguna, dan kata sandi kapan saja di halaman <a href="<?= base_url('settings'); ?>" class="pp-link">Pengaturan</a>.</p>
            </div>
            <div class="pp-check">
                <i data-lucide="check"></i>
                <p>Kamu bisa menghapus unggahan dan komentar milikmu sendiri.</p>
            </div>
            <div class="pp-check">
                <i data-lucide="check"></i>
                <p>Kamu bisa mengajukan permintaan penghapusan akun beserta seluruh datanya lewat halaman <a href="<?= base_url('contact'); ?>" class="pp-link">Kontak</a>.</p>
            </div>
        </div>
    </section>

    <!-- PIHAK KETIGA -->
    <section class="pp-section" id="pihak-ketiga">
        <div class="pp-head">
            <span class="pp-num">05</span>
            <h2>Data F1 dari pihak ketiga</h2>
        </div>
        <p class="pp-text">
            Hasil balapan, klasemen, dan data pembalap di Race Hub diambil dari
            <a href="https://jolpi.ca" target="_blank" rel="noopener noreferrer" class="pp-link">Jolpica</a>
            (API Ergast) berlisensi CC BY-NC-SA. Kami tidak menyimpan data pembalap di luar yang
            sudah tersedia secara publik dari API tersebut.
        </p>
    </section>

    <!-- DONASI -->
    <section class="pp-section" id="donasi">
        <div class="pp-head">
            <span class="pp-num">06</span>
            <h2>Donasi</h2>
        </div>
        <p class="pp-text">
            Donasi bersifat sukarela dan diproses oleh penyedia layanan donasi pihak ketiga.
            PaddockID tidak menerima, tidak mengolah, dan tidak menyimpan data pembayaran
            apa pun &mdash; termasuk nomor kartu, PIN, atau kredensial rekening. Detail donasi
            ada di halaman <a href="<?= base_url('donate'); ?>" class="pp-link">Dukung Kami</a>.
        </p>
    </section>

    <!-- CTA -->
    <div class="pp-cta">
        <div>
            <h2>Ada pertanyaan soal privasi?</h2>
            <p>Kirim pesan ke pengelola PaddockID, kami balas dalam 1&ndash;2 hari kerja.</p>
        </div>
        <a href="<?= base_url('contact'); ?>" class="pp-cta__btn">
            <i data-lucide="mail"></i> Hubungi Kami
        </a>
    </div>

</div>
