<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="font-syne text-lg uppercase tracking-tight text-white">User List</h1>
            <p class="text-xs text-slate-500 mt-1"><?= number_format($total); ?> total user</p>
        </div>
    </div>

    <!-- Filter Form -->
    <form method="GET" action="<?= base_url('admin/users'); ?>" class="glass-card p-4 rounded-2xl border border-white/[0.04]">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
            <div class="col-span-2 lg:col-span-1">
                <label class="text-[10px] text-slate-500 uppercase font-semibold block mb-1">Cari</label>
                <input type="text" name="search" value="<?= htmlspecialchars($filter['search'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                       placeholder="Username, email, nama..." class="w-full bg-slate-900/60 text-xs text-slate-300 border border-white/[0.06] rounded-lg px-3 py-2 focus:outline-none focus:border-red-500/50 placeholder-slate-600">
            </div>
            <div>
                <label class="text-[10px] text-slate-500 uppercase font-semibold block mb-1">Status</label>
                <select name="status" class="w-full bg-slate-900/60 text-xs text-slate-300 border border-white/[0.06] rounded-lg px-3 py-2 focus:outline-none focus:border-red-500/50">
                    <option value="">Semua</option>
                    <option value="active" <?= ($filter['status'] ?? '') === 'active' ? 'selected' : ''; ?>>Active</option>
                    <option value="banned" <?= ($filter['status'] ?? '') === 'banned' ? 'selected' : ''; ?>>Banned</option>
                </select>
            </div>
            <div>
                <label class="text-[10px] text-slate-500 uppercase font-semibold block mb-1">Role</label>
                <select name="role" class="w-full bg-slate-900/60 text-xs text-slate-300 border border-white/[0.06] rounded-lg px-3 py-2 focus:outline-none focus:border-red-500/50">
                    <option value="">Semua</option>
                    <option value="admin" <?= ($filter['role'] ?? '') === 'admin' ? 'selected' : ''; ?>>Admin</option>
                    <option value="user" <?= ($filter['role'] ?? '') === 'user' ? 'selected' : ''; ?>>User</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="px-4 py-2 bg-red-500/10 text-red-400 text-[10px] font-semibold rounded-lg border border-red-500/20 hover:bg-red-500/20 transition-all flex-shrink-0">
                    <i data-lucide="search" class="w-3 h-3 inline"></i> Filter
                </button>
                <a href="<?= base_url('admin/users'); ?>" class="px-4 py-2 bg-white/[0.03] text-slate-400 text-[10px] font-semibold rounded-lg border border-white/[0.06] hover:bg-white/[0.06] transition-all flex-shrink-0">
                    Reset
                </a>
            </div>
        </div>
    </form>

    <!-- Table -->
    <div class="glass-card rounded-2xl border border-white/[0.04] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-white/[0.06]">
                        <th class="px-4 py-3 text-[10px] font-syne uppercase tracking-wider text-slate-500">User</th>
                        <th class="px-4 py-3 text-[10px] font-syne uppercase tracking-wider text-slate-500">Email</th>
                        <th class="px-4 py-3 text-[10px] font-syne uppercase tracking-wider text-slate-500">Role</th>
                        <th class="px-4 py-3 text-[10px] font-syne uppercase tracking-wider text-slate-500">Status</th>
                        <th class="px-4 py-3 text-[10px] font-syne uppercase tracking-wider text-slate-500">Login Via</th>
                        <th class="px-4 py-3 text-[10px] font-syne uppercase tracking-wider text-slate-500">Bergabung</th>
                        <th class="px-4 py-3 text-[10px] font-syne uppercase tracking-wider text-slate-500 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/[0.04]">
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-xs text-slate-500">Tidak ada user ditemukan.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($users as $u): ?>
                            <tr class="hover:bg-white/[0.02] transition-colors" id="user-row-<?= $u['id_user']; ?>">
                                <td class="px-4 py-2.5">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-full bg-slate-800 flex items-center justify-center overflow-hidden flex-shrink-0 border border-white/[0.06]">
                                            <?php
                                                $avatar = !empty($u['avatar']) ? $u['avatar'] : 'default.jpg';
                                                $avatar_url = strpos($avatar, 'http') === 0 ? $avatar : assets_url('uploads/profile/' . $avatar);
                                            ?>
                                            <img src="<?= $avatar_url; ?>" alt="" class="w-full h-full object-cover" onerror="this.src='<?= assets_url('default.jpg'); ?>';">
                                        </div>
                                        <div class="min-w-0">
                                            <a href="<?= base_url('user/' . $u['username']); ?>" target="_blank" class="text-xs font-bold text-white hover:text-red-400 transition-colors block truncate">
                                                @<?= htmlspecialchars($u['username'], ENT_QUOTES, 'UTF-8'); ?>
                                            </a>
                                            <?php if (!empty($u['display_name']) && $u['display_name'] !== $u['username']): ?>
                                                <p class="text-[10px] text-slate-500 truncate"><?= htmlspecialchars($u['display_name'], ENT_QUOTES, 'UTF-8'); ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-2.5">
                                    <span class="text-[10px] text-slate-400"><?= htmlspecialchars($u['email'], ENT_QUOTES, 'UTF-8'); ?></span>
                                </td>
                                <td class="px-4 py-2.5">
                                    <span class="px-2 py-0.5 rounded text-[9px] font-semibold
                                        <?= ($u['role'] ?? '') === 'admin' ? 'bg-purple-500/10 text-purple-400 border border-purple-500/20' : 'bg-slate-500/10 text-slate-400 border border-slate-500/20' ?>">
                                        <?= htmlspecialchars($u['role'] ?? 'user', ENT_QUOTES, 'UTF-8'); ?>
                                    </span>
                                </td>
                                <td class="px-4 py-2.5">
                                    <span class="px-2 py-0.5 rounded text-[9px] font-semibold
                                        <?= $u['status'] === 'banned' ? 'bg-red-500/10 text-red-400 border border-red-500/20' : 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' ?>">
                                        <?= $u['status']; ?>
                                    </span>
                                </td>
                                <td class="px-4 py-2.5">
                                    <span class="text-[10px] text-slate-500"><?= htmlspecialchars($u['login_type'] ?? 'regular', ENT_QUOTES, 'UTF-8'); ?></span>
                                </td>
                                <td class="px-4 py-2.5">
                                    <span class="text-[10px] text-slate-500 font-mono whitespace-nowrap"><?= date('d M Y', strtotime($u['created_at'])); ?></span>
                                </td>
                                <td class="px-4 py-2.5 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="<?= base_url('user/' . $u['username']); ?>" target="_blank"
                                           class="p-1.5 rounded-lg bg-white/[0.03] text-slate-400 border border-white/[0.06] hover:bg-white/[0.06] transition-all"
                                           title="Lihat Profil">
                                            <i data-lucide="external-link" class="w-3 h-3"></i>
                                        </a>
                                        <?php if ($u['role'] !== 'admin'): ?>
                                            <?php if ($u['status'] !== 'banned'): ?>
                                            <button onclick="banUser('<?= $u['id_user']; ?>')"
                                                    class="p-1.5 rounded-lg bg-red-500/10 text-red-400 border border-red-500/20 hover:bg-red-500/20 transition-all"
                                                    title="Ban User">
                                                <i data-lucide="ban" class="w-3 h-3"></i>
                                            </button>
                                            <?php else: ?>
                                            <button onclick="unbanUser('<?= $u['id_user']; ?>')"
                                                    class="p-1.5 rounded-lg bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 hover:bg-emerald-500/20 transition-all"
                                                    title="Unban User">
                                                <i data-lucide="shield-check" class="w-3 h-3"></i>
                                            </button>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
    <div class="flex items-center justify-center gap-2">
        <?php if ($current_page > 1): ?>
            <a href="<?= base_url('admin/users?' . http_build_query(array_merge($filter, ['page' => $current_page - 1]))); ?>"
               class="px-3 py-1.5 rounded-lg text-[10px] font-semibold glass-card border border-white/[0.06] text-slate-400 hover:text-white transition-all">
                <i data-lucide="chevron-left" class="w-3 h-3 inline"></i> Prev
            </a>
        <?php endif; ?>
        <span class="text-[10px] text-slate-500 font-mono">Hal <?= $current_page; ?> / <?= $total_pages; ?></span>
        <?php if ($current_page < $total_pages): ?>
            <a href="<?= base_url('admin/users?' . http_build_query(array_merge($filter, ['page' => $current_page + 1]))); ?>"
               class="px-3 py-1.5 rounded-lg text-[10px] font-semibold glass-card border border-white/[0.06] text-slate-400 hover:text-white transition-all">
                Next <i data-lucide="chevron-right" class="w-3 h-3 inline"></i>
            </a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<script>
function banUser(userId) {
    if (!confirm('Yakin ingin ban user ini?')) return;

    const csrfName = document.querySelector('meta[name="csrf-token-name"]').content;
    const csrfHash = document.querySelector('meta[name="csrf-token-hash"]').content;

    const body = new FormData();
    body.append('user_id', userId);
    body.append(csrfName, csrfHash);

    fetch('<?= base_url("admin/ban_user"); ?>', { method: 'POST', body })
        .then(r => r.json())
        .then(data => {
            if (data.status === 'success') {
                showToast(data.message, 'green');
                location.reload();
            }
        });
}

function unbanUser(userId) {
    if (!confirm('Yakin ingin unban user ini?')) return;

    const csrfName = document.querySelector('meta[name="csrf-token-name"]').content;
    const csrfHash = document.querySelector('meta[name="csrf-token-hash"]').content;

    const body = new FormData();
    body.append('user_id', userId);
    body.append(csrfName, csrfHash);

    fetch('<?= base_url("admin/unban_user"); ?>', { method: 'POST', body })
        .then(r => r.json())
        .then(data => {
            if (data.status === 'success') {
                showToast(data.message, 'green');
                location.reload();
            }
        });
}
</script>
