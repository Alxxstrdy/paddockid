<div class="pp">

    <!-- HERO -->
    <header class="pp-hero">
        <div class="pp-hero__top">
            <span class="pp-date">Balasan 1&ndash;2 hari kerja</span>
        </div>

        <h1 class="pp-title">Hubungi Kami</h1>
        <p class="pp-lead">
            Punya pertanyaan, mau melaporkan konten yang tidak pantas, atau ingin menghapus data
            kamu? Isi formulir di bawah, atau kirim email langsung ke pengelola PaddockID.
        </p>
    </header>

    <!-- FORMULIR -->
    <section class="pp-section" id="kirim">
        <div class="pp-head">
            <span class="pp-num">01</span>
            <h2>Kirim pesan</h2>
        </div>

        <form id="contact-form" class="pp-form" novalidate>

            <div class="hidden" aria-hidden="true">
                <label for="website">Website</label>
                <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
            </div>

            <div class="pp-field">
                <label class="pp-label" for="c-name">Nama</label>
                <input
                    type="text" id="c-name" name="name" maxlength="80" required
                    class="pp-input"
                    placeholder="Nama kamu">
            </div>

            <div class="pp-field">
                <label class="pp-label" for="c-email">Email</label>
                <input
                    type="email" id="c-email" name="email" maxlength="120" required
                    class="pp-input"
                    placeholder="email@example.com">
                <p class="pp-hint">Kami hanya pakai ini untuk membalas pesan kamu.</p>
            </div>

            <div class="pp-field">
                <label class="pp-label" for="c-category">Kategori</label>
                
                <!-- Custom Dropdown Component -->
                <div class="pp-dropdown" role="combobox" aria-haspopup="listbox" aria-expanded="false" aria-label="Pilih kategori">
                    <input type="hidden" name="category" id="c-category" value="umum">
                    
                    <button type="button" class="pp-dropdown__trigger" aria-controls="c-category-options" aria-expanded="false" tabindex="0">
                        <span class="pp-dropdown__value">Pertanyaan umum</span>
                        <i data-lucide="chevron-down" class="pp-dropdown__icon" aria-hidden="true"></i>
                    </button>
                    
                    <ul id="c-category-options" class="pp-dropdown__menu hidden" role="listbox" tabindex="-1">
                        <li role="option" data-value="umum" class="pp-dropdown__option pp-dropdown__option--selected" aria-selected="true">Pertanyaan umum</li>
                        <li role="option" data-value="laporan" class="pp-dropdown__option" aria-selected="false">Laporan konten</li>
                        <li role="option" data-value="hapus" class="pp-dropdown__option" aria-selected="false">Permintaan penghapusan data</li>
                        <li role="option" data-value="kerja sama" class="pp-dropdown__option" aria-selected="false">Kerja sama / sponsor</li>
                    </ul>
                </div>
            </div>

            <div class="pp-field">
                <label class="pp-label" for="c-message">Pesan</label>
                <textarea
                    id="c-message" name="message" rows="6" maxlength="2000" required
                    class="pp-textarea"
                    placeholder="Tulis pesan kamu di sini (minimal 10 karakter)"></textarea>
            </div>

            <button type="submit" id="contact-submit" class="pp-submit">
                <i data-lucide="send"></i>
                <span>Kirim Pesan</span>
            </button>

            <p id="contact-status" class="pp-status" style="display:none;"></p>
        </form>
    </section>

    <!-- PENGHAPUSAN DATA -->
    <section class="pp-section" id="hapus-data">
        <div class="pp-head">
            <span class="pp-num">02</span>
            <h2>Soal penghapusan data</h2>
        </div>
        <div class="pp-list">
            <div class="pp-item">
                <span class="pp-item__icon"><i data-lucide="user-round-x"></i></span>
                <div class="pp-item__body">
                    <h3>Minta hapus akun</h3>
                    <p>Pilih kategori &ldquo;Permintaan penghapusan data&rdquo; dan sebutkan nama pengguna kamu. Untuk penghapusan akun, sertakan juga alamat email yang dipakai mendaftar supaya kami bisa memastikan request itu benar.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <div class="pp-cta">
        <div>
            <h2>Lebih suka email langsung?</h2>
            <p>Kirim pesanmu ke kotak masuk pengelola:
                <a href="mailto:<?= htmlspecialchars($contact_email ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="pp-link"><?= htmlspecialchars($contact_email ?? '', ENT_QUOTES, 'UTF-8'); ?></a>.
            </p>
        </div>
        <a href="mailto:<?= htmlspecialchars($contact_email ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="pp-cta__btn">
            <i data-lucide="mail"></i> Email Kami
        </a>
    </div>

</div>

<script>
(function () {
    const form = document.getElementById('contact-form');
    if (!form) return;

    const statusEl = document.getElementById('contact-status');
    const btn = document.getElementById('contact-submit');

    function setStatus(text, ok) {
        if (!statusEl) return;
        statusEl.textContent = text;
        statusEl.style.display = 'block';
        statusEl.style.color = ok ? 'var(--color-success,#16a34a)' : 'var(--color-danger,#ef4444)';
    }

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const name = form.name.value.trim();
        const email = form.email.value.trim();
        const message = form.message.value.trim();

        if (!name) return setStatus('Nama wajib diisi.', false);
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) return setStatus('Alamat email tidak valid.', false);
        if (message.length < 10) return setStatus('Pesan minimal 10 karakter.', false);

        btn.disabled = true;
        const original = btn.innerHTML;
        btn.innerHTML = 'Mengirim...';

        fetch('<?= base_url('page/send_message'); ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: getCsrfField()
                + '&name=' + encodeURIComponent(name)
                + '&email=' + encodeURIComponent(email)
                + '&category=' + encodeURIComponent(form.category.value)
                + '&message=' + encodeURIComponent(message)
                + '&website=' + encodeURIComponent(form.website.value)
        })
            .then(r => r.json().catch(() => ({ status: 'error', message: 'Respons server tidak valid.' })))
            .then(data => {
                if (data.status === 'success') {
                    form.reset();
                    setStatus(data.message || 'Pesan terkirim.', true);
                    btn.style.display = 'none';
                } else {
                    setStatus(data.message || 'Gagal mengirim pesan.', false);
                    btn.disabled = false;
                    btn.innerHTML = original;
                }
            })
            .catch(() => {
                setStatus('Tidak bisa menghubungi server. Periksa koneksi kamu.', false);
                btn.disabled = false;
                btn.innerHTML = original;
            });
    });
})();

// Custom Dropdown for Kategori
(function () {
    const dropdown = document.querySelector('.pp-dropdown');
    if (!dropdown) return;

    const trigger = dropdown.querySelector('.pp-dropdown__trigger');
    const menu = dropdown.querySelector('.pp-dropdown__menu');
    const hiddenInput = dropdown.querySelector('input[type="hidden"]');
    const options = dropdown.querySelectorAll('.pp-dropdown__option');
    const form = document.getElementById('contact-form');

    let isOpen = false;

    function openDropdown() {
        isOpen = true;
        dropdown.classList.add('pp-dropdown--open');
        menu.classList.remove('hidden');
        trigger.setAttribute('aria-expanded', 'true');
        
        // Focus the selected option
        const selected = menu.querySelector('[aria-selected="true"]');
        if (selected) selected.focus();
    }

    function closeDropdown() {
        isOpen = false;
        dropdown.classList.remove('pp-dropdown--open');
        menu.classList.add('hidden');
        trigger.setAttribute('aria-expanded', 'false');
    }

    function toggleDropdown() {
        if (isOpen) closeDropdown();
        else openDropdown();
    }

    function selectOption(option) {
        const value = option.dataset.value;
        const text = option.textContent;

        // Update hidden input
        hiddenInput.value = value;

        // Update trigger display
        trigger.querySelector('.pp-dropdown__value').textContent = text;

        // Update aria-selected
        options.forEach(opt => {
            opt.setAttribute('aria-selected', 'false');
            opt.classList.remove('pp-dropdown__option--selected');
        });
        option.setAttribute('aria-selected', 'true');
        option.classList.add('pp-dropdown__option--selected');

        closeDropdown();
    }

    function resetDropdown() {
        // Reset to first option (umum)
        const firstOption = options[0];
        if (firstOption) {
            selectOption(firstOption);
        }
    }

    // Trigger click
    trigger.addEventListener('click', function (e) {
        e.preventDefault();
        toggleDropdown();
    });

    // Option click
    options.forEach(option => {
        option.addEventListener('click', function () {
            selectOption(this);
        });

        // Keyboard support
        option.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                selectOption(this);
            } else if (e.key === 'Escape') {
                closeDropdown();
                trigger.focus();
            } else if (e.key === 'ArrowDown') {
                e.preventDefault();
                const next = this.nextElementSibling;
                if (next) next.focus();
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                const prev = this.previousElementSibling;
                if (prev) prev.focus();
            }
        });
    });

    // Close on outside click
    document.addEventListener('mousedown', function (e) {
        if (isOpen && !dropdown.contains(e.target)) {
            closeDropdown();
        }
    });

    // Close on Escape key
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && isOpen) {
            closeDropdown();
            trigger.focus();
        }
    });

    // Reset dropdown when form is reset
    if (form) {
        form.addEventListener('reset', function () {
            // Use setTimeout to run after the native reset completes
            setTimeout(resetDropdown, 0);
        });
    }

    // Re-render icons after dropdown content changes
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
})();
</script>
