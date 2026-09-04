
        </main>
    </div>

    <!-- Toast -->
    <div id="admin-toast" class="admin-toast">
        <div class="admin-toast-inner">
            <i data-lucide="check-circle" class="admin-toast-icon" id="admin-toast-icon"></i>
            <span id="admin-toast-text"></span>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            var sidebar = document.getElementById('admin-sidebar');
            var overlay = document.getElementById('sidebar-overlay');
            sidebar.classList.toggle('sidebar-closed');
            overlay.classList.toggle('hidden');
        }

        document.addEventListener('click', function(e) {
            var sidebar = document.getElementById('admin-sidebar');
            var overlay = document.getElementById('sidebar-overlay');
            if (sidebar && overlay && !sidebar.contains(e.target) && !overlay.classList.contains('hidden')) {
                toggleSidebar();
            }
        });

        function showToast(message, color) {
            var toast = document.getElementById('admin-toast');
            var text = document.getElementById('admin-toast-text');
            var icon = document.getElementById('admin-toast-icon');
            if (!toast || !text || !icon) return;
            text.textContent = message;
            icon.setAttribute('data-lucide', color === 'red' ? 'alert-circle' : 'check-circle');
            icon.className = 'admin-toast-icon ' + (color === 'red' ? 'c-danger' : 'c-success');
            lucide.createIcons();
            toast.classList.add('toast-visible');
            clearTimeout(window._toastTimer);
            window._toastTimer = setTimeout(function() {
                toast.classList.remove('toast-visible');
            }, 3000);
        }

        var notifPanelOpen = false;

        function toggleNotifPanel() {
            notifPanelOpen = !notifPanelOpen;
            var panel = document.getElementById('notif-panel');
            if (panel) panel.classList.toggle('open', notifPanelOpen);
        }

        document.addEventListener('click', function(e) {
            var wrapper = document.getElementById('notif-wrapper');
            if (wrapper && !wrapper.contains(e.target) && notifPanelOpen) {
                notifPanelOpen = false;
                var panel = document.getElementById('notif-panel');
                if (panel) panel.classList.remove('open');
            }
        });

        function renderNotifPanel() {
            var list = document.getElementById('notif-list');
            if (!list || !window._lastCounts) return;
            var c = window._lastCounts;
            var total = c.post_reports + c.user_reports + c.failed_logins + c.errors_today;
            var html = '';

            if (total === 0) {
                html = '<div class="notif-empty">Tidak ada notifikasi baru.</div>';
            } else {
                if (c.post_reports > 0) html += _notifItem('flag', 'c-danger', 'notif-icon--danger', c.post_reports + ' post report pending', 'Post Reports');
                if (c.user_reports > 0) html += _notifItem('shield-alert', 'c-warning', 'notif-icon--warning', c.user_reports + ' user report pending', 'User Reports');
                if (c.failed_logins > 0) html += _notifItem('log-in', 'c-info', 'notif-icon--info', c.failed_logins + ' login gagal (24j)', 'Login Attempts');
                if (c.errors_today > 0) html += _notifItem('alert-triangle', 'c-danger', 'notif-icon--danger', c.errors_today + ' error/warning hari ini', 'Error Logs');
            }
            list.innerHTML = html;
            lucide.createIcons();

            var totalEl = document.getElementById('notif-panel-total');
            if (totalEl) totalEl.textContent = total > 0 ? total + ' item' : 'bersih';

            var lastCheck = document.getElementById('notif-last-check');
            if (lastCheck) lastCheck.textContent = 'Update: ' + new Date().toLocaleTimeString('id-ID');
        }

        function _notifItem(icon, iconColor, bgColor, text, label) {
            return '<a href="<?= base_url("admin/" . str_replace("_reports", "_reports", $admin_page)); ?>" class="notif-item">' +
                '<div class="notif-item-row">' +
                '<div class="notif-icon-wrapper ' + bgColor + '">' +
                '<i data-lucide="' + icon + '" class="notif-icon ' + iconColor + '"></i></div>' +
                '<div class="notif-item-text"><p class="notif-item-title">' + text + '</p>' +
                '<p class="notif-item-label">' + label + '</p></div></div></a>';
        }

        var COUNTS_URL = '<?= base_url("admin/get_counts"); ?>';
        var THIS_PAGE = window.location.pathname + window.location.search;
        var POLL_MS = 8000;
        window._lastCounts = null;
        window._prevTotal = 0;
        window._prevCounts = { post_reports: -1, user_reports: -1, failed_logins: -1, errors_today: -1 };

        function _updateBadge(id, count) {
            var el = document.getElementById(id);
            if (!el) return;
            if (count > 0) {
                el.textContent = count > 99 ? '99+' : count;
                el.classList.remove('hidden');
            } else {
                el.classList.add('hidden');
            }
        }

        function _updateTitle(count) {
            var base = <?= json_encode(htmlspecialchars($title ?? 'Admin', ENT_QUOTES, 'UTF-8')); ?>;
            document.title = count > 0 ? '(' + count + ') ' + base + ' — PaddockID' : base + ' — PaddockID';
        }

        function _hasChanged(a, b) {
            return a.post_reports !== b.post_reports ||
                   a.user_reports !== b.user_reports ||
                   a.failed_logins !== b.failed_logins ||
                   a.errors_today !== b.errors_today;
        }

        function _refreshPage() {
            var main = document.querySelector('main');
            if (!main) { location.reload(); return; }
            if (main.querySelector('form[data-freeze-refresh]')) return;

            var url = THIS_PAGE + (THIS_PAGE.indexOf('?') > -1 ? '&' : '?') + 'ajax=1&_t=' + Date.now();

            var xhr = new XMLHttpRequest();
            xhr.open('GET', url, true);
            xhr.withCredentials = true;
            xhr.onreadystatechange = function() {
                if (xhr.readyState !== 4) return;
                if (xhr.status === 200 && xhr.responseText.length > 50) {
                    main.innerHTML = xhr.responseText;
                    main.querySelectorAll('script').forEach(function(old) {
                        var s = document.createElement('script');
                        s.textContent = old.textContent;
                        old.parentNode.replaceChild(s, old);
                    });
                    lucide.createIcons();
                    showToast('Data diperbarui', 'green');
                } else {
                    location.reload();
                }
            };
            xhr.onerror = function() { location.reload(); };
            xhr.send();
        }

        function fetchCounts() {
            var xhr = new XMLHttpRequest();
            xhr.open('GET', COUNTS_URL + '?_t=' + Date.now(), true);
            xhr.withCredentials = true;
            xhr.onreadystatechange = function() {
                if (xhr.readyState !== 4) return;
                if (xhr.status !== 200) return;

                try {
                    var c = JSON.parse(xhr.responseText);
                } catch(e) { return; }

                window._lastCounts = c;
                var total = c.post_reports + c.user_reports + c.failed_logins + c.errors_today;

                _updateBadge('badge-post-reports', c.post_reports);
                _updateBadge('badge-user-reports', c.user_reports);
                _updateBadge('badge-failed-logins', c.failed_logins);
                _updateBadge('badge-errors', c.errors_today);

                var brandBadge = document.getElementById('sidebar-total-badge');
                if (brandBadge) {
                    if (total > 0) {
                        brandBadge.textContent = total > 99 ? '99+' : total;
                        brandBadge.classList.remove('hidden');
                    } else {
                        brandBadge.classList.add('hidden');
                    }
                }

                ['mobile-notif-badge', 'desktop-notif-badge'].forEach(function(id) {
                    var el = document.getElementById(id);
                    if (!el) return;
                    if (total > 0) {
                        el.textContent = total > 99 ? '99+' : total;
                        el.classList.remove('hidden');
                    } else {
                        el.classList.add('hidden');
                    }
                });

                _updateTitle(total);

                if (_hasChanged(window._prevCounts, c)) {
                    var diff = total - window._prevTotal;
                    if (diff > 0 && window._prevTotal >= 0) {
                        showToast('+' + diff + ' data baru — updating...', 'red');
                    } else if (total === 0 && window._prevTotal > 0) {
                        showToast('Semua sudah ditangani', 'green');
                    }
                    _refreshPage();
                }

                window._prevTotal = total;
                window._prevCounts = { post_reports: c.post_reports, user_reports: c.user_reports, failed_logins: c.failed_logins, errors_today: c.errors_today };

                renderNotifPanel();

                var dot = document.getElementById('live-dot');
                if (dot) {
                    dot.style.background = '#34d399';
                    setTimeout(function() { dot.style.background = ''; }, 400);
                }
            };
            xhr.send();
        }

        fetchCounts();
        setInterval(fetchCounts, POLL_MS);
    </script>
    <script>lucide.createIcons();</script>
</body>
</html>