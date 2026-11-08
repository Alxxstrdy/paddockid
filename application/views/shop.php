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
                     data-owned="<?= $is_owned ? '1' : '0' ?>"
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
                 data-owned="<?= $is_owned ? '1' : '0' ?>"
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
                 data-owned="<?= $is_owned ? '1' : '0' ?>"
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
                 data-owned="<?= $is_owned ? '1' : '0' ?>"
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

<!-- MODAL -->
<div id="detail-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0" style="background:var(--bg-overlay);" onclick="closeDetail()"></div>
    <div class="absolute inset-0 flex items-end justify-center p-0" style="padding:0;">
        <div id="detail-panel" class="w-full rounded-t-2xl overflow-hidden transition-transform" style="max-width:448px;background:var(--bg-surface);border:1px solid var(--border-default);border-radius:var(--radius-2xl);box-shadow:var(--shadow-xl);transition-duration:200ms;">
            <!-- Preview Area -->
            <div class="relative overflow-hidden" style="background:var(--bg-surface-raised);height:192px;" id="detail-preview-bg">
                <div id="detail-accent" class="absolute top-0 left-0 right-0" style="height:3px;background:var(--accent, var(--color-primary));"></div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="relative" style="width: 35%; aspect-ratio: 1;">
                        <img src="<?= assets_url('default.jpg'); ?>" alt="" class="w-full h-full rounded-full" style="object-fit:cover;" id="detail-avatar">
                        <img id="detail-image" src="" alt="" class="absolute inset-0 w-full h-full" style="object-fit:contain;transform:scale(1.25);transform-origin:center;">
                    </div>
                </div>
                <button onclick="closeDetail()" class="modal-x" aria-label="Tutup">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
            <!-- Info -->
            <div class="p-5">
                <div class="flex-row justify-between gap-3 mb-1">
                    <h3 id="detail-name" class="c-white" style="font-size:18px;font-weight:800;line-height:1.2;"></h3>
                    <div id="detail-price-wrap" class="text-right flex-shrink-0">
                        <span id="detail-price" class="coin-amount" style="font-size:16px;font-weight:800;"></span>
                        <span class="block coin-label">koin</span>
                    </div>
                </div>
                <p id="detail-category" class="text-micro mb-3"></p>
                <p id="detail-desc" class="text-small" style="line-height:1.625;margin-bottom:20px;"></p>

                <!-- Balance -->
                <div class="flex-row gap-3 rounded-lg p-3 mb-4" style="background:var(--bg-surface-raised);border:1px solid var(--border-default);">
                    <div class="rounded-full flex items-center justify-center flex-shrink-0" style="width:32px;height:32px;background:rgba(250,166,26,0.15);">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" fill="rgba(250,166,26,0.3)"/><text x="12" y="16" text-anchor="middle" font-size="12" font-weight="bold" fill="var(--color-warning)">$</text></svg>
                    </div>
                    <div>
                        <span class="block coin-label">Saldo kamu</span>
                        <strong id="detail-my-coins" class="font-bold coin-amount" style="font-size:14px;"><?= number_format($user_coins, 0, ',', '.') ?></strong>
                    </div>
                </div>

                <div id="detail-not-enough" class="hidden flex-row gap-3 rounded-lg p-3 mb-4" style="background:rgba(237,66,69,0.1);border:1px solid rgba(237,66,69,0.2);">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" fill="rgba(237,66,69,0.3)"/><text x="12" y="16" text-anchor="middle" font-size="12" font-weight="bold" fill="var(--color-danger)">!</text></svg>
                    <span class="text-small c-danger">Koin kamu tidak cukup.</span>
                </div>

                <!-- Buttons -->
                <div class="flex gap-3">
                    <button onclick="closeDetail()" class="btn-back">
                        Kembali
                    </button>
                    <button id="detail-buy-btn" class="flex-1 rounded-lg font-semibold text-white transition-all"
                            style="padding:10px 0;font-size:12px;background:var(--color-info);">
                        Beli Sekarang
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let curId = null, curPrice = 0;
let userCoins = <?= (int) $user_coins ?>;

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

function openDetail(el) {
    curId = el.dataset.id;
    curPrice = parseInt(el.dataset.price);
    const owned = el.dataset.owned === '1';
    const cat = el.dataset.cat;
    const accent = cat === 'premium' ? 'var(--color-warning)' : cat === 'team' ? 'var(--color-info)' : 'var(--color-success)';
    const bgMap = { premium: '#1f1a12', team: '#121a2c', free: '#122c1a' };

    document.getElementById('detail-name').textContent = el.dataset.name;
    document.getElementById('detail-desc').textContent = el.dataset.desc || 'Border keren buat profil kamu.';
    document.getElementById('detail-image').src = el.dataset.image;
    document.getElementById('detail-accent').style.background = accent;
    document.getElementById('detail-preview-bg').style.background = bgMap[cat] || '#1a1a1a';

    const catLabels = { premium: 'Premium Collection', team: 'Team Edition', free: 'Free' };
    document.getElementById('detail-category').textContent = catLabels[cat] || cat;

    const priceEl = document.getElementById('detail-price');
    const priceWrap = document.getElementById('detail-price-wrap');
    const buyBtn = document.getElementById('detail-buy-btn');
    const notEnough = document.getElementById('detail-not-enough');

    notEnough.classList.add('hidden');
    document.getElementById('detail-my-coins').textContent = userCoins.toLocaleString('id-ID');

    if (owned) {
        priceWrap.classList.add('hidden');
        buyBtn.textContent = 'Sudah Dimiliki';
        buyBtn.disabled = true;
        buyBtn.style.background = 'transparent';
        buyBtn.style.border = '1px solid var(--color-success)';
        buyBtn.style.color = 'var(--color-success)';
    } else if (curPrice <= 0) {
        priceWrap.classList.add('hidden');
        buyBtn.textContent = 'Ambil Gratis';
        buyBtn.disabled = false;
        buyBtn.style.background = 'var(--color-success)';
        buyBtn.style.border = 'none';
        buyBtn.style.color = '#fff';
    } else {
        priceWrap.classList.remove('hidden');
        priceEl.textContent = curPrice.toLocaleString('id-ID');
        buyBtn.textContent = 'Beli Sekarang';
        buyBtn.style.border = 'none';
        buyBtn.style.color = '#fff';

        if (userCoins < curPrice) {
            notEnough.classList.remove('hidden');
            buyBtn.disabled = true;
            buyBtn.style.background = 'transparent';
            buyBtn.style.border = '1px solid var(--border-strong)';
            buyBtn.style.color = 'var(--text-subtle)';
        } else {
            buyBtn.disabled = false;
            buyBtn.style.background = 'var(--color-info)';
            buyBtn.style.color = '#fff';
        }
    }

    document.getElementById('detail-modal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    if (typeof lucide !== 'undefined') lucide.createIcons();
}

function closeDetail() {
    document.getElementById('detail-modal').classList.add('hidden');
    document.body.style.overflow = '';
    curId = null;
}

function buy() {
    if (!curId) return;
    const btn = document.getElementById('detail-buy-btn');
    btn.disabled = true;
    btn.textContent = 'Proses...';

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
            btn.textContent = 'Berhasil!';
            btn.style.background = 'var(--color-success)';
            btn.style.color = '#fff';
            setTimeout(() => location.reload(), 1000);
        } else {
            btn.textContent = 'Beli Sekarang';
            btn.disabled = false;
            btn.style.background = 'var(--color-info)';
            btn.style.color = '#fff';
            alert(d.message);
        }
    })
    .catch(() => {
        btn.textContent = 'Beli Sekarang';
        btn.disabled = false;
        btn.style.background = 'var(--color-info)';
        btn.style.color = '#fff';
        alert('Terjadi kesalahan.');
    });
}

document.getElementById('detail-buy-btn').addEventListener('click', function() {
    if (!this.disabled) buy();
});
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeDetail(); });
</script>
