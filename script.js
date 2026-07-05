/* =========================
   Nama file: slider-and-ui.js
   Vanilla JS module for homepage interactions:
   - Slider behavior (auto/manual)
   - Basic UI helpers (ARIA updates, pause/resume)
   No external libraries required.
   ========================= */

(() => {
  'use strict';

  /**
   * Configuration
   */
  const CONFIG = {
    sliderSelector: '.hero-slider',
    slideSelector: '.hero-slide',
    activeClass: 'is-active',
    intervalMs: 6000,           // autoplay interval
    transitionDuration: 420,    // match CSS (ms)
    pauseOnHover: true,
    indicatorsContainerClass: 'slider-indicators',
    controlsContainerClass: 'slider-controls'
  };

  /* Utilities */
  const $$ = (sel, ctx = document) => Array.from(ctx.querySelectorAll(sel));
  const $ = (sel, ctx = document) => ctx.querySelector(sel);

  /* Respect prefers-reduced-motion: disable auto-play & transitions if user prefers reduced motion */
  const prefersReducedMotion = () =>
    window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  /* Slider Class */
  class Slider {
    constructor(root, cfg = {}) {
      this.root = root;
      this.cfg = Object.assign({}, CONFIG, cfg);
      this.slides = $$(this.cfg.slideSelector, this.root);
      this.total = this.slides.length;
      this.current = 0;
      this.timer = null;
      this.isPaused = false;
      this.isTouched = false;
      this.touchStartX = 0;
      this.touchDelta = 0;
      this.indicators = null;
      this.controls = null;

      if (!this.root || this.total === 0) return;

      this.init();
    }

    init() {
      // Prepare markup: set roles/aria
      this.root.setAttribute('role', 'region');
      this.root.setAttribute('aria-roledescription', 'carousel');
      this.root.setAttribute('aria-label', 'Slide promosi utama');

      this.slides.forEach((slide, idx) => {
        slide.setAttribute('role', 'group');
        slide.setAttribute('aria-roledescription', 'slide');
        slide.setAttribute('data-slide-index', idx);
        // start all hidden
        slide.classList.remove(this.cfg.activeClass);
        slide.setAttribute('aria-hidden', 'true');
        slide.tabIndex = -1;
      });

      // Make first slide visible
      this.show(0, false);

      // Build controls
      this.buildControls();

      // Events
      this.bindEvents();

      // Start autoplay unless reduced-motion
      if (!prefersReducedMotion()) {
        this.start();
      }
    }

    buildControls() {
      // Create prev/next + indicators if not present
      let controlsWrapper = this.root.querySelector(`.${this.cfg.controlsContainerClass}`);
      if (!controlsWrapper) {
        controlsWrapper = document.createElement('div');
        controlsWrapper.className = this.cfg.controlsContainerClass;
        this.root.appendChild(controlsWrapper);
      }

      // Prev button
      const prevBtn = document.createElement('button');
      prevBtn.className = 'slider-button slider-prev';
      prevBtn.type = 'button';
      prevBtn.setAttribute('aria-label', 'Sebelumnya');
      prevBtn.innerHTML = '◀';
      controlsWrapper.appendChild(prevBtn);

      // Indicators container
      const indicators = document.createElement('div');
      indicators.className = this.cfg.indicatorsContainerClass;
      controlsWrapper.appendChild(indicators);

      // Next button
      const nextBtn = document.createElement('button');
      nextBtn.className = 'slider-button slider-next';
      nextBtn.type = 'button';
      nextBtn.setAttribute('aria-label', 'Berikutnya');
      nextBtn.innerHTML = '▶';
      controlsWrapper.appendChild(nextBtn);

      // build indicators
      for (let i = 0; i < this.total; i++) {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'slider-indicator';
        btn.setAttribute('data-index', i);
        btn.setAttribute('aria-pressed', i === this.current ? 'true' : 'false');
        btn.setAttribute('aria-label', `Slide ${i + 1} dari ${this.total}`);
        indicators.appendChild(btn);
      }

      this.controls = {
        prev: prevBtn,
        next: nextBtn,
        indicators: Array.from(indicators.children)
      };

      this.indicators = indicators;
    }

    bindEvents() {
      // Prev/Next clicks
      this.controls.prev.addEventListener('click', () => this.prev());
      this.controls.next.addEventListener('click', () => this.next());

      // Indicators
      this.controls.indicators.forEach(btn => {
        btn.addEventListener('click', (e) => {
          const idx = parseInt(e.currentTarget.getAttribute('data-index'), 10);
          this.goto(idx);
        });
      });

      // Pause on hover / focus
      if (this.cfg.pauseOnHover) {
        this.root.addEventListener('mouseenter', () => this.pause());
        this.root.addEventListener('mouseleave', () => this.resume());
        this.root.addEventListener('focusin', () => this.pause());
        this.root.addEventListener('focusout', () => this.resume());
      }

      // Keyboard navigation
      this.root.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowLeft') {
          e.preventDefault();
          this.prev();
        } else if (e.key === 'ArrowRight') {
          e.preventDefault();
          this.next();
        }
      });

      // Touch swipe (basic)
      this.root.addEventListener('touchstart', (e) => this.onTouchStart(e), {passive: true});
      this.root.addEventListener('touchmove', (e) => this.onTouchMove(e), {passive: true});
      this.root.addEventListener('touchend', (e) => this.onTouchEnd(e));

      // Visibility change — pause when tab not active
      document.addEventListener('visibilitychange', () => {
        if (document.hidden) this.pause();
        else this.resume();
      });

      // Window resize — can be used for responsive tweaks (debounced)
      let resizeTimer = null;
      window.addEventListener('resize', () => {
        if (resizeTimer) clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => {
          // placeholder for any responsive recalculations later
        }, 200);
      });
    }

    onTouchStart(e) {
      if (!e.touches || e.touches.length === 0) return;
      this.isTouched = true;
      this.touchStartX = e.touches[0].clientX;
      this.touchDelta = 0;
      this.pause();
    }

    onTouchMove(e) {
      if (!this.isTouched) return;
      const x = e.touches[0].clientX;
      this.touchDelta = x - this.touchStartX;
      // Optionally: visual feedback could be implemented, but avoid layout thrash
    }

    onTouchEnd() {
      if (!this.isTouched) return;
      this.isTouched = false;
      const abs = Math.abs(this.touchDelta);
      const threshold = 50; // px
      if (abs > threshold) {
        if (this.touchDelta > 0) this.prev();
        else this.next();
      }
      this.touchDelta = 0;
      this.resume();
    }

    start() {
      if (this.timer || prefersReducedMotion()) return;
      this.timer = setInterval(() => {
        if (!this.isPaused) this.next();
      }, this.cfg.intervalMs);
    }

    stop() {
      if (this.timer) {
        clearInterval(this.timer);
        this.timer = null;
      }
    }

    pause() {
      this.isPaused = true;
    }

    resume() {
      this.isPaused = false;
    }

    goto(index) {
      if (index < 0) index = 0;
      if (index >= this.total) index = this.total - 1;
      if (index === this.current) return;
      this.show(index, true);
    }

    prev() {
      const idx = (this.current - 1 + this.total) % this.total;
      this.show(idx, true);
    }

    next() {
      const idx = (this.current + 1) % this.total;
      this.show(idx, true);
    }

    show(index, withTransition = true) {
      // guard
      if (!this.slides[index]) return;

      const prevIndex = this.current;
      const prevSlide = this.slides[prevIndex];
      const nextSlide = this.slides[index];

      // Mark prev inactive
      if (prevSlide) {
        prevSlide.classList.remove(this.cfg.activeClass);
        prevSlide.setAttribute('aria-hidden', 'true');
        prevSlide.tabIndex = -1;
      }

      // Activate new
      nextSlide.classList.add(this.cfg.activeClass);
      nextSlide.setAttribute('aria-hidden', 'false');
      nextSlide.tabIndex = 0;
      nextSlide.focus && nextSlide.focus({preventScroll: true});

      // update indicators
      if (this.controls && this.controls.indicators) {
        this.controls.indicators.forEach((btn, i) => {
          btn.setAttribute('aria-pressed', i === index ? 'true' : 'false');
        });
      }

      this.current = index;

      // Reset timer to give users full interval after manual navigation
      if (this.timer) {
        clearInterval(this.timer);
        this.timer = null;
        // restart with small delay to avoid double-next when user clicks repeatedly
        setTimeout(() => {
          if (!prefersReducedMotion()) this.start();
        }, 200);
      }
    }
  }

  /* Initialize all sliders found on page */
  function initSliders() {
    const roots = $$(CONFIG.sliderSelector);
    roots.forEach(root => new Slider(root));
  }

  /* MAIN INIT on DOMContentLoaded */
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initSliders);
  } else {
    initSliders();
  }

  /* Expose a tiny API on window for debugging if needed */
  window.__NamaPlatformSlider = {
    init: initSliders,
    prefersReducedMotion: prefersReducedMotion
  };

})();
