<div class="flex-row gap-3 mb-4 pb-4 border-b">
    <a href="<?= base_url('post/' . $post['username'] . '/' . $post['id_post']); ?>" class="btn-icon-sm btn-ghost c-muted transition-colors" style="border-radius: var(--radius-lg);">
        <i data-lucide="arrow-left" class="text-sm"></i>
    </a>
    <h2 class="text-section-title">Edit Postingan</h2>
</div>

<div class="card rounded-2xl p-5">
    <form id="edit-post-form">
        <input type="hidden" id="edit-post-id" value="<?= $post['id_post']; ?>">

        <div class="flex-row gap-3 mb-4 pb-4 border-b">
            <div class="avatar" style="width: 36px; height: 36px;">
                <div class="avatar-ring">
                    <img src="<?= $post['avatar']; ?>" alt="User">
                </div>
                <?php if (!empty($post['border'])): ?>
                    <div class="avatar-border">
                        <img src="<?= $post['border']; ?>" alt="Border">
                    </div>
                <?php endif; ?>
            </div>
            <div class="flex-col">
                <span class="text-small font-semibold c-white"><?= htmlspecialchars($post['username']); ?></span>
                <span class="text-micro c-subtle"><?= $post['created_at']; ?></span>
            </div>
        </div>

        <textarea
            id="edit-post-content"
            rows="6"
            class="textarea text-sm mb-4"
            required
        ><?= htmlspecialchars($post['content'], ENT_QUOTES, 'UTF-8'); ?></textarea>

        <div class="flex-row gap-3 mb-4">
            <select id="edit-post-category" class="select select--sm" style="width: auto;">
                <option value="">Tanpa Kategori</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id_category']; ?>" <?= $cat['id_category'] == $post['post_category'] ? 'selected' : ''; ?>><?= htmlspecialchars($cat['category_name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="flex-row justify-between items-center pt-3 border-t">
            <a href="<?= base_url('post/' . $post['username'] . '/' . $post['id_post']); ?>" class="btn btn-secondary btn-sm">
                Batal
            </a>
            <button type="submit" id="submit-btn" class="btn btn-primary btn-sm" style="padding-left: 24px; padding-right: 24px; box-shadow: 0 4px 12px rgba(220, 38, 38, 0.1);">
                Simpan
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('edit-post-form');
    const submitBtn = document.getElementById('submit-btn');

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        submitBtn.disabled = true;
        submitBtn.textContent = 'Menyimpan...';

        const formData = new FormData();
        formData.append('id_post', document.getElementById('edit-post-id').value);
        formData.append('content', document.getElementById('edit-post-content').value);
        formData.append('category', document.getElementById('edit-post-category').value);
        formData.append(document.querySelector('meta[name="csrf-token-name"]').content, document.querySelector('meta[name="csrf-token-hash"]').content);

        fetch('<?= base_url("post/edit_post"); ?>', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                window.location.href = '<?= base_url("post/" . $post["username"] . "/" . $post["id_post"]); ?>';
            } else {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Simpan';
                alert(data.message);
            }
        })
        .catch(err => {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Simpan';
            console.error('Error:', err);
            alert('Terjadi kesalahan. Silakan coba lagi.');
        });
    });
});
</script>
</main>
