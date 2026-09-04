<div class="space-y-6">
    <div class="flex-row justify-between">
        <h1 class="text-page-title text-lg">Race Hub</h1>
    </div>

    <div class="tabs">
        <button id="tab-calendar" onclick="switchRaceTab('calendar')" class="tab tab-active" data-tab="calendar">
            <i data-lucide="calendar" class="w-3-5 h-3-5 inline-block mr-1"></i>Calendar
        </button>
        <button id="tab-standings" onclick="switchRaceTab('standings')" class="tab tab-inactive" data-tab="standings">
            <i data-lucide="trophy" class="w-3-5 h-3-5 inline-block mr-1"></i>Standings
        </button>
        <button id="tab-results" onclick="switchRaceTab('results')" class="tab tab-inactive" data-tab="results">
            <i data-lucide="flag" class="w-3-5 h-3-5 inline-block mr-1"></i>Results
        </button>
    </div>

    <div id="race-calendar" class="space-y-3">
        <div class="text-center py-12 c-subtle text-xs">
            <div class="spinner spinner--sm spinner-white inline-block mr-1 align-middle"></div>
            Memuat kalender...
        </div>
    </div>

    <div id="race-standings" class="hidden space-y-6">
        <div class="text-center py-12 c-subtle text-xs">
            <div class="spinner spinner--sm spinner-white inline-block mr-1 align-middle"></div>
            Memuat klasemen...
        </div>
    </div>

    <div id="race-results" class="hidden space-y-4">
        <div class="card p-4">
            <label class="text-xs c-muted block mb-2 font-semibold">Pilih Grand Prix</label>
            <select id="results-round-select" onchange="loadRaceResults(this.value)" class="w-full select" style="font-size:12px;padding:10px 12px">
                <option value="">-- Pilih Race --</option>
            </select>
        </div>
        <div id="results-content">
            <div class="text-center py-12 c-subtle text-xs">Pilih Grand Prix untuk melihat hasil.</div>
        </div>
    </div>
</div>

<script>
let raceSchedule = [];

function switchRaceTab(tab) {
    document.querySelectorAll('[id^="race-"]').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('[id^="tab-"]').forEach(btn => {
        btn.className = btn.className.replace(/tab-active/g, 'tab-inactive');
    });
    document.getElementById('race-' + tab).classList.remove('hidden');
    const activeBtn = document.getElementById('tab-' + tab);
    activeBtn.className = activeBtn.className.replace(/tab-inactive/g, 'tab-active');

    if (tab === 'calendar' && raceSchedule.length === 0) loadRaceSchedule();
    if (tab === 'standings') loadRaceStandings();
}

function loadRaceSchedule() {
    const container = document.getElementById('race-calendar');
    container.innerHTML = '<div class="text-center py-12 c-subtle text-xs"><div class="spinner spinner--sm spinner-white inline-block mr-1 align-middle"></div>Memuat kalender...</div>';

    fetch('<?= base_url("race/get_schedule"); ?>')
        .then(r => r.json())
        .then(data => {
            raceSchedule = data;
            renderCalendar(data, container);
            populateResultsDropdown();
        })
        .catch(() => {
            container.innerHTML = '<div class="card p-8 text-center c-subtle text-xs">Gagal memuat kalender.</div>';
        });
}

function renderCalendar(races, container) {
    if (!races || races.length === 0) {
        container.innerHTML = '<div class="card p-8 text-center c-subtle text-xs">Belum ada jadwal race tersedia.</div>';
        return;
    }

    let html = '';
    races.forEach((race, idx) => {
        const isUpcoming = race.status === 'upcoming';
        const isLive = race.status === 'live';
        const isCompleted = race.status === 'completed';

        let statusBadge = '';
        let statusColor = '';
        if (isUpcoming) {
            statusBadge = 'Upcoming';
            statusColor = 'badge badge-pill badge-upcoming';
        } else if (isLive) {
            statusBadge = 'Live';
            statusColor = 'badge badge-pill badge-live animate-pulse';
        } else {
            statusBadge = 'Completed';
            statusColor = 'badge badge-pill badge-completed';
        }

        const sessionsHtml = race.sessions.map(s => {
            let sColor = 'c-muted';
            let sDot = 'dot-muted';
            if (s.status === 'live') { sColor = 'c-success'; sDot = 'dot-success'; }
            else if (s.status === 'completed') { sColor = 'c-subtle'; sDot = 'dot-faint'; }
            return `<div class="flex-row justify-between gap-2 text-xs py-0-5">
                <div class="flex-row gap-2 min-w-0">
                    <span class="w-2 h-2 rounded-full ${sDot} flex-shrink-0"></span>
                    <span class="font-semibold w-28 text-truncate" style="color:var(--text-secondary)">${escapeHtml(s.name)}</span>
                    <span class="${sColor}">${escapeHtml(s.date)} ${escapeHtml(s.time)} WIB</span>
                </div>
                <a href="${s.chat_slug ? '<?= base_url('chat/room/') ?>' + encodeURIComponent(s.chat_slug) : '#'}"
                   class="flex-shrink-0 text-micro font-semibold rounded-full transition-colors px-2 py-0-5 ${
                       s.status === 'live'
                           ? 'badge badge-pill badge-upcoming'
                           : s.status === 'completed'
                               ? 'badge badge-pill badge-completed'
                               : 'badge badge-pill badge-completed c-muted'
                   }" title="Chat Room" style="font-size:9px">
                    <i data-lucide="message-circle" class="w-3 h-3 inline-block"></i>
                </a>
            </div>`;
        }).join('');

        const raceId = 'race-detail-' + race.round;
        const isExpanded = idx === 0 && !isCompleted;

        html += `
            <div class="card rounded-xl overflow-hidden transition-colors ${isLive ? 'ring-2' : ''}" style="border:1px solid var(--border-subtle)">
                <div class="p-4 sm-p-5">
                    <div class="flex-row justify-between mb-2">
                        <div class="flex-row gap-3">
                            <span class="text-value rounded-md px-2 py-1" style="background:var(--bg-surface-subtle);font-size:10px">R${escapeHtml(race.round)}</span>
                            <span class="text-xs font-semibold" style="color:var(--text-secondary)">${escapeHtml(race.name)}</span>
                        </div>
                        <div class="flex-row gap-2">
                            <span class="${statusColor}" style="font-size:9px;text-transform:uppercase;letter-spacing:0.06em">${statusBadge}</span>
                            <button onclick="toggleRaceDetail(${race.round})" class="c-subtle p-1 transition-colors" onmouseover="this.style.color='var(--text-secondary)'" onmouseout="this.style.color=''">
                                <i data-lucide="chevron-down" class="w-4 h-4 transition-transform ${isExpanded ? 'rotate-180' : ''}" id="chevron-${race.round}"></i>
                            </button>
                        </div>
                    </div>
                    <div class="flex-row gap-2 text-micro c-subtle">
                        <i data-lucide="map-pin" class="w-3 h-3"></i>
                        <span>${escapeHtml(race.circuit)}, ${escapeHtml(race.locality)}, ${escapeHtml(race.country)}</span>
                    </div>
                    <div class="flex-row gap-2 text-micro c-subtle mt-1">
                        <i data-lucide="clock" class="w-3 h-3"></i>
                        <span>Race: ${escapeHtml(race.date)}</span>
                    </div>
                </div>
                <div id="${raceId}" class="border-t ${isExpanded ? '' : 'hidden'}" style="border-color:var(--border-subtle)">
                    <div class="p-4 sm-p-5 space-y-2" style="background:var(--bg-surface-subtle)">
                        ${sessionsHtml}
                    </div>
                </div>
            </div>
        `;
    });

    container.innerHTML = html;
    if (typeof lucide !== 'undefined') lucide.createIcons();
}

function toggleRaceDetail(round) {
    const el = document.getElementById('race-detail-' + round);
    const chevron = document.getElementById('chevron-' + round);
    el.classList.toggle('hidden');
    if (chevron) chevron.classList.toggle('rotate-180');
}

function loadRaceStandings() {
    const container = document.getElementById('race-standings');
    container.innerHTML = '<div class="text-center py-12 c-subtle text-xs"><div class="spinner spinner--sm spinner-white inline-block mr-1 align-middle"></div>Memuat klasemen...</div>';

    fetch('<?= base_url("race/get_standings"); ?>')
        .then(r => r.json())
        .then(data => {
            renderStandings(data, container);
        })
        .catch(() => {
            container.innerHTML = '<div class="card p-8 text-center c-subtle text-xs">Gagal memuat klasemen.</div>';
        });
}

function renderStandings(data, container) {
    const drivers = data.drivers || [];
    const constructors = data.constructors || [];

    function posBadge(pos, color) {
        const colors = {
            1: 'pos-gold',
            2: 'pos-silver',
            3: 'pos-bronze',
        };
        const cls = colors[pos] || 'badge-completed';
        return `<span class="badge ${cls} rounded-md" style="font-size:10px;width:24px;height:24px;display:inline-flex;align-items:center;justify-content:center">${pos}</span>`;
    }

    let driverRows = drivers.map(d => {
        const color = d.constructorColor || '#666';
        return `<tr class="border-b transition-colors" style="border-color:var(--border-subtle);border-left-color:${escapeHtml(color)};border-left-width:2px" onmouseover="this.style.background='var(--bg-surface-subtle)'" onmouseout="this.style.background=''">
            <td class="py-2-5 pl-2 pr-1">${posBadge(d.position, color)}</td>
            <td class="py-2-5 px-2">
                <div class="flex-row gap-2">
                    <div class="w-5 h-5 rounded-full flex-shrink-0 overflow-hidden flex-row justify-center">
                        <img src="${escapeHtml(d.constructorImage)}" alt="${escapeHtml(d.constructor)}" class="w-full h-full" style="object-fit:contain" loading="lazy" onerror="this.style.display='none'">
                    </div>
                    <span class="text-xs font-bold" style="color:var(--text-secondary)">${escapeHtml(d.code)}</span>
                    <span class="text-xs c-muted hide-mobile">${escapeHtml(d.driver)}</span>
                </div>
            </td>
            <td class="py-2-5 px-2 text-xs c-muted hide-desktop" style="display:none">
                <span class="flex-row gap-1-5">
                    <span class="w-2 h-2 rounded-full flex-shrink-0" style="background:${escapeHtml(color)}"></span>
                    ${escapeHtml(d.constructor)}
                </span>
            </td>
            <td class="py-2-5 px-2 text-xs c-subtle hide-desktop" style="display:none">${d.wins > 0 ? d.wins + '🏁' : '-'}</td>
            <td class="py-2-5 px-2 text-right"><span class="text-xs font-bold ${d.points > 0 ? 'c-white' : 'c-subtle'}">${d.points}</span></td>
        </tr>`;
    }).join('');

    let constructorRows = constructors.map(c => {
        const color = c.constructorColor || '#666';
        return `<tr class="border-b transition-colors" style="border-color:var(--border-subtle);border-left-color:${escapeHtml(color)};border-left-width:2px" onmouseover="this.style.background='var(--bg-surface-subtle)'" onmouseout="this.style.background=''">
            <td class="py-2-5 pl-2 pr-1">${posBadge(c.position, color)}</td>
            <td class="py-2-5 px-2">
                <div class="flex-row gap-2-5">
                    <div class="w-6 h-6 rounded-lg flex-shrink-0 overflow-hidden flex-row justify-center">
                        <img src="${escapeHtml(c.constructorImage)}" alt="${escapeHtml(c.constructor)}" class="w-full h-full" style="object-fit:contain" loading="lazy" onerror="this.style.display='none'">
                    </div>
                    <span class="text-xs font-bold" style="color:var(--text-secondary)">${escapeHtml(c.constructor)}</span>
                </div>
            </td>
            <td class="py-2-5 px-2 text-xs c-muted hide-mobile">${escapeHtml(c.nationality || '-')}</td>
            <td class="py-2-5 px-2 text-xs c-subtle hide-desktop" style="display:none">${c.wins > 0 ? c.wins + '🏁' : '-'}</td>
            <td class="py-2-5 px-2 text-right"><span class="text-xs font-bold ${c.points > 0 ? 'c-white' : 'c-subtle'}">${c.points}</span></td>
        </tr>`;
    }).join('');

    container.innerHTML = `
        <div class="card p-4 sm-p-5 overflow-hidden">
                <h3 class="text-xs font-bold c-white mb-4 flex-row gap-2" style="text-transform:uppercase;letter-spacing:0.06em">
                <i data-lucide="users" class="w-4 h-4 c-danger"></i> Driver Standings
            </h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-micro border-b" style="font-size:9px;letter-spacing:0.1em;border-color:var(--border-subtle)">
                            <th class="pb-2 pl-2 pr-1 font-medium w-10">Pos</th>
                            <th class="pb-2 px-2 font-medium">Driver</th>
                            <th class="pb-2 px-2 font-medium hide-mobile">Team</th>
                            <th class="pb-2 px-2 font-medium hide-desktop" style="display:none">Wins</th>
                            <th class="pb-2 px-2 text-right font-medium">Pts</th>
                        </tr>
                    </thead>
                    <tbody>${driverRows}</tbody>
                </table>
            </div>
        </div>

        <div class="card p-4 sm-p-5 overflow-hidden">
                <h3 class="text-xs font-bold c-white mb-4 flex-row gap-2" style="text-transform:uppercase;letter-spacing:0.06em">
                <i data-lucide="building" class="w-4 h-4 c-danger"></i> Constructor Standings
            </h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-micro border-b" style="font-size:9px;letter-spacing:0.1em;border-color:var(--border-subtle)">
                            <th class="pb-2 pl-2 pr-1 font-medium w-10">Pos</th>
                            <th class="pb-2 px-2 font-medium">Team</th>
                            <th class="pb-2 px-2 font-medium hide-mobile">Nationality</th>
                            <th class="pb-2 px-2 font-medium hide-desktop" style="display:none">Wins</th>
                            <th class="pb-2 px-2 text-right font-medium">Pts</th>
                        </tr>
                    </thead>
                    <tbody>${constructorRows}</tbody>
                </table>
            </div>
        </div>
    `;
    if (typeof lucide !== 'undefined') lucide.createIcons();
}

function loadRaceResults(round) {
    if (!round) {
        document.getElementById('results-content').innerHTML = '<div class="text-center py-12 c-subtle text-xs">Pilih Grand Prix untuk melihat hasil.</div>';
        return;
    }

    const container = document.getElementById('results-content');
    container.innerHTML = '<div class="text-center py-12 c-subtle text-xs"><div class="spinner spinner--sm spinner-white inline-block mr-1 align-middle"></div>Memuat hasil...</div>';

    fetch('<?= base_url("race/get_results/"); ?>' + round)
        .then(r => r.json())
        .then(data => {
            if (data.error) {
                container.innerHTML = '<div class="card p-8 text-center c-subtle text-xs">' + escapeHtml(data.error) + '</div>';
                return;
            }
            renderResults(data, container);
        })
        .catch(() => {
            container.innerHTML = '<div class="card p-8 text-center c-subtle text-xs">Gagal memuat hasil.</div>';
        });
}

function renderResults(data, container) {
    const top3 = data.results.slice(0, 3);
    const rest = data.results.slice(3);

    const podiumColors = ['c-yellow', 'c-white', 'c-amber'];

    let podiumHtml = '<div class="flex-row justify-center gap-3 sm-gap-6 mb-6 py-4">';
    const podiumOrder = [1, 0, 2];
    const slotHeights = ['h-20', 'h-28', 'h-16'];
    const slotOrders = ['order-1', 'order-2', 'order-3'];
    const barColors = ['bar-gold', 'bar-silver', 'bar-bronze'];
    podiumOrder.forEach((idx, slotIndex) => {
        const r = top3[idx];
        if (!r) return;
        podiumHtml += `
            <div class="flex-col-center ${slotOrders[slotIndex]}">
                <span class="text-xs font-bold ${podiumColors[idx]}">${escapeHtml(r.positionText)}</span>
                <span class="text-xs font-semibold" style="color:var(--text-secondary)">${escapeHtml(r.code)}</span>
                <span class="text-micro c-muted">${escapeHtml(r.driver)}</span>
                <div class="w-16 sm-w-20 ${slotHeights[slotIndex]} rounded-t-lg flex-col items-end justify-center pb-2 ${barColors[idx]}">
                    <span class="text-micro font-bold" style="color:var(--text-secondary);font-size:10px">${r.points} pts</span>
                </div>
            </div>`;
    });
    podiumHtml += '</div>';

    let resultsRows = rest.map(r => `
        <tr class="border-b transition-colors" style="border-color:var(--border-subtle)" onmouseover="this.style.background='var(--bg-surface-subtle)'" onmouseout="this.style.background=''">
            <td class="py-2 px-2 text-xs c-subtle w-8 text-center">${escapeHtml(r.position)}</td>
            <td class="py-2 px-2 text-xs font-semibold" style="color:var(--text-secondary)">${escapeHtml(r.code)}</td>
            <td class="py-2 px-2 text-xs" style="color:var(--text-secondary)">${escapeHtml(r.driver)}</td>
            <td class="py-2 px-2 text-xs c-muted hide-mobile">${escapeHtml(r.constructor)}</td>
            <td class="py-2 px-2 text-xs c-muted">${escapeHtml(r.time)}</td>
            <td class="py-2 px-2 text-right text-xs font-semibold" style="color:var(--text-secondary)">${r.points}</td>
        </tr>
    `).join('');

    container.innerHTML = `
        <div class="card p-4 sm-p-5">
            <div class="text-center mb-4">
                <h3 class="text-heading text-xs" style="font-weight:700">${escapeHtml(data.name)}</h3>
                <p class="text-micro c-subtle mt-1">${escapeHtml(data.circuit)} • ${escapeHtml(data.date)}</p>
            </div>
            ${podiumHtml}
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-micro border-b" style="font-size:9px;letter-spacing:0.1em;border-color:var(--border-subtle)">
                            <th class="pb-2 px-2 font-medium">Pos</th>
                            <th class="pb-2 px-2 font-medium">Driver</th>
                            <th class="pb-2 px-2 font-medium">Name</th>
                            <th class="pb-2 px-2 font-medium hide-mobile">Team</th>
                            <th class="pb-2 px-2 font-medium">Time</th>
                            <th class="pb-2 px-2 text-right font-medium">Pts</th>
                        </tr>
                    </thead>
                    <tbody>${resultsRows}</tbody>
                </table>
            </div>
            ${data.fastest && data.fastest.time ? `
                <div class="mt-4 pt-3 border-t text-center" style="border-color:var(--border-subtle)">
                    <span class="text-micro c-subtle">Fastest Lap</span>
                    <span class="text-xs font-semibold c-success ml-1">${escapeHtml(data.fastest.driver)} — ${escapeHtml(data.fastest.time)}</span>
                </div>
            ` : ''}
        </div>
    `;
    if (typeof lucide !== 'undefined') lucide.createIcons();
}

document.addEventListener('DOMContentLoaded', function() {
    loadRaceSchedule();
});

function populateResultsDropdown() {
    const select = document.getElementById('results-round-select');
    select.innerHTML = '<option value="">-- Pilih Race --</option>';
    raceSchedule.forEach(r => {
        const opt = document.createElement('option');
        opt.value = r.round;
        opt.textContent = 'R' + r.round + ' - ' + r.name;
        select.appendChild(opt);
    });
}
</script>

</main>
