<style>
    .shop-card {
        flex: 0 0 auto;
        width: 180px;
        position: relative;
        cursor: pointer;
        border-radius: 16px;
        background: #2f3136;
        border: 1px solid #202225;
        transition: transform 0.3s cubic-bezier(0.22,1,0.36,1), border-color 0.3s, box-shadow 0.3s;
    }
    .shop-card:hover {
        transform: translateY(-6px);
        border-color: color-mix(in srgb, var(--accent) 30%, transparent);
        box-shadow: 0 12px 40px -8px rgba(0,0,0,0.5), 0 0 0 1px color-mix(in srgb, var(--accent) 15%, transparent);
    }
    .shop-card:hover .preview-area img.border-overlay {
        transform: scale(1.35);
    }
    .shop-card:nth-child(even):hover .preview-area img.border-overlay {
        transform: scale(1.35) rotate(2deg);
    }
    .shop-card:hover .avatar-ring img.avatar-img {
        box-shadow: 0 0 0 3px var(--accent), 0 0 20px -2px var(--accent);
    }
    .shop-card:hover .card-buy-btn {
        opacity: 1;
        transform: translateY(0);
    }
    .preview-area {
        position: relative;
        width: 100%;
        aspect-ratio: 1;
        background: #18191c;
        border-radius: 15px 15px 0 0;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }
    .preview-area::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 50%;
        background: linear-gradient(to top, #2f3136, transparent);
        pointer-events: none;
        z-index: 4;
    }
    .preview-area::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 120%;
        height: 120%;
        transform: translate(-50%, -50%);
        background: radial-gradient(circle, var(--accent) 0%, transparent 60%);
        opacity: 0;
        filter: blur(30px);
        transition: opacity 0.3s;
        pointer-events: none;
        z-index: 0;
    }
    .shop-card:hover .preview-area::before {
        opacity: 0.12;
    }
    .preview-container {
        position: relative;
        width: 75%;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 3;
    }
    .avatar-ring {
        position: relative;
        width: 84%;
    }
    .avatar-ring img.avatar-img {
        width: 100%;
        aspect-ratio: 1;
        border-radius: 50%;
        object-fit: cover;
        display: block;
        background: #232528;
        transition: box-shadow 0.3s;
    }
    .preview-container img.border-overlay {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: contain;
        transform: scale(1.25);
        transform-origin: center;
        transition: transform 0.4s cubic-bezier(0.22,1,0.36,1);
        z-index: 5;
        pointer-events: none;
    }
    .card-badge {
        position: absolute;
        top: 10px;
        left: 10px;
        z-index: 6;
        display: flex;
        gap: 4px;
    }
    .card-badge span {
        font-size: 9px;
        font-weight: 800;
        letter-spacing: 0.06em;
        padding: 2px 7px;
        border-radius: 4px;
        text-transform: uppercase;
    }
    .card-owned-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        z-index: 6;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: #57f287;
        display: none;
        align-items: center;
        justify-content: center;
    }
    .shop-card[data-owned="1"] .card-owned-badge {
        display: flex;
    }
    .card-price-tag {
        position: absolute;
        bottom: 10px;
        right: 10px;
        z-index: 6;
        background: rgba(0,0,0,0.65);
        backdrop-filter: blur(8px);
        border: 1px solid rgba(255,255,255,0.06);
        border-radius: 6px;
        padding: 3px 8px;
        display: flex;
        align-items: center;
        gap: 3px;
    }
    .card-price-tag .coin-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: linear-gradient(135deg, #faa61a, #f47b67);
        flex-shrink: 0;
    }
    .card-price-tag span {
        font-size: 11px;
        font-weight: 700;
        color: #faa61a;
    }
    .card-price-tag.free span {
        color: #57f287;
    }
    .card-buy-btn {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        z-index: 7;
        margin: 0 10px 10px;
        padding: 6px 0;
        border-radius: 8px;
        border: none;
        font-size: 11px;
        font-weight: 700;
        cursor: pointer;
        color: #fff;
        background: var(--accent);
        opacity: 0;
        transform: translateY(6px);
        transition: all 0.25s cubic-bezier(0.22,1,0.36,1);
    }
    .shop-card:hover .card-buy-btn {
        opacity: 1;
        transform: translateY(0);
    }
    .card-buy-btn:hover {
        filter: brightness(1.1);
    }
    .card-buy-btn:active {
        transform: scale(0.97) !important;
    }
    .card-info {
        padding: 10px 12px 12px;
        display: flex;
        flex-direction: column;
        gap: 2px;
    }
    .card-info .card-name {
        font-size: 12px;
        font-weight: 700;
        color: #dcddde;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .card-info .card-meta {
        font-size: 11px;
        color: #72767d;
    }
    .section-scroll {
        display: flex;
        gap: 12px;
        overflow-x: auto;
        scroll-snap-type: x mandatory;
        -webkit-overflow-scrolling: touch;
        padding-bottom: 8px;
        scrollbar-width: none;
    }
    .section-scroll::-webkit-scrollbar { display: none; }
    .section-scroll .shop-card { scroll-snap-align: start; }
    .hero-banner {
        position: relative;
        border-radius: 16px;
        overflow: hidden;
        background: linear-gradient(135deg, #2c1810 0%, #1a0a0a 30%, #0b0d11 70%);
    }
    .hero-banner::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 60%;
        height: 200%;
        background: radial-gradient(circle, rgba(237,66,69,0.12) 0%, transparent 70%);
        pointer-events: none;
    }
    .hero-banner::after {
        content: '';
        position: absolute;
        bottom: -30%;
        left: -10%;
        width: 40%;
        height: 160%;
        background: radial-gradient(circle, rgba(250,166,26,0.08) 0%, transparent 70%);
        pointer-events: none;
    }
    .coin-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(250,166,26,0.1);
        border: 1px solid rgba(250,166,26,0.15);
        border-radius: 999px;
        padding: 6px 14px 6px 8px;
        transition: background 0.2s;
    }
    .coin-pill:hover {
        background: rgba(250,166,26,0.18);
    }
    .coin-dot {
        width: 22px;
        height: 22px;
        border-radius: 50%;
        background: linear-gradient(135deg, #faa61a, #f47b67);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .cat-tab {
        padding: 8px 18px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        color: #8e9297;
        background: transparent;
        border: none;
        cursor: pointer;
        transition: all 0.15s;
        white-space: nowrap;
    }
    .cat-tab:hover {
        color: #dcddde;
        background: rgba(255,255,255,0.04);
    }
    .cat-tab.active {
        color: #fff;
        background: rgba(255,255,255,0.08);
    }
    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .shop-card { animation: fadeUp 0.4s cubic-bezier(0.22,1,0.36,1) both; }
</style>

<div class="flex-1 max-w-5xl w-full mx-auto px-4 py-5">

    <!-- Top Bar -->
    <div class="flex items-center justify-between mb-5">
        <div class="flex items-center gap-3">
            <a href="<?= base_url('borders'); ?>" class="w-9 h-9 flex items-center justify-center rounded-lg bg-[#2c2f33] hover:bg-[#36393f] text-[#8e9297] hover:text-white transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
            </a>
            <h1 class="text-white font-extrabold text-base tracking-tight">Border Shop</h1>

        </div>
        <div class="coin-pill">
            <div class="coin-dot">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" fill="rgba(0,0,0,0.2)"/><text x="12" y="16" text-anchor="middle" font-size="12" font-weight="bold" fill="#fff">$</text></svg>
            </div>
            <div class="flex flex-col leading-none">
                <span id="user-coins" class="text-sm font-bold text-[#faa61a]"><?= number_format($user_coins, 0, ',', '.') ?></span>
                <span class="text-[9px] text-[#8e9297]">koin</span>
            </div>
        </div>
    </div>

    <!-- Hero Banner -->
    <div class="hero-banner mb-6 p-6 sm:p-8 flex flex-col sm:flex-row items-start sm:items-center gap-5 relative z-10">
        <div class="flex-1">
            <div class="inline-flex items-center gap-1.5 bg-[#ed4245]/15 border border-[#ed4245]/20 rounded-full px-3 py-1 mb-3">
                <span class="w-1.5 h-1.5 rounded-full bg-[#ed4245] animate-pulse"></span>
                <span class="text-[10px] font-bold text-[#ed4245] uppercase tracking-wider">Hot Deal</span>
            </div>
            <h2 class="text-white font-extrabold text-xl sm:text-2xl leading-tight mb-2">
                Border Keren<br>Profil Kamu?
            </h2>
            <p class="text-[#8e9297] text-sm leading-relaxed max-w-md">
                Pilih border dari koleksi kami. Tampil beda di setiap postingan dan chat.
            </p>
        </div>
        <div class="relative w-24 h-24 sm:w-32 sm:h-32 flex-shrink-0">
            <div class="w-full h-full rounded-full bg-[#2c2f33] overflow-hidden">
                <img src="<?= assets_url('default.jpg'); ?>" alt="" class="w-full h-full object-cover rounded-full">
            </div>
            <?php if (!empty($borders)): ?>
                <?php $first = $borders[0]; ?>
                <div class="absolute inset-0 scale-[1.4] origin-center">
                    <img src="<?= assets_url($first['image_url']); ?>" alt="" class="w-full h-full object-contain">
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Category Tabs -->
    <div class="flex items-center gap-1 mb-5 bg-[#2f3136] rounded-lg p-1 w-fit">
        <button class="cat-tab active" onclick="filterCat('all', this)">Semua</button>
        <button class="cat-tab" onclick="filterCat('premium', this)">Premium</button>
        <button class="cat-tab" onclick="filterCat('team', this)">Team</button>
        <button class="cat-tab" onclick="filterCat('free', this)">Gratis</button>
    </div>

    <!-- Section: Semua Item -->
    <div class="mb-6" id="section-all">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-white font-bold text-sm">Semua Border</h3>
        </div>
        <div class="section-scroll" id="scroll-all">
            <?php if (!empty($borders)): ?>
                <?php foreach ($borders as $i => $b):
                    $price_val = (int) $b['price'];
                    $is_owned = $b['owned'];
                    $category = $b['category'];
                    $accentColor = $category === 'premium' ? '#faa61a' : ($category === 'team' ? '#5865f2' : '#57f287');
                    $gradients = ['#2c1a1a','#1a1a2c','#1a2c1a','#2c2c1a','#1a2c2c','#2c1a2c'];
                    $bg = $gradients[$i % count($gradients)];
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
                        <div class="card-badge">
                            <?php if ($category === 'premium'): ?>
                                <span style="background: rgba(250,166,26,0.15); color: #faa61a; border: 1px solid rgba(250,166,26,0.2);">Premium</span>
                            <?php elseif ($category === 'team'): ?>
                                <span style="background: rgba(88,101,242,0.15); color: #5865f2; border: 1px solid rgba(88,101,242,0.2);">Team</span>
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
                <div class="w-full text-center py-12 text-[#72767d] text-sm">Belum ada border.</div>
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
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center gap-2">
                <h3 class="text-white font-bold text-sm">Premium Collection</h3>
                <span class="bg-[#faa61a]/15 text-[#faa61a] text-[9px] font-bold px-2 py-0.5 rounded-md"><?= count($premium_borders) ?></span>
            </div>
        </div>
        <div class="section-scroll">
            <?php foreach ($premium_borders as $i => $b):
                $price_val = (int) $b['price'];
                $is_owned = $b['owned'];
            ?>
            <div class="shop-card"
                 style="animation-delay: <?= $i * 0.05 ?>s; --accent: #faa61a;"
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
                    <div class="card-badge"><span style="background: rgba(250,166,26,0.15); color: #faa61a; border: 1px solid rgba(250,166,26,0.2);">Premium</span></div>
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
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center gap-2">
                <h3 class="text-white font-bold text-sm">Team Edition</h3>
                <span class="bg-[#5865f2]/15 text-[#5865f2] text-[9px] font-bold px-2 py-0.5 rounded-md"><?= count($team_borders) ?></span>
            </div>
        </div>
        <div class="section-scroll">
            <?php foreach ($team_borders as $i => $b):
                $price_val = (int) $b['price'];
                $is_owned = $b['owned'];
            ?>
            <div class="shop-card"
                 style="animation-delay: <?= $i * 0.05 ?>s; --accent: #5865f2;"
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
                    <div class="card-badge"><span style="background: rgba(88,101,242,0.15); color: #5865f2; border: 1px solid rgba(88,101,242,0.2);">Team</span></div>
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
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center gap-2">
                <h3 class="text-white font-bold text-sm">Gratis</h3>
                <span class="bg-[#57f287]/15 text-[#57f287] text-[9px] font-bold px-2 py-0.5 rounded-md"><?= count($free_borders) ?></span>
            </div>
        </div>
        <div class="section-scroll">
            <?php foreach ($free_borders as $i => $b):
                $price_val = (int) $b['price'];
                $is_owned = $b['owned'];
            ?>
            <div class="shop-card"
                 style="animation-delay: <?= $i * 0.05 ?>s; --accent: #57f287;"
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
                    <div class="card-badge"><span style="background: rgba(87,242,135,0.15); color: #57f287; border: 1px solid rgba(87,242,135,0.2);">Free</span></div>
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
    <div class="absolute inset-0 bg-[#111214]/90 backdrop-blur-sm" onclick="closeDetail()"></div>
    <div class="absolute inset-0 flex items-end sm:items-center justify-center p-0 sm:p-4">
        <div id="detail-panel" class="w-full sm:max-w-md bg-[#2f3136] rounded-t-2xl sm:rounded-2xl border border-[#202225] shadow-2xl overflow-hidden transition-transform duration-200">
            <!-- Preview Area -->
            <div class="relative bg-[#18191c] overflow-hidden h-48 sm:h-56" id="detail-preview-bg">
                <div class="absolute top-0 left-0 right-0 h-[3px]" id="detail-accent" style="background: linear-gradient(90deg, var(--accent, #5865f2), transparent);"></div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="relative" style="width: 35%; aspect-ratio: 1;">
                        <img src="<?= assets_url('default.jpg'); ?>" alt="" class="w-full h-full rounded-full object-cover" id="detail-avatar">
                        <img id="detail-image" src="" alt="" class="absolute inset-0 w-full h-full object-contain scale-[1.25] transform origin-center">
                    </div>
                </div>
                <button onclick="closeDetail()" class="absolute top-3 right-3 w-8 h-8 bg-black/50 hover:bg-black/70 rounded-full flex items-center justify-content-center text-[#b9bbbe] hover:text-white transition-colors z-20" style="display:flex;align-items:center;justify-content:center;">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
            <!-- Info -->
            <div class="p-5">
                <div class="flex items-start justify-between gap-3 mb-1">
                    <h3 id="detail-name" class="text-white font-extrabold text-lg leading-tight"></h3>
                    <div id="detail-price-wrap" class="text-right flex-shrink-0">
                        <span id="detail-price" class="text-[#faa61a] font-extrabold text-base"></span>
                        <span class="text-[#72767d] text-[10px] block">koin</span>
                    </div>
                </div>
                <p id="detail-category" class="text-[10px] text-[#72767d] uppercase tracking-widest font-semibold mb-3"></p>
                <p id="detail-desc" class="text-[#b9bbbe] text-[12px] leading-relaxed mb-5"></p>

                <!-- Balance -->
                <div class="flex items-center gap-3 bg-[#232528] rounded-lg p-3 mb-4 border border-[#202225]">
                    <div class="w-8 h-8 rounded-full bg-[#faa61a]/15 flex items-center justify-center flex-shrink-0">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" fill="rgba(250,166,26,0.3)"/><text x="12" y="16" text-anchor="middle" font-size="12" font-weight="bold" fill="#faa61a">$</text></svg>
                    </div>
                    <div>
                        <span class="text-[10px] text-[#72767d] block">Saldo kamu</span>
                        <strong id="detail-my-coins" class="text-[#faa61a] font-bold text-sm"><?= number_format($user_coins, 0, ',', '.') ?></strong>
                    </div>
                </div>

                <div id="detail-not-enough" class="hidden flex items-center gap-3 bg-[#ed4245]/10 border border-[#ed4245]/20 rounded-lg p-3 mb-4">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" fill="rgba(237,66,69,0.3)"/><text x="12" y="16" text-anchor="middle" font-size="12" font-weight="bold" fill="#ed4245">!</text></svg>
                    <span class="text-[12px] text-[#ed4245]">Koin kamu tidak cukup.</span>
                </div>

                <!-- Buttons -->
                <div class="flex gap-3">
                    <button onclick="closeDetail()" class="flex-1 py-2.5 rounded-lg bg-[#232528] hover:bg-[#292b2f] text-[#b9bbbe] hover:text-white font-semibold text-xs transition-colors border border-[#202225]">
                        Kembali
                    </button>
                    <button id="detail-buy-btn" class="flex-1 py-2.5 rounded-lg font-semibold text-xs text-white transition-all active:scale-[0.97]"
                            style="background: #5865f2;">
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
    const accent = cat === 'premium' ? '#faa61a' : cat === 'team' ? '#5865f2' : '#57f287';
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
        buyBtn.style.border = '1px solid #57f287';
        buyBtn.style.color = '#57f287';
    } else if (curPrice <= 0) {
        priceWrap.classList.add('hidden');
        buyBtn.textContent = 'Ambil Gratis';
        buyBtn.disabled = false;
        buyBtn.style.background = '#57f287';
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
            buyBtn.style.border = '1px solid #40444b';
            buyBtn.style.color = '#72767d';
        } else {
            buyBtn.disabled = false;
            buyBtn.style.background = '#5865f2';
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
    fd.append(document.querySelector('meta[name="csrf-token-name"]').content, document.querySelector('meta[name="csrf-token-hash"]').content);

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
            btn.style.background = '#57f287';
            btn.style.color = '#fff';
            setTimeout(() => location.reload(), 1000);
        } else {
            btn.textContent = 'Beli Sekarang';
            btn.disabled = false;
            btn.style.background = '#5865f2';
            btn.style.color = '#fff';
            alert(d.message);
        }
    })
    .catch(() => {
        btn.textContent = 'Beli Sekarang';
        btn.disabled = false;
        btn.style.background = '#5865f2';
        btn.style.color = '#fff';
        alert('Terjadi kesalahan.');
    });
}

document.getElementById('detail-buy-btn').addEventListener('click', function() {
    if (!this.disabled) buy();
});
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeDetail(); });
</script>
</main>
