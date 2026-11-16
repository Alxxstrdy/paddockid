<div class="flex-1 w-full mx-auto px-4 py-5" style="max-width:1024px;">

    <!-- Top Bar -->
    <div class="flex-row justify-between mb-5">
        <div class="flex-row gap-3">
            <a href="<?= base_url('borders'); ?>" class="icon-btn" aria-label="Kembali">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
            </a>
            <h1 class="text-heading text-sm c-white">Border Shop</h1>

        </div>
        <div class="coin-pill">
            <div class="coin-dot">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" fill="rgba(0,0,0,0.2)"/><text x="12" y="16" text-anchor="middle" font-size="12" font-weight="bold" fill="#fff">$</text></svg>
            </div>
            <div class="flex-col leading-none">
                <span id="user-coins" class="text-sm font-bold coin-amount"><?= number_format($user_coins, 0, ',', '.') ?></span>
                <span class="coin-label">koin</span>
            </div>
        </div>
    </div>

    <!-- Hero Banner -->
    <div class="hero-banner mb-6 p-6 flex-col items-start gap-5 relative z-10">
        <div class="flex-1">
            <h2 class="hero-title">
                Border Keren<br>Profil Kamu?
            </h2>
            <p class="hero-desc">
                Pilih border dari koleksi kami. Tampil beda di setiap postingan dan chat.
            </p>
        </div>
        <div class="relative flex-shrink-0" style="width:96px;height:96px;">
            <div class="w-full h-full rounded-full overflow-hidden" style="background:var(--bg-surface-raised);">
                <img src="<?= assets_url('default.jpg'); ?>" alt="" class="w-full h-full rounded-full" style="object-fit:cover;">
            </div>
            <?php if (!empty($borders)): ?>
                <?php 
                    // Pick a random border for the hero
                    $randomBorder = $borders[array_rand($borders)];
                ?>
                <div class="absolute inset-0 origin-center" style="transform:scale(1.4);">
                    <img src="<?= assets_url($randomBorder['image_url']); ?>" alt="" class="w-full h-full" style="object-fit:contain;">
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Category Tabs -->
    <div class="flex-row gap-1 mb-5 rounded-lg p-1 w-fit" style="background:var(--bg-surface-raised);">
        <button class="cat-tab active" onclick="filterCat('all', this)">Semua</button>
        <button class="cat-tab" onclick="filterCat('premium', this)">Premium</button>
        <button class="cat-tab" onclick="filterCat('team', this)">Team</button>
        <button class="cat-tab" onclick="filterCat('free', this)">Gratis</button>
    </div>

    <!-- Section: Semua Item -->
    <div class="mb-6" id="section-all">
        <div class="flex-row justify-between mb-3">
            <h3 class="c-white font-bold" style="font-size:14px;">Semua Border</h3>
        </div>
        <div class="section-scroll" id="scroll-all" data-sk="shop">
            <?php if (!empty($borders)): ?>
                <?php foreach ($borders as $i => $b):
                    $price_val = (int) $b['price'];
                    $is_owned = $b['owned'];
                    $category = $b['category'];
                    $accentColor = $category === 'premium' ? 'var(--color-warning)' : ($category === 'team' ? 'var(--color-info)' : 'var(--color-success)');
                ?>
                <div class="shop-card"
                     style="animation-delay: <?= $i * 0.05 ?>s; --accent: <?= $accentColor ?>;"
                     data-cat="<?= $category ?>"
                     data-id="<?= $b['id_border'] ?>"
                     data-name="<?= htmlspecialchars($b['border_name'], ENT_QUOTES, 'UTF-8') ?>"
                     data-desc="<?= htmlspecialchars($b['description'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                     data-price="<?= $price_val ?>"
                     data-image="<?= assets_url($b['image_url']) ?>"
                     data-owned="<?= $is_owned ? '1' : '0' ?>" data-equipped="<?= !empty($b['active']) ? '1' : '0' ?>"
                     onclick="openDetail(this)">
                    <div class="preview-area">
                        <div class="preview-container">
                            <div class="avatar-ring">
                                <img class="avatar-img" src="<?= assets_url('default.jpg'); ?>" alt="">
                            </div>
                            <img src="<?= assets_url($b['image_url']); ?>" alt="<?= htmlspecialchars($b['border_name']); ?>" class="border-overlay">
                        </div>
                        <div class="card-badge card-badge--<?= $category ?>">
                            <?php if ($category === 'premium'): ?>
                                <span>Premium</span>
                            <?php elseif ($category === 'team'): ?>
                                <span>Team</span>
                            <?php endif; ?>
                        </div>
                        <div class="card-owned-badge"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="3" stroke-linecap="round"><path d="M5 13l4 4L19 7"/></svg></div>
                        <div class="card-price-tag <?= $price_val <= 0 ? 'free' : '' ?>">
                            <?php if ($price_val > 0): ?><div class="coin-dot"></div><?php endif; ?>
                            <span><?= $price_val > 0 ? number_format($price_val, 0, ',', '.') : 'Free' ?></span>
                        </div>
                        <?php if (!$is_owned): ?>
                        <button class="card-buy-btn" onclick="event.stopPropagation(); openDetail(this.closest('.shop-card'))">
                            <?= $price_val > 0 ? 'Beli' : 'Ambil' ?>
                        </button>
                        <?php endif; ?>
                    </div>
                    <div class="card-info">
                        <p class="card-name"><?= htmlspecialchars($b['border_name']); ?></p>
                        <p class="card-meta"><?= $is_owned ? 'Dimiliki' : ($price_val > 0 ? number_format($price_val, 0, ',', '.') . ' koin' : 'Gratis') ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="w-full text-center py-12 text-small c-subtle">Belum ada border.</div>
            <?php endif; ?>
        </div>
    </div>

    <?php
    $premium_borders = array_filter($borders, fn($b) => $b['category'] === 'premium');
    $team_borders = array_filter($borders, fn($b) => $b['category'] === 'team');
    $free_borders = array_filter($borders, fn($b) => $b['category'] === 'free');
    ?>

    <!-- Section: Premium -->
    <?php if (!empty($premium_borders)): ?>
    <div class="mb-6" data-section="premium">
        <div class="flex-row justify-between mb-3">
            <div class="flex-row gap-2">
                <h3 class="c-white font-bold" style="font-size:14px;">Premium Collection</h3>
                <span class="badge badge-warning badge-pill"><?= count($premium_borders) ?></span>
            </div>
        </div>
        <div class="section-scroll" data-sk="shop">
            <?php foreach ($premium_borders as $i => $b):
                $price_val = (int) $b['price'];
                $is_owned = $b['owned'];
            ?>
            <div class="shop-card"
                 style="animation-delay: <?= $i * 0.05 ?>s; --accent: var(--color-warning);"
                 data-cat="premium"
                 data-id="<?= $b['id_border'] ?>"
                 data-name="<?= htmlspecialchars($b['border_name'], ENT_QUOTES, 'UTF-8') ?>"
                 data-desc="<?= htmlspecialchars($b['description'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                 data-price="<?= $price_val ?>"
                 data-image="<?= assets_url($b['image_url']) ?>"
                 data-owned="<?= $is_owned ? '1' : '0' ?>" data-equipped="<?= !empty($b['active']) ? '1' : '0' ?>"
                 onclick="openDetail(this)">
                <div class="preview-area">
                    <div class="preview-container">
                        <div class="avatar-ring">
                            <img class="avatar-img" src="<?= assets_url('default.jpg'); ?>" alt="">
                        </div>
                        <img src="<?= assets_url($b['image_url']); ?>" alt="<?= htmlspecialchars($b['border_name']); ?>" class="border-overlay">
                    </div>
                    <div class="card-badge card-badge--premium"><span>Premium</span></div>
                    <div class="card-owned-badge"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="3" stroke-linecap="round"><path d="M5 13l4 4L19 7"/></svg></div>
                    <div class="card-price-tag"><div class="coin-dot"></div><span><?= number_format($price_val, 0, ',', '.') ?></span></div>
                    <?php if (!$is_owned): ?>
                    <button class="card-buy-btn" onclick="event.stopPropagation(); openDetail(this.closest('.shop-card'))">Beli</button>
                    <?php endif; ?>
                </div>
                <div class="card-info">
                    <p class="card-name"><?= htmlspecialchars($b['border_name']); ?></p>
                    <p class="card-meta"><?= $is_owned ? 'Dimiliki' : number_format($price_val, 0, ',', '.') . ' koin' ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Section: Team Edition -->
    <?php if (!empty($team_borders)): ?>
    <div class="mb-6" data-section="team">
        <div class="flex-row justify-between mb-3">
            <div class="flex-row gap-2">
                <h3 class="c-white font-bold" style="font-size:14px;">Team Edition</h3>
                <span class="badge badge-info badge-pill"><?= count($team_borders) ?></span>
            </div>
        </div>
        <div class="section-scroll" data-sk="shop">
            <?php foreach ($team_borders as $i => $b):
                $price_val = (int) $b['price'];
                $is_owned = $b['owned'];
            ?>
            <div class="shop-card"
                 style="animation-delay: <?= $i * 0.05 ?>s; --accent: var(--color-info);"
                 data-cat="team"
                 data-id="<?= $b['id_border'] ?>"
                 data-name="<?= htmlspecialchars($b['border_name'], ENT_QUOTES, 'UTF-8') ?>"
                 data-desc="<?= htmlspecialchars($b['description'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                 data-price="<?= $price_val ?>"
                 data-image="<?= assets_url($b['image_url']) ?>"
                 data-owned="<?= $is_owned ? '1' : '0' ?>" data-equipped="<?= !empty($b['active']) ? '1' : '0' ?>"
                 onclick="openDetail(this)">
                <div class="preview-area">
                    <div class="preview-container">
                        <div class="avatar-ring">
                            <img class="avatar-img" src="<?= assets_url('default.jpg'); ?>" alt="">
                        </div>
                        <img src="<?= assets_url($b['image_url']); ?>" alt="<?= htmlspecialchars($b['border_name']); ?>" class="border-overlay">
                    </div>
                    <div class="card-badge card-badge--team"><span>Team</span></div>
                    <div class="card-owned-badge"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="3" stroke-linecap="round"><path d="M5 13l4 4L19 7"/></svg></div>
                    <div class="card-price-tag <?= $price_val <= 0 ? 'free' : '' ?>">
                        <?php if ($price_val > 0): ?><div class="coin-dot"></div><?php endif; ?>
                        <span><?= $price_val > 0 ? number_format($price_val, 0, ',', '.') : 'Free' ?></span>
                    </div>
                    <?php if (!$is_owned): ?>
                    <button class="card-buy-btn" onclick="event.stopPropagation(); openDetail(this.closest('.shop-card'))"><?= $price_val > 0 ? 'Beli' : 'Ambil' ?></button>
                    <?php endif; ?>
                </div>
                <div class="card-info">
                    <p class="card-name"><?= htmlspecialchars($b['border_name']); ?></p>
                    <p class="card-meta"><?= $is_owned ? 'Dimiliki' : ($price_val > 0 ? number_format($price_val, 0, ',', '.') . ' koin' : 'Gratis') ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Section: Gratis -->
    <?php if (!empty($free_borders)): ?>
    <div class="mb-6" data-section="free">
        <div class="flex-row justify-between mb-3">
            <div class="flex-row gap-2">
                <h3 class="c-white font-bold" style="font-size:14px;">Gratis</h3>
                <span class="badge badge-success badge-pill"><?= count($free_borders) ?></span>
            </div>
        </div>
        <div class="section-scroll" data-sk="shop">
            <?php foreach ($free_borders as $i => $b):
                $price_val = (int) $b['price'];
                $is_owned = $b['owned'];
            ?>
            <div class="shop-card"
                 style="animation-delay: <?= $i * 0.05 ?>s; --accent: var(--color-success);"
                 data-cat="free"
                 data-id="<?= $b['id_border'] ?>"
                 data-name="<?= htmlspecialchars($b['border_name'], ENT_QUOTES, 'UTF-8') ?>"
                 data-desc="<?= htmlspecialchars($b['description'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                 data-price="<?= $price_val ?>"
                 data-image="<?= assets_url($b['image_url']) ?>"
                 data-owned="<?= $is_owned ? '1' : '0' ?>" data-equipped="<?= !empty($b['active']) ? '1' : '0' ?>"
                 onclick="openDetail(this)">
                <div class="preview-area">
                    <div class="preview-container">
                        <div class="avatar-ring">
                            <img class="avatar-img" src="<?= assets_url('default.jpg'); ?>" alt="">
                        </div>
                        <img src="<?= assets_url($b['image_url']); ?>" alt="<?= htmlspecialchars($b['border_name']); ?>" class="border-overlay">
                    </div>
                    <div class="card-badge card-badge--free"><span>Free</span></div>
                    <div class="card-owned-badge"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="3" stroke-linecap="round"><path d="M5 13l4 4L19 7"/></svg></div>
                    <div class="card-price-tag free"><span>Free</span></div>
                    <?php if (!$is_owned): ?>
                    <button class="card-buy-btn" onclick="event.stopPropagation(); openDetail(this.closest('.shop-card'))">Ambil</button>
                    <?php endif; ?>
                </div>
                <div class="card-info">
                    <p class="card-name"><?= htmlspecialchars($b['border_name']); ?></p>
                    <p class="card-meta"><?= $is_owned ? 'Dimiliki' : 'Gratis' ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

</div>

<!-- MODAL DETAIL BORDER -->
<div id="detail-modal" class="border-modal hidden" role="dialog" aria-modal="true" aria-hidden="true" aria-labelledby="detail-name">
    <div class="border-modal__backdrop" onclick="closeDetail()"></div>
    <div class="border-modal__viewport">
        <div id="detail-panel" class="border-modal__panel">
            <button type="button" onclick="closeDetail()" class="border-modal__close" aria-label="Tutup detail border">
                <i data-lucide="x"></i>
            </button>

            <!-- Preview Border -->
            <div class="border-modal__preview" id="detail-preview-bg" data-cat="free">
                <div class="border-modal__accent"></div>
                <div class="border-modal__stage">
                    <div class="border-modal__avatar">
                        <img src="<?= assets_url('default.jpg'); ?>" alt="Avatar pengguna">
                        <img id="detail-image" src="" alt="">
                    </div>
                </div>
            </div>

            <!-- Informasi Border -->
            <div class="border-modal__body">
                <p id="detail-category" class="border-modal__category"></p>

                <div class="border-modal__title-row">
                    <h3 id="detail-name" class="border-modal__title"></h3>
                    <span id="detail-badge" class="border-modal__badge"></span>
                </div>

                <p id="detail-desc" class="border-modal__desc"></p>

                <div id="detail-price-wrap" class="border-modal__price hidden">
                    <span class="border-modal__price-label">Harga</span>
                    <span class="border-modal__price-value">
                        <svg class="border-modal__coin-svg" width="15" height="15" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <circle cx="12" cy="12" r="10" fill="currentColor" opacity="0.22"/>
                            <text x="12" y="16" text-anchor="middle" font-size="12" font-weight="bold" fill="currentColor">$</text>
                        </svg>
                        <span id="detail-price"></span>
                        <span class="border-modal__unit">koin</span>
                    </span>
                </div>

                <div id="detail-balance" class="border-modal__balance hidden">
                    <span class="border-modal__coin-badge" aria-hidden="true">
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none">
                            <circle cx="12" cy="12" r="10" fill="currentColor" opacity="0.25"/>
                            <text x="12" y="16" text-anchor="middle" font-size="12" font-weight="bold" fill="currentColor">$</text>
                        </svg>
                    </span>
                    <span>Saldo kamu</span>
                    <strong id="detail-my-coins" class="border-modal__balance-value"><?= number_format($user_coins, 0, ',', '.') ?></strong>
                </div>

                <div id="detail-not-enough" class="border-modal__alert hidden">
                    <span class="border-modal__alert-icon" aria-hidden="true">!</span>
                    <span>Koin kamu tidak cukup untuk border ini.</span>
                </div>

                <p id="detail-status-note" class="border-modal__note hidden"></p>

                <div class="border-modal__actions">
                    <button id="detail-buy-btn" type="button" class="border-modal__cta" data-state="purchase">
                        Beli Border
                    </button>
                    <button type="button" onclick="closeDetail()" class="border-modal__dismiss">Tutup</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let curId = null, curPrice = 0;
let curOwned = false, curEquipped = false;
let userCoins = <?= (int) $user_coins ?>;
let detailReturnFocus = null;

function filterCat(cat, btn) {
    document.querySelectorAll('.cat-tab').forEach(t => t.classList.remove('active'));
    btn.classList.add('active');
    
    document.querySelectorAll('.shop-card').forEach(c => {
        c.style.display = (cat === 'all' || c.dataset.cat === cat) ? '' : 'none';
    });
    document.querySelectorAll('[data-section]').forEach(s => {
        s.style.display = (cat === 'all' || s.dataset.section === cat) ? '' : 'none';
    });
    const allSection = document.getElementById('section-all');
    if (allSection) allSection.style.display = cat === 'all' ? '' : 'none';
}

function setDetailCta(state, label, disabled) {
    const btn = document.getElementById('detail-buy-btn');
    btn.dataset.state = state;
    btn.textContent = label;
    btn.disabled = !!disabled;
}

function renderDetailCta() {
    if (curEquipped) {
        setDetailCta('equipped', 'Sedang Digunakan', true);
    } else if (curOwned) {
        setDetailCta('owned', 'Sudah Dimiliki', true);
    } else if (curPrice <= 0) {
        setDetailCta('free', 'Ambil Border', false);
    } else if (userCoins < curPrice) {
        setDetailCta('insufficient', 'Koin Tidak Cukup', true);
    } else {
        setDetailCta('purchase', 'Beli Border', false);
    }
}

function openDetail(el) {
    curId = el.dataset.id;
    curPrice = parseInt(el.dataset.price, 10) || 0;
    curOwned = el.dataset.owned === '1';
    curEquipped = curOwned && el.dataset.equipped === '1';

    const cat = el.dataset.cat || 'free';
    const catLabels = { premium: 'Premium Collection', team: 'Team Edition', free: 'Gratis' };

    document.getElementById('detail-name').textContent = el.dataset.name;
    document.getElementById('detail-desc').textContent = el.dataset.desc || 'Border keren buat profil kamu.';
    document.getElementById('detail-category').textContent = catLabels[cat] || cat;

    const detailImage = document.getElementById('detail-image');
    detailImage.src = el.dataset.image;
    detailImage.alt = el.dataset.name + ' border';

    document.getElementById('detail-preview-bg').dataset.cat = cat;

    // Badge status: OWNED menang atas kategori.
    let badgeText = 'Free';
    let badgeTone = 'free';
    if (curOwned) {
        badgeText = 'Owned';
        badgeTone = 'owned';
    } else if (cat === 'premium') {
        badgeText = 'Limited';
        badgeTone = 'limited';
    } else if (cat === 'team') {
        badgeText = 'Team';
        badgeTone = 'team';
    }
    const badge = document.getElementById('detail-badge');
    badge.textContent = badgeText;
    badge.className = 'border-modal__badge border-modal__badge--' + badgeTone;

    // Harga & saldo hanya relevan untuk border berbayar yang belum dimiliki.
    const showPayment = !curOwned && curPrice > 0;
    document.getElementById('detail-price-wrap').classList.toggle('hidden', !showPayment);
    document.getElementById('detail-balance').classList.toggle('hidden', !showPayment);
    if (showPayment) {
        document.getElementById('detail-price').textContent = curPrice.toLocaleString('id-ID');
        document.getElementById('detail-my-coins').textContent = userCoins.toLocaleString('id-ID');
    }

    const notEnough = document.getElementById('detail-not-enough');
    notEnough.classList.toggle('hidden', !(showPayment && userCoins < curPrice));

    const note = document.getElementById('detail-status-note');
    note.classList.remove('border-modal__note--active');
    if (curEquipped) {
        note.textContent = 'Border ini sedang dipakai di profilmu.';
        note.classList.add('border-modal__note--active');
        note.classList.remove('hidden');
    } else if (curOwned) {
        note.textContent = 'Border ini sudah ada di koleksimu.';
        note.classList.remove('hidden');
    } else {
        note.textContent = '';
        note.classList.add('hidden');
    }

    renderDetailCta();

    detailReturnFocus = document.activeElement;
    const modal = document.getElementById('detail-modal');
    modal.classList.remove('hidden');
    modal.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
    if (typeof lucide !== 'undefined') lucide.createIcons();
    const closeBtn = modal.querySelector('.border-modal__close');
    if (closeBtn) closeBtn.focus({ preventScroll: true });
}

function closeDetail() {
    const modal = document.getElementById('detail-modal');
    if (modal.classList.contains('hidden')) return;
    modal.classList.add('hidden');
    modal.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
    curId = null;
    if (detailReturnFocus && typeof detailReturnFocus.focus === 'function') {
        detailReturnFocus.focus({ preventScroll: true });
    }
    detailReturnFocus = null;
}

function buy() {
    if (!curId) return;
    const btn = document.getElementById('detail-buy-btn');
    btn.disabled = true;
    btn.dataset.state = 'loading';
    btn.textContent = 'Memproses...';

    const fd = new FormData();
    fd.append('border_id', curId);
    fd.append(getCsrfTokenName(), getCsrfToken());

    fetch('<?= base_url("borders/purchase"); ?>', { method: 'POST', body: fd })
    .then(r => r.json())
    .then(d => {
        if (d.status === 'success') {
            if (d.remaining !== undefined) {
                userCoins = d.remaining;
                document.getElementById('user-coins').textContent = userCoins.toLocaleString('id-ID');
                document.getElementById('detail-my-coins').textContent = userCoins.toLocaleString('id-ID');
            }
            btn.dataset.state = 'success';
            btn.textContent = 'Berhasil!';
            btn.disabled = true;
            setTimeout(() => location.reload(), 900);
        } else {
            renderDetailCta();
            alert(d.message);
        }
    })
    .catch(() => {
        renderDetailCta();
        alert('Terjadi kesalahan.');
    });
}

document.getElementById('detail-buy-btn').addEventListener('click', function() {
    if (!this.disabled) buy();
});
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeDetail(); });
</script>
