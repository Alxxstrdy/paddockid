<div class="flex-row justify-between mb-4 pb-4 border-b">
    <div class="flex-row gap-3">
        <a href="<?= base_url('home'); ?>" class="p-2 c-muted rounded-xl transition-colors" style="cursor:pointer" onmouseover="this.style.color='var(--text-primary)';this.style.background='var(--bg-surface-active)'" onmouseout="this.style.color='';this.style.background=''">
            <i data-lucide="arrow-left" class="w-5 h-5"></i>
        </a>
        <h2 class="text-heading text-sm c-white">Border Shop</h2>
    </div>
    <a href="<?= base_url('borders/shop'); ?>" class="btn btn-xs gap-1-5 shadow-lg" style="background:var(--color-warning);color:#fff" onmouseover="this.style.background='var(--color-warning)'" onmouseout="this.style.background='var(--color-warning)'">
        <i data-lucide="store" class="w-3-5 h-3-5"></i> Store
    </a>
</div>

<div class="space-y-4">
    <!-- Preview Panel (above search, always visible) -->
    <div id="preview-panel" class="card rounded-2xl p-5">
        <div class="flex-row gap-5 sm-gap-6">
            <div class="relative flex-shrink-0" style="width:80px;height:80px">
                <div class="w-full h-full rounded-full overflow-hidden" style="background:var(--bg-surface-active)">
                    <img id="preview-avatar" src="<?= $user_avatar; ?>" alt="Avatar" class="w-full h-full rounded-full" style="object-fit:cover">
                </div>
                <div id="preview-border-overlay" class="absolute inset-0 w-full h-full pointer-events-none z-20 <?= $active_border ? '' : 'hidden' ?>" style="transform:scale(1.25);transform-origin:center">
                    <img id="preview-border-img" src="<?= $active_border ? assets_url($active_border['image_url']) : '' ?>" alt="Border" class="w-full h-full" style="object-fit:contain">
                </div>
            </div>
            <div class="flex-1 min-w-0">
                <p id="preview-name" class="text-xs font-semibold c-white"><?= $active_border ? htmlspecialchars($active_border['border_name']) : 'Pilih border' ?></p>
                <p id="preview-description" class="text-caption mt-0-5" style="line-clamp:2;-webkit-line-clamp:2;display:-webkit-box;-webkit-box-orient:vertical;overflow:hidden"><?= $active_border && !empty($active_border['description']) ? htmlspecialchars($active_border['description']) : '' ?></p>
                <p id="preview-status" class="text-caption mt-1 c-subtle"><?= $active_border ? 'Sedang digunakan' : 'Klik border untuk melihat preview' ?></p>
                <div id="preview-actions" class="flex-row gap-2 mt-3">
                    <button id="btn-equip" class="hidden btn btn-primary btn-xs shadow-lg" onclick="equipBorder()">
                        <i data-lucide="check-circle" class="w-3-5 h-3-5 inline-block mr-1"></i> Equip
                    </button>
                    <button id="btn-remove" class="hidden btn btn-xs rounded-xl border" style="background:var(--bg-surface-active);color:var(--text-secondary);border-color:var(--border-default)" onclick="removeBorder()" onmouseover="this.style.background='var(--bg-surface-active)';this.style.color='var(--text-primary)'" onmouseout="this.style.background='';this.style.color=''">
                        <i data-lucide="x-circle" class="w-3-5 h-3-5 inline-block mr-1"></i> Remove
                    </button>
                    <a id="btn-shop" href="<?= base_url('borders/shop'); ?>" class="hidden btn btn-xs shadow-lg" style="background:var(--color-warning);color:#fff">
                        <i data-lucide="store" class="w-3-5 h-3-5 inline-block mr-1"></i> Go to Shop
                    </a>
                    <div id="btn-event" class="hidden btn btn-xs rounded-xl border" style="background:var(--bg-surface-subtle);color:var(--text-subtle);border-color:var(--border-subtle);cursor:not-allowed">
                        <i data-lucide="gift" class="w-3-5 h-3-5 inline-block mr-1"></i> Event telah berakhir
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search -->
    <div class="relative">
        <i data-lucide="search" class="absolute w-4 h-4 c-subtle pointer-events-none" style="left:14px;top:50%;transform:translateY(-50%)"></i>
        <input type="text" id="border-search" placeholder="Cari border..." class="w-full input rounded-xl" style="padding-left:40px;background:var(--bg-surface-raised);border-color:var(--border-default)">
    </div>

    <!-- Border Grid -->
    <div id="border-grid" class="grid-2 sm-grid-3" style="gap:12px">
        <?php foreach ($borders as $b):
            $is_owned = in_array($b['id_border'], $owned_ids);
            $is_active = $b['id_border'] == $active_id;
            $is_purchasable = !empty($b['is_premium']) && $b['price'] > 0;
        ?>
            <div 
                class="border-card card rounded-xl overflow-hidden cursor-pointer transition-colors <?= $is_owned ? 'border' : 'border' ?>"
                style="<?= !$is_owned ? 'opacity:0.6' : '' ?>"
                data-border-id="<?= $b['id_border']; ?>"
                data-name="<?= htmlspecialchars($b['border_name'], ENT_QUOTES, 'UTF-8'); ?>"
                data-description="<?= htmlspecialchars($b['description'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                data-image="<?= assets_url($b['image_url']); ?>"
                data-owned="<?= $is_owned ? '1' : '0'; ?>"
                data-active="<?= $is_active ? '1' : '0'; ?>"
                data-purchasable="<?= $is_purchasable ? '1' : '0'; ?>"
                data-price="<?= $b['price']; ?>"
                onclick="selectBorder(this)"
                onmouseover="<?= !$is_owned ? "this.style.opacity='0.8'" : '' ?>"
                onmouseout="<?= !$is_owned ? "this.style.opacity='0.6'" : '' ?>"
            >
                <div class="relative flex-row justify-center" style="aspect-ratio:1/1;background:var(--bg-body)">
                    <div class="relative flex-row justify-center" style="width:75%;height:75%">
                        <div class="w-full h-full rounded-full overflow-hidden" style="background:var(--bg-surface-active)">
                            <img src="<?= assets_url('default.jpg'); ?>" alt="" class="w-full h-full rounded-full" style="object-fit:cover">
                        </div>
                        <div class="absolute inset-0 w-full h-full pointer-events-none" style="transform:scale(1.25);transform-origin:center">
                            <img src="<?= assets_url($b['image_url']); ?>" alt="<?= htmlspecialchars($b['border_name']); ?>" class="w-full h-full" style="object-fit:contain">
                        </div>
                    </div>
                    <?php if ($is_active): ?>
                        <span class="absolute badge badge-success badge-pill" style="top:6px;right:6px;font-size:8px;font-weight:700;text-transform:uppercase;letter-spacing:0.06em">Active</span>
                    <?php elseif (!$is_owned): ?>
                        <div class="absolute inset-0 flex-row justify-center" style="background:rgba(5,7,12,0.4)">
                            <i data-lucide="lock" class="w-6 h-6 c-muted"></i>
                        </div>
                    <?php endif; ?>
                </div>
                <div style="padding:10px">
                    <p class="text-xs font-medium text-truncate" style="color:var(--text-secondary)"><?= htmlspecialchars($b['border_name']); ?></p>
                    <?php if (!empty($b['description'])): ?>
                        <p class="text-micro mt-0-5 leading-relaxed" style="font-size:10px;color:var(--text-subtle);-webkit-line-clamp:2;display:-webkit-box;-webkit-box-orient:vertical;overflow:hidden"><?= htmlspecialchars($b['description']); ?></p>
                    <?php endif; ?>
                    <p class="text-micro mt-0-5" style="font-size:10px;color:var(--text-subtle)"><?= $is_owned ? 'Dimiliki' : ($is_purchasable ? number_format($b['price'], 0, ',', '.') : 'Event') ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
    <div class="pagination pt-2">
        <?php if ($page > 1): ?>
            <a href="<?= base_url('borders/page/' . ($page - 1)); ?>" class="pagination-btn">
                <i data-lucide="chevron-left" class="w-3-5 h-3-5 inline-block"></i>
            </a>
        <?php endif; ?>
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="<?= base_url('borders/page/' . $i); ?>" class="pagination-btn <?= $i == $page ? 'btn-primary' : '' ?>" style="<?= $i == $page ? 'background:var(--color-primary);color:#fff;border-color:var(--color-primary)' : '' ?>">
                <?= $i; ?>
            </a>
        <?php endfor; ?>
        <?php if ($page < $total_pages): ?>
            <a href="<?= base_url('borders/page/' . ($page + 1)); ?>" class="pagination-btn">
                <i data-lucide="chevron-right" class="w-3-5 h-3-5 inline-block"></i>
            </a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<script>
let selectedBorderId = <?= $active_id ?: 'null' ?>;

function selectBorder(el) {
    document.querySelectorAll('.border-card').forEach(c => c.classList.remove('ring-2'));
    el.classList.add('ring-2');

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
    document.querySelector('.border-card[data-active="1"]')?.classList.add('ring-2');
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