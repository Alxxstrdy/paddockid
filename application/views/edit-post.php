<div class="flex items-center gap-3 mb-4 pb-4 border-b border-white/[0.04]">
    <a href="<?= base_url('post/' . $post['username'] . '/' . $post['id_post']); ?>" class="p-2 text-slate-400 hover:text-white hover:bg-white/[0.05] rounded-xl transition-colors">
        <i data-lucide="arrow-left" class="w-5 h-5"></i>
    </a>
    <h2 class="font-syne text-sm uppercase tracking-tight text-white">Edit Postingan</h2>
</div>

<div class="glass-card rounded-2xl border border-white/[0.06] p-5">
    <form id="edit-post-form">
        <input type="hidden" id="edit-post-id" value="<?= $post['id_post']; ?>">

        <div class="flex items-center gap-3 mb-4 pb-4 border-b border-white/[0.04]">
            <div class="relative w-9 h-9 flex items-center justify-center select-none">
                <div class="w-full h-full rounded-full overflow-hidden bg-slate-800">
                    <img src="<?= $post['avatar']; ?>" alt="User" class="w-full h-full object-cover rounded-full">
                </div>
                <?php if (!empty($post['border'])): ?>
                    <div class="absolute inset-0 w-full h-full pointer-events-none scale-[1.25] transform origin-center">
                        <img src="<?= $post['border']; ?>" alt="Border" class="w-full h-full object-contain">
                    </div>
                <?php endif; ?>
            </div>
            <div class="flex flex-col">
                <span class="font-semibold text-xs text-slate-200"><?= htmlspecialchars($post['username']); ?></span>
                <span class="text-[10px] text-slate-500"><?= $post['created_at']; ?></span>
            </div>
        </div>

        <textarea
            id="edit-post-content"
            rows="6"
            class="w-full bg-transparent text-sm text-slate-200 placeholder-slate-500 focus:outline-none resize-none border-b border-white/[0.03] pb-3 focus:border-red-500/50 transition-colors mb-4"
            required
        ><?= htmlspecialchars($post['content'], ENT_QUOTES, 'UTF-8'); ?></textarea>

        <div class="flex items-center gap-3 mb-4">
            <select id="edit-post-category" class="bg-slate-800 text-xs text-slate-300 border border-white/[0.06] rounded-lg px-3 py-2 focus:outline-none focus:border-red-500/50">
                <option value="">Tanpa Kategori</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id_category']; ?>" <?= $cat['id_category'] == $post['post_category'] ? 'selected' : ''; ?>><?= htmlspecialchars($cat['category_name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="flex justify-between items-center pt-3 border-t border-white/[0.04]">
            <a href="<?= base_url('post/' . $post['username'] . '/' . $post['id_post']); ?>" class="px-4 py-2.5 text-xs font-semibold text-slate-300 bg-white/[0.05] hover:bg-white/[0.08] rounded-xl transition-colors border border-white/[0.06]">
                Batal
            </a>
            <button type="submit" id="submit-btn" class="bg-red-600 hover:bg-red-700 text-white font-semibold text-xs px-6 py-2.5 rounded-xl transition-colors shadow-lg shadow-red-600/10 active:scale-[0.98]">
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
