<div class="max-w-3xl mx-auto">
    <h1 class="text-lg font-bold text-heading text-transform-uppercase inline-letter-spacing-006em mb-5 c-white">
        <i data-lucide="mail" class="w-4 h-4 inline-block mr-2"></i>
        Pesan Pribadi
    </h1>

    <?php if (!empty($conversations)): ?>
        <div class="space-y-2" data-sk="rooms">
            <?php foreach ($conversations as $conv): ?>
                <a href="<?= base_url('dm/conversation/' . $conv['id_dm']); ?>"
                   class="block rounded-xl p-4 border transition dm-conv-item"
                   style="background:rgba(255,255,255,0.03);border-color:<?= $conv['unread_count'] > 0 ? 'var(--color-primary-border)' : 'var(--border-subtle)'; ?>;"
                   onmouseover="this.style.background='rgba(255,255,255,0.06)'" onmouseout="this.style.background='rgba(255,255,255,0.03)'">
                    <div class="flex-row items-center gap-3">
                        <div class="relative flex-shrink-0" data-user-id="<?= $conv['partner_id']; ?>" style="width:44px;height:44px;">
                            <div class="w-full h-full rounded-full overflow-hidden" style="background:var(--bg-surface);border:1px solid var(--border-strong);">
                                <img src="<?= $conv['partner_avatar']; ?>" alt="" class="w-full h-full" style="object-fit:cover;"
                                     onerror="this.src='<?= assets_url('default.jpg'); ?>';">
                            </div>
                            <?php if (!empty($conv['partner_border'])): ?>
                                <div class="absolute inset-0 w-full h-full pointer-events-none" style="transform:scale(1.25);transform-origin:center;">
                                    <img src="<?= $conv['partner_border']; ?>" alt="" class="w-full h-full" style="object-fit:contain;">
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="flex-1 min-w-0">
                            <div class="flex-row items-center justify-between gap-2">
                                <h3 class="text-sm font-semibold text-truncate <?= $conv['unread_count'] > 0 ? 'c-white' : 'c-secondary'; ?>">
                                    <?= htmlspecialchars($conv['partner_display_name'] ?: $conv['partner_username'], ENT_QUOTES, 'UTF-8'); ?>
                                    <?php if ($conv['partner_verified'] == 1): ?>
                                        <i data-lucide="badge-check" class="w-3.5 h-3.5 inline-block c-primary ml-0-5" style="fill:var(--color-primary);"></i>
                                    <?php endif; ?>
                                </h3>
                                <?php if (!empty($conv['last_message_at'])): ?>
                                    <span class="text-caption c-subtle flex-shrink-0"><?= formatWaktuSosmed($conv['last_message_at']); ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="flex-row items-center justify-between gap-2 mt-1">
                                <p class="text-xs text-truncate <?= $conv['unread_count'] > 0 ? 'c-white font-semibold' : 'c-subtle'; ?>" style="color:var(--text-subtle);">
                                    <?php if (!empty($conv['last_message_at'])): ?>
                                        <?= $conv['is_last_from_me'] ? '<span class="c-primary font-semibold">Kamu: </span>' : ''; ?>
                                        <?php if (!empty($conv['last_message_post'])): ?>
                                            <i data-lucide="share-2" class="w-3.5 h-3.5 inline-block mr-0-5 c-primary"></i> Membagikan postingan
                                        <?php elseif (!empty($conv['last_message_image']) && empty($conv['last_message_content'])): ?>
                                            <i data-lucide="image" class="w-3.5 h-3.5 inline-block mr-0-5"></i> Foto
                                        <?php elseif (!empty($conv['last_message_image'])): ?>
                                            <?= htmlspecialchars(mb_substr($conv['last_message_content'], 0, 80), ENT_QUOTES, 'UTF-8'); ?> <i data-lucide="image" class="w-3 h-3 inline-block ml-1 c-subtle"></i>
                                        <?php else: ?>
                                            <?= htmlspecialchars(mb_substr($conv['last_message_content'], 0, 80), ENT_QUOTES, 'UTF-8'); ?>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="italic">Belum ada pesan. Mulai percakapan!</span>
                                    <?php endif; ?>
                                </p>
                                <?php if ($conv['unread_count'] > 0): ?>
                                    <span class="conv-dm-badge rounded-full flex-shrink-0" style="min-width:18px;height:18px;padding:0 5px;background:var(--color-primary);color:#fff;font-size:10px;font-weight:700;display:flex;align-items:center;justify-content:center;">
                                        <?= $conv['unread_count'] > 9 ? '9+' : $conv['unread_count']; ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="card p-8 text-center text-xs c-subtle">
            <i data-lucide="mail" class="w-8 h-8 mx-auto mb-3 c-faint"></i>
            <p>Belum ada percakapan.</p>
            <p style="margin-top:4px;">Kunjungi profil pengguna lalu tekan "Kirim Pesan" untuk memulai DM!</p>
        </div>
    <?php endif; ?>
</div>
</main>

<script>
if (typeof lucide !== 'undefined') lucide.createIcons();
</script>