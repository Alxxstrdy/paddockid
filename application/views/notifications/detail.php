<div class="flex-1 flex-col min-w-0">
    <div class="card p-6">
        <div class="flex-col gap-4">
            <div class="flex-row items-center gap-3">
                <?php if ($notif['type'] === 'admin'): ?>
                    <?php
                        $adminIcon = 'megaphone';
                        $iconColor = 'var(--color-primary)';
                        $adminType = strtolower($notif['admin_type'] ?? '');
                        if ($adminType === 'warning') {
                            $adminIcon = 'alert-triangle';
                            $iconColor = 'var(--color-warning)';
                        } elseif ($adminType === 'gift') {
                            $adminIcon = 'gift';
                            $iconColor = 'var(--color-primary)';
                        } elseif ($adminType === 'info') {
                            $adminIcon = 'info';
                            $iconColor = 'var(--color-info)';
                        }
                    ?>
                    <div class="flex-shrink-0" style="width:40px;height:40px;display:flex;align-items:center;justify-content:center;border-radius:50%;background:var(--bg-surface-subtle);">
                        <i data-lucide="<?= $adminIcon ?>" style="width:20px;height:20px;color:<?= $iconColor ?>;"></i>
                    </div>
                <?php else: ?>
                    <div class="avatar" style="width:40px;height:40px;">
                        <img src="<?= htmlspecialchars($notif['actor_avatar'], ENT_QUOTES, 'UTF-8'); ?>" alt="" style="width:40px;height:40px;border-radius:50%;object-fit:cover;" onerror="this.src='<?= assets_url('default.jpg'); ?>'">
                    </div>
                <?php endif; ?>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold c-white">
                        <?= $notif['type'] === 'admin' ? 'SISTEM' : htmlspecialchars($notif['actor_username'], ENT_QUOTES, 'UTF-8'); ?>
                    </p>
                    <p class="text-caption c-subtle"><?= htmlspecialchars($notif['created_at'], ENT_QUOTES, 'UTF-8'); ?></p>
                </div>
            </div>
            <div>
                <?php if (!empty($notif['title'])): ?>
                    <h2 class="text-lg font-bold c-white"><?= htmlspecialchars($notif['title'], ENT_QUOTES, 'UTF-8'); ?></h2>
                <?php endif; ?>
                <?php if (!empty($notif['message'])): ?>
                    <p class="text-sm <?= !empty($notif['title']) ? 'mt-2' : ''; ?>" style="color:var(--text-secondary);white-space:pre-wrap;word-break:break-word;"><?= htmlspecialchars($notif['message'], ENT_QUOTES, 'UTF-8'); ?></p>
                <?php endif; ?>
            </div>
            <?php if ($notif['type'] === 'admin' && ($notif['gift_type'] || $notif['gift_coins'])): ?>
                <div class="p-3 rounded-lg" style="background:var(--bg-surface-subtle);border:1px solid var(--border-subtle);">
                    <p class="text-xs c-subtle">Hadiah terlampir</p>
                    <?php if ($notif['gift_type'] === 'border' && !empty($notif['gift_border_id'])): ?>
                        <p class="text-sm font-medium c-white mt-1">Border</p>
                    <?php elseif ($notif['gift_type'] === 'point' && !empty($notif['gift_coins'])): ?>
                        <p class="text-sm font-medium c-white mt-1"><?= number_format($notif['gift_coins']); ?> Koin</p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            <div class="flex-row gap-2">
                <?php if ($notif['type'] === 'follow'): ?>
                    <a href="<?= base_url('user/' . rawurlencode($notif['actor_username'])); ?>" class="btn btn-primary btn-sm">Lihat Profil</a>
                <?php elseif (in_array($notif['type'], ['like','comment','reply']) && !empty($notif['id_post'])): ?>
                    <a href="<?= base_url('post/' . rawurlencode($notif['post_author_username'] ?: $notif['actor_username']) . '/' . $notif['id_post']); ?>" class="btn btn-primary btn-sm">Lihat Postingan</a>
                <?php elseif ($notif['type'] === 'dm' && !empty($notif['id_dm'])): ?>
                    <a href="<?= base_url('dm/conversation/' . $notif['id_dm']); ?>" class="btn btn-primary btn-sm">Buka Chat</a>
                <?php elseif ($notif['type'] === 'admin'): ?>
                    <?php if ($notif['gift_type'] || $notif['gift_coins']): ?>
                        <a href="<?= base_url('borders'); ?>" class="btn btn-primary btn-sm">Lihat Hadiah</a>
                    <?php else: ?>
                        <a href="<?= base_url('home'); ?>" class="btn btn-primary btn-sm">Kembali ke Beranda</a>
                    <?php endif; ?>
                <?php endif; ?>
                <a href="<?= base_url('home'); ?>" class="btn btn-secondary btn-sm">Kembali</a>
            </div>
        </div>
    </div>
</div>
