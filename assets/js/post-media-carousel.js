/**
 * PaddockID — Post Media Carousel
 *
 * Renderer + behaviour tunggal untuk media postingan. Dipakai oleh home, detail
 * post, profil, halaman user, dan search (server-rendered maupun JS-rendered).
 *
 * - 1 gambar  -> tampilan gambar biasa, tanpa navigasi.
 * - >1 gambar -> carousel, satu slide per gambar, urutan sesuai file_url.
 * - Area media memakai --pmc-ratio yang diambil dari metadata (jika ada) atau
 *   dari naturalWidth/naturalHeight gambar aktif. Tidak ada fixed ratio.
 */
(function (global) {
    'use strict';

    if (global.PaddockPostMedia) return;

    var FALLBACK_RATIO = '4 / 3';
    var SWIPE_THRESHOLD = 45;
    var DIM_STORE = 'pmc-image-dims';
    var dimsCache = Object.create(null);
    var storedDims = null;

    function escapeHtml(value) {
        return String(value === null || value === undefined ? '' : value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function splitUrls(fileUrl) {
        if (!fileUrl) return [];
        return String(fileUrl).split(',').map(function (url) {
            return String(url).trim();
        }).filter(function (url) {
            return url !== '';
        });
    }

    function ratioFrom(width, height) {
        var w = Number(width);
        var h = Number(height);
        return (w > 0 && h > 0) ? (w + ' / ' + h) : '';
    }

    function readStored(src) {
        try {
            if (!storedDims && global.sessionStorage) {
                storedDims = JSON.parse(global.sessionStorage.getItem(DIM_STORE) || '{}');
            }
            var entry = storedDims && storedDims[src];
            return (entry && entry[0] > 0 && entry[1] > 0)
                ? { width: entry[0], height: entry[1] }
                : null;
        } catch (e) {
            return null;
        }
    }

    function writeStored(src, width, height) {
        try {
            if (!global.sessionStorage) return;
            if (!storedDims) storedDims = JSON.parse(global.sessionStorage.getItem(DIM_STORE) || '{}');
            storedDims[src] = [width, height];
            global.sessionStorage.setItem(DIM_STORE, JSON.stringify(storedDims));
        } catch (e) {
            /* Storage bisa dinonaktifkan; carousel tetap bekerja. */
        }
    }

    function rememberDims(img) {
        if (!img || !img.naturalWidth || !img.naturalHeight) return;
        var src = img.getAttribute('src') || img.getAttribute('data-src');
        if (!src) return;
        dimsCache[src] = { width: img.naturalWidth, height: img.naturalHeight };
        writeStored(src, img.naturalWidth, img.naturalHeight);
    }

    function getDims(img) {
        if (!img) return null;
        var width = parseInt(img.getAttribute('data-width') || img.getAttribute('width') || '0', 10);
        var height = parseInt(img.getAttribute('data-height') || img.getAttribute('height') || '0', 10);
        if (width > 0 && height > 0) return { width: width, height: height };
        if (img.naturalWidth > 0 && img.naturalHeight > 0) {
            return { width: img.naturalWidth, height: img.naturalHeight };
        }
        var src = img.getAttribute('src') || img.getAttribute('data-src');
        if (!src) return null;
        if (dimsCache[src]) return dimsCache[src];
        return readStored(src);
    }

    function setFrameRatio(frame, img) {
        var dims = getDims(img);
        frame.style.setProperty('--pmc-ratio', dims ? ratioFrom(dims.width, dims.height) : FALLBACK_RATIO);
    }

    function activateSrc(img) {
        if (!img) return;
        var src = img.getAttribute('data-src');
        if (src && !img.getAttribute('src')) {
            img.setAttribute('src', src);
            img.removeAttribute('data-src');
        }
    }

    function refreshIcons(scope) {
        if (global.lucide && typeof global.lucide.createIcons === 'function') {
            try { global.lucide.createIcons(); } catch (e) { /* abaikan */ }
        }
    }

    /**
     * Bangun markup media yang identik dengan post_media_html() di PHP.
     * @param {string} fileUrl Daftar URL dipisahkan koma.
     * @param {Array}  dimensions Opsi [{width, height}, ...] bila tersedia.
     */
    function render(fileUrl, dimensions) {
        dimensions = dimensions || [];
        var urls = splitUrls(fileUrl);
        if (!urls.length) return '';

        var count = urls.length;
        var isCarousel = count > 1;
        var first = dimensions[0] || {};
        var initialRatio = ratioFrom(first.width, first.height) || FALLBACK_RATIO;

        var html = '<div class="h-post__media">';
        html += '<div class="post-media ' + (isCarousel ? 'post-media--carousel' : 'post-media--single') + '" data-pmc data-pmc-count="' + count + '">';
        html += '<div class="post-media__frame" data-pmc-frame style="--pmc-ratio: ' + initialRatio + ';"';
        if (isCarousel) {
            html += ' role="group" aria-roledescription="carousel" aria-label="Galeri postingan" tabindex="0"';
        }
        html += '>';

        urls.forEach(function (url, index) {
            var dims = dimensions[index] || {};
            var width = parseInt(dims.width || dims[0] || '0', 10);
            var height = parseInt(dims.height || dims[1] || '0', 10);
            var dimAttr = (width > 0 && height > 0)
                ? ' data-width="' + width + '" data-height="' + height + '"'
                : '';
            var active = index === 0;
            var srcAttr = active
                ? ' src="' + escapeHtml(url) + '"'
                : ' data-src="' + escapeHtml(url) + '"';

            html += '<div class="post-media__slide' + (active ? ' is-active' : '') + '" data-pmc-slide data-pmc-index="' + index + '" data-pmc-open';
            if (!active) html += ' aria-hidden="true"';
            html += '>';
            html += '<img' + srcAttr + ' alt="Media postingan ' + (index + 1) + '" loading="lazy" decoding="async" data-pmc-img' + dimAttr + '>';
            html += '</div>';
        });

        if (isCarousel) {
            html += '<button type="button" class="pmc__nav pmc__nav--prev" data-pmc-prev aria-label="Gambar sebelumnya"><i data-lucide="chevron-left"></i></button>';
            html += '<button type="button" class="pmc__nav pmc__nav--next" data-pmc-next aria-label="Gambar berikutnya"><i data-lucide="chevron-right"></i></button>';
            html += '<span class="pmc__counter" data-pmc-counter aria-live="polite">1 / ' + count + '</span>';
        }

        html += '</div></div></div>';
        return html;
    }

    function markBroken(slide) {
        if (slide.querySelector('.pmc__fallback')) return;
        slide.classList.add('is-broken');
        var fallback = document.createElement('span');
        fallback.className = 'pmc__fallback';
        fallback.innerHTML = '<i data-lucide="image-off"></i><span>Gambar tidak tersedia</span>';
        slide.appendChild(fallback);
        refreshIcons(slide);
    }

    function initCarousel(root) {
        if (root.getAttribute('data-pmc-ready') === '1') return;
        root.setAttribute('data-pmc-ready', '1');

        var frame = root.querySelector('[data-pmc-frame]');
        var slides = Array.prototype.slice.call(root.querySelectorAll('[data-pmc-slide]'));
        if (!frame || !slides.length) return;

        var state = { index: 0, suppressClick: false };
        var counter = root.querySelector('[data-pmc-counter]');
        var prevBtn = root.querySelector('[data-pmc-prev]');
        var nextBtn = root.querySelector('[data-pmc-next]');

        function prefetchNext() {
            var next = slides[state.index + 1];
            if (next) activateSrc(next.querySelector('[data-pmc-img]'));
        }

        function goTo(index) {
            if (index < 0 || index >= slides.length || index === state.index) return;

            var current = slides[state.index];
            var target = slides[index];
            var targetImg = target.querySelector('[data-pmc-img]');

            /* Terapkan rasio slide tujuan SEBELUM menampilkannya. Bila belum
               diketahui, pakai ratio netral — bukan tinggi slide sebelumnya. */
            setFrameRatio(frame, targetImg);

            state.index = index;
            current.classList.remove('is-active');
            current.setAttribute('aria-hidden', 'true');
            target.classList.add('is-active');
            target.removeAttribute('aria-hidden');
            if (counter) counter.textContent = (index + 1) + ' / ' + slides.length;

            activateSrc(targetImg);
            prefetchNext();
        }

        slides.forEach(function (slide, index) {
            var img = slide.querySelector('[data-pmc-img]');
            if (!img) return;

            img.addEventListener('load', function () {
                rememberDims(img);
                slide.classList.remove('is-broken');
                var fallback = slide.querySelector('.pmc__fallback');
                if (fallback) fallback.remove();
                if (index === state.index) setFrameRatio(frame, img);
            });

            img.addEventListener('error', function () {
                markBroken(slide);
                if (index === state.index) setFrameRatio(frame, img);
            });

            /* Gambar server-rendered bisa selesai (atau gagal) sebelum listener
               sempat dipasang. Tangani kondisi complete di sini juga. */
            if (img.complete && img.getAttribute('src')) {
                if (img.naturalWidth > 0) {
                    rememberDims(img);
                    if (index === state.index) setFrameRatio(frame, img);
                } else {
                    markBroken(slide);
                }
            }

            slide.addEventListener('click', function (e) {
                if (state.suppressClick) {
                    e.preventDefault();
                    e.stopPropagation();
                    return;
                }

                var event = new CustomEvent('postmedia:open', {
                    bubbles: true,
                    cancelable: true,
                    detail: { index: index, count: slides.length, root: root }
                });
                var notHandled = slide.dispatchEvent(event);
                if (!notHandled) {
                    e.preventDefault();
                    e.stopPropagation();
                    return;
                }

                /* Feed: klik gambar tetap membuka detail post seperti sebelumnya. */
                var card = root.closest ? root.closest('.h-post') : null;
                var link = card ? card.querySelector('.h-post__link') : null;
                if (link) {
                    e.preventDefault();
                    e.stopPropagation();
                    link.click();
                }
            });
        });

        if (prevBtn) {
            prevBtn.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                if (state.suppressClick) return;
                goTo((state.index - 1 + slides.length) % slides.length);
            });
        }
        if (nextBtn) {
            nextBtn.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                if (state.suppressClick) return;
                goTo((state.index + 1) % slides.length);
            });
        }

        frame.addEventListener('keydown', function (e) {
            if (e.key === 'ArrowLeft') {
                e.preventDefault();
                goTo((state.index - 1 + slides.length) % slides.length);
            } else if (e.key === 'ArrowRight') {
                e.preventDefault();
                goTo((state.index + 1) % slides.length);
            }
        });

        var touchStart = null;
        frame.addEventListener('touchstart', function (e) {
            if (e.touches.length !== 1) {
                touchStart = null;
                return;
            }
            touchStart = { x: e.touches[0].clientX, y: e.touches[0].clientY };
        }, { passive: true });

        frame.addEventListener('touchend', function (e) {
            if (!touchStart || !e.changedTouches.length) return;
            var dx = e.changedTouches[0].clientX - touchStart.x;
            var dy = e.changedTouches[0].clientY - touchStart.y;
            touchStart = null;

            /* Geser horizontal menang; geser vertikal dibiarkan untuk scroll. */
            if (Math.abs(dx) < SWIPE_THRESHOLD || Math.abs(dx) <= Math.abs(dy)) return;

            state.suppressClick = true;
            setTimeout(function () { state.suppressClick = false; }, 350);
            goTo(dx < 0 ? state.index + 1 : state.index - 1);
        }, { passive: true });

        frame.addEventListener('touchcancel', function () { touchStart = null; }, { passive: true });

        /* Pasang rasio awal dari metadata/cache, lalu muat slide pertama. */
        var activeImg = slides[0].querySelector('[data-pmc-img]');
        setFrameRatio(frame, activeImg);
        activateSrc(activeImg);
        if (slides.length > 1) prefetchNext();
    }

    function init(root) {
        var scope = root || document;
        var nodes;
        if (scope.getAttribute && scope.getAttribute('data-pmc') !== null) {
            nodes = [scope];
        } else {
            nodes = Array.prototype.slice.call(scope.querySelectorAll('[data-pmc]:not([data-pmc-ready])'));
        }
        nodes.forEach(initCarousel);
        if (nodes.length) refreshIcons(scope);
        return nodes.length;
    }

    global.PaddockPostMedia = {
        render: render,
        init: init,
        splitUrls: splitUrls
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () { init(document); }, { once: true });
    } else {
        init(document);
    }
})(window);
