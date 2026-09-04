<style>
    .password-toggle { transition: all 0.2s; }
    .req-dot { transition: all 0.3s; }
</style>

<div class="flex-1 max-w-2xl w-full mx-auto px-4 py-6">

    <div class="flex-row items-center gap-3 mb-6 pb-4" style="border-bottom:1px solid rgba(255,255,255,0.04)">
        <a href="<?= base_url('profile'); ?>" class="p-2 rounded-xl transition-colors" style="transition:background-color 0.2s" onmouseover="this.style.background='rgba(255,255,255,0.05)';this.style.color='var(--text-primary)'" onmouseout="this.style.background='';this.style.color='var(--text-muted)'">
            <i data-lucide="arrow-left" style="width:20px;height:20px"></i>
        </a>
        <div>
            <h1 class="text-heading text-small uppercase" style="letter-spacing:-0.025em;color:var(--text-primary)">Pengaturan Akun</h1>
            <p class="text-micro c-subtle" style="margin-top:2px">@<?= htmlspecialchars($user['username']) ?></p>
        </div>
    </div>

    <div id="toast-container"></div>

    <!-- SECTION: Keamanan Akun -->
    <div class="card rounded-2xl p-5 mb-4" style="border:1px solid var(--border-default)">
        <div class="flex-row items-center gap-2.5 mb-5">
            <div class="rounded-lg flex items-center justify-center" style="width:32px;height:32px;background:rgba(59,130,246,0.1)">
                <i data-lucide="shield" class="c-info" style="width:16px;height:16px"></i>
            </div>
            <h2 class="text-heading text-small uppercase" style="letter-spacing:-0.025em;color:var(--text-primary)">Keamanan Akun</h2>
        </div>

        <!-- Login Type Info -->
        <div class="flex-row items-center gap-3 p-3 rounded-xl" style="background:rgba(0,0,0,0.3);border:1px solid rgba(255,255,255,0.03);margin-bottom:20px">
            <i data-lucide="info" class="c-subtle shrink-0" style="width:16px;height:16px"></i>
            <div class="text-small c-muted">
                <span class="c-secondary font-medium">Tipe login:</span>
                <?php if ($user['login_type'] === 'google'): ?>
                    <span class="inline-flex items-center gap-1" style="margin-left:4px">
                        <svg style="width:12px;height:12px" viewBox="0 0 24 24"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 01-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
                        Google
                    </span>
                <?php elseif ($user['login_type'] === 'both'): ?>
                    <span class="c-secondary">Email + Google</span>
                <?php else: ?>
                    <span class="c-secondary">Email</span>
                <?php endif; ?>
            </div>
        </div>

        <!-- Ubah Password (untuk regular atau both) -->
        <?php if ($user['login_type'] !== 'google'): ?>
        <div class="mb-5">
            <h3 class="text-small c-secondary font-semibold mb-3">Ubah Password</h3>
            <form id="change-password-form" class="space-y-3">
                <div>
                    <label class="text-micro c-subtle block" style="text-transform:uppercase;letter-spacing:0.05em;margin-bottom:4px">Password Saat Ini</label>
                    <div class="relative">
                        <input type="password" name="current_password" required
                               class="input w-full"
                               style="background:var(--bg-surface-raised);font-size:14px;color:var(--text-secondary);border:1px solid var(--border-default);border-radius:0.75rem;padding:0 1rem;padding-right:2.5rem;padding-top:0.625rem;padding-bottom:0.625rem;transition:border-color 0.2s"
                               placeholder="Masukkan password saat ini">
                        <button type="button" onclick="togglePasswordVisibility(this)" class="absolute c-subtle" style="right:12px;top:50%;transform:translateY(-50%);transition:color 0.2s" onmouseover="this.style.color='var(--text-secondary)'" onmouseout="this.style.color=''">
                            <i data-lucide="eye" style="width:16px;height:16px"></i>
                        </button>
                    </div>
                </div>
                <div>
                    <label class="text-micro c-subtle block" style="text-transform:uppercase;letter-spacing:0.05em;margin-bottom:4px">Password Baru</label>
                    <div class="relative">
                        <input type="password" name="new_password" id="new-password-input" required
                               class="input w-full"
                               style="background:var(--bg-surface-raised);font-size:14px;color:var(--text-secondary);border:1px solid var(--border-default);border-radius:0.75rem;padding:0 1rem;padding-right:2.5rem;padding-top:0.625rem;padding-bottom:0.625rem;transition:border-color 0.2s"
                               placeholder="Masukkan password baru" oninput="checkPasswordStrength(this.value, 'change-pwd-reqs')">
                        <button type="button" onclick="togglePasswordVisibility(this)" class="absolute c-subtle" style="right:12px;top:50%;transform:translateY(-50%);transition:color 0.2s" onmouseover="this.style.color='var(--text-secondary)'" onmouseout="this.style.color=''">
                            <i data-lucide="eye" style="width:16px;height:16px"></i>
                        </button>
                    </div>
                    <div id="change-pwd-reqs" class="grid grid-cols-2 gap-x-3 gap-y-1 mt-2">
                        <div class="flex items-center gap-1.5 text-micro c-subtle" data-req="length"><div class="req-dot" style="width:6px;height:6px;border-radius:9999px;background:var(--color-danger)"></div>Min 8 karakter</div>
                        <div class="flex items-center gap-1.5 text-micro c-subtle" data-req="upper"><div class="req-dot" style="width:6px;height:6px;border-radius:9999px;background:var(--color-danger)"></div>Huruf besar</div>
                        <div class="flex items-center gap-1.5 text-micro c-subtle" data-req="lower"><div class="req-dot" style="width:6px;height:6px;border-radius:9999px;background:var(--color-danger)"></div>Huruf kecil</div>
                        <div class="flex items-center gap-1.5 text-micro c-subtle" data-req="number"><div class="req-dot" style="width:6px;height:6px;border-radius:9999px;background:var(--color-danger)"></div>Angka</div>
                        <div class="flex items-center gap-1.5 text-micro c-subtle" data-req="symbol"><div class="req-dot" style="width:6px;height:6px;border-radius:9999px;background:var(--color-danger)"></div>Simbol (@$!%*?&)</div>
                    </div>
                </div>
                <div>
                    <label class="text-micro c-subtle block" style="text-transform:uppercase;letter-spacing:0.05em;margin-bottom:4px">Konfirmasi Password</label>
                    <div class="relative">
                        <input type="password" name="confirm_password" required
                               class="input w-full"
                               style="background:var(--bg-surface-raised);font-size:14px;color:var(--text-secondary);border:1px solid var(--border-default);border-radius:0.75rem;padding:0 1rem;padding-right:2.5rem;padding-top:0.625rem;padding-bottom:0.625rem;transition:border-color 0.2s"
                               placeholder="Ulangi password baru">
                        <button type="button" onclick="togglePasswordVisibility(this)" class="absolute c-subtle" style="right:12px;top:50%;transform:translateY(-50%);transition:color 0.2s" onmouseover="this.style.color='var(--text-secondary)'" onmouseout="this.style.color=''">
                            <i data-lucide="eye" style="width:16px;height:16px"></i>
                        </button>
                    </div>
                </div>
                <div class="flex justify-end pt-1">
                    <button type="submit" class="change-pwd-btn btn-primary flex items-center gap-2" style="padding:0.5rem 1.25rem;font-size:12px;transition:all 0.2s" onmouseover="this.style.transform='scale(0.98)'" onmouseout="this.style.transform=''">
                        <i data-lucide="key" style="width:14px;height:14px"></i>
                        Ubah Password
                    </button>
                </div>
            </form>
        </div>
        <?php endif; ?>

        <!-- Set Password (untuk Google-only users) -->
        <?php if ($user['login_type'] === 'google' && empty($user['password'])): ?>
        <div class="mb-5 p-4 rounded-xl" style="background:rgba(245,158,11,0.05);border:1px solid rgba(245,158,11,0.2)">
            <div class="flex-row items-start gap-2.5 mb-3">
                <i data-lucide="alert-triangle" class="c-warning shrink-0" style="width:16px;height:16px;margin-top:2px"></i>
                <div>
                    <p class="text-small c-warning font-semibold">Kamu login hanya dengan Google</p>
                    <p class="text-caption c-muted" style="margin-top:4px">Buat password agar bisa login tanpa Google dan bisa memutuskan hubungan Google nanti.</p>
                </div>
            </div>
            <form id="set-password-form" class="space-y-3">
                <div>
                    <label class="text-micro c-subtle block" style="text-transform:uppercase;letter-spacing:0.05em;margin-bottom:4px">Password Baru</label>
                    <div class="relative">
                        <input type="password" name="new_password" id="set-password-input" required
                               class="input w-full"
                               style="background:var(--bg-surface-raised);font-size:14px;color:var(--text-secondary);border:1px solid var(--border-default);border-radius:0.75rem;padding:0 1rem;padding-right:2.5rem;padding-top:0.625rem;padding-bottom:0.625rem;transition:border-color 0.2s"
                               placeholder="Buat password baru" oninput="checkPasswordStrength(this.value, 'set-pwd-reqs')">
                        <button type="button" onclick="togglePasswordVisibility(this)" class="absolute c-subtle" style="right:12px;top:50%;transform:translateY(-50%);transition:color 0.2s" onmouseover="this.style.color='var(--text-secondary)'" onmouseout="this.style.color=''">
                            <i data-lucide="eye" style="width:16px;height:16px"></i>
                        </button>
                    </div>
                    <div id="set-pwd-reqs" class="grid grid-cols-2 gap-x-3 gap-y-1 mt-2">
                        <div class="flex items-center gap-1.5 text-micro c-subtle" data-req="length"><div class="req-dot" style="width:6px;height:6px;border-radius:9999px;background:var(--color-danger)"></div>Min 8 karakter</div>
                        <div class="flex items-center gap-1.5 text-micro c-subtle" data-req="upper"><div class="req-dot" style="width:6px;height:6px;border-radius:9999px;background:var(--color-danger)"></div>Huruf besar</div>
                        <div class="flex items-center gap-1.5 text-micro c-subtle" data-req="lower"><div class="req-dot" style="width:6px;height:6px;border-radius:9999px;background:var(--color-danger)"></div>Huruf kecil</div>
                        <div class="flex items-center gap-1.5 text-micro c-subtle" data-req="number"><div class="req-dot" style="width:6px;height:6px;border-radius:9999px;background:var(--color-danger)"></div>Angka</div>
                        <div class="flex items-center gap-1.5 text-micro c-subtle" data-req="symbol"><div class="req-dot" style="width:6px;height:6px;border-radius:9999px;background:var(--color-danger)"></div>Simbol (@$!%*?&)</div>
                    </div>
                </div>
                <div>
                    <label class="text-micro c-subtle block" style="text-transform:uppercase;letter-spacing:0.05em;margin-bottom:4px">Konfirmasi Password</label>
                    <div class="relative">
                        <input type="password" name="confirm_password" required
                               class="input w-full"
                               style="background:var(--bg-surface-raised);font-size:14px;color:var(--text-secondary);border:1px solid var(--border-default);border-radius:0.75rem;padding:0 1rem;padding-right:2.5rem;padding-top:0.625rem;padding-bottom:0.625rem;transition:border-color 0.2s"
                               placeholder="Ulangi password baru">
                        <button type="button" onclick="togglePasswordVisibility(this)" class="absolute c-subtle" style="right:12px;top:50%;transform:translateY(-50%);transition:color 0.2s" onmouseover="this.style.color='var(--text-secondary)'" onmouseout="this.style.color=''">
                            <i data-lucide="eye" style="width:16px;height:16px"></i>
                        </button>
                    </div>
                </div>
                <div class="flex justify-end pt-1">
                    <button type="submit" class="btn flex items-center gap-2" style="padding:0.5rem 1.25rem;font-size:12px;font-weight:600;color:var(--text-primary);background:var(--color-warning);border-radius:0.75rem;transition:all 0.2s;transition-property:background-color" onmouseover="this.style.background='var(--color-warning)';this.style.transform='scale(0.98)'" onmouseout="this.style.transform=''">
                        <i data-lucide="key" style="width:14px;height:14px"></i>
                        Buat Password
                    </button>
                </div>
            </form>
        </div>
        <?php endif; ?>

        <!-- Google Connection -->
        <div>
            <h3 class="text-small c-secondary font-semibold mb-3">Google</h3>
            <div class="flex-row items-center justify-between p-3 rounded-xl" style="background:rgba(0,0,0,0.3);border:1px solid rgba(255,255,255,0.03)">
                <div class="flex-row items-center gap-3">
                    <svg style="width:20px;height:20px" viewBox="0 0 24 24"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 01-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
                    <div>
                        <p class="text-small c-secondary font-medium">
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
                        <span class="text-micro c-subtle" style="font-style:italic">Buat password dulu</span>
                    <?php else: ?>
                        <button onclick="unlinkGoogle()" class="c-primary" style="font-size:11px;font-weight:600;padding:6px 12px;border-radius:0.5rem;background:rgba(239,68,68,0.1);transition:background-color 0.2s;border:1px solid rgba(239,68,68,0.2)" onmouseover="this.style.background='rgba(239,68,68,0.2)'" onmouseout="this.style.background='rgba(239,68,68,0.1)'">
                            Putuskan
                        </button>
                    <?php endif; ?>
                <?php else: ?>
                    <a href="<?= base_url('auth/google_login'); ?>" class="c-info" style="font-size:11px;font-weight:600;padding:6px 12px;border-radius:0.5rem;background:rgba(59,130,246,0.1);transition:background-color 0.2s;border:1px solid rgba(59,130,246,0.2)" onmouseover="this.style.background='rgba(59,130,246,0.2)'" onmouseout="this.style.background='rgba(59,130,246,0.1)'">
                        Hubungkan
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- SECTION: Email -->
    <div class="card rounded-2xl p-5 mb-4" style="border:1px solid var(--border-default)">
        <div class="flex-row items-center gap-2.5 mb-5">
            <div class="rounded-lg flex items-center justify-center" style="width:32px;height:32px;background:rgba(139,92,246,0.1)">
                <i data-lucide="mail" class="c-purple" style="width:16px;height:16px"></i>
            </div>
            <h2 class="text-heading text-small uppercase" style="letter-spacing:-0.025em;color:var(--text-primary)">Email</h2>
        </div>

        <div class="flex-row items-center gap-3 p-3 rounded-xl" style="background:rgba(0,0,0,0.3);border:1px solid rgba(255,255,255,0.03);margin-bottom:20px">
            <i data-lucide="at-sign" class="c-subtle shrink-0" style="width:16px;height:16px"></i>
            <div class="text-small">
                <span class="c-subtle">Email saat ini:</span>
                <span class="c-secondary font-medium" style="margin-left:4px"><?= htmlspecialchars($user['email']) ?></span>
            </div>
        </div>

        <form id="change-email-form" class="space-y-3">
            <div>
                <label class="text-micro c-subtle block" style="text-transform:uppercase;letter-spacing:0.05em;margin-bottom:4px">Email Baru</label>
                <input type="email" name="new_email" required
                       class="input w-full"
                       style="background:var(--bg-surface-raised);font-size:14px;color:var(--text-secondary);border:1px solid var(--border-default);border-radius:0.75rem;padding:0 1rem;padding-top:0.625rem;padding-bottom:0.625rem;transition:border-color 0.2s"
                       placeholder="email@baru.com">
            </div>
            <?php if ($user['login_type'] !== 'google'): ?>
            <div>
                <label class="text-micro c-subtle block" style="text-transform:uppercase;letter-spacing:0.05em;margin-bottom:4px">Password (untuk konfirmasi)</label>
                <div class="relative">
                    <input type="password" name="password" required
                           class="input w-full"
                           style="background:var(--bg-surface-raised);font-size:14px;color:var(--text-secondary);border:1px solid var(--border-default);border-radius:0.75rem;padding:0 1rem;padding-right:2.5rem;padding-top:0.625rem;padding-bottom:0.625rem;transition:border-color 0.2s"
                           placeholder="Masukkan password saat ini">
                    <button type="button" onclick="togglePasswordVisibility(this)" class="absolute c-subtle" style="right:12px;top:50%;transform:translateY(-50%);transition:color 0.2s" onmouseover="this.style.color='var(--text-secondary)'" onmouseout="this.style.color=''">
                        <i data-lucide="eye" style="width:16px;height:16px"></i>
                    </button>
                </div>
            </div>
            <?php endif; ?>
            <div class="flex justify-end pt-1">
                <button type="submit" class="btn-primary flex items-center gap-2" style="padding:0.5rem 1.25rem;font-size:12px;transition:all 0.2s" onmouseover="this.style.transform='scale(0.98)'" onmouseout="this.style.transform=''">
                    <i data-lucide="save" style="width:14px;height:14px"></i>
                    Simpan Email
                </button>
            </div>
        </form>
    </div>

    <!-- SECTION: Zona Bahaya -->
    <div class="card rounded-2xl p-5 mb-4" style="border:1px solid rgba(239,68,68,0.2)">
        <div class="flex-row items-center gap-2.5 mb-5">
            <div class="rounded-lg flex items-center justify-center" style="width:32px;height:32px;background:rgba(239,68,68,0.1)">
                <i data-lucide="alert-triangle" class="c-primary" style="width:16px;height:16px"></i>
            </div>
            <h2 class="text-heading text-small uppercase c-primary" style="letter-spacing:-0.025em">Zona Bahaya</h2>
        </div>

        <div class="p-4 rounded-xl mb-4" style="background:rgba(239,68,68,0.05);border:1px solid rgba(239,68,68,0.1)">
            <p class="text-small c-muted leading-relaxed">
                Menghapus akun akan menghapus <span class="c-primary font-medium">semua data</span> kamu secara permanen: postingan, komentar, follow, notifikasi, dan lainnya. Tindakan ini <span class="c-primary font-medium">tidak dapat dibatalkan</span>.
            </p>
        </div>

        <button onclick="document.getElementById('delete-account-section').classList.toggle('hidden')" class="w-full text-left px-4 py-3 rounded-xl transition-colors group" style="background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.2);transition:background-color 0.2s" onmouseover="this.style.background='rgba(239,68,68,0.15)'" onmouseout="this.style.background='rgba(239,68,68,0.1)'">
            <div class="flex-row items-center justify-between">
                <div class="flex-row items-center gap-2.5">
                    <i data-lucide="trash-2" class="c-primary" style="width:16px;height:16px"></i>
                    <span class="text-small c-primary font-semibold">Hapus Akun</span>
                </div>
                <i data-lucide="chevron-down" style="width:16px;height:16px;color:rgba(239,68,68,0.5);transition:color 0.2s" onmouseover="this.style.color='var(--color-primary)'" onmouseout="this.style.color='rgba(239,68,68,0.5)'"></i>
            </div>
        </button>

        <div id="delete-account-section" class="hidden mt-4 space-y-3">
            <div class="p-3 rounded-xl" style="background:rgba(239,68,68,0.05);border:1px solid rgba(239,68,68,0.1)">
                <p class="text-caption c-muted">Ketik <span class="c-primary font-mono font-bold"><?= htmlspecialchars($user['username']) ?></span> untuk konfirmasi:</p>
                <input type="text" id="delete-confirm-username"
                       class="input w-full"
                       style="background:var(--bg-surface-raised);font-size:14px;color:var(--text-secondary);border:1px solid rgba(239,68,68,0.2);border-radius:0.5rem;padding:8px 12px;margin-top:8px;transition:border-color 0.2s"
                       placeholder="Ketik username kamu">
            </div>
            <?php if ($user['login_type'] !== 'google'): ?>
            <div>
                <label class="text-micro c-subtle block" style="text-transform:uppercase;letter-spacing:0.05em;margin-bottom:4px">Password</label>
                <input type="password" id="delete-confirm-password"
                       class="input w-full"
                       style="background:var(--bg-surface-raised);font-size:14px;color:var(--text-secondary);border:1px solid rgba(239,68,68,0.2);border-radius:0.75rem;padding:0 1rem;padding-top:0.625rem;padding-bottom:0.625rem;transition:border-color 0.2s"
                       placeholder="Masukkan password">
            </div>
            <?php endif; ?>
            <button onclick="deleteAccount()" id="delete-account-btn" class="btn-primary w-full flex items-center justify-center gap-2" style="padding:10px;font-size:12px;transition:all 0.2s" onmouseover="this.style.transform='scale(0.98)'" onmouseout="this.style.transform=''">
                <i data-lucide="trash-2" style="width:14px;height:14px"></i>
                Hapus Akun Saya
            </button>
        </div>
    </div>

    <!-- SECTION: Preferensi -->
    <div class="card rounded-2xl p-5 mb-4" style="border:1px solid var(--border-default)">
        <div class="flex-row items-center gap-2.5 mb-5">
            <div class="rounded-lg flex items-center justify-center" style="width:32px;height:32px;background:rgba(139,92,246,0.1)">
                <i data-lucide="cookie" class="c-purple" style="width:16px;height:16px"></i>
            </div>
            <h2 class="text-heading text-small uppercase" style="letter-spacing:-0.025em;color:var(--text-primary)">Preferensi & Cookie</h2>
        </div>

        <!-- Tampilan (Tema) -->
        <div class="mb-5">
            <h3 class="text-small c-secondary font-semibold mb-3">Tampilan</h3>
            <div class="flex gap-2">
                <button data-pref="theme" data-value="dark" class="pref-btn flex-1 px-4 py-2.5 text-small font-semibold rounded-xl transition-all" style="border:1px solid var(--border-default)">
                    <span class="flex items-center justify-center gap-2"><i data-lucide="moon" style="width:14px;height:14px"></i> Gelap</span>
                </button>
                <button data-pref="theme" data-value="light" class="pref-btn flex-1 px-4 py-2.5 text-small font-semibold rounded-xl transition-all" style="border:1px solid var(--border-default)">
                    <span class="flex items-center justify-center gap-2"><i data-lucide="sun" style="width:14px;height:14px"></i> Terang</span>
                </button>
            </div>
            <p class="text-micro c-subtle mt-2">Pilih tampilan yang kamu sukai.</p>
        </div>

        <!-- Bahasa -->
        <div class="mb-5">
            <h3 class="text-small c-secondary font-semibold mb-3">Bahasa</h3>
            <div class="flex gap-2">
                <button data-pref="lang" data-value="id" class="pref-btn flex-1 px-4 py-2.5 text-small font-semibold rounded-xl transition-all" style="border:1px solid var(--border-default)">Indonesia</button>
                <button data-pref="lang" data-value="en" class="pref-btn flex-1 px-4 py-2.5 text-small font-semibold rounded-xl transition-all" style="border:1px solid var(--border-default)">English</button>
            </div>
            <p class="text-micro c-subtle mt-2">Bahasa antarmuka aplikasi.</p>
        </div>

        <!-- Notifikasi -->
        <div class="mb-5">
            <h3 class="text-small c-secondary font-semibold mb-3">Notifikasi</h3>
            <div class="flex gap-2">
                <button data-pref="notif_sound" data-value="on" class="pref-btn flex-1 px-4 py-2.5 text-small font-semibold rounded-xl transition-all" style="border:1px solid var(--border-default)">Suara Nyala</button>
                <button data-pref="notif_sound" data-value="off" class="pref-btn flex-1 px-4 py-2.5 text-small font-semibold rounded-xl transition-all" style="border:1px solid var(--border-default)">Suara Mati</button>
            </div>
        </div>

        <!-- Izin Cookie -->
        <div class="pt-4" style="border-top:1px solid var(--border-default)">
            <div class="flex-row items-center justify-between gap-3">
                <div>
                    <h3 class="text-small c-secondary font-semibold">Izin Cookie & Iklan</h3>
                    <p class="text-micro c-subtle" style="margin-top:2px">Kelola izin cookie dan iklan pihak ketiga.</p>
                </div>
                <div class="flex gap-2 flex-shrink-0">
                    <button onclick="saveConsentFromSettings('essential_only')" class="px-3 py-2 text-micro font-semibold c-secondary rounded-lg transition-colors" style="background:rgba(255,255,255,0.05);border:1px solid var(--border-default);transition:background-color 0.2s" onmouseover="this.style.background='rgba(255,255,255,0.1)'" onmouseout="this.style.background='rgba(255,255,255,0.05)'">Hanya Penting</button>
                    <button onclick="saveConsentFromSettings('accept_all')" class="btn-primary px-3 py-2 text-micro font-semibold rounded-lg transition-colors">Terima Semua</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Logout -->
    <a href="<?= base_url('auth/logout'); ?>" class="flex-row items-center justify-center gap-2 w-full px-4 py-3 text-small font-semibold c-muted rounded-xl transition-all duration-300 mt-2" style="background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.04);transition-property:color,background-color,border-color" onmouseover="this.style.color='var(--color-primary)';this.style.background='rgba(239,68,68,0.05)';this.style.borderColor='rgba(239,68,68,0.2)'" onmouseout="this.style.color='';this.style.background='';this.style.borderColor=''">
        <i data-lucide="log-out" style="width:16px;height:16px"></i>
        Logout
    </a>

</div>

<script>
function showToast(message, type) {
    const container = document.getElementById('toast-container');
    const toast = document.createElement('div');
    const bgColor = type === 'success' ? 'var(--color-success)' : 'var(--color-danger)';
    toast.className = 'fixed z-50';
    toast.style.cssText = `bottom:5rem;left:50%;transform:translateX(-50%);z-index:9999;background:${bgColor};color:white;font-size:12px;font-weight:600;padding:10px 1rem;border-radius:0.75rem;box-shadow:0 10px 15px -3px rgba(0,0,0,0.3)`;
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
            el.style.color = 'var(--color-success)';
            dot.style.background = 'var(--color-success)';
        } else {
            el.style.color = '';
            dot.style.background = '';
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
            btn.innerHTML = '<div class="spinner" style="width:14px;height:14px;border-width:2px;border-color:white;border-top-color:transparent"></div> Menyimpan...';

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
                btn.innerHTML = '<i data-lucide="key" style="width:14px;height:14px"></i> Ubah Password';
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
            btn.innerHTML = '<div class="spinner" style="width:14px;height:14px;border-width:2px;border-color:white;border-top-color:transparent"></div> Menyimpan...';

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
                btn.innerHTML = '<i data-lucide="key" style="width:14px;height:14px"></i> Buat Password';
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
            btn.innerHTML = '<div class="spinner" style="width:14px;height:14px;border-width:2px;border-color:white;border-top-color:transparent"></div> Menyimpan...';

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
                btn.innerHTML = '<i data-lucide="save" style="width:14px;height:14px"></i> Simpan Email';
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
    btn.innerHTML = '<div class="spinner" style="width:14px;height:14px;border-width:2px;border-color:white;border-top-color:transparent"></div> Menghapus...';

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
            btn.innerHTML = '<i data-lucide="trash-2" style="width:14px;height:14px"></i> Hapus Akun Saya';
            lucide.createIcons();
        }
    })
    .catch(() => {
        showToast('Terjadi kesalahan.', 'error');
        btn.disabled = false;
        btn.innerHTML = '<i data-lucide="trash-2" style="width:14px;height:14px"></i> Hapus Akun Saya';
        lucide.createIcons();
    });
}

// === PREFFERENSI & COOKIE PREFERENCES ===
function getSettingsCsrf() {
    const name = document.querySelector('meta[name="csrf-token-name"]').content;
    const hash = document.querySelector('meta[name="csrf-token-hash"]').content;
    return name + '=' + encodeURIComponent(hash);
}

const CURRENT_PREFS = {
    theme: <?= json_encode(get_pref_cookie('theme', 'dark')); ?>,
    lang: <?= json_encode(get_pref_cookie('lang', 'id')); ?>,
    notif_sound: <?= json_encode(get_pref_cookie('notif_sound', 'on')); ?>
};

function prefBtnClass(active) {
    return active
        ? 'pref-btn flex-1 px-4 py-2.5 text-xs font-semibold rounded-xl transition-all'
        : 'pref-btn flex-1 px-4 py-2.5 text-xs font-semibold rounded-xl transition-all';
}

function prefBtnStyle(active) {
    return active
        ? 'border:1px solid rgba(239,68,68,0.4);background:rgba(239,68,68,0.1);color:var(--text-secondary)'
        : 'border:1px solid var(--border-default);background:rgba(255,255,255,0.03);color:var(--text-muted);transition:background-color 0.2s';
}

function renderPrefButtons() {
    document.querySelectorAll('.pref-btn').forEach(btn => {
        const key = btn.getAttribute('data-pref');
        const val = btn.getAttribute('data-value');
        const active = (CURRENT_PREFS[key] === val);
        btn.className = prefBtnClass(active);
        btn.style.cssText = prefBtnStyle(active);
    });
    if (typeof lucide !== 'undefined') lucide.createIcons();
}

function saveConsentFromSettings(action) {
    const body = getSettingsCsrf() + '&action=' + encodeURIComponent(action);
    fetch('<?= base_url("consent/save"); ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: body
    })
    .then(r => r.json())
    .then(data => {
        showToast(data.status === 'success' ? 'Preferensi cookie disimpan.' : data.message, data.status === 'success' ? 'success' : 'error');
        if (action === 'accept_all' && typeof loadAdsAfterConsent === 'function') loadAdsAfterConsent();
    })
    .catch(() => showToast('Terjadi kesalahan.', 'error'));
}

document.querySelectorAll('.pref-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const key = this.getAttribute('data-pref');
        const val = this.getAttribute('data-value');
        CURRENT_PREFS[key] = val;

        const body = getSettingsCsrf() + '&key=' + encodeURIComponent(key) + '&value=' + encodeURIComponent(val);
        fetch('<?= base_url("consent/set_preference"); ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: body
        })
        .then(r => r.json())
        .then(data => {
            if (data.status === 'success') {
                showToast('Preferensi disimpan.', 'success');
                if (key === 'theme') {
                    document.body.classList.toggle('light', val === 'light');
                }
                renderPrefButtons();
            } else {
                showToast(data.message, 'error');
            }
        })
        .catch(() => showToast('Terjadi kesalahan.', 'error'));
    });
});

renderPrefButtons();

</script>
</main>