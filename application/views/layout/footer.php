    <!-- LOGIN PROMPT MODAL -->
    <div id="login-modal" class="fixed inset-0 z-[999] hidden">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="hideLoginModal()"></div>
        <div class="absolute inset-0 flex items-center justify-center p-4">
            <div class="glass-card rounded-2xl w-full max-w-sm border border-white/[0.06] shadow-2xl p-6 text-center">
                <div class="w-12 h-12 mx-auto mb-4 rounded-full bg-red-500/10 flex items-center justify-center">
                    <i data-lucide="lock" class="w-6 h-6 text-red-500"></i>
                </div>
                <h3 class="font-syne text-sm uppercase tracking-tight text-white mb-2">Login Diperlukan</h3>
                <p class="text-xs text-slate-400 leading-relaxed mb-6">Silakan masuk atau daftar akun terlebih dahulu untuk mengakses fitur ini.</p>
                <div class="flex gap-3">
                    <button onclick="hideLoginModal()" class="flex-1 px-4 py-2.5 text-xs font-semibold text-slate-300 bg-white/[0.05] hover:bg-white/[0.08] rounded-xl transition-colors border border-white/[0.06]">
                        Kembali
                    </button>
                    <a href="<?= base_url('auth'); ?>" class="flex-1 px-4 py-2.5 text-xs font-semibold text-white bg-red-600 hover:bg-red-500 rounded-xl transition-colors shadow-lg shadow-red-600/10">
                        Masuk / Daftar
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- MOBILE BOTTOM NAVIGATION (Hanya muncul di Layar HP) -->
    <div class="block lg:hidden fixed bottom-0 left-0 right-0 z-50 bg-[#05070c]/80 backdrop-blur-xl border-t border-white/[0.04] px-6 py-3">
        <div class="flex items-center justify-between text-slate-400">
            <a href="<?= base_url('home'); ?>" class="flex flex-col items-center justify-center gap-1 nav-bottom" data-nav="home">
                <i data-lucide="layout-grid" class="w-5 h-5"></i>
                <span class="text-[9px] font-medium">Feed</span>
            </a>
            <a href="#" class="flex flex-col items-center justify-center gap-1 hover:text-white transition-colors nav-bottom" data-nav="race">
                <i data-lucide="calendar" class="w-5 h-5"></i>
                <span class="text-[9px] font-medium">Race Hub</span>
            </a>
            <a href="#" class="flex flex-col items-center justify-center gap-1 hover:text-white transition-colors nav-bottom" data-nav="search">
                <i data-lucide="search" class="w-5 h-5"></i>
                <span class="text-[9px] font-medium">Search</span>
            </a>
            <a href="#" class="flex flex-col items-center justify-center gap-1 hover:text-white transition-colors nav-bottom" data-nav="shop">
                <i data-lucide="sparkles" class="w-5 h-5"></i>
                <span class="text-[9px] font-medium">Shop</span>
            </a>
        </div>
    </div>

    <script>
        const IS_LOGGED_IN = <?= $this->session->userdata('user_logged_in') ? 'true' : 'false'; ?>;

        function showLoginModal() {
            document.getElementById('login-modal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function hideLoginModal() {
            document.getElementById('login-modal').classList.add('hidden');
            document.body.style.overflow = '';
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Prevent post detail click for guests
            document.addEventListener('click', function(e) {
                const cardLink = e.target.closest('a[href*="/post/"]');
                if (cardLink && !IS_LOGGED_IN) {
                    e.preventDefault();
                    showLoginModal();
                }
            });

            // Active navigation highlighting
            const currentPath = window.location.pathname;
            const navLinks = document.querySelectorAll('.nav-bottom, .nav-sidebar');
            
            navLinks.forEach(link => {
                const href = link.getAttribute('href');
                if (!href || href === '#') return;
                
                const linkPath = new URL(href, window.location.origin).pathname;
                
                // Remove existing active state
                link.classList.remove('text-white', 'text-red-500');
                const icon = link.querySelector('[data-lucide]');
                if (icon) icon.classList.remove('text-red-500');

                if (currentPath === linkPath) {
                    link.classList.add('text-white');
                    if (icon) icon.classList.add('text-red-500');
                    
                    // For sidebar items, also add bg highlight
                    if (link.classList.contains('nav-sidebar')) {
                        link.classList.add('bg-white/[0.04]');
                        link.querySelector('span')?.classList.remove('text-slate-400');
                        link.querySelector('span')?.classList.add('text-white');
                    }

                    // For bottom nav
                    if (link.classList.contains('nav-bottom')) {
                        const span = link.querySelector('span');
                        if (span) {
                            span.classList.remove('text-slate-400');
                            span.classList.add('text-white');
                        }
                    }
                }
            });
        });

        // Lucide Icons
        lucide.createIcons();
    </script>
</body>
</html>
