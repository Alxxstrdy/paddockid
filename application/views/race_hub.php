<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="font-syne text-xl uppercase tracking-tight text-white font-extrabold">Race Hub</h1>
    </div>

    <div class="flex items-center border-b border-white/[0.04] gap-0">
        <button id="tab-calendar" onclick="switchRaceTab('calendar')" class="px-5 pb-3 text-xs font-semibold text-center transition-colors border-b-2 text-white border-red-500" data-tab="calendar">
            <i data-lucide="calendar" class="w-3.5 h-3.5 inline-block mr-1.5"></i>Calendar
        </button>
        <button id="tab-standings" onclick="switchRaceTab('standings')" class="px-5 pb-3 text-xs font-semibold text-center transition-colors border-b-2 text-slate-500 border-transparent hover:text-slate-300" data-tab="standings">
            <i data-lucide="trophy" class="w-3.5 h-3.5 inline-block mr-1.5"></i>Standings
        </button>
        <button id="tab-results" onclick="switchRaceTab('results')" class="px-5 pb-3 text-xs font-semibold text-center transition-colors border-b-2 text-slate-500 border-transparent hover:text-slate-300" data-tab="results">
            <i data-lucide="flag" class="w-3.5 h-3.5 inline-block mr-1.5"></i>Results
        </button>
    </div>

    <div id="race-calendar" class="space-y-3">
        <div class="text-center py-12 text-slate-500 text-xs">
            <div class="inline-block animate-spin rounded-full h-5 w-5 border-b-2 border-red-500 mr-2 align-middle"></div>
            Memuat kalender...
        </div>
    </div>

    <div id="race-standings" class="hidden space-y-6">
        <div class="text-center py-12 text-slate-500 text-xs">
            <div class="inline-block animate-spin rounded-full h-5 w-5 border-b-2 border-red-500 mr-2 align-middle"></div>
            Memuat klasemen...
        </div>
    </div>

    <div id="race-results" class="hidden space-y-4">
        <div class="glass-card p-4">
            <label class="text-xs text-slate-400 block mb-2 font-semibold">Pilih Grand Prix</label>
            <select id="results-round-select" onchange="loadRaceResults(this.value)" class="w-full bg-slate-800 text-xs text-slate-300 border border-white/[0.06] rounded-lg px-3 py-2.5 focus:outline-none focus:border-red-500/50">
                <option value="">-- Pilih Race --</option>
            </select>
        </div>
        <div id="results-content">
            <div class="text-center py-12 text-slate-500 text-xs">Pilih Grand Prix untuk melihat hasil.</div>
        </div>
    </div>
</div>

<script>
let raceSchedule = [];

function switchRaceTab(tab) {
    document.querySelectorAll('[id^="race-"]').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('[id^="tab-"]').forEach(btn => {
        btn.className = btn.className.replace(/text-white border-red-500/g, 'text-slate-500 border-transparent hover:text-slate-300');
    });
    document.getElementById('race-' + tab).classList.remove('hidden');
    const activeBtn = document.getElementById('tab-' + tab);
    activeBtn.className = activeBtn.className.replace(/text-slate-500 border-transparent hover:text-slate-300/g, 'text-white border-red-500');

    if (tab === 'calendar' && raceSchedule.length === 0) loadRaceSchedule();
    if (tab === 'standings') loadRaceStandings();
}

function loadRaceSchedule() {
    const container = document.getElementById('race-calendar');
    container.innerHTML = '<div class="text-center py-12 text-slate-500 text-xs"><div class="inline-block animate-spin rounded-full h-5 w-5 border-b-2 border-red-500 mr-2 align-middle"></div>Memuat kalender...</div>';

    fetch('<?= base_url("race/get_schedule"); ?>')
        .then(r => r.json())
        .then(data => {
            raceSchedule = data;
            renderCalendar(data, container);
            populateResultsDropdown();
        })
        .catch(() => {
            container.innerHTML = '<div class="glass-card p-8 text-center text-slate-500 text-xs">Gagal memuat kalender.</div>';
        });
}

function renderCalendar(races, container) {
    if (!races || races.length === 0) {
        container.innerHTML = '<div class="glass-card p-8 text-center text-slate-500 text-xs">Belum ada jadwal race tersedia.</div>';
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
            statusColor = 'text-emerald-400 border-emerald-500/20 bg-emerald-500/5';
        } else if (isLive) {
            statusBadge = 'Live';
            statusColor = 'text-red-400 border-red-500/20 bg-red-500/5 animate-pulse';
        } else {
            statusBadge = 'Completed';
            statusColor = 'text-slate-400 border-white/[0.06] bg-white/[0.02]';
        }

        const sessionsHtml = race.sessions.map(s => {
            let sColor = 'text-slate-400';
            let sDot = 'bg-slate-600';
            if (s.status === 'live') { sColor = 'text-emerald-400'; sDot = 'bg-emerald-500'; }
            else if (s.status === 'completed') { sColor = 'text-slate-500'; sDot = 'bg-slate-700'; }
            return `<div class="flex items-center gap-2 text-xs">
                <span class="w-2 h-2 rounded-full ${sDot} flex-shrink-0"></span>
                <span class="font-semibold w-28 text-slate-300">${s.name}</span>
                <span class="${sColor}">${s.date} ${s.time} WIB</span>
            </div>`;
        }).join('');

        const raceId = 'race-detail-' + race.round;
        const isExpanded = idx === 0 && !isCompleted;

        html += `
            <div class="glass-card overflow-hidden transition-all hover:bg-white/[0.02] group ${isLive ? 'ring-1 ring-red-500/20' : ''}">
                <div class="p-4 sm:p-5">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-3">
                            <span class="text-[10px] font-bold text-slate-600 bg-white/[0.03] px-2 py-1 rounded-md">R${race.round}</span>
                            <span class="text-xs font-semibold text-slate-300">${race.name}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-[9px] font-semibold uppercase tracking-wider px-2 py-0.5 rounded-full border ${statusColor}">${statusBadge}</span>
                            <button onclick="toggleRaceDetail(${race.round})" class="text-slate-500 hover:text-slate-300 transition-colors p-1">
                                <i data-lucide="chevron-down" class="w-4 h-4 transition-transform ${isExpanded ? 'rotate-180' : ''}" id="chevron-${race.round}"></i>
                            </button>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 text-[10px] text-slate-500">
                        <i data-lucide="map-pin" class="w-3 h-3"></i>
                        <span>${race.circuit}, ${race.locality}, ${race.country}</span>
                    </div>
                    <div class="flex items-center gap-2 text-[10px] text-slate-500 mt-1">
                        <i data-lucide="clock" class="w-3 h-3"></i>
                        <span>Race: ${race.date}</span>
                    </div>
                </div>
                <div id="${raceId}" class="border-t border-white/[0.03] ${isExpanded ? '' : 'hidden'}">
                    <div class="p-4 sm:p-5 space-y-2 bg-white/[0.01]">
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
    container.innerHTML = '<div class="text-center py-12 text-slate-500 text-xs"><div class="inline-block animate-spin rounded-full h-5 w-5 border-b-2 border-red-500 mr-2 align-middle"></div>Memuat klasemen...</div>';

    fetch('<?= base_url("race/get_standings"); ?>')
        .then(r => r.json())
        .then(data => {
            renderStandings(data, container);
        })
        .catch(() => {
            container.innerHTML = '<div class="glass-card p-8 text-center text-slate-500 text-xs">Gagal memuat klasemen.</div>';
        });
}

function renderStandings(data, container) {
    const drivers = data.drivers || [];
    const constructors = data.constructors || [];

    function posBadge(pos, color) {
        const colors = {
            1: { text: 'text-yellow-400', bg: 'bg-yellow-500/20', border: 'border-yellow-500/30' },
            2: { text: 'text-slate-300', bg: 'bg-slate-400/20', border: 'border-slate-400/30' },
            3: { text: 'text-amber-600', bg: 'bg-amber-600/20', border: 'border-amber-600/30' },
        };
        const c = colors[pos] || { text: 'text-slate-400', bg: 'bg-white/[0.03]', border: 'border-white/[0.06]' };
        return `<span class="font-bold text-[10px] w-6 h-6 inline-flex items-center justify-center rounded-md ${c.text} ${c.bg} ${c.border} border">${pos}</span>`;
    }

    let driverRows = drivers.map(d => {
        const color = d.constructorColor || '#666';
        return `<tr class="border-b border-white/[0.03] hover:bg-white/[0.02] transition-colors" style="border-left-color:${color}; border-left-width:2px">
            <td class="py-2.5 pl-2 pr-1">${posBadge(d.position, color)}</td>
            <td class="py-2.5 px-2">
                <div class="flex items-center gap-2">
                    <div class="w-5 h-5 rounded-full flex-shrink-0 overflow-hidden flex items-center justify-center">
                        <img src="${d.constructorImage}" alt="${d.constructor}" class="w-full h-full object-contain" loading="lazy" onerror="this.style.display='none'">
                    </div>
                    <span class="text-xs font-bold text-slate-200">${d.code}</span>
                    <span class="text-xs text-slate-400 hidden sm:inline">${d.driver}</span>
                </div>
            </td>
            <td class="py-2.5 px-2 text-xs text-slate-400 hidden md:table-cell">
                <span class="flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full flex-shrink-0" style="background:${color}"></span>
                    ${d.constructor}
                </span>
            </td>
            <td class="py-2.5 px-2 text-xs text-slate-500 hidden lg:table-cell">${d.wins > 0 ? d.wins + '🏁' : '-'}</td>
            <td class="py-2.5 px-2 text-right"><span class="text-xs font-bold ${d.points > 0 ? 'text-slate-200' : 'text-slate-500'}">${d.points}</span></td>
        </tr>`;
    }).join('');

    let constructorRows = constructors.map(c => {
        const color = c.constructorColor || '#666';
        return `<tr class="border-b border-white/[0.03] hover:bg-white/[0.02] transition-colors" style="border-left-color:${color}; border-left-width:2px">
            <td class="py-2.5 pl-2 pr-1">${posBadge(c.position, color)}</td>
            <td class="py-2.5 px-2">
                <div class="flex items-center gap-2.5">
                    <div class="w-6 h-6 rounded-lg flex-shrink-0 overflow-hidden flex items-center justify-center">
                        <img src="${c.constructorImage}" alt="${c.constructor}" class="w-full h-full object-contain" loading="lazy" onerror="this.style.display='none'">
                    </div>
                    <span class="text-xs font-bold text-slate-200">${c.constructor}</span>
                </div>
            </td>
            <td class="py-2.5 px-2 text-xs text-slate-400 hidden sm:table-cell">${c.nationality || '-'}</td>
            <td class="py-2.5 px-2 text-xs text-slate-500 hidden lg:table-cell">${c.wins > 0 ? c.wins + '🏁' : '-'}</td>
            <td class="py-2.5 px-2 text-right"><span class="text-xs font-bold ${c.points > 0 ? 'text-slate-200' : 'text-slate-500'}">${c.points}</span></td>
        </tr>`;
    }).join('');

    container.innerHTML = `
        <div class="glass-card p-4 sm:p-5 overflow-hidden">
            <h3 class="text-xs font-bold text-white uppercase tracking-wider mb-4 flex items-center gap-2">
                <i data-lucide="users" class="w-4 h-4 text-red-500"></i> Driver Standings
            </h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-[9px] uppercase tracking-widest text-slate-500 border-b border-white/[0.04]">
                            <th class="pb-2 pl-2 pr-1 font-medium w-10">Pos</th>
                            <th class="pb-2 px-2 font-medium">Driver</th>
                            <th class="pb-2 px-2 font-medium hidden md:table-cell">Team</th>
                            <th class="pb-2 px-2 font-medium hidden lg:table-cell">Wins</th>
                            <th class="pb-2 px-2 text-right font-medium">Pts</th>
                        </tr>
                    </thead>
                    <tbody>${driverRows}</tbody>
                </table>
            </div>
        </div>

        <div class="glass-card p-4 sm:p-5 overflow-hidden">
            <h3 class="text-xs font-bold text-white uppercase tracking-wider mb-4 flex items-center gap-2">
                <i data-lucide="building" class="w-4 h-4 text-red-500"></i> Constructor Standings
            </h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-[9px] uppercase tracking-widest text-slate-500 border-b border-white/[0.04]">
                            <th class="pb-2 pl-2 pr-1 font-medium w-10">Pos</th>
                            <th class="pb-2 px-2 font-medium">Team</th>
                            <th class="pb-2 px-2 font-medium hidden sm:table-cell">Nationality</th>
                            <th class="pb-2 px-2 font-medium hidden lg:table-cell">Wins</th>
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
        document.getElementById('results-content').innerHTML = '<div class="text-center py-12 text-slate-500 text-xs">Pilih Grand Prix untuk melihat hasil.</div>';
        return;
    }

    const container = document.getElementById('results-content');
    container.innerHTML = '<div class="text-center py-12 text-slate-500 text-xs"><div class="inline-block animate-spin rounded-full h-5 w-5 border-b-2 border-red-500 mr-2 align-middle"></div>Memuat hasil...</div>';

    fetch('<?= base_url("race/get_results/"); ?>' + round)
        .then(r => r.json())
        .then(data => {
            if (data.error) {
                container.innerHTML = '<div class="glass-card p-8 text-center text-slate-500 text-xs">' + data.error + '</div>';
                return;
            }
            renderResults(data, container);
        })
        .catch(() => {
            container.innerHTML = '<div class="glass-card p-8 text-center text-slate-500 text-xs">Gagal memuat hasil.</div>';
        });
}

function renderResults(data, container) {
    const top3 = data.results.slice(0, 3);
    const rest = data.results.slice(3);

    const podiumColors = ['text-yellow-400', 'text-slate-300', 'text-amber-600'];

    let podiumHtml = '<div class="flex items-end justify-center gap-3 sm:gap-6 mb-6 py-4">';
    const podiumOrder = [1, 0, 2]; // display: 2nd (left), 1st (center), 3rd (right)
    const slotHeights = ['h-20', 'h-28', 'h-16'];
    const slotOrders = ['order-1', 'order-2', 'order-3'];
    podiumOrder.forEach((idx, slotIndex) => {
        const r = top3[idx];
        if (!r) return;
        const barColors = ['bg-yellow-500/20 border border-yellow-500/30', 'bg-slate-400/20 border border-slate-400/30', 'bg-amber-600/20 border border-amber-600/30'];
        podiumHtml += `
            <div class="flex flex-col items-center gap-2 text-center ${slotOrders[slotIndex]}">
                <span class="text-xs font-bold ${podiumColors[idx]}">${r.positionText}</span>
                <span class="text-xs font-semibold text-slate-200">${r.code}</span>
                <span class="text-[10px] text-slate-400">${r.driver}</span>
                <div class="w-16 sm:w-20 ${slotHeights[slotIndex]} rounded-t-lg flex items-end justify-center pb-2 ${barColors[idx]}">
                    <span class="text-[10px] font-bold text-slate-300">${r.points} pts</span>
                </div>
            </div>`;
    });
    podiumHtml += '</div>';

    let resultsRows = rest.map(r => `
        <tr class="border-b border-white/[0.03] hover:bg-white/[0.02] transition-colors">
            <td class="py-2 px-2 text-xs text-slate-500 w-8 text-center">${r.position}</td>
            <td class="py-2 px-2 text-xs font-semibold text-slate-200">${r.code}</td>
            <td class="py-2 px-2 text-xs text-slate-300">${r.driver}</td>
            <td class="py-2 px-2 text-xs text-slate-400 hidden sm:table-cell">${r.constructor}</td>
            <td class="py-2 px-2 text-xs text-slate-400">${r.time}</td>
            <td class="py-2 px-2 text-right text-xs font-semibold text-slate-200">${r.points}</td>
        </tr>
    `).join('');

    container.innerHTML = `
        <div class="glass-card p-4 sm:p-5">
            <div class="text-center mb-4">
                <h3 class="font-syne text-sm uppercase tracking-tight text-white font-bold">${data.name}</h3>
                <p class="text-[10px] text-slate-500 mt-1">${data.circuit} • ${data.date}</p>
            </div>
            ${podiumHtml}
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-[9px] uppercase tracking-widest text-slate-500 border-b border-white/[0.04]">
                            <th class="pb-2 px-2 font-medium">Pos</th>
                            <th class="pb-2 px-2 font-medium">Driver</th>
                            <th class="pb-2 px-2 font-medium">Name</th>
                            <th class="pb-2 px-2 font-medium hidden sm:table-cell">Team</th>
                            <th class="pb-2 px-2 font-medium">Time</th>
                            <th class="pb-2 px-2 text-right font-medium">Pts</th>
                        </tr>
                    </thead>
                    <tbody>${resultsRows}</tbody>
                </table>
            </div>
            ${data.fastest && data.fastest.time ? `
                <div class="mt-4 pt-3 border-t border-white/[0.03] text-center">
                    <span class="text-[9px] uppercase tracking-widest text-slate-500">Fastest Lap</span>
                    <span class="text-xs font-semibold text-emerald-400 ml-2">${data.fastest.driver} — ${data.fastest.time}</span>
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
