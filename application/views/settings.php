<style>
    .password-toggle { transition: all 0.2s; }
    .req-dot { transition: all 0.3s; }
</style>

<div class="flex-1 max-w-2xl w-full mx-auto px-4 py-6">

    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-white/[0.04]">
        <a href="<?= base_url('profile'); ?>" class="p-2 text-slate-400 hover:text-white hover:bg-white/[0.05] rounded-xl transition-colors">
            <i data-lucide="arrow-left" class="w-5 h-5"></i>
        </a>
        <div>
            <h1 class="font-syne text-sm uppercase tracking-tight text-white">Pengaturan Akun</h1>
            <p class="text-[10px] text-slate-500 mt-0.5">@<?= htmlspecialchars($user['username']) ?></p>
        </div>
    </div>

    <div id="toast-container"></div>

    <!-- SECTION: Keamanan Akun -->
    <div class="glass-card rounded-2xl border border-white/[0.06] p-5 mb-4">
        <div class="flex items-center gap-2.5 mb-5">
            <div class="w-8 h-8 rounded-lg bg-blue-500/10 flex items-center justify-center">
                <i data-lucide="shield" class="w-4 h-4 text-blue-400"></i>
            </div>
            <h2 class="font-syne text-xs uppercase tracking-tight text-white">Keamanan Akun</h2>
        </div>

        <!-- Login Type Info -->
        <div class="flex items-center gap-3 p-3 bg-slate-800/50 rounded-xl border border-white/[0.03] mb-5">
            <i data-lucide="info" class="w-4 h-4 text-slate-500 shrink-0"></i>
            <div class="text-xs text-slate-400">
                <span class="text-slate-300 font-medium">Tipe login:</span>
                <?php if ($user['login_type'] === 'google'): ?>
                    <span class="inline-flex items-center gap-1 ml-1">
                        <svg class="w-3 h-3" viewBox="0 0 24 24"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 01-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
                        Google
                    </span>
                <?php elseif ($user['login_type'] === 'both'): ?>
                    <span class="text-slate-300">Email + Google</span>
                <?php else: ?>
                    <span class="text-slate-300">Email</span>
                <?php endif; ?>
            </div>
        </div>

        <!-- Ubah Password (untuk regular atau both) -->
        <?php if ($user['login_type'] !== 'google'): ?>
        <div class="mb-5">
            <h3 class="text-xs text-slate-300 font-semibold mb-3">Ubah Password</h3>
            <form id="change-password-form" class="space-y-3">
                <div>
                    <label class="text-[10px] text-slate-500 uppercase tracking-wider mb-1 block">Password Saat Ini</label>
                    <div class="relative">
                        <input type="password" name="current_password" required
                               class="w-full bg-slate-800 text-sm text-slate-200 placeholder-slate-500 focus:outline-none border border-white/[0.06] rounded-xl px-4 py-2.5 focus:border-red-500/50 transition-colors pr-10"
                               placeholder="Masukkan password saat ini">
                        <button type="button" onclick="togglePasswordVisibility(this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 hover:text-slate-300 transition-colors">
                            <i data-lucide="eye" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>
                <div>
                    <label class="text-[10px] text-slate-500 uppercase tracking-wider mb-1 block">Password Baru</label>
                    <div class="relative">
                        <input type="password" name="new_password" id="new-password-input" required
                               class="w-full bg-slate-800 text-sm text-slate-200 placeholder-slate-500 focus:outline-none border border-white/[0.06] rounded-xl px-4 py-2.5 focus:border-red-500/50 transition-colors pr-10"
                               placeholder="Masukkan password baru" oninput="checkPasswordStrength(this.value, 'change-pwd-reqs')">
                        <button type="button" onclick="togglePasswordVisibility(this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 hover:text-slate-300 transition-colors">
                            <i data-lucide="eye" class="w-4 h-4"></i>
                        </button>
                    </div>
                    <div id="change-pwd-reqs" class="grid grid-cols-2 gap-x-3 gap-y-1 mt-2">
                        <div class="flex items-center gap-1.5 text-[10px] text-slate-500" data-req="length"><div class="req-dot w-1.5 h-1.5 rounded-full bg-slate-600"></div>Min 8 karakter</div>
                        <div class="flex items-center gap-1.5 text-[10px] text-slate-500" data-req="upper"><div class="req-dot w-1.5 h-1.5 rounded-full bg-slate-600"></div>Huruf besar</div>
                        <div class="flex items-center gap-1.5 text-[10px] text-slate-500" data-req="lower"><div class="req-dot w-1.5 h-1.5 rounded-full bg-slate-600"></div>Huruf kecil</div>
                        <div class="flex items-center gap-1.5 text-[10px] text-slate-500" data-req="number"><div class="req-dot w-1.5 h-1.5 rounded-full bg-slate-600"></div>Angka</div>
                        <div class="flex items-center gap-1.5 text-[10px] text-slate-500" data-req="symbol"><div class="req-dot w-1.5 h-1.5 rounded-full bg-slate-600"></div>Simbol (@$!%*?&)</div>
                    </div>
                </div>
                <div>
                    <label class="text-[10px] text-slate-500 uppercase tracking-wider mb-1 block">Konfirmasi Password</label>
                    <div class="relative">
                        <input type="password" name="confirm_password" required
                               class="w-full bg-slate-800 text-sm text-slate-200 placeholder-slate-500 focus:outline-none border border-white/[0.06] rounded-xl px-4 py-2.5 focus:border-red-500/50 transition-colors pr-10"
                               placeholder="Ulangi password baru">
                        <button type="button" onclick="togglePasswordVisibility(this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 hover:text-slate-300 transition-colors">
                            <i data-lucide="eye" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>
                <div class="flex justify-end pt-1">
                    <button type="submit" class="change-pwd-btn px-5 py-2 text-xs font-semibold text-white bg-red-600 hover:bg-red-500 rounded-xl transition-all shadow-lg shadow-red-600/10 flex items-center gap-2 active:scale-[0.98]">
                        <i data-lucide="key" class="w-3.5 h-3.5"></i>
                        Ubah Password
                    </button>
                </div>
            </form>
        </div>
        <?php endif; ?>

        <!-- Set Password (untuk Google-only users) -->
        <?php if ($user['login_type'] === 'google' && empty($user['password'])): ?>
        <div class="mb-5 p-4 bg-amber-500/5 border border-amber-500/20 rounded-xl">
            <div class="flex items-start gap-2.5 mb-3">
                <i data-lucide="alert-triangle" class="w-4 h-4 text-amber-400 shrink-0 mt-0.5"></i>
                <div>
                    <p class="text-xs text-amber-300 font-semibold">Kamu login hanya dengan Google</p>
                    <p class="text-[11px] text-slate-400 mt-1">Buat password agar bisa login tanpa Google dan bisa memutuskan hubungan Google nanti.</p>
                </div>
            </div>
            <form id="set-password-form" class="space-y-3">
                <div>
                    <label class="text-[10px] text-slate-500 uppercase tracking-wider mb-1 block">Password Baru</label>
                    <div class="relative">
                        <input type="password" name="new_password" id="set-password-input" required
                               class="w-full bg-slate-800 text-sm text-slate-200 placeholder-slate-500 focus:outline-none border border-white/[0.06] rounded-xl px-4 py-2.5 focus:border-amber-500/50 transition-colors pr-10"
                               placeholder="Buat password baru" oninput="checkPasswordStrength(this.value, 'set-pwd-reqs')">
                        <button type="button" onclick="togglePasswordVisibility(this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 hover:text-slate-300 transition-colors">
                            <i data-lucide="eye" class="w-4 h-4"></i>
                        </button>
                    </div>
                    <div id="set-pwd-reqs" class="grid grid-cols-2 gap-x-3 gap-y-1 mt-2">
                        <div class="flex items-center gap-1.5 text-[10px] text-slate-500" data-req="length"><div class="req-dot w-1.5 h-1.5 rounded-full bg-slate-600"></div>Min 8 karakter</div>
                        <div class="flex items-center gap-1.5 text-[10px] text-slate-500" data-req="upper"><div class="req-dot w-1.5 h-1.5 rounded-full bg-slate-600"></div>Huruf besar</div>
                        <div class="flex items-center gap-1.5 text-[10px] text-slate-500" data-req="lower"><div class="req-dot w-1.5 h-1.5 rounded-full bg-slate-600"></div>Huruf kecil</div>
                        <div class="flex items-center gap-1.5 text-[10px] text-slate-500" data-req="number"><div class="req-dot w-1.5 h-1.5 rounded-full bg-slate-600"></div>Angka</div>
                        <div class="flex items-center gap-1.5 text-[10px] text-slate-500" data-req="symbol"><div class="req-dot w-1.5 h-1.5 rounded-full bg-slate-600"></div>Simbol (@$!%*?&)</div>
                    </div>
                </div>
                <div>
                    <label class="text-[10px] text-slate-500 uppercase tracking-wider mb-1 block">Konfirmasi Password</label>
                    <div class="relative">
                        <input type="password" name="confirm_password" required
                               class="w-full bg-slate-800 text-sm text-slate-200 placeholder-slate-500 focus:outline-none border border-white/[0.06] rounded-xl px-4 py-2.5 focus:border-amber-500/50 transition-colors pr-10"
                               placeholder="Ulangi password baru">
                        <button type="button" onclick="togglePasswordVisibility(this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 hover:text-slate-300 transition-colors">
                            <i data-lucide="eye" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>
                <div class="flex justify-end pt-1">
                    <button type="submit" class="px-5 py-2 text-xs font-semibold text-white bg-amber-600 hover:bg-amber-500 rounded-xl transition-all shadow-lg shadow-amber-600/10 flex items-center gap-2 active:scale-[0.98]">
                        <i data-lucide="key" class="w-3.5 h-3.5"></i>
                        Buat Password
                    </button>
                </div>
            </form>
        </div>
        <?php endif; ?>

        <!-- Google Connection -->
        <div>
            <h3 class="text-xs text-slate-300 font-semibold mb-3">Google</h3>
            <div class="flex items-center justify-between p-3 bg-slate-800/50 rounded-xl border border-white/[0.03]">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5" viewBox="0 0 24 24"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 01-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
                    <div>
                        <p class="text-xs text-slate-300 font-medium">
                            <?php if (!empty($user['google_id'])): ?>
                                Terhubung
                            <?php else: ?>
                                Tidak terhubung
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
                <?php if (!empty($user['google_id'])): ?>
                    <?php if (empty($user['password'])): ?>
                        <span class="text-[10px] text-slate-500 italic">Buat password dulu</span>
                    <?php else: ?>
                        <button onclick="unlinkGoogle()" class="text-[11px] font-semibold text-red-400 hover:text-red-300 px-3 py-1.5 rounded-lg bg-red-600/10 hover:bg-red-600/20 border border-red-600/20 transition-colors">
                            Putuskan
                        </button>
                    <?php endif; ?>
                <?php else: ?>
                    <a href="<?= base_url('auth/google_login'); ?>" class="text-[11px] font-semibold text-blue-400 hover:text-blue-300 px-3 py-1.5 rounded-lg bg-blue-600/10 hover:bg-blue-600/20 border border-blue-600/20 transition-colors">
                        Hubungkan
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- SECTION: Email -->
    <div class="glass-card rounded-2xl border border-white/[0.06] p-5 mb-4">
        <div class="flex items-center gap-2.5 mb-5">
            <div class="w-8 h-8 rounded-lg bg-purple-500/10 flex items-center justify-center">
                <i data-lucide="mail" class="w-4 h-4 text-purple-400"></i>
            </div>
            <h2 class="font-syne text-xs uppercase tracking-tight text-white">Email</h2>
        </div>

        <div class="flex items-center gap-3 p-3 bg-slate-800/50 rounded-xl border border-white/[0.03] mb-5">
            <i data-lucide="at-sign" class="w-4 h-4 text-slate-500 shrink-0"></i>
            <div class="text-xs">
                <span class="text-slate-500">Email saat ini:</span>
                <span class="text-slate-300 font-medium ml-1"><?= htmlspecialchars($user['email']) ?></span>
            </div>
        </div>

        <form id="change-email-form" class="space-y-3">
            <div>
                <label class="text-[10px] text-slate-500 uppercase tracking-wider mb-1 block">Email Baru</label>
                <input type="email" name="new_email" required
                       class="w-full bg-slate-800 text-sm text-slate-200 placeholder-slate-500 focus:outline-none border border-white/[0.06] rounded-xl px-4 py-2.5 focus:border-red-500/50 transition-colors"
                       placeholder="email@baru.com">
            </div>
            <?php if ($user['login_type'] !== 'google'): ?>
            <div>
                <label class="text-[10px] text-slate-500 uppercase tracking-wider mb-1 block">Password (untuk konfirmasi)</label>
                <div class="relative">
                    <input type="password" name="password" required
                           class="w-full bg-slate-800 text-sm text-slate-200 placeholder-slate-500 focus:outline-none border border-white/[0.06] rounded-xl px-4 py-2.5 focus:border-red-500/50 transition-colors pr-10"
                           placeholder="Masukkan password saat ini">
                    <button type="button" onclick="togglePasswordVisibility(this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 hover:text-slate-300 transition-colors">
                        <i data-lucide="eye" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>
            <?php endif; ?>
            <div class="flex justify-end pt-1">
                <button type="submit" class="px-5 py-2 text-xs font-semibold text-white bg-red-600 hover:bg-red-500 rounded-xl transition-all shadow-lg shadow-red-600/10 flex items-center gap-2 active:scale-[0.98]">
                    <i data-lucide="save" class="w-3.5 h-3.5"></i>
                    Simpan Email
                </button>
            </div>
        </form>
    </div>

    <!-- SECTION: Zona Bahaya -->
    <div class="glass-card rounded-2xl border border-red-500/20 p-5 mb-4">
        <div class="flex items-center gap-2.5 mb-5">
            <div class="w-8 h-8 rounded-lg bg-red-500/10 flex items-center justify-center">
                <i data-lucide="alert-triangle" class="w-4 h-4 text-red-400"></i>
            </div>
            <h2 class="font-syne text-xs uppercase tracking-tight text-red-400">Zona Bahaya</h2>
        </div>

        <div class="p-4 bg-red-500/5 border border-red-500/10 rounded-xl mb-4">
            <p class="text-xs text-slate-400 leading-relaxed">
                Menghapus akun akan menghapus <span class="text-red-400 font-medium">semua data</span> kamu secara permanen: postingan, komentar, follow, notifikasi, dan lainnya. Tindakan ini <span class="text-red-400 font-medium">tidak dapat dibatalkan</span>.
            </p>
        </div>

        <button onclick="document.getElementById('delete-account-section').classList.toggle('hidden')" class="w-full text-left px-4 py-3 bg-red-600/10 hover:bg-red-600/15 border border-red-600/20 rounded-xl transition-colors group">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <i data-lucide="trash-2" class="w-4 h-4 text-red-400"></i>
                    <span class="text-xs font-semibold text-red-400">Hapus Akun</span>
                </div>
                <i data-lucide="chevron-down" class="w-4 h-4 text-red-400/50 group-hover:text-red-400 transition-colors"></i>
            </div>
        </button>

        <div id="delete-account-section" class="hidden mt-4 space-y-3">
            <div class="p-3 bg-red-500/5 border border-red-500/10 rounded-xl">
                <p class="text-[11px] text-slate-400">Ketik <span class="text-red-400 font-mono font-bold"><?= htmlspecialchars($user['username']) ?></span> untuk konfirmasi:</p>
                <input type="text" id="delete-confirm-username" 
                       class="w-full bg-slate-800 text-sm text-slate-200 placeholder-slate-500 focus:outline-none border border-red-500/20 rounded-lg px-3 py-2 mt-2 focus:border-red-500/50 transition-colors"
                       placeholder="Ketik username kamu">
            </div>
            <?php if ($user['login_type'] !== 'google'): ?>
            <div>
                <label class="text-[10px] text-slate-500 uppercase tracking-wider mb-1 block">Password</label>
                <input type="password" id="delete-confirm-password"
                       class="w-full bg-slate-800 text-sm text-slate-200 placeholder-slate-500 focus:outline-none border border-red-500/20 rounded-xl px-4 py-2.5 focus:border-red-500/50 transition-colors"
                       placeholder="Masukkan password">
            </div>
            <?php endif; ?>
            <button onclick="deleteAccount()" id="delete-account-btn" class="w-full px-4 py-2.5 text-xs font-semibold text-white bg-red-600 hover:bg-red-500 rounded-xl transition-all shadow-lg shadow-red-600/10 flex items-center justify-center gap-2 active:scale-[0.98]">
                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                Hapus Akun Saya
            </button>
        </div>
    </div>

    <!-- Logout -->
    <a href="<?= base_url('auth/logout'); ?>" class="flex items-center justify-center gap-2 w-full px-4 py-3 text-xs font-semibold text-slate-400 hover:text-red-400 bg-white/[0.02] hover:bg-red-600/5 rounded-xl border border-white/[0.04] hover:border-red-600/20 transition-all duration-300 mt-2">
        <i data-lucide="log-out" class="w-4 h-4"></i>
        Logout
    </a>

</div>

<script>
function showToast(message, type) {
    const container = document.getElementById('toast-container');
    const toast = document.createElement('div');
    const bgColor = type === 'success' ? 'bg-emerald-600' : 'bg-red-600';
    toast.className = `fixed bottom-20 left-1/2 -translate-x-1/2 z-[9999] ${bgColor} text-white text-xs font-semibold px-4 py-2.5 rounded-xl shadow-lg`;
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}

function togglePasswordVisibility(btn) {
    const input = btn.parentElement.querySelector('input');
    const icon = btn.querySelector('[data-lucide]');
    if (input.type === 'password') {
        input.type = 'text';
        icon.setAttribute('data-lucide', 'eye-off');
    } else {
        input.type = 'password';
        icon.setAttribute('data-lucide', 'eye');
    }
    lucide.createIcons();
}

function checkPasswordStrength(password, containerId) {
    const container = document.getElementById(containerId);
    const rules = {
        length: password.length >= 8,
        upper: /[A-Z]/.test(password),
        lower: /[a-z]/.test(password),
        number: /[0-9]/.test(password),
        symbol: /[@$!%*?&]/.test(password)
    };

    for (const [key, pass] of Object.entries(rules)) {
        const el = container.querySelector(`[data-req="${key}"]`);
        if (!el) continue;
        const dot = el.querySelector('.req-dot');
        if (pass) {
            el.classList.remove('text-slate-500');
            el.classList.add('text-emerald-400');
            dot.classList.remove('bg-slate-600');
            dot.classList.add('bg-emerald-400');
        } else {
            el.classList.remove('text-emerald-400');
            el.classList.add('text-slate-500');
            dot.classList.remove('bg-emerald-400');
            dot.classList.add('bg-slate-600');
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Change Password
    const changePwdForm = document.getElementById('change-password-form');
    if (changePwdForm) {
        changePwdForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = this.querySelector('button[type="submit"]');
            btn.disabled = true;
            btn.innerHTML = '<div class="w-3.5 h-3.5 border-2 border-white border-t-transparent rounded-full animate-spin"></div> Menyimpan...';

            const formData = new FormData(this);
            fetch('<?= base_url("settings/change_password"); ?>', {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                if (data.status === 'success') {
                    showToast(data.message, 'success');
                    this.reset();
                    checkPasswordStrength('', 'change-pwd-reqs');
                } else {
                    showToast(data.message, 'error');
                }
            })
            .catch(() => showToast('Terjadi kesalahan.', 'error'))
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = '<i data-lucide="key" class="w-3.5 h-3.5"></i> Ubah Password';
                lucide.createIcons();
            });
        });
    }

    // Set Password (Google-only)
    const setPwdForm = document.getElementById('set-password-form');
    if (setPwdForm) {
        setPwdForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = this.querySelector('button[type="submit"]');
            btn.disabled = true;
            btn.innerHTML = '<div class="w-3.5 h-3.5 border-2 border-white border-t-transparent rounded-full animate-spin"></div> Menyimpan...';

            const formData = new FormData(this);
            fetch('<?= base_url("settings/set_password"); ?>', {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                if (data.status === 'success') {
                    showToast(data.message, 'success');
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    showToast(data.message, 'error');
                }
            })
            .catch(() => showToast('Terjadi kesalahan.', 'error'))
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = '<i data-lucide="key" class="w-3.5 h-3.5"></i> Buat Password';
                lucide.createIcons();
            });
        });
    }

    // Change Email
    const changeEmailForm = document.getElementById('change-email-form');
    if (changeEmailForm) {
        changeEmailForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = this.querySelector('button[type="submit"]');
            btn.disabled = true;
            btn.innerHTML = '<div class="w-3.5 h-3.5 border-2 border-white border-t-transparent rounded-full animate-spin"></div> Menyimpan...';

            const formData = new FormData(this);
            fetch('<?= base_url("settings/change_email"); ?>', {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                if (data.status === 'success') {
                    showToast(data.message, 'success');
                    this.reset();
                } else {
                    showToast(data.message, 'error');
                }
            })
            .catch(() => showToast('Terjadi kesalahan.', 'error'))
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = '<i data-lucide="save" class="w-3.5 h-3.5"></i> Simpan Email';
                lucide.createIcons();
            });
        });
    }
});

function unlinkGoogle() {
    showConfirmModal('Yakin ingin memutuskan hubungan dengan Google?', {
        title: 'Putuskan Google',
        danger: true,
        confirmText: 'Ya, Putuskan'
    }).then(confirmed => {
        if (!confirmed) return;

        const formData = new FormData();
        const csrfName = document.querySelector('meta[name="csrf-token-name"]').content;
        const csrfHash = document.querySelector('meta[name="csrf-token-hash"]').content;
        formData.append(csrfName, csrfHash);

        fetch('<?= base_url("settings/unlink_google"); ?>', {
            method: 'POST',
            body: formData
        })
    .then(r => r.json())
    .then(data => {
        if (data.status === 'success') {
            showToast(data.message, 'success');
            setTimeout(() => window.location.reload(), 1500);
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(() => showToast('Terjadi kesalahan.', 'error'));
    });
}

function deleteAccount() {
    const username = document.getElementById('delete-confirm-username').value;
    const passwordEl = document.getElementById('delete-confirm-password');
    const password = passwordEl ? passwordEl.value : '';

    if (!username) {
        showToast('Ketik username kamu untuk konfirmasi.', 'error');
        return;
    }

    const btn = document.getElementById('delete-account-btn');
    btn.disabled = true;
    btn.innerHTML = '<div class="w-3.5 h-3.5 border-2 border-white border-t-transparent rounded-full animate-spin"></div> Menghapus...';

    const formData = new FormData();
    const csrfName = document.querySelector('meta[name="csrf-token-name"]').content;
    const csrfHash = document.querySelector('meta[name="csrf-token-hash"]').content;
    formData.append(csrfName, csrfHash);
    formData.append('confirm_username', username);
    formData.append('password', password);

    fetch('<?= base_url("settings/delete_account"); ?>', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.status === 'success') {
            showToast(data.message, 'success');
            setTimeout(() => window.location.href = '<?= base_url(); ?>', 1500);
        } else {
            showToast(data.message, 'error');
            btn.disabled = false;
            btn.innerHTML = '<i data-lucide="trash-2" class="w-3.5 h-3.5"></i> Hapus Akun Saya';
            lucide.createIcons();
        }
    })
    .catch(() => {
        showToast('Terjadi kesalahan.', 'error');
        btn.disabled = false;
        btn.innerHTML = '<i data-lucide="trash-2" class="w-3.5 h-3.5"></i> Hapus Akun Saya';
        lucide.createIcons();
    });
}
</script>
</main>
