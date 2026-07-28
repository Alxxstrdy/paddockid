<div class="space-y-6">
    <div>
        <h1 class="font-syne text-lg uppercase tracking-tight text-white">Race Sessions</h1>
        <p class="text-xs text-slate-500 mt-1">Kelola status sesi balapan secara real-time</p>
    </div>

    <div class="glass-card p-4 rounded-2xl border border-white/[0.04]">
        <div class="flex items-start gap-3">
            <div class="w-8 h-8 rounded-xl bg-blue-500/10 flex items-center justify-center flex-shrink-0 mt-0.5">
                <i data-lucide="info" class="w-4 h-4 text-blue-400"></i>
            </div>
            <div class="text-[11px] text-slate-500 leading-relaxed">
                <p class="text-xs text-slate-300 font-medium mb-1">Cara kerja:</p>
                <p>Klik tombol <span class="text-white font-semibold">status</span> pada sesi untuk mengubah. Status akan langsung terlihat di sidebar pengguna dalam ~15 detik.</p>
                <div class="flex flex-wrap gap-2 mt-2">
                    <span class="px-1.5 py-0.5 rounded bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 font-semibold">Normal</span>
                    <span class="px-1.5 py-0.5 rounded bg-yellow-500/10 text-yellow-400 border border-yellow-500/20 font-semibold">Yellow Flag</span>
                    <span class="px-1.5 py-0.5 rounded bg-red-500/10 text-red-400 border border-red-500/20 font-semibold">Red Flag</span>
                    <span class="px-1.5 py-0.5 rounded bg-orange-500/10 text-orange-400 border border-orange-500/20 font-semibold">SC</span>
                    <span class="px-1.5 py-0.5 rounded bg-amber-500/10 text-amber-400 border border-amber-500/20 font-semibold">VSC</span>
                    <span class="px-1.5 py-0.5 rounded bg-slate-500/10 text-slate-400 border border-slate-500/20 font-semibold">Finished</span>
                </div>
            </div>
        </div>
    </div>

    <?php
    $now = time();
    $session_window = 14400;
    $prev_race_id = null;
    ?>

    <div class="space-y-4">
        <?php if (empty($sessions)): ?>
            <div class="glass-card p-8 rounded-2xl border border-white/[0.04] text-center">
                <i data-lucide="timer" class="w-8 h-8 mx-auto mb-3 text-slate-600"></i>
                <p class="text-xs text-slate-500">Belum ada sesi balapan.</p>
            </div>
        <?php else: ?>
            <?php foreach ($sessions as $s):
                $start_ts = strtotime($s['start_datetime']);
                $end_ts = $start_ts + $session_window;
                $current_flag = $s['Session_info'];
                $is_finished = $current_flag === 'FINISHED';
                $is_flagged = in_array($current_flag, ['RED FLAG', 'YELLOW FLAG', 'SC', 'VSC'], true);
                $is_started = $now >= $start_ts;
                $is_within_window = $now <= $end_ts;
                $is_live = $is_started && $is_within_window && !$is_finished;

                if ($is_flagged) {
                    $dot_color = 'bg-red-500 animate-pulse';
                } elseif ($is_finished) {
                    $dot_color = 'bg-slate-500';
                } elseif ($is_live) {
                    $dot_color = 'bg-emerald-500 animate-pulse';
                } elseif ($is_started) {
                    $dot_color = 'bg-amber-500';
                } else {
                    $dot_color = 'bg-blue-500';
                }

                if ($is_flagged) {
                    $status_label = $current_flag;
                    $status_color = 'bg-red-500/10 text-red-400 border-red-500/20';
                } elseif ($is_finished) {
                    $status_label = 'Finished';
                    $status_color = 'bg-slate-500/10 text-slate-400 border-slate-500/20';
                } elseif ($is_live) {
                    $status_label = 'Live';
                    $status_color = 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20';
                } elseif ($is_started) {
                    $status_label = 'Selesai';
                    $status_color = 'bg-amber-500/10 text-amber-400 border-amber-500/20';
                } else {
                    $status_label = 'Upcoming';
                    $status_color = 'bg-blue-500/10 text-blue-400 border-blue-500/20';
                }

                if ($s['race_id'] !== $prev_race_id):
                    $prev_race_id = $s['race_id'];
                ?>
                    <div class="flex items-center gap-3 pt-2">
                        <div class="h-px flex-1 bg-white/[0.04]"></div>
                        <span class="text-[10px] font-syne uppercase tracking-widest text-slate-500 font-bold"><?= htmlspecialchars($s['gp_name']); ?></span>
                        <div class="h-px flex-1 bg-white/[0.04]"></div>
                    </div>
                <?php endif; ?>

                <div class="glass-card p-4 rounded-xl border border-white/[0.04] hover:border-white/[0.08] transition-all" data-session-id="<?= $s['id_session']; ?>">
                    <div class="flex items-center justify-between gap-4">
                        <div class="flex items-center gap-4 min-w-0">
                            <div class="w-2 h-2 rounded-full flex-shrink-0 <?= $dot_color; ?>"></div>
                            <div class="min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <?php if (!empty($s['gp_subtitle'])): ?>
                                        <span class="text-[10px] text-slate-500"><?= htmlspecialchars($s['gp_subtitle']); ?></span>
                                    <?php endif; ?>
                                    <span class="text-[10px] font-bold text-slate-300 uppercase tracking-wider"><?= htmlspecialchars($s['session_name']); ?></span>
                                    <span class="inline-flex items-center text-[9px] font-semibold px-1.5 py-0.5 rounded-full border <?= $status_color; ?>">
                                        <?= $status_label; ?>
                                    </span>
                                </div>
                                <p class="text-[10px] text-slate-500 font-mono mt-1"><?= gmdate('d M Y, H:i', $start_ts) . ' UTC'; ?></p>
                            </div>
                        </div>

                        <div class="flex items-center gap-1.5 flex-shrink-0 relative" data-dropdown-wrap>
                            <button onclick="toggleDropdown(this)" class="session-ctrl-btn px-3 py-1.5 bg-white/[0.04] text-slate-300 text-[10px] font-semibold rounded-lg border border-white/[0.08] hover:bg-white/[0.08] transition-all flex items-center gap-1.5">
                                <i data-lucide="sliders-horizontal" class="w-3 h-3"></i> Atur
                                <i data-lucide="chevron-down" class="w-3 h-3"></i>
                            </button>
                            <div class="session-dropdown hidden absolute right-0 top-full mt-1 z-50 w-48 py-1 bg-slate-800 border border-white/[0.08] rounded-xl shadow-2xl overflow-hidden">
                                <button onclick="setFlag(<?= $s['id_session']; ?>, 'NULL', this)" class="w-full text-left px-3 py-2 text-[11px] text-emerald-400 hover:bg-emerald-500/10 flex items-center gap-2 transition-colors">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Normal
                                </button>
                                <button onclick="setFlag(<?= $s['id_session']; ?>, 'YELLOW FLAG', this)" class="w-full text-left px-3 py-2 text-[11px] text-yellow-400 hover:bg-yellow-500/10 flex items-center gap-2 transition-colors">
                                    <span class="w-2 h-2 rounded-full bg-yellow-500"></span> Yellow Flag
                                </button>
                                <button onclick="setFlag(<?= $s['id_session']; ?>, 'RED FLAG', this)" class="w-full text-left px-3 py-2 text-[11px] text-red-400 hover:bg-red-500/10 flex items-center gap-2 transition-colors">
                                    <span class="w-2 h-2 rounded-full bg-red-500"></span> Red Flag
                                </button>
                                <button onclick="setFlag(<?= $s['id_session']; ?>, 'SC', this)" class="w-full text-left px-3 py-2 text-[11px] text-orange-400 hover:bg-orange-500/10 flex items-center gap-2 transition-colors">
                                    <span class="w-2 h-2 rounded-full bg-orange-500"></span> Safety Car
                                </button>
                                <button onclick="setFlag(<?= $s['id_session']; ?>, 'VSC', this)" class="w-full text-left px-3 py-2 text-[11px] text-amber-400 hover:bg-amber-500/10 flex items-center gap-2 transition-colors">
                                    <span class="w-2 h-2 rounded-full bg-amber-500"></span> VSC
                                </button>
                                <div class="h-px bg-white/[0.06] my-1"></div>
                                <button onclick="setFlag(<?= $s['id_session']; ?>, 'FINISHED', this)" class="w-full text-left px-3 py-2 text-[11px] text-slate-400 hover:bg-slate-500/10 flex items-center gap-2 transition-colors">
                                    <span class="w-2 h-2 rounded-full bg-slate-500"></span> Finished
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script>
function toggleDropdown(btn) {
    const wrap = btn.closest('[data-dropdown-wrap]');
    const dropdown = wrap.querySelector('.session-dropdown');
    document.querySelectorAll('.session-dropdown').forEach(d => {
        if (d !== dropdown) d.classList.add('hidden');
    });
    dropdown.classList.toggle('hidden');
}

document.addEventListener('click', function(e) {
    if (!e.target.closest('[data-dropdown-wrap]')) {
        document.querySelectorAll('.session-dropdown').forEach(d => d.classList.add('hidden'));
    }
});

function setFlag(idSession, status, btn) {
    const dropdown = btn.closest('.session-dropdown');
    dropdown.classList.add('hidden');

    const card = btn.closest('[data-session-id]');
    const ctrlBtn = card.querySelector('.session-ctrl-btn');
    ctrlBtn.disabled = true;
    ctrlBtn.innerHTML = '<span class="inline-block animate-spin rounded-full h-3 w-3 border-b-2 border-slate-400 mr-1 align-middle"></span> ...';

    const csrfName = document.querySelector('meta[name="csrf-token-name"]').content;
    const csrfHash = document.querySelector('meta[name="csrf-token-hash"]').content;

    fetch('<?= base_url("admin/set_session_status"); ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: csrfName + '=' + encodeURIComponent(csrfHash) + '&id_session=' + idSession + '&status=' + encodeURIComponent(status)
    })
    .then(r => r.json())
    .then(data => {
        ctrlBtn.disabled = false;
        ctrlBtn.innerHTML = '<i data-lucide="sliders-horizontal" class="w-3 h-3"></i> Atur <i data-lucide="chevron-down" class="w-3 h-3"></i>';
        lucide.createIcons();

        if (data.status === 'success') {
            const dot = card.querySelector('.w-2.h-2.rounded-full');
            const badge = card.querySelector('.inline-flex.items-center');
            const newStatus = data.new_status;

            const flagColors = {
                'YELLOW FLAG': { dot: 'bg-yellow-500 animate-pulse', badge: 'bg-yellow-500/10 text-yellow-400 border-yellow-500/20', label: 'YELLOW FLAG' },
                'RED FLAG':    { dot: 'bg-red-500 animate-pulse',    badge: 'bg-red-500/10 text-red-400 border-red-500/20',       label: 'RED FLAG' },
                'SC':          { dot: 'bg-orange-500 animate-pulse',  badge: 'bg-orange-500/10 text-orange-400 border-orange-500/20', label: 'SC' },
                'VSC':         { dot: 'bg-amber-500 animate-pulse',   badge: 'bg-amber-500/10 text-amber-400 border-amber-500/20',  label: 'VSC' },
                'FINISHED':    { dot: 'bg-slate-500',                badge: 'bg-slate-500/10 text-slate-400 border-slate-500/20', label: 'Finished' },
                null:          { dot: 'bg-emerald-500',              badge: 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20', label: 'Normal' },
            };

            const colors = flagColors[newStatus] || flagColors[null];
            dot.className = 'w-2 h-2 rounded-full flex-shrink-0 ' + colors.dot;
            badge.className = 'inline-flex items-center text-[9px] font-semibold px-1.5 py-0.5 rounded-full border ' + colors.badge;
            badge.innerText = colors.label;

            showToast(data.message, 'green');
        } else {
            showToast(data.message, 'red');
        }
    })
    .catch(() => {
        ctrlBtn.disabled = false;
        ctrlBtn.innerHTML = '<i data-lucide="sliders-horizontal" class="w-3 h-3"></i> Atur <i data-lucide="chevron-down" class="w-3 h-3"></i>';
        lucide.createIcons();
        showToast('Terjadi kesalahan jaringan.', 'red');
    });
}
</script>
