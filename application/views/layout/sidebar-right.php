
    <!-- SIDEBAR KANAN -->
    <aside class="sidebar-right">
        <!-- LIVE CHAT CARD -->
        <a id="chat-card-desktop" href="<?= base_url('chat'); ?>" class="chat-card hidden mb-6">
            <div class="p-4">
                <div class="flex-row justify-between mb-2-5">
                    <div class="flex-row gap-2-5">
                        <div style="width:32px;height:32px;border-radius:var(--radius-md);background:var(--color-primary-bg);border:1px solid var(--color-primary-border);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i data-lucide="message-circle" style="width:16px;height:16px;" class="c-primary"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-micro c-primary font-bold">Live Chat</p>
                            <p id="chat-card-session-desktop" class="text-caption text-truncate">...</p>
                        </div>
                    </div>
                    <span class="btn-xs c-primary" style="border:1px solid var(--color-primary-border);background:var(--color-primary-bg);border-radius:var(--radius-pill);flex-shrink:0;">
                        Masuk <i data-lucide="arrow-right" style="width:12px;height:12px;" class="inline-block"></i>
                    </span>
                </div>
                <p id="chat-card-event-desktop" class="text-caption text-truncate">...</p>
            </div>
        </a>

        <div class="card p-4">
            <!-- Standings -->
            <div class="p-4 rounded-lg" style="max-width:384px;">
                <div class="flex-row justify-between mb-4">
                    <div class="flex-row gap-2">
                        <span style="width:3px;height:12px;background:var(--color-primary);border-radius:2px;" class="animate-pulse"></span>
                        <h3 class="text-section-title">Championship Standings</h3>
                    </div>
                    <a href="#" class="btn-icon-sm c-subtle transition-colors">
                        <i data-lucide="chevron-right" style="width:16px;height:16px;"></i>
                    </a>
                </div>

                <div id="driver-standings-list" class="space-y-1-5">
                    <div class="text-center p-4 text-small animate-pulse">
                        Loading Data...
                    </div>
                </div>
            </div>
        </div>
    </aside>
    </div>
</div>


<script>
    const getTeamColorClass = (constructorId) => {
        const colors = {
            'red_bull': 'standings-row--team-red-bull',
            'mclaren': 'standings-row--team-mclaren',
            'ferrari': 'standings-row--team-ferrari',
            'mercedes': 'standings-row--team-mercedes',
            'aston_martin': 'standings-row--team-aston-martin',
            'alpine': 'standings-row--team-alpine',
            'haas': 'standings-row--team-haas',
            'rb': 'standings-row--team-rb',
            'sauber': 'standings-row--team-sauber',
            'williams': 'standings-row--team-williams'
        };
        return colors[constructorId] || '';
    };

    async function fetchF1Standings() {
        const container = document.getElementById('driver-standings-list');
        
        try {
            const response = await fetch('https://api.jolpi.ca/ergast/f1/current/driverStandings.json');
            const data = await response.json();
            
            const standings = data.MRData.StandingsTable.StandingsLists[0].DriverStandings;
            const top10 = standings.slice(0, 5);
            
            container.innerHTML = '';
            
            top10.forEach(item => {
                const position = item.position;
                const points = item.points;
                const driver = item.Driver;
                const constructor = item.Constructors[0];
                
                const shortName = `${driver.givenName.charAt(0)}. ${driver.familyName}`;
                const nationality = driver.nationality.substring(0, 3).toUpperCase();
                const teamClass = getTeamColorClass(constructor.constructorId);

                const rowHtml = `
                    <div class="standings-row ${teamClass}">
                        <div class="flex-row gap-3">
                            <span class="text-heading text-xs font-black c-subtle" style="width:16px;">${escapeHtml(position)}</span>
                            <div>
                                <div class="flex-row gap-1-5">
                                    <p class="text-xs font-bold text-truncate">${escapeHtml(shortName)}</p>
                                    <span class="text-micro text-faint" style="font-size:8px;text-transform:none;letter-spacing:0;">${escapeHtml(nationality)}</span>
                                </div>
                                <p class="text-micro" style="font-size:9px;margin-top:2px;">${escapeHtml(constructor.name)}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-mono text-xs font-bold text-truncate">${escapeHtml(points)} <span class="text-micro" style="font-size:9px;font-weight:400;">PTS</span></span>
                        </div>
                    </div>
                `;
                
                container.innerHTML += rowHtml;
            });
            
        } catch (error) {
            console.error('Gagal mengambil data F1:', error);
            container.innerHTML = `<div class="text-center p-4 text-xs c-danger">Failed to load standings.</div>`;
        }
    }

    document.addEventListener('DOMContentLoaded', fetchF1Standings);
</script>
