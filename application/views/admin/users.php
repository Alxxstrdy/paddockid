<div class="space-y-6">
    <!-- Header -->
    <div class="flex-row justify-between">
        <div>
            <h1 class="text-page-title">User List</h1>
            <p class="text-caption mt-1"><?= number_format($total); ?> total user</p>
        </div>
    </div>

    <!-- Filter Form -->
    <form method="GET" action="<?= base_url('admin/users'); ?>" class="card p-4 rounded-2xl">
        <div class="grid-2 gap-3" style="grid-template-columns:repeat(4,1fr);">
            <div style="grid-column:span 1;">
                <label class="form-label">Cari</label>
                <input type="text" name="search" value="<?= htmlspecialchars($filter['search'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                       placeholder="Username, email, nama..." class="input input--sm">
            </div>
            <div>
                <label class="form-label">Status</label>
                <select name="status" class="select select--sm">
                    <option value="">Semua</option>
                    <option value="active" <?= ($filter['status'] ?? '') === 'active' ? 'selected' : ''; ?>>Active</option>
                    <option value="banned" <?= ($filter['status'] ?? '') === 'banned' ? 'selected' : ''; ?>>Banned</option>
                </select>
            </div>
            <div>
                <label class="form-label">Role</label>
                <select name="role" class="select select--sm">
                    <option value="">Semua</option>
                    <option value="admin" <?= ($filter['role'] ?? '') === 'admin' ? 'selected' : ''; ?>>Admin</option>
                    <option value="user" <?= ($filter['role'] ?? '') === 'user' ? 'selected' : ''; ?>>User</option>
                </select>
            </div>
            <div class="flex-row gap-2" style="align-items:flex-end;">
                <button type="submit" class="btn btn-xs btn-outline-red flex-shrink-0">
                    <i data-lucide="search" class="w-3 h-3"></i> Filter
                </button>
                <a href="<?= base_url('admin/users'); ?>" class="btn btn-xs btn-secondary flex-shrink-0">
                    Reset
                </a>
            </div>
        </div>
    </form>

    <!-- Table -->
    <div class="card rounded-2xl overflow-hidden">
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Login Via</th>
                        <th>Bergabung</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-caption">Tidak ada user ditemukan.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($users as $u): ?>
                            <tr id="user-row-<?= $u['id_user']; ?>">
                                <td>
                                    <div class="flex-row gap-2-5">
                                        <div class="rounded-full overflow-hidden flex-shrink-0" style="width:32px;height:32px;background:var(--bg-surface-raised);border:1px solid var(--border-default);">
                                            <?php
                                                $avatar = !empty($u['avatar']) ? $u['avatar'] : 'default.jpg';
                                                $avatar_url = strpos($avatar, 'http') === 0 ? $avatar : assets_url('uploads/profile/' . $avatar);
                                            ?>
                                            <img src="<?= $avatar_url; ?>" alt="" class="w-full h-full" style="object-fit:cover;" onerror="this.src='<?= assets_url('default.jpg'); ?>';">
                                        </div>
                                        <div class="min-w-0">
                                            <a href="<?= base_url('user/' . $u['username']); ?>" target="_blank" class="truncate font-bold transition-colors" style="font-size:12px;color:var(--text-primary);display:block;" onmouseover="this.style.color='var(--color-primary)'" onmouseout="this.style.color='var(--text-primary)'">
                                                @<?= htmlspecialchars($u['username'], ENT_QUOTES, 'UTF-8'); ?>
                                            </a>
                                            <?php if (!empty($u['display_name']) && $u['display_name'] !== $u['username']): ?>
                                                <p class="text-caption truncate"><?= htmlspecialchars($u['display_name'], ENT_QUOTES, 'UTF-8'); ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-caption"><?= htmlspecialchars($u['email'], ENT_QUOTES, 'UTF-8'); ?></span>
                                </td>
                                <td>
                                    <span class="badge <?= ($u['role'] ?? '') === 'admin' ? 'badge-purple' : 'badge-muted' ?>">
                                        <?= htmlspecialchars($u['role'] ?? 'user', ENT_QUOTES, 'UTF-8'); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge <?= $u['status'] === 'banned' ? 'badge-danger' : 'badge-success' ?>">
                                        <?= $u['status']; ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="text-caption"><?= htmlspecialchars($u['login_type'] ?? 'regular', ENT_QUOTES, 'UTF-8'); ?></span>
                                </td>
                                <td>
                                    <span class="text-micro whitespace-nowrap"><?= date('d M Y', strtotime($u['created_at'])); ?></span>
                                </td>
                                <td class="text-right">
                                    <div class="flex-row justify-end gap-1-5">
                                        <a href="<?= base_url('user/' . $u['username']); ?>" target="_blank"
                                           class="btn-icon-sm btn-secondary"
                                           title="Lihat Profil">
                                            <i data-lucide="external-link" class="w-3 h-3"></i>
                                        </a>
                                        <?php if ($u['role'] !== 'admin'): ?>
                                            <?php if ($u['status'] !== 'banned'): ?>
                                            <button onclick="banUser('<?= $u['id_user']; ?>')"
                                                    class="btn-icon-sm btn-outline-red"
                                                    title="Ban User">
                                                <i data-lucide="ban" class="w-3 h-3"></i>
                                            </button>
                                            <?php else: ?>
                                            <button onclick="unbanUser('<?= $u['id_user']; ?>')"
                                                    class="btn-icon-sm btn-outline-red"
                                                    title="Unban User" style="color:var(--color-success);border-color:var(--color-success-border);">
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
    <div class="pagination">
        <?php if ($current_page > 1): ?>
            <a href="<?= base_url('admin/users?' . http_build_query(array_merge($filter, ['page' => $current_page - 1]))); ?>"
               class="pagination-btn">
                <i data-lucide="chevron-left" class="w-3 h-3"></i> Prev
            </a>
        <?php endif; ?>
        <span class="pagination-info">Hal <?= $current_page; ?> / <?= $total_pages; ?></span>
        <?php if ($current_page < $total_pages): ?>
            <a href="<?= base_url('admin/users?' . http_build_query(array_merge($filter, ['page' => $current_page + 1]))); ?>"
               class="pagination-btn">
                Next <i data-lucide="chevron-right" class="w-3 h-3"></i>
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
