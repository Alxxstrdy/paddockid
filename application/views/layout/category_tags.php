<div class="flex gap-2 overflow-x-auto no-scrollbar mx-[-16px] px-4 sm:mx-0 sm:px-0 pb-1">
    <a href="<?= base_url('home'); ?>" class="px-3.5 py-1.5 text-xs font-semibold rounded-full cursor-pointer whitespace-nowrap <?= empty($active_category_slug) ? 'bg-white text-black' : 'glass-card hover:bg-white/[0.05] text-slate-300'; ?>">For You</a>
    <span class="px-3.5 py-1.5 glass-card hover:bg-white/[0.05] text-slate-300 text-xs font-medium rounded-full cursor-pointer whitespace-nowrap">Trending</span>
    <?php if (isset($categories) && !empty($categories)): ?>
        <?php foreach ($categories as $cat): ?>
            <a href="<?= base_url('home/category/' . $cat['slug']); ?>" class="px-3.5 py-1.5 text-xs font-medium rounded-full cursor-pointer whitespace-nowrap uppercase <?= (isset($active_category_slug) && $active_category_slug === $cat['slug']) ? 'bg-white text-black' : 'glass-card hover:bg-white/[0.05] text-slate-300'; ?>">
                <?= $cat['category_name']; ?>
            </a>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
