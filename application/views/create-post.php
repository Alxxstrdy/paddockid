<div class="cp-head">
    <a href="<?= base_url('home'); ?>" class="cp-head__back">
        <i data-lucide="arrow-left" class="w-5 h-5"></i>
    </a>
    <h2 class="cp-head__title">Buat Postingan</h2>
</div>

<div class="cp-card">
    <form id="create-post-form" enctype="multipart/form-data" novalidate>

        <div class="cp-author">
            <div class="cp-author__avatar">
                <img src="<?= $user['avatar']; ?>" alt="<?= htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8'); ?>"
                     onerror="this.src='<?= assets_url('default.jpg'); ?>';">
            </div>
            <div class="cp-author__meta">
                <span class="cp-author__name"><?= htmlspecialchars($user['display_name'], ENT_QUOTES, 'UTF-8'); ?></span>
                <span class="cp-author__username">@<?= htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
        </div>

        <div class="cp-body">
            <textarea
                id="post-content"
                rows="4"
                class="cp-input"
                placeholder="Apa yang ingin kamu bagikan?"
                required
                autofocus
            ></textarea>
        </div>

        <label for="post-images" id="cp-dropzone" class="cp-dropzone">
            <input type="file" id="post-images" name="images[]" accept="image/*" multiple class="hidden">
            <span class="cp-dropzone__inner">
                <span class="cp-dropzone__icon"><i data-lucide="image-plus" class="w-5 h-5"></i></span>
                <span>
                    <span class="cp-dropzone__title">Tambahkan Foto</span>
                    <span class="cp-dropzone__hint">Klik atau seret &amp; lepas foto di sini (maks. 4)</span>
                </span>
            </span>
        </label>

        <div id="image-preview" class="cp-grid"></div>

        <div class="cp-toolbar">
            <div class="cp-toolbar__left">
                <button type="button" id="btn-emoji" class="cp-tool" title="Emoji" aria-label="Emoji">
                    <i data-lucide="smile-plus" class="w-5 h-5"></i>
                </button>
                <label class="cp-tool" title="Foto" aria-label="Foto">
                    <i data-lucide="image" class="w-5 h-5"></i>
                    <input type="file" id="post-images-tool" name="images_tool[]" accept="image/*" multiple class="hidden">
                </label>
            </div>

            <div class="cp-toolbar__right">
                <span id="char-count" class="cp-count"></span>
                <button type="submit" id="submit-btn" class="cp-submit" disabled>
                    <span class="cp-submit__label">Posting</span>
                </button>
            </div>
        </div>
    </form>

    <div id="emoji-picker" class="cp-emoji hidden">
        <p class="cp-emoji__title">Emoji</p>
        <div class="cp-emoji__grid" id="emoji-grid"></div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const textarea = document.getElementById('post-content');
    const charCount = document.getElementById('char-count');
    const imageInput = document.getElementById('post-images');
    const imageTool = document.getElementById('post-images-tool');
    const preview = document.getElementById('image-preview');
    const form = document.getElementById('create-post-form');
    const submitBtn = document.getElementById('submit-btn');
    const dropzone = document.getElementById('cp-dropzone');
    const MAX_FILES = 4;
    const MAX_CHARS = 2000;

    let selectedFiles = [];

    function updateSubmitState() {
        const hasContent = textarea.value.trim().length > 0;
        submitBtn.disabled = !hasContent;
        submitBtn.classList.toggle('cp-submit--idle', !hasContent);
    }

    function updateCharCount() {
        const len = textarea.value.length;
        charCount.textContent = len > 0 ? len.toLocaleString('id-ID') : '';
        charCount.classList.toggle('cp-count--warn', len > MAX_CHARS * 0.8 && len <= MAX_CHARS);
        charCount.classList.toggle('cp-count--danger', len > MAX_CHARS);
        updateSubmitState();
    }

    textarea.addEventListener('input', updateCharCount);

    function autoGrow() {
        textarea.style.height = 'auto';
        textarea.style.height = Math.min(textarea.scrollHeight, 360) + 'px';
    }
    textarea.addEventListener('input', autoGrow);
    autoGrow();

    function renderPreviews() {
        preview.innerHTML = '';
        textarea.classList.toggle('cp-input--with-media', selectedFiles.length > 0);

        if (selectedFiles.length === 0) {
            dropzone.style.display = 'flex';
            return;
        }
        dropzone.style.display = 'none';

        preview.classList.toggle('cp-grid--multi', selectedFiles.length > 1);

        selectedFiles.forEach(function(file, index) {
            const tile = document.createElement('div');
            tile.className = 'cp-grid__tile cp-grid__tile--' + selectedFiles.length;

            const img = document.createElement('img');
            img.className = 'cp-grid__img';
            img.src = URL.createObjectURL(file);
            img.alt = 'Preview ' + (index + 1);

            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'cp-grid__remove';
            removeBtn.title = 'Hapus';
            removeBtn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>';
            removeBtn.addEventListener('click', function() {
                selectedFiles.splice(index, 1);
                renderPreviews();
            });

            tile.appendChild(img);
            tile.appendChild(removeBtn);
            preview.appendChild(tile);
        });
    }

    function addFiles(fileList) {
        const newFiles = Array.from(fileList).filter(f => f.type.startsWith('image/'));
        if (selectedFiles.length + newFiles.length > MAX_FILES) {
            alert('Maksimal ' + MAX_FILES + ' gambar per postingan. Saat ini ada ' + selectedFiles.length + ' gambar.');
            return;
        }
        selectedFiles = selectedFiles.concat(newFiles);
        renderPreviews();
    }

    imageInput.addEventListener('change', function() {
        addFiles(this.files);
        this.value = '';
    });

    imageTool.addEventListener('change', function() {
        addFiles(this.files);
        this.value = '';
    });

    dropzone.addEventListener('dragover', function(e) {
        e.preventDefault();
    });

    dropzone.addEventListener('dragenter', function(e) {
        e.preventDefault();
    });

    dropzone.addEventListener('dragleave', function(e) {
        e.preventDefault();
        dropzone.classList.remove('cp-dropzone--hover');
    });

    dropzone.addEventListener('drop', function(e) {
        e.preventDefault();
        dropzone.classList.remove('cp-dropzone--hover');
        addFiles(e.dataTransfer.files);
    });

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        submitBtn.disabled = true;
        submitBtn.classList.add('cp-submit--loading');

        const formData = new FormData();
        formData.append('content', textarea.value);

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
                submitBtn.classList.remove('cp-submit--loading');
                alert(data.message || 'Gagal membuat postingan.');
            }
        })
        .catch(err => {
            submitBtn.disabled = false;
            submitBtn.classList.remove('cp-submit--loading');
            console.error('Error:', err);
            alert('Terjadi kesalahan. Silakan coba lagi.');
        });
    });

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
        const items = mentionDropdown.querySelectorAll('.post-mention-item');
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
        mentionDropdown.className = 'cp-mention';
        mentionDropdown.style.cssText = 'z-index:120;max-height:220px;overflow-y:auto';

        users.forEach((user, i) => {
            const item = document.createElement('button');
            item.type = 'button';
            item.className = 'cp-mention__item' + (i === 0 ? ' mention-active' : '');
            item.innerHTML = '<img src="' + escapeHtml(user.avatar) + '" alt="" onerror="this.src=\'<?= assets_url('default.jpg'); ?>\'"> <span class="cp-mention__name">' + escapeHtml(user.username) + '</span>';
            item.addEventListener('click', function() {
                insertMention(user.username);
            });
            mentionDropdown.appendChild(item);
        });

        const rect = textarea.getBoundingClientRect();
        mentionDropdown.style.position = 'fixed';
        mentionDropdown.style.left = rect.left + 'px';
        mentionDropdown.style.top = (rect.top - 12) + 'px';
        mentionDropdown.style.width = Math.min(rect.width, 320) + 'px';
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

    // Emoji picker
    const EMOJI = ['🏎️','🏁','🔥','🏆','👑','❤️','😍','😂','🤣','😎','👍','🙌','🤙','👏','💪','🫶','🏆','🥇','🥈','🥉','🎉','🎊','⭐','🚀','🍾','🥳','😤','😱','😴','🤯'];
    const emojiPicker = document.getElementById('emoji-picker');
    const emojiGrid = document.getElementById('emoji-grid');
    const btnEmoji = document.getElementById('btn-emoji');

    EMOJI.forEach(function(emoji) {
        const b = document.createElement('button');
        b.type = 'button';
        b.className = 'cp-emoji__cell';
        b.textContent = emoji;
        b.addEventListener('click', function() {
            const start = textarea.selectionStart;
            const end = textarea.selectionEnd;
            textarea.value = textarea.value.slice(0, start) + emoji + textarea.value.slice(end);
            const newPos = start + emoji.length;
            textarea.setSelectionRange(newPos, newPos);
            textarea.focus();
            textarea.dispatchEvent(new Event('input'));
            emojiPicker.classList.add('hidden');
            btnEmoji.classList.remove('cp-tool--active');
        });
        emojiGrid.appendChild(b);
    });

    btnEmoji.addEventListener('click', function() {
        emojiPicker.classList.toggle('hidden');
        btnEmoji.classList.toggle('cp-tool--active', !emojiPicker.classList.contains('hidden'));
        textarea.focus();
    });

    document.addEventListener('click', function(e) {
        if (!emojiPicker.classList.contains('hidden') && !emojiPicker.contains(e.target) && !btnEmoji.contains(e.target)) {
            emojiPicker.classList.add('hidden');
            btnEmoji.classList.remove('cp-tool--active');
        }
    });

    updateCharCount();
});
</script>
</main>
