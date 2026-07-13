<div class="max-w-3xl mx-auto">
    <h1 class="text-lg font-bold font-heading uppercase tracking-wider text-white mb-5">
        <i data-lucide="message-square" class="w-4 h-4 inline-block mr-2"></i>
        Live Chat
    </h1>

    <?php if (!empty($active_rooms)): ?>
        <div class="mb-6">
            <h2 class="text-[10px] font-semibold uppercase tracking-widest text-emerald-400 mb-3 flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                Active Now
            </h2>
            <div class="space-y-2">
                <?php foreach ($active_rooms as $room): ?>
                    <a href="<?= base_url('chat/room/' . $room['slug']); ?>" class="block bg-white/[0.03] hover:bg-white/[0.06] rounded-xl p-4 border border-emerald-500/20 transition-all group">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-[10px] uppercase tracking-wider text-slate-500"><?= htmlspecialchars($room['race_name']); ?></span>
                                <h3 class="text-sm font-semibold text-white mt-0.5 group-hover:text-red-400 transition-colors"><?= htmlspecialchars($room['session_name']); ?></h3>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                                <span class="text-[10px] text-emerald-400 font-semibold uppercase tracking-wider">LIVE</span>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if (!empty($upcoming_rooms)): ?>
        <div class="mb-6">
            <h2 class="text-[10px] font-semibold uppercase tracking-widest text-blue-400 mb-3">
                Upcoming
            </h2>
            <div class="space-y-2">
                <?php foreach ($upcoming_rooms as $room): ?>
                    <div class="bg-white/[0.02] rounded-xl p-4 border border-white/[0.04] opacity-70">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-[10px] uppercase tracking-wider text-slate-500"><?= htmlspecialchars($room['race_name']); ?></span>
                                <h3 class="text-sm font-semibold text-white mt-0.5"><?= htmlspecialchars($room['session_name']); ?></h3>
                                <span class="text-[10px] text-slate-500 mt-1 block">
                                    Opens <?= date('d M H:i', strtotime($room['opens_at'])); ?>
                                </span>
                            </div>
                            <span class="text-[10px] text-blue-400 font-semibold uppercase tracking-wider">Upcoming</span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if (empty($active_rooms) && empty($upcoming_rooms)): ?>
        <div class="glass-card p-8 text-center text-slate-500 text-xs">
            <i data-lucide="message-square" class="w-8 h-8 mx-auto mb-3 text-slate-600"></i>
            <p>No chat rooms available right now.</p>
            <p class="mt-1">Check back during the next race weekend!</p>
        </div>
    <?php endif; ?>
</div>

<script>
if (typeof lucide !== 'undefined') lucide.createIcons();
</script>
