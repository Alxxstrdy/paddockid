<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$stage_html = isset($stage_html) ? $stage_html : '';
$mode       = isset($mode) ? $mode : 'lobby';
?>

<section id="gd-stage">
    <?= $stage_html; ?>
</section>

<script>
(function () {
    'use strict';
    var stage = document.getElementById('gd-stage');
    if (!stage) return;

    var inFlight = false;

    function showAlert(msg) {
        var a = stage.querySelector('#gd-alert');
        if (!a) return;
        a.textContent = msg;
        a.classList.remove('hidden');
    }
    function clearAlert() {
        var a = stage.querySelector('#gd-alert');
        if (a) { a.textContent = ''; a.classList.add('hidden'); }
    }

    function post(url, body) {
        var params = new URLSearchParams(body);
        params.append(getCsrfTokenName(), getCsrfToken());
        return fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: params.toString(),
            credentials: 'same-origin'
        }).then(function (r) {
            if (r.status === 401) {
                window.location.href = BASE_URL_JS + 'auth';
                throw new Error('Sesi berakhir. Mengarahkan ke login…');
            }
            if (!r.ok) throw new Error('Server merespons ' + r.status);
            return r.json().catch(function () {
                throw new Error('Respons server tidak valid.');
            });
        });
    }

    function replaceStage(html) {
        stage.innerHTML = html;
        if (typeof lucide !== 'undefined' && lucide.createIcons) lucide.createIcons();
        bind(stage);
        var input = stage.querySelector('#gd-guess-input');
        if (input) input.focus();
    }

    function doAction(url, extra) {
        if (inFlight) return Promise.resolve();
        inFlight = true;
        clearAlert();
        return post(url, extra || {})
            .then(function (res) {
                if (!res.ok) { showAlert(res.error || 'Terjadi kesalahan.'); return; }
                replaceStage(res.stage);
            })
            .catch(function (e) { showAlert(e.message || 'Terjadi kesalahan jaringan.'); })
            .finally(function () { inFlight = false; });
    }

    function bind(root) {
        // Play dari lobby
        var playBtn = root.querySelector('#gd-play');
        if (playBtn) {
            playBtn.addEventListener('click', function () {
                doAction(BASE_URL_JS + 'game/start');
            });
        }

        // Submit tebakan (Enter atau klik Tebak)
        var form = root.querySelector('#gd-guess-form');
        if (form) {
            form.addEventListener('submit', function (ev) {
                ev.preventDefault();
                if (inFlight) return;
                var input = root.querySelector('#gd-guess-input');
                var val = (input && input.value.trim()) || '';
                if (!val) { showAlert('Ketik nama pembalap lebih dulu.'); return; }
                var btn = form.querySelector('button[type="submit"]');
                if (btn) btn.disabled = true;
                doAction(BASE_URL_JS + 'game/guess', { driver: val })
                    .finally(function () { if (btn) btn.disabled = false; });
            });
        }

        // Aksi hasil: main lagi / kembali ke lobby
        var actBtns = root.querySelectorAll('[data-gd-action]');
        if (actBtns.length) {
            Array.prototype.forEach.call(actBtns, function (b) {
                b.addEventListener('click', function () {
                    var act = b.getAttribute('data-gd-action');
                    var url = act === 'restart' ? BASE_URL_JS + 'game/start' : BASE_URL_JS + 'game/quit';
                    doAction(url);
                });
            });
        }
    }

    bind(stage);
})();
</script>