<div class="space-y-6">
    <div class="flex-row justify-between">
        <h1 class="text-page-title text-lg">Race Hub</h1>
    </div>

    <p class="rh-credits">
        Data F1 dari <a href="https://github.com/jolpica/jolpica-f1" target="_blank" rel="noopener noreferrer">Jolpica F1</a>
        (Ergast API) &mdash; lisensi
        <a href="https://github.com/jolpica/jolpica-f1/blob/main/TERMS.md" target="_blank" rel="noopener noreferrer">CC BY-NC-SA 4.0</a>,
        penggunaan non-komersial.
    </p>

    <div class="tabs">
        <button id="tab-calendar" onclick="switchRaceTab('calendar')" class="tab tab-active" data-tab="calendar">
            Calendar
        </button>
        <button id="tab-standings" onclick="switchRaceTab('standings')" class="tab tab-inactive" data-tab="standings">
            Standings
        </button>
        <button id="tab-results" onclick="switchRaceTab('results')" class="tab tab-inactive" data-tab="results">
            Results
        </button>
    </div>

    <div id="race-calendar" data-race-panel="calendar" class="space-y-3" data-sk="feed">
        <div class="text-center py-12 c-subtle text-xs">
            <div class="spinner spinner--sm spinner-white inline-block mr-1 align-middle"></div>
            Memuat kalender...
        </div>
    </div>

    <div id="race-standings" data-race-panel="standings" class="hidden space-y-6" data-sk="feed">
        <div class="text-center py-12 c-subtle text-xs">
            <div class="spinner spinner--sm spinner-white inline-block mr-1 align-middle"></div>
            Memuat klasemen...
        </div>
    </div>

    <div id="race-results" data-race-panel="results" class="hidden space-y-4" data-sk="feed">
        <div class="card rh-picker">
            <span class="rh-picker__label" id="results-round-label">Pilih Grand Prix</span>
            <div class="rh-dd" id="results-round-dropdown">
                <button type="button" id="results-round-trigger" class="rh-dd__btn"
                        role="combobox" aria-haspopup="listbox" aria-expanded="false"
                        aria-controls="results-round-list" aria-autocomplete="none"
                        aria-labelledby="results-round-label results-round-value">                      
                    <span class="rh-dd__flag" id="results-round-flag" aria-hidden="true">🏁</span>
                    <span class="rh-dd__val" id="results-round-value">Pilih negara</span>
                    <i data-lucide="chevron-down" class="rh-dd__caret w-4 h-4" aria-hidden="true"></i>
                </button>
                <ul id="results-round-list" class="rh-dd__list" role="listbox"
                    aria-labelledby="results-round-label" tabindex="-1" hidden></ul>
            </div>
        </div>
        <div id="results-content" aria-live="polite">
            <div class="text-center py-12 c-subtle text-xs">Pilih negara untuk melihat hasil.</div>
        </div>
    </div>
</div>

<script>
let raceSchedule = [];
let expandedRounds = new Set();
let raceDropdownList = [];
let raceDropdownSelected = '';
let raceDropdownActive = -1;
let raceDropdownOpen = false;

function switchRaceTab(tab) {
    if (tab !== 'results') closeRaceDropdown(false);
    document.querySelectorAll('[data-race-panel]').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('[id^="tab-"]').forEach(btn => {
        btn.className = btn.className.replace(/tab-active/g, 'tab-inactive');
    });
    document.getElementById('race-' + tab).classList.remove('hidden');
    const activeBtn = document.getElementById('tab-' + tab);
    activeBtn.className = activeBtn.className.replace(/tab-inactive/g, 'tab-active');

    if (tab === 'calendar' && raceSchedule.length === 0) loadRaceSchedule();
    if (tab === 'standings') loadRaceStandings();
}

function fetchRaceSchedule() {
    return fetch('<?= base_url("race/get_schedule"); ?>', { headers: { 'Accept': 'application/json' } })
        .then(r => {
            const type = (r.headers.get('content-type') || '');
            if (!r.ok || type.indexOf('application/json') === -1) {
                const err = new Error('Jadwal balapan tidak merespons JSON (status ' + r.status + ', tipe "' + (type || 'kosong') + '")');
                err.raceUnauthenticated = r.redirected && /\/auth(\?|#|$)/.test(r.url);
                throw err;
            }
            return r.json();
        });
}

// Dropdown negara tidak boleh menjatuhkan kalender: kegagalan di sini
// hanya dicatat ke console, bukan mengubah tampilan kalender.
function safePopulateResultsDropdown() {
    try {
        populateResultsDropdown();
    } catch (err) {
        console.error('[race] dropdown negara gagal diisi:', err);
    }
}

function loadRaceSchedule() {
    const container = document.getElementById('race-calendar');
    container.innerHTML = '<div class="text-center py-12 c-subtle text-xs"><div class="spinner spinner--sm spinner-white inline-block mr-1 align-middle"></div>Memuat kalender...</div>';

    fetchRaceSchedule()
        .then(data => {
            raceSchedule = data;
            if (expandedRounds.size === 0 && data[0] && data[0].status !== 'completed') {
                expandedRounds.add(data[0].round);
            }
            renderCalendar(data, container);
            safePopulateResultsDropdown();
        })
        .catch(err => {
            console.error('[race] gagal memuat kalender:', err);
            container.innerHTML = (err && err.raceUnauthenticated)
                ? '<div class="card p-8 text-center c-subtle text-xs">Sesi Anda sudah berakhir. Silakan <a class="btn-link c-primary" href="<?= base_url("auth"); ?>">login ulang</a> untuk memuat kalender.</div>'
                : '<div class="card p-8 text-center c-subtle text-xs">Gagal memuat kalender.</div>';
            safePopulateResultsDropdown();
        });
}

function refreshRaceSchedule() {
    if (document.getElementById('race-calendar').classList.contains('hidden')) return;
    fetchRaceSchedule()
        .then(data => {
            if (JSON.stringify(data) === JSON.stringify(raceSchedule)) return;
            raceSchedule = data;
            renderCalendar(data, document.getElementById('race-calendar'));
            safePopulateResultsDropdown();
        })
        .catch(() => {});
}

function goToRaceResults(round) {
    switchRaceTab('results');
    setRaceDropdownRound(round);
    loadRaceResults(round);
}

const COUNTRY_FLAGS = {
    australia: '🇦🇺', bahrain: '🇧🇭', china: '🇨🇳', japan: '🇯🇵', usa: '🇺🇸',
    'united states': '🇺🇸', canada: '🇨🇦', monaco: '🇲🇨', spain: '🇪🇸', austria: '🇦🇹',
    'great britain': '🇬🇧', 'united kingdom': '🇬🇧', uk: '🇬🇧', belgium: '🇧🇪', hungary: '🇭🇺',
    netherlands: '🇳🇱', italy: '🇮🇹', azerbaijan: '🇦🇿', singapore: '🇸🇬', mexico: '🇲🇽',
    brazil: '🇧🇷', 'las vegas': '🇺🇸', qatar: '🇶🇦', 'united arab emirates': '🇦🇪',
    'abu dhabi': '🇦🇪', uae: '🇦🇪', 'uni emirate arab': '🇦🇪', malaysia: '🇲🇾', 'saudi arabia': '🇸🇦', 'new zealand': '🇳🇿',
    argentina: '🇦🇷', 'south africa': '🇿🇦', turkey: '🇹🇷', korea: '🇰🇷',
    france: '🇫🇷', germany: '🇩🇪', switzerland: '🇨🇭', czechia: '🇨🇿', 'czech republic': '🇨🇿'
};
const FLAG_BY_HOST_EVENT = { malaysia: '🇧🇭' };
function flagEmoji(countryCode) {
    if (!countryCode) return '🏁';
    const k = String(countryCode).toLowerCase();
    return FLAG_BY_HOST_EVENT[k] || COUNTRY_FLAGS[k] || '🏁';
}
const COUNTRY_LABELS = {
    british: 'UK',
    uae: 'Uni Emirate Arab',
    'united arab emirates': 'Uni Emirate Arab',
    'abu dhabi': 'Uni Emirate Arab'
};
function countryLabel(country) {
    if (!country) return '';
    return COUNTRY_LABELS[String(country).toLowerCase()] || country;
}

const ID_MONTHS = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
const ID_DAYS = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];

function fmtDateId(dateStr) {
    const p = String(dateStr).split('-');
    return p[2] + ' ' + ID_MONTHS[parseInt(p[1], 10) - 1] + ' ' + p[0];
}
function dayName(dateStr) {
    return ID_DAYS[new Date(dateStr + 'T00:00:00').getDay()];
}
function raceDateRange(sessions) {
    const dates = (sessions || []).map(s => s.date).filter(Boolean).sort();
    if (!dates.length) return '';
    if (dates[0] === dates[dates.length - 1]) return fmtDateId(dates[0]);
    return fmtDateId(dates[0]) + ' – ' + fmtDateId(dates[dates.length - 1]);
}
function countdownText(timestamp) {
    const diff = Math.max(0, timestamp * 1000 - Date.now());
    if (diff <= 0) return 'sekarang';
    const d = Math.floor(diff / 86400000);
    const h = Math.floor((diff % 86400000) / 3600000);
    const m = Math.floor((diff % 3600000) / 60000);
    if (d > 0) return (h > 0 ? d + ' hari ' + h + ' jam' : d + ' hari');
    if (h > 0) return (h > 0 && m > 0 ? h + ' jam ' + m + ' mnt' : h + ' jam');
    return m + ' mnt';
}
function renderHeroCountdown() {
    const el = document.getElementById('rh-countdown');
    if (!el || !el.dataset.ts) return;
    const label = el.closest('.rh-hero__count');
    el.textContent = countdownText(parseInt(el.dataset.ts, 10));
    if (label) label.classList.add('rh-hero__count--ticking');
}

function buildNextRaceHero(race) {
    const isLive = race.status === 'live';
    const raceSess = race.sessions[race.sessions.length - 1];
    const chips = race.sessions.map(s => {
        let cls = 'rh-chip';
        if (s.status === 'live') cls += ' rh-chip--live';
        else if (s.status === 'completed') cls += ' rh-chip--done';
        const dot = s.status === 'live' ? 'rh-chip__dot--live' : s.status === 'completed' ? 'rh-chip__dot--done' : '';
        const chatHref = s.chat_slug ? '<?= base_url('chat/room/') ?>' + encodeURIComponent(s.chat_slug) : '#';
        return `<a class="${cls}" href="${chatHref}" title="Buka chat ${escapeHtml(s.name)}">
            <span class="rh-chip__dot ${dot}"></span>
            <span class="rh-chip__body">
                <span class="rh-chip__name">${escapeHtml(s.name)}</span>
                <span class="rh-chip__meta">${dayName(s.date)}, ${escapeHtml(s.date)} · ${escapeHtml(s.time)} WIB</span>
            </span>
        </a>`;
    }).join('');

    return `
    <div class="rh-hero ${isLive ? 'rh-hero--live' : 'rh-hero--upcoming'}">
        <span class="rh-hero__round" aria-hidden="true">${escapeHtml(race.round)}</span>
        <div class="rh-hero__top">
            <span class="rh-hero__eyebrow">${isLive ? 'Ongoing' : 'Next Race'}</span>
            <span class="rh-hero__flag">${flagEmoji(countryLabel(race.country))}</span>
        </div>
        <h2 class="rh-hero__name">${escapeHtml(race.name)}</h2>
        <p class="rh-hero__circuit">${escapeHtml(race.circuit)}<span class="rh-hero__sep">•</span>${escapeHtml(race.locality)}, ${escapeHtml(countryLabel(race.country))}</p>
        <div class="rh-hero__meta">
            <span class="rh-hero__range">${escapeHtml(raceDateRange(race.sessions))}</span>
            <span class="rh-hero__count">
                ${isLive
                    ? '<span class="rh-live-label">Sedang berlangsung</span>'
                    : 'Race <b>' + (raceSess ? escapeHtml(raceSess.time) + ' WIB' : '') + '</b> · mulai dalam <b id="rh-countdown" data-ts="' + race.timestamp + '">' + countdownText(race.timestamp) + '</b>'}
            </span>
        </div>
        <div class="rh-hero__sessions">${chips}</div>
    </div>`;
}

function renderCalendar(races, container) {
    if (!races || races.length === 0) {
        container.innerHTML = '<div class="card p-8 text-center c-subtle text-xs">Belum ada jadwal race tersedia.</div>';
        return;
    }

    const nextRace = races.find(r => r.status === 'upcoming' || r.status === 'live') || null;
    let html = nextRace ? buildNextRaceHero(nextRace) : '';

    races.forEach((race) => {
        const st = race.status;
        let statusBadge = '';
        let statusColor = '';
        if (st === 'upcoming') { statusBadge = 'Upcoming'; statusColor = 'badge badge-pill badge-upcoming'; }
        else if (st === 'live') { statusBadge = 'Live'; statusColor = 'badge badge-pill badge-live'; }
        else { statusBadge = 'Selesai'; statusColor = 'badge badge-pill badge-completed'; }

        const range = raceDateRange(race.sessions);
        const isExpanded = expandedRounds.has(race.round);

        const sessionsHtml = race.sessions.map(s => {
            const dotCls = s.status === 'live' ? 'rh-sess-dot--live' : s.status === 'completed' ? 'rh-sess-dot--done' : '';
            const sessionColor = s.status === 'live' ? 'c-success' : s.status === 'completed' ? 'c-subtle' : 'c-muted';
            const chatHref = s.chat_slug ? '<?= base_url('chat/room/') ?>' + encodeURIComponent(s.chat_slug) : '#';
            return `<div class="rh-sess-row">
                <span class="rh-sess-dot ${dotCls}"></span>
                <span class="rh-sess-name">${escapeHtml(s.name)}</span>
                <span class="rh-sess-time ${sessionColor}">${dayName(s.date)}, ${escapeHtml(s.date)} ${escapeHtml(s.time)} WIB</span>
                <a href="${chatHref}" class="rh-sess-chat" title="Buka chat ${escapeHtml(s.name)}">
                    <i data-lucide="message-circle" class="w-3 h-3"></i>
                </a>
            </div>`;
        }).join('');

        html += `
            <div class="rh-card rh-card--${st}">
                <div class="rh-card__main" onclick="toggleRaceDetail(${race.round})">
                    <div class="rh-card__lead">
                        <span class="rh-card__round">R${escapeHtml(race.round)}</span>
                        <span class="rh-card__flag">${flagEmoji(countryLabel(race.country))}</span>
                    </div>
                    <div class="rh-card__body">
                        <div class="rh-card__title">
                            <h3 class="rh-card__name">${escapeHtml(race.name)}</h3>
                            <span class="${statusColor}" style="font-size:9px;text-transform:uppercase;letter-spacing:0.06em">${statusBadge}</span>
                        </div>
                        <p class="rh-card__sub">${escapeHtml(race.circuit)} • ${escapeHtml(race.locality)}, ${escapeHtml(countryLabel(race.country))}</p>
                        <p class="rh-card__date">${escapeHtml(range)} · Race: ${escapeHtml(race.date)}</p>
                    </div>
                    ${st === 'completed'
                        ? `<button type="button" class="rh-card__results" onclick="event.stopPropagation();goToRaceResults(${race.round})">Hasil R${escapeHtml(race.round)}</button>`
                        : `<span class="rh-card__chevron"><i data-lucide="chevron-down" class="w-4 h-4 transition-transform ${isExpanded ? 'rotate-180' : ''}" id="chevron-${race.round}"></i></span>`}
                </div>
                <div id="race-detail-${race.round}" class="rh-card__sessions ${isExpanded ? '' : 'hidden'}">
                    ${sessionsHtml || '<div class="c-subtle text-xs">Belum ada jadwal sesi.</div>'}
                </div>
            </div>`;
    });

    container.innerHTML = html;
    if (typeof lucide !== 'undefined') lucide.createIcons();
}

function toggleRaceDetail(round) {
    const el = document.getElementById('race-detail-' + round);
    const chevron = document.getElementById('chevron-' + round);
    const hidden = el.classList.toggle('hidden');
    if (hidden) expandedRounds.delete(round);
    else expandedRounds.add(round);
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

function hexToRgba(hex, alpha) {
    const h = String(hex || '').replace('#', '');
    const c = h.length === 3 ? h.split('').map(x => x + x).join('') : h;
    const n = parseInt(c, 16);
    if (isNaN(n)) return 'rgba(128,128,128,' + alpha + ')';
    return 'rgba(' + ((n >> 16) & 255) + ',' + ((n >> 8) & 255) + ',' + (n & 255) + ',' + alpha + ')';
}

function rankTile(pos) {
    return `<span class="rh-rank__tile">${pos}</span>`;
}

function teamLogo(src, color, alt) {
    if (!src) return '';
    return `<span class="rh-logo"><img src="${escapeHtml(src)}" alt="${escapeHtml(alt)}" loading="lazy" onerror="this.parentNode.remove()"></span>`;
}

function renderStandings(data, container) {
    const drivers = data.drivers || [];
    const constructors = data.constructors || [];

    function standingsHead(title, sub, count) {
        return `
        <header class="rh-st__head">
            <div>
                <h3 class="rh-st__title">${escapeHtml(title)}</h3>
                <p class="rh-st__sub">${escapeHtml(sub)}</p>
            </div>
        </header>`;
    }

    function leadStyle(d) {
        if (d.position !== 1 || !d.constructorColor) return '--team:#999';
        return '--team:' + escapeHtml(d.constructorColor)
            + ';--lead-bg:' + hexToRgba(d.constructorColor, 0.05)
            + ';--lead-hover:' + hexToRgba(d.constructorColor, 0.09);
    }

    const driverRows = drivers.map(d => `
        <tr class="rh-row${d.position === 1 ? ' rh-row--lead' : ''}" style="${leadStyle(d)}">
            <td class="rh-rk">${rankTile(d.position)}</td>
            <td class="rh-name">
                <span class="rh-name__code">${escapeHtml(d.code)}</span>
                <span class="rh-name__full">${escapeHtml(d.driver)}</span>
            </td>
            <td class="rh-team hide-mobile"><span class="rh-team__wrap">${teamLogo(d.constructorImage, d.constructorColor, d.constructor)}<span class="rh-team__name">${escapeHtml(d.constructor)}</span></span></td>
            <td class="rh-wins hide-mobile">${d.wins > 0 ? d.wins : '–'}</td>
            <td class="rh-pts">${d.points}</td>
        </tr>`).join('');

    const constructorRows = constructors.map(c => `
        <tr class="rh-row${c.position === 1 ? ' rh-row--lead' : ''}" style="${leadStyle(c)}">
            <td class="rh-rk">${rankTile(c.position)}</td>
<td class="rh-team"><span class="rh-team__wrap">
                    ${teamLogo(c.constructorImage, c.constructorColor, c.constructor)}
                    <span class="rh-team__name">${escapeHtml(c.constructor)}</span>
                </span></td>
            <td class="rh-wins hide-mobile">${c.wins > 0 ? c.wins : '–'}</td>
            <td class="rh-pts">${c.points}</td>
        </tr>`).join('');

    container.innerHTML = `
        <section class="card rh-st">
            ${standingsHead('Klasemen Pembalap', 'Pembaruan otomatis setelah setiap balapan')}
            <div class="rh-st__body">
                <table>
                    <thead>
                        <tr class="rh-st__hd">
                            <th class="rh-rk">Pos</th>
                            <th>Drivers</th>
                            <th class="hide-mobile">Team</th>
                            <th class="rh-wins hide-mobile">Win</th>
                            <th class="rh-pts">Points</th>
                        </tr>
                    </thead>
                    <tbody>${driverRows}</tbody>
                </table>
            </div>
        </section>

        <section class="card rh-st">
            ${standingsHead('Klasemen Konstruktor', 'Pembaruan otomatis setelah setiap balapan',)}
            <div class="rh-st__body">
                <table>
                    <thead>
                        <tr class="rh-st__hd">
                            <th class="rh-rk">Pos</th>
                            <th>Team</th>
                            <th class="rh-wins hide-mobile">Win</th>
                            <th class="rh-pts">Points</th>
                        </tr>
                    </thead>
                    <tbody>${constructorRows}</tbody>
                </table>
            </div>
        </section>`;
}

function loadRaceResults(round) {
    if (!round) {
        const container = document.getElementById('results-content');
        container.setAttribute('aria-busy', 'false');
        container.innerHTML = '<div class="text-center py-12 c-subtle text-xs">Pilih negara untuk melihat hasil.</div>';
        return;
    }

    const container = document.getElementById('results-content');
    container.setAttribute('aria-busy', 'true');
    container.innerHTML = '<div class="text-center py-12 c-subtle text-xs"><div class="spinner spinner--sm spinner-white inline-block mr-1 align-middle"></div>Memuat hasil...</div>';

    fetch('<?= base_url("race/get_results/"); ?>' + encodeURIComponent(round))
        .then(r => r.json())
        .then(data => {
            container.setAttribute('aria-busy', 'false');
            if (data.error) {
                container.innerHTML = '<div class="card p-8 text-center c-subtle text-xs">' + escapeHtml(data.error) + '</div>';
                return;
            }
            renderResults(data, container);
        })
        .catch(() => {
            container.setAttribute('aria-busy', 'false');
            container.innerHTML = '<div class="card p-8 text-center c-subtle text-xs">Gagal memuat hasil.</div>';
        });
}

function raceTimeTitle(value) {
    return String(value || '')
        .replace(/[_.]+/g, ' ')
        .replace(/\b\w/g, c => c.toUpperCase())
        .trim();
}

function resultStatus(r) {
    const time = String(r.time || '');
    if (/^\d{1,2}:\d{2}(:\d{2})?/.test(time)) return { text: time, cls: 'rh-res__status' };
    return { text: time || '–', cls: 'rh-res__status rh-res__status--dnf' };
}

function renderResults(data, container) {
    const results = data.results || [];

    const rows = results.map(r => {
        const st = resultStatus(r);
        return `
        <tr class="rh-res__row">
            <td class="rh-res__poscell">${escapeHtml(r.position)}</td>
            <th scope="row" class="rh-res__drv">${escapeHtml(r.driver)}</th>
            <td class="rh-res__teamcol"><span class="rh-team__wrap">${teamLogo(r.constructorImage, r.constructorColor, r.constructor)}<span class="rh-team__name">${escapeHtml(r.constructor)}</span></span></td>
            <td class="rh-res__num">${escapeHtml(r.laps)}</td>
            <td class="${st.cls}">${escapeHtml(st.text)}</td>
            <td class="rh-res__ptscell">${r.points}</td>
        </tr>`;
    }).join('');

    container.innerHTML = `
        <section class="card rh-res">
            <header class="rh-res__head">
                <h3 class="rh-res__name">${escapeHtml(data.name)}</h3>
                <p class="rh-res__meta">${escapeHtml(data.circuit)} · ${escapeHtml(data.date)}</p>
            </header>
            <div class="rh-res__scroll">
                <table class="rh-res__table">
                    <caption class="sr-only">Hasil finis lengkap ${escapeHtml(data.name)}</caption>
                    <thead>
                        <tr>
                            <th scope="col" class="rh-res__poshead">Pos</th>
                            <th scope="col">Pembalap</th>
                            <th scope="col" class="rh-res__teamcol">Tim</th>
                            <th scope="col" class="rh-res__numhead">Laps</th>
                            <th scope="col">Waktu / Status</th>
                            <th scope="col" class="rh-res__ptshead">Pts</th>
                        </tr>
                    </thead>
                    <tbody>${rows}</tbody>
                </table>
            </div>
            <p class="rh-credits rh-credits--inline">
                Sumber data: <a href="https://github.com/jolpica/jolpica-f1" target="_blank" rel="noopener noreferrer">Jolpica F1</a> &mdash;
                <a href="https://github.com/jolpica/jolpica-f1/blob/main/TERMS.md" target="_blank" rel="noopener noreferrer">CC BY-NC-SA 4.0</a>, non-komersial.
            </p>
        </section>
    `;
    if (typeof lucide !== 'undefined') lucide.createIcons();
}

document.addEventListener('DOMContentLoaded', function() {
    initRaceDropdown();
    loadRaceSchedule();
    setInterval(refreshRaceSchedule, 60000);
    setInterval(renderHeroCountdown, 30000);
});

function raceHasResults(r) {
    return r && r.status === 'completed';
}

function raceDropdownNode(id) {
    return document.getElementById(id);
}

function setRaceDropdownNode(id, prop, value) {
    const node = raceDropdownNode(id);
    if (node) node[prop] = value;
    return !!node;
}

function populateResultsDropdown() {
    const list = raceDropdownNode('results-round-list');
    if (!list) return;

    const keep = raceDropdownSelected;
    raceDropdownList = raceSchedule;
    raceDropdownActive = -1;
    setRaceDropdownNode('results-round-trigger', 'disabled', raceDropdownList.length === 0);

    if (raceDropdownList.length === 0) {
        closeRaceDropdown(false);
        list.innerHTML = '<li class="rh-dd__empty" role="presentation">Jadwal race belum tersedia.</li>';
        resetRaceDropdownValue();
        return;
    }

    list.innerHTML = raceDropdownList.map((r, i) => {
        const ready = raceHasResults(r);
        return `
        <li class="rh-dd__opt${ready ? '' : ' is-locked'}" role="option" id="rr-opt-${i}"
            data-round="${escapeHtml(r.round)}" aria-selected="false"${ready ? '' : ' aria-disabled="true"'}>
            <span class="rh-dd__oflag" aria-hidden="true">${flagEmoji(r.country)}</span>
            <span class="rh-dd__oname">${escapeHtml(countryLabel(r.country))}</span>
            ${ready ? '' : '<span class="rh-dd__onote">Belum ada hasil</span>'}
        </li>`;
    }).join('');

    if (keep && setRaceDropdownRound(keep)) return;
    resetRaceDropdownValue();
}

function resetRaceDropdownValue() {
    raceDropdownSelected = '';
    setRaceDropdownNode('results-round-value', 'textContent', 'Pilih negara');
    setRaceDropdownNode('results-round-flag', 'textContent', '🏁');
    syncDropdownSelectionState();
}

function syncDropdownSelectionState() {
    const list = raceDropdownNode('results-round-list');
    if (!list) return;
    Array.prototype.forEach.call(list.children, (el, i) => {
        const race = raceDropdownList[i];
        const on = race && String(race.round) === raceDropdownSelected;
        el.setAttribute('aria-selected', on ? 'true' : 'false');
    });
}

function setRaceDropdownRound(round) {
    const race = raceSchedule.find(r => String(r.round) === String(round));
    if (!race) return false;
    raceDropdownSelected = String(round);
    setRaceDropdownNode('results-round-value', 'textContent', countryLabel(race.country));
    setRaceDropdownNode('results-round-flag', 'textContent', flagEmoji(race.country));
    syncDropdownSelectionState();
    return true;
}

function raceDropdownStep(from, dir) {
    const n = raceDropdownList.length;
    if (!n) return -1;
    const start = from < 0 ? (dir > 0 ? -1 : n) : from;
    for (let i = 1; i <= n; i++) {
        const idx = (start + dir * i + n) % n;
        if (raceHasResults(raceDropdownList[idx])) return idx;
    }
    return -1;
}

function raceDropdownEdge(fromStart) {
    const n = raceDropdownList.length;
    for (let k = 0; k < n; k++) {
        const idx = fromStart ? k : n - 1 - k;
        if (raceHasResults(raceDropdownList[idx])) return idx;
    }
    return -1;
}

function openRaceDropdown() {
    const trigger = raceDropdownNode('results-round-trigger');
    const list = raceDropdownNode('results-round-list');
    if (raceDropdownOpen || !trigger || !list || raceDropdownList.length === 0) return;
    list.hidden = false;
    trigger.setAttribute('aria-expanded', 'true');
    raceDropdownOpen = true;
    const sel = raceDropdownList.findIndex(r => String(r.round) === raceDropdownSelected);
    setRaceDropdownActive(raceHasResults(raceDropdownList[sel]) ? sel : raceDropdownEdge(true));
}

function closeRaceDropdown(refocus) {
    if (!raceDropdownOpen) return;
    const trigger = raceDropdownNode('results-round-trigger');
    const list = raceDropdownNode('results-round-list');
    if (!trigger || !list) {
        raceDropdownOpen = false;
        raceDropdownActive = -1;
        return;
    }
    list.hidden = true;
    trigger.setAttribute('aria-expanded', 'false');
    trigger.removeAttribute('aria-activedescendant');
    raceDropdownOpen = false;
    raceDropdownActive = -1;
    if (refocus) trigger.focus();
}

function setRaceDropdownActive(index) {
    const list = document.getElementById('results-round-list');
    if (!list || index < 0 || index >= raceDropdownList.length) return;
    raceDropdownActive = index;
    Array.prototype.forEach.call(list.children, (el, i) => {
        el.classList.toggle('is-active', i === raceDropdownActive);
    });
    const active = list.children[raceDropdownActive];
    if (!active) return;
    const trigger = raceDropdownNode('results-round-trigger');
    if (trigger) trigger.setAttribute('aria-activedescendant', active.id);
    active.scrollIntoView({ block: 'nearest' });
}

function chooseRaceDropdownActive() {
    const race = raceDropdownList[raceDropdownActive];
    if (!raceHasResults(race)) return;
    setRaceDropdownRound(race.round);
    closeRaceDropdown(true);
    loadRaceResults(race.round);
}

function onRaceDropdownClick(e) {
    const opt = e.target.closest('.rh-dd__opt');
    if (opt) {
        if (opt.getAttribute('aria-disabled') === 'true') return;
        setRaceDropdownActive(Array.prototype.indexOf.call(opt.parentNode.children, opt));
        chooseRaceDropdownActive();
        return;
    }
    if (e.target.closest('#results-round-trigger')) {
        raceDropdownOpen ? closeRaceDropdown(false) : openRaceDropdown();
    }
}

function onRaceDropdownHover(e) {
    const opt = e.target.closest('.rh-dd__opt');
    if (opt && opt.getAttribute('aria-disabled') !== 'true') {
        setRaceDropdownActive(Array.prototype.indexOf.call(opt.parentNode.children, opt));
    }
}

function onRaceDropdownKeydown(e) {
    const key = e.key;

    if (!raceDropdownOpen) {
        if (key === 'ArrowDown' || key === 'ArrowUp' || key === 'Enter' || key === ' ' || key === 'Spacebar') {
            e.preventDefault();
            openRaceDropdown();
        }
        return;
    }

    if (key === 'ArrowDown') { e.preventDefault(); setRaceDropdownActive(raceDropdownStep(raceDropdownActive, 1)); }
    else if (key === 'ArrowUp') { e.preventDefault(); setRaceDropdownActive(raceDropdownStep(raceDropdownActive, -1)); }
    else if (key === 'Home') { e.preventDefault(); setRaceDropdownActive(raceDropdownEdge(true)); }
    else if (key === 'End') { e.preventDefault(); setRaceDropdownActive(raceDropdownEdge(false)); }
    else if (key === 'Enter' || key === ' ' || key === 'Spacebar') { e.preventDefault(); chooseRaceDropdownActive(); }
    else if (key === 'Escape') { e.preventDefault(); closeRaceDropdown(true); }
    else if (key === 'Tab') { closeRaceDropdown(false); }
}

function initRaceDropdown() {
    const wrap = document.getElementById('results-round-dropdown');
    if (!wrap) return;
    wrap.addEventListener('click', onRaceDropdownClick);
    wrap.addEventListener('keydown', onRaceDropdownKeydown);
    const list = raceDropdownNode('results-round-list');
    if (list) list.addEventListener('mouseover', onRaceDropdownHover);
    document.addEventListener('click', function (e) {
        if (raceDropdownOpen && !wrap.contains(e.target)) closeRaceDropdown(false);
    });
}
</script>

</main>
