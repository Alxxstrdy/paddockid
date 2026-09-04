<div class="space-y-6">
    <div>
        <h1 class="text-page-title">Race Sessions</h1>
        <p class="text-caption mt-1">Kelola status sesi balapan secara real-time</p>
    </div>

    <div class="info-box">
        <div class="info-box__icon info-box__icon--info">
            <i data-lucide="info" class="w-4 h-4"></i>
        </div>
        <div class="info-box__text">
            <p class="text-xs c-secondary font-medium mb-1">Cara kerja:</p>
            <p>Klik tombol <span class="c-white font-semibold">status</span> pada sesi untuk mengubah. Status akan langsung terlihat di sidebar pengguna dalam ~15 detik.</p>
            <div class="flex flex-wrap gap-2 mt-2">
                <span class="badge badge-success font-semibold">Normal</span>
                <span class="badge badge-yellow font-semibold">Yellow Flag</span>
                <span class="badge badge-danger font-semibold">Red Flag</span>
                <span class="badge badge-orange font-semibold">SC</span>
                <span class="badge badge-warning font-semibold">VSC</span>
                <span class="badge badge-muted font-semibold">Finished</span>
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
            <div class="card empty-state">
                <i data-lucide="timer" class="w-8 h-8 mb-3" style="color:var(--text-faint);"></i>
                <p class="empty-state__text">Belum ada sesi balapan.</p>
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
                    $dot_bg = 'var(--color-danger)'; $dot_pulse = true;
                } elseif ($is_finished) {
                    $dot_bg = 'var(--text-subtle)'; $dot_pulse = false;
                } elseif ($is_live) {
                    $dot_bg = 'var(--color-success)'; $dot_pulse = true;
                } elseif ($is_started) {
                    $dot_bg = 'var(--color-warning)'; $dot_pulse = false;
                } else {
                    $dot_bg = 'var(--color-info)'; $dot_pulse = false;
                }

                if ($is_flagged) {
                    $status_label = $current_flag;
                    $status_color = 'badge-danger';
                } elseif ($is_finished) {
                    $status_label = 'Finished';
                    $status_color = 'badge-muted';
                } elseif ($is_live) {
                    $status_label = 'Live';
                    $status_color = 'badge-success';
                } elseif ($is_started) {
                    $status_label = 'Selesai';
                    $status_color = 'badge-warning';
                } else {
                    $status_label = 'Upcoming';
                    $status_color = 'badge-info';
                }

                if ($s['race_id'] !== $prev_race_id):
                    $prev_race_id = $s['race_id'];
                ?>
                    <div class="flex-row gap-3 pt-2">
                        <div class="divider-v"></div>
                        <span class="text-section-title"><?= htmlspecialchars($s['gp_name']); ?></span>
                        <div class="divider-v"></div>
                    </div>
                <?php endif; ?>

                <div class="card rounded-xl transition-colors" style="padding:16px;" data-session-id="<?= $s['id_session']; ?>" onmouseover="this.style.borderColor='var(--border-strong)'" onmouseout="this.style.borderColor='var(--border-subtle)'">
                    <div class="flex-row justify-between gap-4">
                        <div class="flex-row gap-4 min-w-0">
                            <div class="flex-shrink-0 <?= $dot_pulse ? 'animate-pulse' : '' ?>" style="width:8px;height:8px;border-radius:50%;background:<?= $dot_bg ?>;"></div>
                            <div class="min-w-0">
                                <div class="flex-row gap-2 flex-wrap">
                                    <?php if (!empty($s['gp_subtitle'])): ?>
                                        <span class="text-caption"><?= htmlspecialchars($s['gp_subtitle']); ?></span>
                                    <?php endif; ?>
                                    <span class="font-bold uppercase" style="font-size:10px;color:var(--text-secondary);letter-spacing:0.04em;"><?= htmlspecialchars($s['session_name']); ?></span>
                                    <span class="badge <?= $status_color; ?> badge-pill">
                                        <?= $status_label; ?>
                                    </span>
                                </div>
                                <p class="text-micro mt-1"><?= gmdate('d M Y, H:i', $start_ts) . ' UTC'; ?></p>
                            </div>
                        </div>

                        <div class="flex-row gap-1-5 flex-shrink-0 relative" data-dropdown-wrap>
                            <button onclick="toggleDropdown(this)" class="session-ctrl-btn btn btn-xs btn-secondary flex-row gap-1-5">
                                <i data-lucide="sliders-horizontal" class="w-3 h-3"></i> Atur
                                <i data-lucide="chevron-down" class="w-3 h-3"></i>
                            </button>
                            <div class="session-dropdown hidden absolute right-0 top-full z-dropdown" style="margin-top:4px;width:192px;padding:4px 0;background:var(--bg-surface-raised);border:1px solid var(--border-strong);border-radius:var(--radius-lg);box-shadow:var(--shadow-xl);overflow:hidden;">
                                <button onclick="setFlag(<?= $s['id_session']; ?>, 'NULL', this)" class="w-full text-left flex-row gap-2 transition-colors" style="padding:8px 12px;font-size:11px;color:var(--color-success);" onmouseover="this.style.background='var(--color-success-bg)'" onmouseout="this.style.background='transparent'">
                                    <span class="rounded-full" style="width:8px;height:8px;background:var(--color-success);"></span> Normal
                                </button>
                                <button onclick="setFlag(<?= $s['id_session']; ?>, 'YELLOW FLAG', this)" class="w-full text-left flex-row gap-2 transition-colors" style="padding:8px 12px;font-size:11px;color:var(--color-yellow);" onmouseover="this.style.background='var(--color-yellow-bg)'" onmouseout="this.style.background='transparent'">
                                    <span class="rounded-full" style="width:8px;height:8px;background:var(--color-yellow);"></span> Yellow Flag
                                </button>
                                <button onclick="setFlag(<?= $s['id_session']; ?>, 'RED FLAG', this)" class="w-full text-left flex-row gap-2 transition-colors" style="padding:8px 12px;font-size:11px;color:var(--color-danger);" onmouseover="this.style.background='var(--color-danger-bg)'" onmouseout="this.style.background='transparent'">
                                    <span class="rounded-full" style="width:8px;height:8px;background:var(--color-danger);"></span> Red Flag
                                </button>
                                <button onclick="setFlag(<?= $s['id_session']; ?>, 'SC', this)" class="w-full text-left flex-row gap-2 transition-colors" style="padding:8px 12px;font-size:11px;color:var(--color-orange);" onmouseover="this.style.background='var(--color-orange-bg)'" onmouseout="this.style.background='transparent'">
                                    <span class="rounded-full" style="width:8px;height:8px;background:var(--color-orange);"></span> Safety Car
                                </button>
                                <button onclick="setFlag(<?= $s['id_session']; ?>, 'VSC', this)" class="w-full text-left flex-row gap-2 transition-colors" style="padding:8px 12px;font-size:11px;color:var(--color-warning);" onmouseover="this.style.background='var(--color-warning-bg)'" onmouseout="this.style.background='transparent'">
                                    <span class="rounded-full" style="width:8px;height:8px;background:var(--color-warning);"></span> VSC
                                </button>
                                <div class="divider" style="margin:4px 0;"></div>
                                <button onclick="setFlag(<?= $s['id_session']; ?>, 'FINISHED', this)" class="w-full text-left flex-row gap-2 transition-colors" style="padding:8px 12px;font-size:11px;color:var(--text-subtle);" onmouseover="this.style.background='rgba(100,116,139,0.08)'" onmouseout="this.style.background='transparent'">
                                    <span class="rounded-full" style="width:8px;height:8px;background:var(--text-subtle);"></span> Finished
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
    ctrlBtn.innerHTML = '<span class="inline-block animate-spin rounded-full h-3 w-3 border-b-2 mr-1 align-middle" style="border-color:var(--text-subtle);border-bottom-color:transparent;"></span> ...';

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
            const dot = card.querySelector('.rounded-full');
            const badge = card.querySelector('.badge');
            const newStatus = data.new_status;

            const flagColors = {
                'YELLOW FLAG': { dotBg: 'var(--color-yellow)', dotPulse: true, badge: 'badge-yellow', label: 'YELLOW FLAG' },
                'RED FLAG':    { dotBg: 'var(--color-danger)', dotPulse: true, badge: 'badge-danger', label: 'RED FLAG' },
                'SC':          { dotBg: 'var(--color-orange)', dotPulse: true, badge: 'badge-orange', label: 'SC' },
                'VSC':         { dotBg: 'var(--color-warning)', dotPulse: true, badge: 'badge-warning', label: 'VSC' },
                'FINISHED':    { dotBg: 'var(--text-subtle)', dotPulse: false, badge: 'badge-muted', label: 'Finished' },
                null:          { dotBg: 'var(--color-success)', dotPulse: false, badge: 'badge-success', label: 'Normal' },
            };

            const colors = flagColors[newStatus] || flagColors[null];
            dot.style.background = colors.dotBg;
            dot.className = 'flex-shrink-0 rounded-full' + (colors.dotPulse ? ' animate-pulse' : '');
            dot.style.width = '8px';
            dot.style.height = '8px';
            badge.className = 'badge ' + colors.badge + ' badge-pill';
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
