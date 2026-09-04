<div class="flex items-center justify-between mb-4 pb-4 border-b border-white/[0.04]">
    <div class="flex items-center gap-3">
        <a href="<?= base_url('home'); ?>" class="p-2 text-slate-400 hover:text-white hover:bg-white/[0.05] rounded-xl transition-colors">
            <i data-lucide="arrow-left" class="w-5 h-5"></i>
        </a>
        <h2 class="font-syne text-sm uppercase tracking-tight text-white">Border Shop</h2>
    </div>
    <a href="<?= base_url('borders/shop'); ?>" class="flex items-center gap-1.5 bg-amber-600 hover:bg-amber-500 text-white font-semibold text-xs px-3.5 py-2 rounded-xl transition-all shadow-lg shadow-amber-600/10 active:scale-[0.98]">
        <i data-lucide="store" class="w-3.5 h-3.5"></i> Store
    </a>
</div>

<div class="space-y-4">
    <!-- Preview Panel (above search, always visible) -->
    <div id="preview-panel" class="glass-card rounded-2xl border border-white/[0.06] p-5">
        <div class="flex items-center gap-5 sm:gap-6">
            <div class="relative w-20 h-20 sm:w-24 sm:h-24 flex-shrink-0">
                <div class="w-full h-full rounded-full overflow-hidden bg-slate-800 ring-2 ring-white/[0.08]">
                    <img id="preview-avatar" src="<?= $user_avatar; ?>" alt="Avatar" class="w-full h-full object-cover rounded-full">
                </div>
                <div id="preview-border-overlay" class="absolute inset-0 w-full h-full pointer-events-none scale-[1.25] transform origin-center z-20 <?= $active_border ? '' : 'hidden' ?>">
                    <img id="preview-border-img" src="<?= $active_border ? assets_url($active_border['image_url']) : '' ?>" alt="Border" class="w-full h-full object-contain">
                </div>
            </div>
            <div class="flex-1 min-w-0">
                <p id="preview-name" class="text-sm font-semibold text-white"><?= $active_border ? htmlspecialchars($active_border['border_name']) : 'Pilih border' ?></p>
                <p id="preview-description" class="text-[11px] text-slate-400 mt-0.5 line-clamp-2"><?= $active_border && !empty($active_border['description']) ? htmlspecialchars($active_border['description']) : '' ?></p>
                <p id="preview-status" class="text-[11px] text-slate-500 mt-1"><?= $active_border ? 'Sedang digunakan' : 'Klik border untuk melihat preview' ?></p>
                <div id="preview-actions" class="flex items-center gap-2 mt-3">
                    <button id="btn-equip" class="hidden bg-red-600 hover:bg-red-500 text-white font-semibold text-xs px-4 py-2 rounded-xl transition-all shadow-lg shadow-red-600/10 active:scale-[0.98]" onclick="equipBorder()">
                        <i data-lucide="check-circle" class="w-3.5 h-3.5 inline-block -mt-0.5 mr-1"></i> Equip
                    </button>
                    <button id="btn-remove" class="hidden bg-white/[0.05] hover:bg-white/[0.08] text-slate-300 hover:text-white font-semibold text-xs px-4 py-2 rounded-xl border border-white/[0.06] transition-all active:scale-[0.98]" onclick="removeBorder()">
                        <i data-lucide="x-circle" class="w-3.5 h-3.5 inline-block -mt-0.5 mr-1"></i> Remove
                    </button>
                    <a id="btn-shop" href="<?= base_url('borders/shop'); ?>" class="hidden bg-amber-600 hover:bg-amber-500 text-white font-semibold text-xs px-4 py-2 rounded-xl transition-all shadow-lg shadow-amber-600/10 active:scale-[0.98]">
                        <i data-lucide="store" class="w-3.5 h-3.5 inline-block -mt-0.5 mr-1"></i> Go to Shop
                    </a>
                    <div id="btn-event" class="hidden bg-white/[0.03] text-slate-500 font-semibold text-xs px-4 py-2 rounded-xl border border-white/[0.04] cursor-not-allowed">
                        <i data-lucide="gift" class="w-3.5 h-3.5 inline-block -mt-0.5 mr-1"></i> Event telah berakhir
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search -->
    <div class="relative">
        <i data-lucide="search" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500 pointer-events-none"></i>
        <input type="text" id="border-search" placeholder="Cari border..." class="w-full bg-slate-900/60 border border-white/[0.08] focus:border-red-500/50 rounded-xl pl-10 pr-4 py-2.5 text-xs sm:text-sm text-slate-200 placeholder-slate-600 focus:outline-none focus:ring-1 focus:ring-red-500/30 transition-all">
    </div>

    <!-- Border Grid -->
    <div id="border-grid" class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-3">
        <?php foreach ($borders as $b):
            $is_owned = in_array($b['id_border'], $owned_ids);
            $is_active = $b['id_border'] == $active_id;
            $is_purchasable = !empty($b['is_premium']) && $b['price'] > 0;
        ?>
            <div 
                class="border-card glass-card rounded-xl overflow-hidden cursor-pointer transition-all duration-300 group <?= $is_owned ? 'border border-white/[0.08]' : 'border border-white/[0.04] opacity-60 hover:opacity-80'; ?>"
                data-border-id="<?= $b['id_border']; ?>"
                data-name="<?= htmlspecialchars($b['border_name'], ENT_QUOTES, 'UTF-8'); ?>"
                data-description="<?= htmlspecialchars($b['description'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                data-image="<?= assets_url($b['image_url']); ?>"
                data-owned="<?= $is_owned ? '1' : '0'; ?>"
                data-active="<?= $is_active ? '1' : '0'; ?>"
                data-purchasable="<?= $is_purchasable ? '1' : '0'; ?>"
                data-price="<?= $b['price']; ?>"
                onclick="selectBorder(this)"
            >
                <div class="aspect-square bg-slate-950 relative flex items-center justify-center">
                    <div class="relative w-3/4 h-3/4 flex items-center justify-center">
                        <div class="w-full h-full rounded-full overflow-hidden bg-slate-800">
                            <img src="<?= assets_url('default.jpg'); ?>" alt="" class="w-full h-full object-cover rounded-full">
                        </div>
                        <div class="absolute inset-0 w-full h-full pointer-events-none scale-[1.25] transform origin-center">
                            <img src="<?= assets_url($b['image_url']); ?>" alt="<?= htmlspecialchars($b['border_name']); ?>" class="w-full h-full object-contain">
                        </div>
                    </div>
                    <?php if ($is_active): ?>
                        <span class="absolute top-1.5 right-1.5 bg-emerald-500 text-white text-[8px] font-bold px-1.5 py-0.5 rounded-full uppercase tracking-wider shadow-lg">Active</span>
                    <?php elseif (!$is_owned): ?>
                        <div class="absolute inset-0 bg-slate-950/40 flex items-center justify-center">
                            <i data-lucide="lock" class="w-6 h-6 text-slate-400"></i>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="p-2.5">
                    <p class="text-xs font-medium text-slate-200 truncate"><?= htmlspecialchars($b['border_name']); ?></p>
                    <?php if (!empty($b['description'])): ?>
                        <p class="text-[10px] text-slate-500 mt-0.5 line-clamp-2 leading-relaxed"><?= htmlspecialchars($b['description']); ?></p>
                    <?php endif; ?>
                    <p class="text-[10px] text-slate-500 mt-0.5"><?= $is_owned ? 'Dimiliki' : ($is_purchasable ? number_format($b['price'], 0, ',', '.') : 'Event') ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
    <div class="flex items-center justify-center gap-2 pt-2">
        <?php if ($page > 1): ?>
            <a href="<?= base_url('borders/page/' . ($page - 1)); ?>" class="px-3 py-1.5 text-xs font-medium text-slate-300 bg-white/[0.04] hover:bg-white/[0.08] rounded-lg border border-white/[0.06] transition-colors">
                <i data-lucide="chevron-left" class="w-3.5 h-3.5 inline-block -mt-0.5"></i>
            </a>
        <?php endif; ?>
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="<?= base_url('borders/page/' . $i); ?>" class="px-3 py-1.5 text-xs font-medium rounded-lg border transition-colors <?= $i == $page ? 'bg-red-600 text-white border-red-600' : 'text-slate-300 bg-white/[0.04] hover:bg-white/[0.08] border-white/[0.06]'; ?>">
                <?= $i; ?>
            </a>
        <?php endfor; ?>
        <?php if ($page < $total_pages): ?>
            <a href="<?= base_url('borders/page/' . ($page + 1)); ?>" class="px-3 py-1.5 text-xs font-medium text-slate-300 bg-white/[0.04] hover:bg-white/[0.08] rounded-lg border border-white/[0.06] transition-colors">
                <i data-lucide="chevron-right" class="w-3.5 h-3.5 inline-block -mt-0.5"></i>
            </a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<script>
let selectedBorderId = <?= $active_id ?: 'null' ?>;

function selectBorder(el) {
    document.querySelectorAll('.border-card').forEach(c => c.classList.remove('ring-2', 'ring-red-500'));
    el.classList.add('ring-2', 'ring-red-500');

    selectedBorderId = el.dataset.borderId;
    const name = el.dataset.name;
    const description = el.dataset.description || '';
    const image = el.dataset.image;
    const owned = el.dataset.owned === '1';
    const active = el.dataset.active === '1';
    const purchasable = el.dataset.purchasable === '1';

    document.getElementById('preview-name').textContent = name;
    document.getElementById('preview-description').textContent = description;
    document.getElementById('preview-border-img').src = image;

    const overlay = document.getElementById('preview-border-overlay');
    overlay.classList.remove('hidden');

    document.getElementById('btn-equip').classList.add('hidden');
    document.getElementById('btn-remove').classList.add('hidden');
    document.getElementById('btn-shop').classList.add('hidden');
    document.getElementById('btn-event').classList.add('hidden');

    if (owned) {
        document.getElementById('preview-status').textContent = active ? 'Sedang digunakan' : 'Dimiliki — belum dipasang';
        if (active) {
            document.getElementById('btn-remove').classList.remove('hidden');
        } else {
            document.getElementById('btn-equip').classList.remove('hidden');
        }
    } else {
        if (purchasable) {
            document.getElementById('preview-status').textContent = 'Rp' + Number(el.dataset.price).toLocaleString('id-ID');
            document.getElementById('btn-shop').classList.remove('hidden');
        } else {
            document.getElementById('preview-status').textContent = 'Border hadiah event';
            document.getElementById('btn-event').classList.remove('hidden');
        }
    }

    if (typeof lucide !== 'undefined') lucide.createIcons();
}

function equipBorder() {
    if (!selectedBorderId) return;
    const btn = document.getElementById('btn-equip');
    btn.disabled = true;
    btn.textContent = 'Memasang...';

    const formData = new FormData();
    formData.append('border_id', selectedBorderId);
    formData.append(document.querySelector('meta[name="csrf-token-name"]').content, document.querySelector('meta[name="csrf-token-hash"]').content);

    fetch('<?= base_url("borders/equip"); ?>', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.status === 'success') {
            location.reload();
        } else {
            btn.disabled = false;
            btn.textContent = 'Equip';
            alert(data.message);
        }
    })
    .catch(() => {
        btn.disabled = false;
        btn.textContent = 'Equip';
        alert('Terjadi kesalahan.');
    });
}

function removeBorder() {
    showConfirmModal('Lepaskan border ini?', {
        title: 'Lepaskan Border',
        danger: true,
        confirmText: 'Ya, Lepaskan'
    }).then(confirmed => {
        if (!confirmed) return;
        const btn = document.getElementById('btn-remove');
        btn.disabled = true;
        btn.textContent = 'Melepas...';

        const formData = new FormData();
        formData.append(document.querySelector('meta[name="csrf-token-name"]').content, document.querySelector('meta[name="csrf-token-hash"]').content);

        fetch('<?= base_url("borders/remove"); ?>', {
            method: 'POST',
            body: formData
        })
    .then(r => r.json())
    .then(data => {
        if (data.status === 'success') {
            location.reload();
        } else {
            btn.disabled = false;
            btn.textContent = 'Remove';
            alert(data.message);
        }
    })
    .catch(() => {
        btn.disabled = false;
        btn.textContent = 'Remove';
        alert('Terjadi kesalahan.');
    });
    });
}

document.addEventListener('DOMContentLoaded', function() {
    <?php if ($active_border): ?>
    document.getElementById('btn-remove').classList.remove('hidden');
    document.querySelector('.border-card[data-active="1"]')?.classList.add('ring-2', 'ring-red-500');
    <?php endif; ?>

    const searchInput = document.getElementById('border-search');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const q = this.value.toLowerCase().trim();
            document.querySelectorAll('.border-card').forEach(card => {
                const name = card.dataset.name.toLowerCase();
                card.style.display = (!q || name.includes(q)) ? '' : 'none';
            });
        });
    }
});
</script>
</main>
