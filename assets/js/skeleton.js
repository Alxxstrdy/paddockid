/* ============================================================
   SKELETON LOADER ENGINE
   ----------------------------------------------
   Kontainer bertanda [data-sk] memunculkan placeholder skeleton
   sebelum konten aslinya ditampilkan ("loading dulu, baru konten").
   Workflow per kontainer:
     1. Konten disembunyikan CSS via sk-init (anti-flash).
     2. Overlay per-objek (mengikuti ukuran asli => tanpa layout shift)
        ATAU placeholder "await-ajax" bila konten diisi lewat JS.
     3. Reveal: menunggu gambar selesai / MIN_DELAY / max timeout,
        lalu sk-show (konten tampil) + fade-in + hapus skeleton.
   ============================================================ */
(function () {
    'use strict';

    if (!document.documentElement.classList.contains('sk-init')) return;
    if (window.__skLoaded) return;
    window.__skLoaded = true;

    var MIN_DELAY = 450;          // ms minimal skeleton terlihat
    var IMG_TIMEOUT = 3000;       // ms maksimal tunggu gambar
    var AJAX_TIMEOUT = 2500;      // ms fallback untuk konten async

    /* ---------- helper ---------- */

    function isVisible(node) {
        if (!node) return false;
        var r = node.getBoundingClientRect();
        return r.width > 0 && r.height > 0;
    }

    function isAux(node) {
        if (!node || node.nodeType !== 1) return true;
        if (node.hasAttribute('data-sk-aux')) return true;
        var c = String(node.className || '');
        if (c.indexOf('h-empty') >= 0) return true;
        if (/(^|\s)(empty|empty-state)($|\s)/.test(c)) return true;
        return false;
    }

    function objectChildren(box) {
        var kids = [];
        Array.prototype.forEach.call(box.children, function (k) {
            if (k.nodeType !== 1) return;
            if (k.classList && k.classList.contains('hidden')) return;
            if (isAux(k)) return;
            kids.push(k);
        });
        return kids;
    }

    function release(box, keepFade) {
        if (box.classList.contains('sk-show')) return;
        box.classList.add('sk-show');
        if (keepFade) box.classList.add('sk-reveal');
        var phs = box.querySelectorAll('.sk-ph');
        for (var i = 0; i < phs.length; i++) phs[i].remove();
    }

    /* ---------- pembuat bentuk skeleton ---------- */

    function el(cls) {
        var e = document.createElement('div');
        e.className = cls;
        return e;
    }

    function line(w) {
        var e = el('sk-line');
        e.style.width = w;
        return e;
    }

    function objectPlaceholder(child, kind) {
        var ph = el('sk-ph' + (kind === 'chat' ? ' sk-chat' : ''));
        var h = child.offsetHeight || 120;
        ph.style.top = child.offsetTop + 'px';
        ph.style.height = h + 'px';

        if (kind === 'chat') {
            for (var i = 0; i < 4; i++) {
                ph.appendChild(el('sk-bubble' + (i % 2 ? ' is-me' : '')));
            }
            return ph;
        }

        var avaBox = null;
        var nodes = child.querySelectorAll('[class*="avatar"], .avatar');
        for (var a = 0; a < nodes.length; a++) {
            if (nodes[a].offsetHeight >= 20 && nodes[a].offsetHeight <= 64) {
                avaBox = nodes[a];
                break;
            }
        }
        var avaH = avaBox ? avaBox.offsetHeight : 0;
        if (avaH < 24 || avaH > 64) avaH = 40;

        var mediaBox = null;
        var mq = child.querySelectorAll('.post-images, .h-post__media, .post-media, .dm-post-card__media');
        for (var m = 0; m < mq.length; m++) {
            if (mq[m].offsetHeight > 40) { mediaBox = mq[m]; break; }
        }
        var mediaH = mediaBox ? mediaBox.offsetHeight : 0;

        var row = el('sk-row');
        var ava = el('sk-ava');
        ava.style.width = avaH + 'px';
        ava.style.height = avaH + 'px';
        ava.style.flexShrink = '0';
        var col = el('sk-col');
        col.appendChild(line('42%'));
        var l2 = line('64%');
        l2.style.height = '10px';
        col.appendChild(l2);
        row.appendChild(ava);
        row.appendChild(col);
        ph.appendChild(row);

        var blocks = el('sk-blocks');
        blocks.appendChild(line('100%'));
        blocks.appendChild(line('90%'));
        if (mediaH > 0) {
            var media = el('sk-media');
            media.style.height = mediaH + 'px';
            media.style.marginTop = '6px';
            blocks.appendChild(media);
        } else {
            var l3 = line('95%');
            l3.style.height = '10px';
            blocks.appendChild(l3);
        }
        ph.appendChild(blocks);

        if (kind === 'feed' || kind === 'post') {
            var acts = el('sk-row');
            acts.style.marginTop = '14px';
            acts.style.gap = '18px';
            for (var i2 = 0; i2 < 3; i2++) {
                var act = el('sk-action');
                act.style.width = '52px';
                act.style.height = '22px';
                acts.appendChild(act);
            }
            ph.appendChild(acts);
        }
        return ph;
    }

    function awaitPlaceholder(box, kind) {
        var ph = el('sk-ph' + (kind === 'chat' ? ' sk-chat' : ''));
        ph.style.top = '0';
        ph.style.height = (box.offsetHeight || 240) + 'px';
        if (kind === 'chat') {
            for (var i = 0; i < 5; i++) {
                ph.appendChild(el('sk-bubble' + (i % 2 ? ' is-me' : '')));
            }
        } else {
            for (var r = 0; r < 3; r++) {
                var row = el('sk-row');
                var ava = el('sk-ava');
                ava.style.width = '40px';
                ava.style.height = '40px';
                ava.style.flexShrink = '0';
                var cols = el('sk-col');
                cols.appendChild(line('46%'));
                var c2 = line('74%');
                c2.style.height = '10px';
                cols.appendChild(c2);
                row.appendChild(ava);
                row.appendChild(cols);
                ph.appendChild(row);
                if (r < 2) ph.appendChild(line('100%'));
            }
        }
        return ph;
    }

    /* ---------- mode: konten sudah dirender server ---------- */

    function waitObjects(box, objects, kind) {
        var imgs = [];
        objects.forEach(function (o) {
            Array.prototype.forEach.call(o.querySelectorAll('img'), function (im) {
                if (!(im.complete && im.naturalWidth > 0)) imgs.push(im);
            });
        });

        var t0 = performance ? performance.now() : Date.now();
        var done = false;
        var timers = [];

        function reveal() {
            if (done) return;
            done = true;
            for (var i = 0; i < timers.length; i++) clearTimeout(timers[i]);
            timers = [];
            release(box, true);
        }

        // Skeleton minimal terlihat MIN_DELAY agar terasa "sengaja".
        timers.push(setTimeout(reveal, MIN_DELAY));
        // Timeout maksimal menunggu gambar (agar tidak stuck).
        timers.push(setTimeout(reveal, IMG_TIMEOUT));

        if (!imgs.length) return;

        var pending = imgs.length;
        function onImgDone() {
            pending--;
            if (pending === 0 && (performance ? performance.now() : Date.now()) - t0 >= MIN_DELAY) reveal();
        }
        imgs.forEach(function (im) {
            im.addEventListener('load', onImgDone, { once: true });
            im.addEventListener('error', onImgDone, { once: true });
        });
    }

    /* ---------- mode: konten diisi lewat JS (await-ajax) ---------- */

    function waitAjax(box, kind) {
        var fallback = setTimeout(function () {
            release(box, false);
        }, AJAX_TIMEOUT);

        var mo = new MutationObserver(function () {
            if (objectChildren(box).length) {
                mo.disconnect();
                clearTimeout(fallback);
                // Konten sudah masuk DOM -> langsung tampilkan.
                release(box, true);
            }
        });
        mo.observe(box, { childList: true });
    }

    /* ---------- entry ---------- */

    function init() {
        var boxes = document.querySelectorAll('[data-sk]');
        Array.prototype.forEach.call(boxes, function (box) {
            var kind = String(box.getAttribute('data-sk') || 'feed');
            var objects = objectChildren(box);

            if (isVisible(box)) {
                if (objects.length) {
                    objects.forEach(function (o) { box.appendChild(objectPlaceholder(o, kind)); });
                    waitObjects(box, objects, kind);
                } else {
                    box.appendChild(awaitPlaceholder(box, kind));
                    waitAjax(box, kind);
                }
            } else if (objects.length) {
                // Kontainer masih tersembunyi (mis. tab) tapi isinya sudah
                // dirender server: langsung lepas supaya tidak terkunci
                // oleh visibility:hidden saat tab nanti dibuka.
                release(box, false);
            } else {
                // Kontainer tersembunyi yang akan diisi lewat JS/AJAX:
                // pasang watcher supaya .sk-show ditambahkan begitu konten masuk.
                waitAjax(box, kind);
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();