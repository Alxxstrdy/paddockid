<div class="flex items-center gap-3" style="margin-bottom:16px;padding-bottom:16px;border-bottom:1px solid var(--border-default)">
    <a href="<?= base_url('home'); ?>" class="p-2 c-muted rounded-xl transition-colors" onmouseover="this.style.color='var(--text-primary)';this.style.background='rgba(255,255,255,0.05)'" onmouseout="this.style.color='';this.style.background=''">
        <i data-lucide="arrow-left" class="w-5 h-5"></i>
    </a>
    <h2 class="text-sm c-white text-heading" style="text-transform:uppercase">Buat Postingan</h2>
</div>

<div class="card rounded-2xl" style="border:1px solid var(--border-strong);padding:20px">
    <form id="create-post-form" enctype="multipart/form-data">
        <textarea 
            id="post-content" 
            rows="6" 
            class="w-full bg-transparent text-sm c-secondary resize-none transition-colors" 
            style="border-bottom:1px solid var(--border-subtle);padding-bottom:12px;outline:none;margin-bottom:16px"
            placeholder="Apa yang ingin kamu bagikan?"
            required
            autofocus
        ></textarea>

        <div class="flex items-center gap-3" style="margin-bottom:16px">
            <select id="post-category" class="select" style="background:var(--bg-surface-raised);font-size:12px">
                <option value="">Tanpa Kategori</option>
                <?php foreach ($this->Post_model->get_categories() as $cat): ?>
                    <option value="<?= $cat['id_category']; ?>"><?= htmlspecialchars($cat['category_name']); ?></option>
                <?php endforeach; ?>
            </select>
            <label class="flex items-center gap-2 text-xs c-muted cursor-pointer transition-colors" onmouseover="this.style.color='var(--text-secondary)'" onmouseout="this.style.color=''">
                <i data-lucide="image" class="w-4 h-4"></i>
                <span>Gambar</span>
                <input type="file" id="post-images" name="images[]" accept="image/*" multiple class="hidden">
            </label>
        </div>

        <div id="image-preview" class="flex flex-wrap gap-2" style="margin-bottom:16px"></div>

        <div class="flex justify-between items-center" style="padding-top:12px;border-top:1px solid var(--border-default)">
            <span id="char-count" class="c-subtle" style="font-size:10px"></span>
            <button type="submit" id="submit-btn" class="btn btn-primary">
                Posting
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const textarea = document.getElementById('post-content');
    const charCount = document.getElementById('char-count');
    const imageInput = document.getElementById('post-images');
    const preview = document.getElementById('image-preview');
    const form = document.getElementById('create-post-form');
    const submitBtn = document.getElementById('submit-btn');
    const MAX_FILES = 4;

    let selectedFiles = [];

    textarea.addEventListener('input', function() {
        const len = this.value.length;
        charCount.textContent = len > 0 ? len + ' karakter' : '';
    });

    function renderPreviews() {
        preview.innerHTML = '';
        selectedFiles.forEach(function(file, index) {
            const wrapper = document.createElement('div');
            wrapper.className = 'relative';
            wrapper.style.cssText = 'width:64px;height:64px;border-radius:8px;overflow:hidden;border:1px solid var(--border-strong)';

            const img = document.createElement('img');
            img.className = 'w-full h-full';
            img.style.objectFit = 'cover';
            img.src = URL.createObjectURL(file);

            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'absolute flex items-center justify-center c-white rounded-full shadow-lg transition-colors';
            removeBtn.style.cssText = 'top:-4px;right:-4px;width:20px;height:20px;background:var(--color-primary)';
            removeBtn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>';
            removeBtn.addEventListener('click', function() {
                selectedFiles.splice(index, 1);
                renderPreviews();
            });

            wrapper.appendChild(img);
            wrapper.appendChild(removeBtn);
            preview.appendChild(wrapper);
        });
    }

    imageInput.addEventListener('change', function() {
        const newFiles = Array.from(this.files);
        if (selectedFiles.length + newFiles.length > MAX_FILES) {
            alert('Maksimal ' + MAX_FILES + ' gambar per postingan. Saat ini ada ' + selectedFiles.length + ' gambar.');
            this.value = '';
            return;
        }
        selectedFiles = selectedFiles.concat(newFiles);
        renderPreviews();
        this.value = '';
    });

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        submitBtn.disabled = true;
        submitBtn.textContent = 'Memposting...';

        const formData = new FormData();
        formData.append('content', textarea.value);
        formData.append('category', document.getElementById('post-category').value);

        for (let i = 0; i < selectedFiles.length; i++) {
            formData.append('images[]', selectedFiles[i]);
        }

        const csrfName = document.querySelector('meta[name="csrf-token-name"]').content;
        const csrfHash = document.querySelector('meta[name="csrf-token-hash"]').content;
        formData.append(csrfName, csrfHash);

        fetch('<?= base_url("post/create_post"); ?>', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                window.location.href = '<?= base_url('home'); ?>';
            } else {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Posting';
                alert(data.message || 'Gagal membuat postingan.');
            }
        })
        .catch(err => {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Posting';
            console.error('Error:', err);
            alert('Terjadi kesalahan. Silakan coba lagi.');
        });
    });

    // @mention autocomplete
    let mentionDropdown = null;
    let mentionTimeout = null;

    textarea.addEventListener('input', function() {
        const cursorPos = this.selectionStart;
        const text = this.value;
        const beforeCursor = text.substring(0, cursorPos);
        const match = beforeCursor.match(/@(\w*)$/);

        if (match) {
            const query = match[1];
            if (mentionTimeout) clearTimeout(mentionTimeout);
            mentionTimeout = setTimeout(() => fetchMentionUsers(query), 200);
        } else {
            if (mentionTimeout) clearTimeout(mentionTimeout);
            removeMentionDropdown();
        }
    });

    textarea.addEventListener('keydown', function(e) {
        if (!mentionDropdown) return;
        const items = mentionDropdown.querySelectorAll('.mention-item');
        const active = mentionDropdown.querySelector('.mention-active');
        let idx = Array.from(items).indexOf(active);

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            const next = (active && idx < items.length - 1) ? items[idx + 1] : items[0];
            if (active) active.classList.remove('mention-active');
            next.classList.add('mention-active');
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            const prev = (active && idx > 0) ? items[idx - 1] : items[items.length - 1];
            if (active) active.classList.remove('mention-active');
            prev.classList.add('mention-active');
        } else if (e.key === 'Enter' || e.key === 'Tab') {
            if (active) {
                e.preventDefault();
                active.click();
            }
        } else if (e.key === 'Escape') {
            removeMentionDropdown();
        }
    });

    function fetchMentionUsers(query) {
        const url = '<?= base_url("search/search_ajax"); ?>?type=users&q=' + encodeURIComponent(query);
        fetch(url)
            .then(r => r.json())
            .then(users => {
                if (!users.length) { removeMentionDropdown(); return; }
                showMentionDropdown(users);
            })
            .catch(() => removeMentionDropdown());
    }

    function showMentionDropdown(users) {
        removeMentionDropdown();
        mentionDropdown = document.createElement('div');
        mentionDropdown.className = 'absolute w-64 rounded-xl shadow-xl overflow-hidden';
        mentionDropdown.style.cssText = 'z-index:100;background:var(--bg-surface);border:1px solid var(--border-subtle);max-height:192px;overflow-y:auto';

        users.forEach((user, i) => {
            const item = document.createElement('button');
            item.type = 'button';
            item.className = 'mention-item w-full flex items-center gap-2.5 px-3 py-2.5 text-xs c-secondary text-left transition-colors ' + (i === 0 ? 'mention-active' : '');
            item.innerHTML = '<img src="' + escapeHtml(user.avatar) + '" alt="" class="w-6 h-6 rounded-full" style="object-fit:cover" onerror="this.src=\'<?= assets_url('default.jpg'); ?>\'"> <span class="font-medium">' + escapeHtml(user.username) + '</span>';
            item.addEventListener('click', function() {
                insertMention(user.username);
            });
            mentionDropdown.appendChild(item);
        });

        const rect = textarea.getBoundingClientRect();
        mentionDropdown.style.position = 'fixed';
        mentionDropdown.style.left = rect.left + 'px';
        mentionDropdown.style.top = (rect.bottom + 4) + 'px';
        mentionDropdown.style.width = rect.width + 'px';
        document.body.appendChild(mentionDropdown);
    }

    function removeMentionDropdown() {
        if (mentionDropdown) {
            mentionDropdown.remove();
            mentionDropdown = null;
        }
    }

    function insertMention(username) {
        const cursorPos = textarea.selectionStart;
        const text = textarea.value;
        const beforeCursor = text.substring(0, cursorPos);
        const afterCursor = text.substring(cursorPos);
        const match = beforeCursor.match(/@(\w*)$/);
        if (match) {
            const newText = beforeCursor.substring(0, beforeCursor.length - match[0].length) + '@' + username + ' ' + afterCursor;
            textarea.value = newText;
            const newPos = beforeCursor.length - match[0].length + username.length + 2;
            textarea.setSelectionRange(newPos, newPos);
            textarea.focus();
            textarea.dispatchEvent(new Event('input'));
        }
        removeMentionDropdown();
    }

    document.addEventListener('click', function(e) {
        if (mentionDropdown && !mentionDropdown.contains(e.target) && e.target !== textarea) {
            removeMentionDropdown();
        }
    });
});
</script>
</main>
