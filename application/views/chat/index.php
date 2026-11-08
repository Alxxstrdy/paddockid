<div class="max-w-3xl mx-auto">
    <div class="flex-row items-center gap-3 mb-5">        
        <div>
            <h1 class="text-lg font-bold text-heading text-transform-uppercase inline-letter-spacing-006em c-white">
                Live Chat
            </h1>
            <p class="text-caption c-subtle" style="margin-top:2px;">
                Ruang obrolan bersama untuk setiap sesi balapan.
            </p>
        </div>
    </div>

    <?php if (!empty($active_rooms)): ?>
        <section class="mb-6">
            <h2 class="flex-row items-center gap-2 mb-3" style="font-size:9.5px;font-weight:700;letter-spacing:0.09em;text-transform:uppercase;color:var(--color-success);">
                <span style="width:6px;height:6px;border-radius:50%;background:var(--color-success);"></span>
                Sedang berlangsung
            </h2>
            <div class="flex-col gap-2" data-sk="rooms">
                <?php foreach ($active_rooms as $room): ?>
                    <a href="<?= base_url('chat/room/' . $room['slug']); ?>" class="chat-room-card chat-room-card--live">
                        <div class="flex-row items-center justify-between gap-3">
                            <div style="min-width:0;">
                                <span class="chat-room-card__race"><?= htmlspecialchars($room['race_name']); ?></span>
                                <h3 class="chat-room-card__session"><?= htmlspecialchars($room['session_name']); ?></h3>
                            </div>
                            <span class="chat-pill chat-pill--live" style="flex-shrink:0;">
                                <span class="chat-pill__dot"></span>
                                Live
                            </span>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>

    <?php if (!empty($upcoming_rooms)): ?>
        <section class="mb-6">
            <h2 class="flex-row items-center gap-2 mb-3" style="font-size:9.5px;font-weight:700;letter-spacing:0.09em;text-transform:uppercase;color:var(--color-info);">
                <i data-lucide="clock" style="width:12px;height:12px;"></i>
                Mendatang
            </h2>
            <div class="flex-col gap-2" data-sk="rooms">
                <?php foreach ($upcoming_rooms as $room): ?>
                    <div class="chat-room-card chat-room-card--upcoming">
                        <div class="flex-row items-center justify-between gap-3">
                            <div style="min-width:0;">
                                <span class="chat-room-card__race"><?= htmlspecialchars($room['race_name']); ?></span>
                                <h3 class="chat-room-card__session"><?= htmlspecialchars($room['session_name']); ?></h3>
                                <p class="chat-room-card__meta">
                                    Dibuka <?= date('d M Y · H:i', strtotime($room['opens_at'])); ?>
                                </p>
                            </div>
                            <span class="chat-pill chat-pill--upcoming" style="flex-shrink:0;">Upcoming</span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>

    <?php if (empty($active_rooms) && empty($upcoming_rooms)): ?>
        <div class="empty-state">
            <i data-lucide="message-square" class="empty-state__icon" style="width:30px;height:30px;"></i>
            <p class="empty-state__text">Belum ada room chat yang tersedia.</p>
            <p class="empty-state__text mt-1">Cek lagi saat race weekend berikutnya.</p>
        </div>
    <?php endif; ?>
</div>

<script>
if (typeof lucide !== 'undefined') lucide.createIcons();
</script>