<div class="max-w-3xl mx-auto">
    <h1 class="text-lg font-bold text-heading text-transform-uppercase inline-letter-spacing-006em mb-5 c-white">
        <i data-lucide="message-square" class="w-4 h-4 inline-block mr-2"></i>
        Live Chat
    </h1>

    <?php if (!empty($active_rooms)): ?>
        <div class="mb-6">
            <h2 class="text-micro font-semibold text-transform-uppercase inline-letter-spacing-01em c-success mb-3 flex-row items-center gap-2">
                <span class="w-1-5 h-1-5 rounded-full bg-emerald-400 animate-pulse" style="width:6px;height:6px;background:var(--color-success);border-radius:50%;"></span>
                Active Now
            </h2>
            <div class="space-y-2">
                <?php foreach ($active_rooms as $room): ?>
                    <a href="<?= base_url('chat/room/' . $room['slug']); ?>" class="block rounded-xl p-4 border transition" style="background:rgba(255,255,255,0.03);border-color:var(--color-success-border);" onmouseover="this.style.background='rgba(255,255,255,0.06)'" onmouseout="this.style.background='rgba(255,255,255,0.03)'">
                        <div class="flex-row items-center justify-between">
                            <div>
                                <span class="text-micro text-transform-uppercase inline-letter-spacing-006em c-subtle"><?= htmlspecialchars($room['race_name']); ?></span>
                                <h3 class="text-sm font-semibold c-white mt-05" style="margin-top:2px;" onmouseover="this.style.color='var(--color-primary)'" onmouseout="this.style.color=''"><?= htmlspecialchars($room['session_name']); ?></h3>
                            </div>
                            <div class="flex-row items-center gap-2">
                                <span class="w-2 h-2 rounded-full animate-pulse" style="width:8px;height:8px;border-radius:50%;background:var(--color-success);"></span>
                                <span class="text-micro c-success font-semibold text-transform-uppercase inline-letter-spacing-006em">LIVE</span>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if (!empty($upcoming_rooms)): ?>
        <div class="mb-6">
            <h2 class="text-micro font-semibold text-transform-uppercase inline-letter-spacing-01em c-info mb-3">
                Upcoming
            </h2>
            <div class="space-y-2">
                <?php foreach ($upcoming_rooms as $room): ?>
                    <div class="rounded-xl p-4 border opacity-70" style="background:rgba(255,255,255,0.02);border-color:var(--border-subtle);">
                        <div class="flex-row items-center justify-between">
                            <div>
                                <span class="text-micro text-transform-uppercase inline-letter-spacing-006em c-subtle"><?= htmlspecialchars($room['race_name']); ?></span>
                                <h3 class="text-sm font-semibold c-white" style="margin-top:2px;"><?= htmlspecialchars($room['session_name']); ?></h3>
                                <span class="text-micro c-subtle block" style="margin-top:4px;">
                                    Opens <?= date('d M H:i', strtotime($room['opens_at'])); ?>
                                </span>
                            </div>
                            <span class="text-micro c-info font-semibold text-transform-uppercase inline-letter-spacing-006em">Upcoming</span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if (empty($active_rooms) && empty($upcoming_rooms)): ?>
        <div class="card p-8 text-center text-xs c-subtle">
            <i data-lucide="message-square" class="w-8 h-8 mx-auto mb-3 c-faint"></i>
            <p>No chat rooms available right now.</p>
            <p style="margin-top:4px;">Check back during the next race weekend!</p>
        </div>
    <?php endif; ?>
</div>
</main>

<script>
if (typeof lucide !== 'undefined') lucide.createIcons();
</script>
