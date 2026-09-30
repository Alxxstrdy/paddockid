<div class="space-y-4 max-w-2xl mx-auto px-4 py-6">
    <div class="flex items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <a href="<?= base_url('home'); ?>" class="p-2 c-muted rounded-xl transition-colors" style="cursor:pointer" onmouseover="this.style.color='var(--text-primary)';this.style.background='var(--bg-surface-active)'" onmouseout="this.style.color='';this.style.background=''">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <div>
                <h2 class="text-heading text-sm c-white">Simulasi Race (Eksperimen)</h2>
                <p class="text-micro mt-0-5" style="color:var(--text-subtle)">What-if: "Kalau <span id="lbl-driver">driver</span> masuk pit di lap X, apa yang terjadi?" — Data gap & posisi dihitung dari lap time (API Ergast/Jolpi).</p>
            </div>
        </div>
        <span class="badge badge-pill badge-warning" style="font-size:8px;font-weight:700;letter-spacing:0.06em;text-transform:uppercase">BETA</span>
    </div>

    <div class="card p-5 space-y-3">
        <div class="grid-2 gap-3" style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
            <div>
                <label class="text-micro font-semibold c-muted" style="display:block;margin-bottom:6px;text-transform:uppercase;letter-spacing:0.05em">Race</label>
                <select id="sel-round" class="input w-full" style="background:var(--bg-surface-raised);border:1px solid var(--border-default)">
                    <?php if (!empty($schedule)): ?>
                        <?php foreach ($schedule as $r): ?>
                            <option value="<?= (int)$r['round']; ?>" data-season="<?= htmlspecialchars($r['season'], ENT_QUOTES, 'UTF-8'); ?>"><?= htmlspecialchars($r['name'], ENT_QUOTES, 'UTF-8'); ?></option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <option value="">Gagal ambil jadwal — cek koneksi API</option>
                    <?php endif; ?>
                </select>
            </div>
            <div>
                <label class="text-micro font-semibold c-muted" style="display:block;margin-bottom:6px;text-transform:uppercase;letter-spacing:0.05em">Driver (masuk pit)</label>
                <select id="sel-driver" class="input w-full" style="background:var(--bg-surface-raised);border:1px solid var(--border-default)">
                    <option value="">Memuat driver…</option>
                </select>
            </div>
        </div>

        <div class="grid-2 gap-3" style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
            <div>
                <label class="text-micro font-semibold c-muted" style="display:block;margin-bottom:6px;text-transform:uppercase;letter-spacing:0.05em">Masuk pit di lap</label>
                <input id="inp-lap" type="number" min="1" value="25" class="input w-full" style="background:var(--bg-surface-raised);border:1px solid var(--border-default)">
            </div>
            <div>
                <label class="text-micro font-semibold c-muted" style="display:block;margin-bottom:6px;text-transform:uppercase;letter-spacing:0.05em">Durasi pit (detik)</label>
                <input id="inp-duration" type="number" min="0" step="0.1" value="20" class="input w-full" style="background:var(--bg-surface-raised);border:1px solid var(--border-default)">
            </div>
        </div>

        <div class="flex items-center justify-between gap-3 pt-1">
            <p class="text-micro leading-relaxed" style="color:var(--text-subtle);flex:1">Kosongkan durasi = pakai durasi pit asli (<span id="lbl-actual">20s</span>). Simulasi dihitung dari waktu kumulatif per lap (estimasi, bukan timing live resmi).</p>
            <button id="btn-run" onclick="runSim(false)" class="btn btn-primary btn-sm shadow-lg" style="flex-shrink:0">
                <i data-lucide="play" class="w-3 h-3 inline-block mr-1"></i> Simulasikan
            </button>
        </div>
    </div>

    <div class="card p-5 space-y-3">
        <div class="flex items-center justify-between flex-wrap gap-2">
            <span class="c-subtle" id="out-status" style="font-size:11px">Belum ada simulasi. Pilih race & driver di atas, lalu klik Simulasikan.</span>
            <button id="btn-reset" class="hidden btn btn-xs btn-secondary" onclick="resultVue.hide()">Tutup hasil</button>
        </div>
    </div>

    <div id="result-box" class="space-y-4 hidden">
        <div class="card p-5">
            <div class="flex items-center gap-3 mb-4">
                <div class="relative flex-shrink-0" style="width:44px;height:44px;">
                    <div class="w-full h-full rounded-full flex items-center justify-center font-bold" style="background:var(--color-primary-bg);color:var(--color-primary);font-size:15px" id="r-code">–</div>
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-bold c-white" id="r-driver">–</p>
                    <p class="text-micro c-subtle" id="r-team">–</p>
                </div>
                <div class="ml-auto flex items-center gap-2" style="text-align:right">
                    <div>
                        <p class="text-micro c-subtle" style="text-transform:uppercase;letter-spacing:0.05em">Asli</p>
                        <p class="text-lg font-extrabold c-white" id="r-base-pos">–</p>
                        <p class="text-micro c-subtle" id="r-base-gap">–</p>
                    </div>
                    <i data-lucide="arrow-right" class="w-4 h-4 c-muted"></i>
                    <div>
                        <p class="text-micro c-subtle" style="text-transform:uppercase;letter-spacing:0.05em">Simulasi</p>
                        <p class="text-lg font-extrabold" id="r-sim-pos" style="color:var(--color-primary)">–</p>
                        <p class="text-micro c-subtle" id="r-sim-gap">–</p>
                    </div>
                </div>
            </div>
            <div id="r-pit" class="hidden flex items-center gap-2 rounded-lg px-3 py-2 text-micro mb-3" style="background:rgba(255,255,255,0.03);border:1px solid var(--border-subtle);color:var(--text-muted)"></div>
            <div class="rounded-lg px-3 py-2 text-micro" style="background:var(--color-primary-bg);color:var(--color-primary);line-height:1.5" id="r-summary"></div>
        </div>

        <div class="card p-5">
            <h3 class="text-xs font-bold c-muted mb-3" style="text-transform:uppercase;letter-spacing:0.06em">Per Lap — <span id="t-driver">driver</span></h3>
            <div class="sim-scroll">
                <table class="sim-table w-full" style="border-collapse:collapse;font-size:11px">
                    <thead>
                        <tr class="c-subtle" style="border-bottom:1px solid var(--border-subtle)">
                            <th style="padding:6px 8px;text-align:left">Lap</th>
                            <th style="padding:6px 8px;text-align:center">Pos Asli</th>
                            <th style="padding:6px 8px;text-align:right">Gap Asli</th>
                            <th style="padding:6px 8px;text-align:center">Pos Sim</th>
                            <th style="padding:6px 8px;text-align:right">Gap Sim</th>
                            <th style="padding:6px 8px;text-align:center">Δ</th>
                        </tr>
                    </thead>
                    <tbody id="r-rows"></tbody>
                </table>
            </div>
        </div>

        <div class="card p-5" id="r-changes-card">
            <h3 class="text-xs font-bold c-muted mb-3" style="text-transform:uppercase;letter-spacing:0.06em">Driver yang berubah posisi</h3>
            <div id="r-changes" class="space-y-2"></div>
        </div>

        <div class="card p-5">
            <h3 class="text-xs font-bold c-muted mb-3" style="text-transform:uppercase;letter-spacing:0.06em">Klasemen Akhir (lap terakhir)</h3>
            <table class="sim-table w-full" style="border-collapse:collapse;font-size:11px">
                <thead>
                    <tr class="c-subtle" style="border-bottom:1px solid var(--border-subtle)">
                        <th style="padding:6px 8px;text-align:left">#</th>
                        <th style="padding:6px 8px;text-align:left">Driver</th>
                        <th style="padding:6px 8px;text-align:center">P1</th>
                        <th style="padding:6px 8px;text-align:center">P2</th>
                        <th style="padding:6px 8px;text-align:center">Δ</th>
                        <th style="padding:6px 8px;text-align:left">Posisi Asli → Sim</th>
                    </tr>
                </thead>
                <tbody id="r-final"></tbody>
            </table>
        </div>
    </div>
</div>

<script>
const BASE = '<?= base_url('simrace/'); ?>';
const resultVue = {
    show() { document.getElementById('result-box').classList.remove('hidden'); document.getElementById('btn-reset').classList.remove('hidden'); },
    hide()  { document.getElementById('result-box').classList.add('hidden'); document.getElementById('btn-reset').classList.add('hidden'); }
};

function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str == null ? '' : String(str);
    return div.innerHTML;
}

function fmtTime(sec) {
    sec = Math.max(0, Number(sec) || 0);
    const ms = Math.round((sec - Math.floor(sec)) * 1000);
    let s2 = Math.floor(sec);
    if (ms >= 1000) { s2 += 1; }
    const h = Math.floor(s2 / 3600);
    const m = Math.floor((s2 % 3600) / 60);
    const s = s2 % 60;
    const pad = (x, n) => String(x).padStart(n, '0');
    if (h > 0) return h + ':' + pad(m,2) + ':' + pad(s,2) + '.' + pad(ms,3);
    return m + ':' + pad(s,2) + '.' + pad(ms,3);
}

function currentRound() {
    const sel = document.getElementById('sel-round');
    return { season: sel.selectedOptions[0]?.dataset.season, round: sel.value };
}

async function loadDrivers() {
    const { season, round } = currentRound();
    const sel = document.getElementById('sel-driver');
    if (!season || !round) return;
    sel.innerHTML = '<option value="">Memuat driver…</option>';
    try {
        const res = await fetch(BASE + 'drivers?season=' + encodeURIComponent(season) + '&round=' + encodeURIComponent(round));
        const drivers = await res.json();
        if (!Array.isArray(drivers) || !drivers.length) { sel.innerHTML = '<option value="">Tidak ada data</option>'; return; }
        sel.innerHTML = drivers.map(d => {
            const label = (d.code ? (d.code + ' — ') : '') + d.name + (d.constructor ? ' (' + d.constructor + ')' : '');
            return '<option value="' + escapeHtml(d.driverId) + '">' + escapeHtml(label) + '</option>';
        }).join('');
        document.getElementById('lbl-driver').textContent = drivers[0].code || 'driver';
        updateActualPit();
    } catch (e) {
        sel.innerHTML = '<option value="">Gagal ambil driver</option>';
    }
}

async function updateActualPit() {
    // ambil informasi durasi pit asli otomatis dari simulasi kosong-kan duration (dipakai saat runSim)
    const { season, round } = currentRound();
    const d = document.getElementById('sel-driver').value;
    if (!season || !round || !d) return;
    const lap = parseInt(document.getElementById('inp-lap').value) || 0;
    try {
        const res = await fetch(BASE + 'api?season=' + encodeURIComponent(season) + '&round=' + encodeURIComponent(round) + '&driver=' + encodeURIComponent(d) + '&pit_lap=' + lap + '&duration=0');
        const data = await res.json();
        if (data && data.actual_pit) {
            document.getElementById('lbl-actual').textContent = data.actual_pit.duration + 's (lap ' + data.actual_pit.lap + ')';
        } else {
            document.getElementById('lbl-actual').textContent = '20s';
        }
    } catch (e) {}
}

async function runSim() {
    const { season, round } = currentRound();
    const driver = document.getElementById('sel-driver').value;
    const pitLap = parseInt(document.getElementById('inp-lap').value) || 0;
    const duration = document.getElementById('inp-duration').value;

    if (!season || !round) { alert('Race belum tersedia, cek koneksi API.'); return; }
    if (!driver) { alert('Driver belum dipilih.'); return; }
    if (!pitLap) { alert('Isi lap masuk pit.'); return; }

    const btn = document.getElementById('btn-run');
    btn.disabled = true;
    document.getElementById('out-status').textContent = 'Mengambil data & menghitung simulasi…';
    resultVue.hide();

    const q = 'season=' + encodeURIComponent(season) + '&round=' + encodeURIComponent(round)
        + '&driver=' + encodeURIComponent(driver) + '&pit_lap=' + pitLap
        + '&duration=' + encodeURIComponent(duration);

    try {
        const res = await fetch(BASE + 'api?' + q);
        const data = await res.json();
        if (data.error) throw new Error(data.error);

        renderResult(data);
        document.getElementById('out-status').textContent = 'Simulasi selesai — makan waktu kira-kira 1 detik per pembacaan lap time yang di-cache.';
        resultVue.show();
    } catch (e) {
        document.getElementById('out-status').textContent = 'Gagal: ' + e.message;
    } finally {
        btn.disabled = false;
    }
}

function renderResult(d) {
    const code = d.driver.code || '?';
    document.getElementById('lbl-driver').textContent = code;
    document.getElementById('t-driver').textContent = d.driver.code + ' — ' + d.driver.name;
    document.getElementById('r-code').textContent = code.slice(0, 3).toUpperCase();
    document.getElementById('r-driver').textContent = d.driver.name + ' (' + d.meta.title + ')';
    document.getElementById('r-team').textContent = d.driver.constructor ? d.driver.constructor + ' · masuk pit lap ' + d.pit_lap + ' · durasi ' + d.duration + 's' : 'masuk pit lap ' + d.pit_lap + ' · durasi ' + d.duration + 's';
    document.getElementById('r-base-pos').textContent = 'P' + d.base_final;
    document.getElementById('r-sim-pos').textContent = 'P' + d.sim_final;
    document.getElementById('r-sim-pos').style.color = d.sim_final < d.base_final ? 'var(--color-success)' : (d.sim_final > d.base_final ? 'var(--color-danger)' : 'var(--color-primary)');

    const baseRows = d.driver_rows;
    const last = baseRows[baseRows.length - 1];
    document.getElementById('r-base-gap').textContent = last ? 'gap ' + fmtTime(last.base_gap) : '–';
    document.getElementById('r-sim-gap').textContent  = last ? 'gap ' + fmtTime(last.sim_gap) : '–';

    const pitBox = document.getElementById('r-pit');
    if (d.actual_pit) {
        pitBox.classList.remove('hidden');
        pitBox.textContent = 'Pit asli di race ini: lap ' + d.actual_pit.lap + ' (stop #' + d.actual_pit.stop + ') ' + d.actual_pit.duration + 's — simulasi memakai lap ' + d.pit_lap + ' selama ' + d.duration + 's.'
            + (d.pit_lap === d.actual_pit.lap ? ' (sama dengan kejadian aslinya)' : '');
    } else {
        pitBox.classList.add('hidden');
    }

    const delta = d.sim_final - d.base_final;
    const summaryEl = document.getElementById('r-summary');
    if (delta === 0) {
        summaryEl.textContent = 'Dengan pit hipotetis ini, posisi ' + code + ' tetap P' + d.sim_final + ' — tidak melewati/menurun. Gap ke leader: ' + (last ? fmtTime(last.base_gap) + ' → ' + fmtTime(last.sim_gap) : '–') + '.';
    } else if (delta < 0) {
        summaryEl.textContent = 'Dengan pit hipotetis ini, ' + code + ' naik dari P' + d.base_final + ' ke P' + d.sim_final + ' (' + Math.abs(delta) + ' posisi), gap ke leader menjadi ' + (last ? fmtTime(last.sim_gap) : '–') + '.';
    } else {
        summaryEl.textContent = 'Pit tambahan membuat ' + code + ' turun dari P' + d.base_final + ' ke P' + d.sim_final + ' (' + delta + ' posisi), gap ke leader menjadi ' + (last ? fmtTime(last.sim_gap) : '–') + '.';
    }

    // tabel per lap
    const tbody = document.getElementById('r-rows');
    tbody.innerHTML = baseRows.map(r => {
        const dPos = r.sim_pos - r.base_pos;
        const dTxt = dPos === 0 ? '<span class="c-subtle">–</span>' : (dPos < 0 ? '<span class="font-bold" style="color:var(--color-success)">+' + Math.abs(dPos) + '</span>' : '<span class="font-bold" style="color:var(--color-danger)">-' + dPos + '</span>');
        const isPitLap = r.lap === d.pit_lap;
        return '<tr style="border-bottom:1px solid var(--border-subtle);' + (isPitLap ? 'background:var(--color-primary-bg);' : '') + '">'
            + '<td style="padding:5px 8px;color:var(--text-muted)"' + (isPitLap ? '><span class="font-bold" style="color:var(--color-primary)">' + r.lap + ' ● PIT</span>' : '>' + r.lap) + '</td>'
            + '<td style="padding:5px 8px;text-align:center;color:var(--text-secondary)">P' + r.base_pos + '</td>'
            + '<td style="padding:5px 8px;text-align:right;color:var(--text-muted)">' + fmtTime(r.base_gap) + '</td>'
            + '<td style="padding:5px 8px;text-align:center;color:var(--text-secondary)">P' + r.sim_pos + '</td>'
            + '<td style="padding:5px 8px;text-align:right;color:var(--text-muted)">' + fmtTime(r.sim_gap) + '</td>'
            + '<td style="padding:5px 8px;text-align:center">' + dTxt + '</td>'
            + '</tr>';
    }).join('');

    // perubahan posisi driver lain
    const changesEl = document.getElementById('r-changes');
    if (d.changes && d.changes.length) {
        document.getElementById('r-changes-card').classList.remove('hidden');
        changesEl.innerHTML = d.changes.slice(0, 20).map(c => {
            return '<div class="flex items-center gap-2 rounded-lg px-3 py-1-5 text-micro" style="background:rgba(255,255,255,0.02);border:1px solid var(--border-subtle)">'
                + '<span class="font-bold c-white">' + escapeHtml(c.code || c.driver) + '</span>'
                + '<span class="c-subtle">lap ' + c.lap + '</span>'
                + '<span class="ml-auto">P' + c.base + ' <i class="c-muted">→</i> P' + c.sim + '</span>'
                + '</div>';
        }).join('');
    } else {
        document.getElementById('r-changes-card').classList.add('hidden');
        changesEl.innerHTML = '';
    }

    // klasemen akhir
    const finalRows = d.final || [];
    const finTbody = document.getElementById('r-final');
    finTbody.innerHTML = finalRows.map((f, i) => {
        const dd = f.sim - f.base;
        const dTxt = dd === 0 ? '<span class="c-subtle">–</span>' : (dd < 0 ? '<span class="font-bold" style="color:var(--color-success)">+' + Math.abs(dd) + '</span>' : '<span class="font-bold" style="color:var(--color-danger)">-' + dd + '</span>');
        const isTarget = f.driver === d.driver.id;
        const move = f.base !== f.sim ? 'P' + f.base + ' → P' + f.sim : 'P' + f.base;
        return '<tr style="border-bottom:1px solid var(--border-subtle);' + (isTarget ? 'background:var(--color-primary-bg);' : '') + '">'
            + '<td style="padding:5px 8px;color:var(--text-muted)">' + (i + 1) + '</td>'
            + '<td style="padding:5px 8px" class="font-bold">' + escapeHtml((f.code || f.driver)) + '</td>'
            + '<td style="padding:5px 8px;text-align:center;color:var(--text-muted)">' + f.base + '</td>'
            + '<td style="padding:5px 8px;text-align:center;color:var(--text-muted)">' + f.sim + '</td>'
            + '<td style="padding:5px 8px;text-align:center">' + dTxt + '</td>'
            + '<td style="padding:5px 8px;color:var(--text-muted)">' + move + '</td>'
            + '</tr>';
    }).join('');

    if (typeof lucide !== 'undefined') lucide.createIcons();
}

document.addEventListener('DOMContentLoaded', function () {
    const selRound = document.getElementById('sel-round');
    selRound.addEventListener('change', function () { loadDrivers(); });
    document.getElementById('sel-driver').addEventListener('change', updateActualPit);
    document.getElementById('inp-lap').addEventListener('change', updateActualPit);
    if (selRound.value) loadDrivers();
});
</script>