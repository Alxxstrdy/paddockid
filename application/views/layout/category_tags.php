<div class="flex-row gap-2 overflow-x-auto no-scrollbar mx-negative px-4 sm:mx-0 sm:px-0 pb-1">
    <a href="<?= base_url('home'); ?>" class="tag-pill <?= empty($active_category_slug) ? 'tag-pill--active' : 'tag-pill--default' ?>">For You</a>
    <span class="tag-pill tag-pill--default">Trending</span>
    <?php if (isset($categories) && !empty($categories)): ?>
        <?php foreach ($categories as $cat): ?>
            <a href="<?= base_url('home/category/' . $cat['slug']); ?>" class="tag-pill <?= (isset($active_category_slug) && $active_category_slug === $cat['slug']) ? 'tag-pill--active' : 'tag-pill--default' ?>">
                <?= $cat['category_name']; ?>
            </a>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
