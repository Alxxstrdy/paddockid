/* ============================================================
   SEARCH SUGGEST (header + halaman /search)
   ----------------------------------------------
   Dropdown custom pengganti autocomplete browser pada kolom pencarian.
   - Kolom kosong (fokus): riwayat pencarian user di platform (login).
   - Saat mengetik        : saran user yang cocok (live).
   - Enter polos          : submit ke halaman search.
   - Enter/klik pada user : langsung buka profil /user/<username>.
   Pakai endpoint yang sudah ada:
     GET /search/history_ajax
     GET /search/search_ajax?type=users&q=
     POST /search/delete_history, /search/clear_history
   ============================================================ */
(function () {
    'use strict';

    var BASE = window.BASE_URL_JS || '';

    /* ---------- helper lokal (mandiri dari script lain) ---------- */

    function escAttr(str) {
        return String(str == null ? '' : str)
            .replace(/&/g, '&amp;')
            .replace(/"/g, '&quot;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    }

    function csrfField() {
        var n = document.querySelector('meta[name="csrf-token-name"]');
        var h = document.querySelector('meta[name="csrf-token-hash"]');
        if (!n || !h) return '';
        return n.content + '=' + encodeURIComponent(h.content);
    }

    function fetchJSON(url) {
        return fetch(url).then(function (r) { return r.json(); });
    }

    function createIcons() {
        if (window.lucide && typeof lucide.createIcons === 'function') {
            try { lucide.createIcons(); } catch (e) {}
        }
    }

    function defAvatar() {
        return escAttr(BASE + 'uploads/default.jpg');
    }

    /* ---------- komponen ---------- */

    function attachSuggest(input, menu) {
        var form = input.closest('form') || null;
        var seq = 0;
        var debTimer = null;
        var activeIdx = -1;
        var rows = [];

        function hide() {
            menu.classList.add('hidden');
            activeIdx = -1;
            rows = [];
        }

        function currentQuery() {
            return (input.value || '').trim();
        }

        function goUser(username) {
            window.location.href = BASE + 'user/' + encodeURIComponent(username);
        }

        function runSearch(keyword) {
            input.value = keyword;
            if (form) form.submit();
        }

        /* ----- render state ----- */

        function renderHistory() {
            fetchJSON(BASE + 'search/history_ajax').then(function (items) {
                if (!Array.isArray(items) || !items.length) {
                    hide();
                    return;
                }
                if (currentQuery() !== '') return; // user sudah mulai mengetik

                var html = '<div class="search-menu__header">' +
                    '<span>Riwayat Pencarian</span>' +
                    '<button type="button" class="search-menu__clear" data-kind="clear">Hapus riwayat</button>' +
                    '</div>';

                items.forEach(function (item) {
                    html += '<div class="search-menu__row" data-kind="history" data-q="' + escAttr(item.keyword) + '">' +
                        '<button type="button" class="search-menu__row-main">' +
                        '<i data-lucide="history" style="width:14px;height:14px;flex-shrink:0"></i>' +
                        '<span class="search-menu__text">' + escAttr(item.keyword) + '</span>' +
                        '</button>' +
                        '<button type="button" class="search-menu__row-x" data-kind="delete" data-id="' + escAttr(item.id) + '" title="Hapus dari riwayat">' +
                        '<i data-lucide="x" style="width:14px;height:14px"></i>' +
                        '</button>' +
                        '</div>';
                });

                menu.innerHTML = html;
                menu.classList.remove('hidden');
                activeIdx = -1;
                rows = Array.prototype.slice.call(menu.querySelectorAll('.search-menu__row'));
                createIcons();
            }).catch(function () {
                hide();
            });
        }

        function renderSuggest(q) {
            var mySeq = ++seq;
            menu.innerHTML = '<div class="search-menu__hint">Mencari pengguna...</div>';
            menu.classList.remove('hidden');

            fetchJSON(BASE + 'search/search_ajax?type=users&q=' + encodeURIComponent(q))
                .then(function (users) {
                    if (mySeq !== seq) return;
                    if (currentQuery() !== q) return;

                    if (!Array.isArray(users) || !users.length) {
                        menu.innerHTML = '<div class="search-menu__empty">Tidak ada pengguna ditemukan</div>';
                        activeIdx = -1;
                        rows = [];
                        return;
                    }

                    var html = users.map(function (u) {
                        var name = u.display_name || u.username;
                        var border = u.border
                            ? '<img src="' + escAttr(u.border) + '" alt="" style="position:absolute;inset:0;width:100%;height:100%;object-fit:contain;pointer-events:none;transform:scale(1.25)">'
                            : '';
                        return '<div class="search-menu__row search-menu__row--user" data-kind="user" data-username="' + escAttr(u.username) + '">' +
                            '<div class="search-menu__ava">' +
                            '<img class="search-menu__ava-img" src="' + escAttr(u.avatar) + '" alt="" loading="lazy" onerror="this.onerror=null;this.src=\'' + defAvatar() + '\'">' +
                            border +
                            '</div>' +
                            '<div class="search-menu__meta">' +
                            '<span class="search-menu__name">' + escAttr(name) + '</span>' +
                            '<span class="search-menu__uname">@' + escAttr(u.username) + '</span>' +
                            '</div>' +
                            '</div>';
                    }).join('');

                    menu.innerHTML = html;
                    activeIdx = -1;
                    rows = Array.prototype.slice.call(menu.querySelectorAll('.search-menu__row'));
                    createIcons();
                })
                .catch(function () {
                    if (mySeq !== seq) return;
                    hide();
                });
        }

        function renderSearch() {
            var q = currentQuery();
            if (q === '') {
                ++seq;
                renderHistory();
            } else {
                renderSuggest(q);
            }
        }

        /* ----- keyboard ----- */

        function setActive(i) {
            rows.forEach(function (r, idx) {
                r.classList.toggle('is-active', idx === i);
            });
            activeIdx = i;
            if (i >= 0 && rows[i] && rows[i].scrollIntoView) {
                rows[i].scrollIntoView({ block: 'nearest' });
            }
        }

        function triggerRow(row) {
            if (!row) return;
            var kind = row.dataset.kind;
            if (kind === 'user') {
                goUser(row.dataset.username);
            } else if (kind === 'history') {
                runSearch(row.dataset.q);
            }
            hide();
        }

        /* ----- aksi history (POST) ----- */

        function deleteHistoryItem(id) {
            fetch(BASE + 'search/delete_history', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: csrfField() + '&id=' + id
            }).then(renderHistory).catch(function () {});
        }

        function clearHistory() {
            if (currentQuery() !== '') return;
            fetch(BASE + 'search/clear_history', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: csrfField()
            }).then(hide).catch(function () {});
        }

        /* ----- events ----- */

        input.addEventListener('focus', renderSearch);

        input.addEventListener('input', function () {
            clearTimeout(debTimer);
            var q = currentQuery();
            if (q === '') {
                ++seq;
                renderHistory();
            } else {
                debTimer = setTimeout(function () {
                    renderSuggest(q);
                }, 250);
            }
        });

        input.addEventListener('keydown', function (e) {
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                if (!rows.length) return;
                setActive((activeIdx + 1) % rows.length);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                if (!rows.length) return;
                setActive((activeIdx - 1 + rows.length) % rows.length);
            } else if (e.key === 'Enter') {
                if (activeIdx >= 0 && rows[activeIdx]) {
                    e.preventDefault();
                    triggerRow(rows[activeIdx]);
                }
                // Enter polos: biarkan form submit (masuk halaman search).
            } else if (e.key === 'Escape') {
                e.preventDefault();
                hide();
                input.blur();
            }
        });

        menu.addEventListener('click', function (e) {
            var del = e.target.closest('[data-kind="delete"]');
            if (del) {
                e.preventDefault();
                e.stopPropagation();
                deleteHistoryItem(del.dataset.id);
                return;
            }
            var clear = e.target.closest('[data-kind="clear"]');
            if (clear) {
                e.preventDefault();
                e.stopPropagation();
                clearHistory();
                return;
            }
            var row = e.target.closest('.search-menu__row');
            if (row) {
                e.preventDefault();
                triggerRow(row);
            }
        });

        menu.addEventListener('mouseover', function (e) {
            var row = e.target.closest('.search-menu__row');
            if (!row) return;
            var i = rows.indexOf(row);
            if (i >= 0) setActive(i);
        });

        if (form) {
            form.addEventListener('submit', hide);
        }

        document.addEventListener('mousedown', function (e) {
            if (menu.classList.contains('hidden')) return;
            if (menu.contains(e.target) || input.contains(e.target)) return;
            hide();
        });
    }

    /* ---------- init ---------- */

    function boot() {
        var headerInput = document.getElementById('site-search');
        var headerMenu = document.getElementById('site-search-menu');
        if (headerInput && headerMenu) attachSuggest(headerInput, headerMenu);

        var pageInput = document.getElementById('search-input');
        var pageMenu = document.getElementById('search-dropdown');
        if (pageInput && pageMenu) attachSuggest(pageInput, pageMenu);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot);
    } else {
        boot();
    }
})();