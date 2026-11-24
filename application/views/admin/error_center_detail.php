<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php
$incident = isset($incident) && is_array($incident) ? $incident : array();
$code = (string) ($incident['error_code'] ?? '');
$status = (string) ($incident['status'] ?? 'open');
$severity = (string) ($incident['severity'] ?? 'error');
$context_json = isset($incident['context']) ? (string) $incident['context'] : '';
$context_data = null;
if ($context_json !== '') {
    $decoded = json_decode($context_json, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        $context_data = $decoded;
    }
}
$status_class = 'badge-danger';
if ($status === 'resolved') $status_class = 'badge-success';
elseif ($status === 'investigating') $status_class = 'badge-warning';
elseif ($status === 'ignored') $status_class = 'badge-default';
?>
<div class="space-y-6 ec-wrap">
    <div class="flex-row justify-between" style="gap:12px;flex-wrap:wrap;align-items:flex-start;">
        <div>
            <a href="<?= base_url('admin/error_center'); ?>" class="text-caption c-subtle" style="display:inline-flex;align-items:center;gap:6px;margin-bottom:8px;">
                <i data-lucide="arrow-left" class="w-3-5 h-3-5"></i> Kembali ke Error Center
            </a>
            <h1 class="text-page-title" style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                <span class="font-mono"><?= htmlspecialchars($code, ENT_QUOTES, 'UTF-8'); ?></span>
                <span class="badge <?= $status_class; ?>" id="incident-status-badge"><?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8'); ?></span>
            </h1>
            <p class="text-caption mt-1">Detail insiden untuk investigasi internal. Informasi teknis ini hanya tersedia untuk admin/developer.</p>
        </div>
        <div class="flex-row gap-2" style="flex-wrap:wrap;">
            <button type="button" class="btn btn-ghost btn-sm" id="copy-incident-code-btn" data-code="<?= htmlspecialchars($code, ENT_QUOTES, 'UTF-8'); ?>">Salin Kode</button>
            <a href="<?= base_url('admin/error_center?error_code=' . rawurlencode($code)); ?>" class="btn btn-ghost btn-sm">Cari Kode Ini</a>
        </div>
    </div>

    <div id="ec-alert" class="card hidden" style="padding:12px 14px;"></div>

    <div class="page-grid" style="gap:16px;">
        <div style="grid-column:span 8;min-width:0;" class="space-y-4">
            <div class="card" style="padding:16px;">
                <p class="text-label mb-3">Ringkasan Insiden</p>
                <div class="ec-kv-grid">
                    <div class="ec-kv"><span class="ec-kv__k">Kode Insiden</span><span class="ec-kv__v font-mono"><?= htmlspecialchars($code, ENT_QUOTES, 'UTF-8'); ?></span></div>
                    <div class="ec-kv"><span class="ec-kv__k">Tipe</span><span class="ec-kv__v"><?= htmlspecialchars((string) ($incident['error_type'] ?? '-'), ENT_QUOTES, 'UTF-8'); ?></span></div>
                    <div class="ec-kv"><span class="ec-kv__k">Severity</span><span class="ec-kv__v"><?= htmlspecialchars($severity, ENT_QUOTES, 'UTF-8'); ?></span></div>
                    <div class="ec-kv"><span class="ec-kv__k">Status</span><span class="ec-kv__v"><?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8'); ?></span></div>
                    <div class="ec-kv"><span class="ec-kv__k">Waktu Terjadi</span><span class="ec-kv__v"><?= htmlspecialchars((string) ($incident['created_at'] ?? '-'), ENT_QUOTES, 'UTF-8'); ?></span></div>
                    <div class="ec-kv"><span class="ec-kv__k">Diperbarui</span><span class="ec-kv__v"><?= htmlspecialchars((string) ($incident['updated_at'] ?? '-'), ENT_QUOTES, 'UTF-8'); ?></span></div>
                    <div class="ec-kv"><span class="ec-kv__k">Exception</span><span class="ec-kv__v font-mono"><?= htmlspecialchars((string) ($incident['exception_class'] ?? '-'), ENT_QUOTES, 'UTF-8'); ?></span></div>
                    <div class="ec-kv"><span class="ec-kv__k">User ID</span><span class="ec-kv__v"><?= htmlspecialchars((string) ($incident['user_id'] ?? '-'), ENT_QUOTES, 'UTF-8'); ?></span></div>
                </div>
            </div>

            <div class="card" style="padding:16px;">
                <p class="text-label mb-2">Pesan Internal (Tersanitasi)</p>
                <pre class="ec-pre"><?= htmlspecialchars((string) ($incident['message'] ?? '-'), ENT_QUOTES, 'UTF-8'); ?></pre>
            </div>

            <div class="card" style="padding:16px;">
                <p class="text-label mb-2">Lokasi Teknis</p>
                <div class="ec-kv-grid">
                    <div class="ec-kv ec-kv--full"><span class="ec-kv__k">File</span><span class="ec-kv__v font-mono" style="word-break:break-all;"><?= htmlspecialchars((string) ($incident['file'] ?? '-'), ENT_QUOTES, 'UTF-8'); ?></span></div>
                    <div class="ec-kv"><span class="ec-kv__k">Line</span><span class="ec-kv__v font-mono"><?= htmlspecialchars((string) ($incident['line'] ?? '-'), ENT_QUOTES, 'UTF-8'); ?></span></div>
                </div>
                <?php if (is_array($context_data) && !empty($context_data['stack_trace'])): ?>
                    <div style="margin-top:12px;">
                        <p class="text-caption c-subtle mb-2">Stack Trace (tersanitasi, jika tersedia)</p>
                        <pre class="ec-pre ec-pre--sm"><?= htmlspecialchars((string) $context_data['stack_trace'], ENT_QUOTES, 'UTF-8'); ?></pre>
                    </div>
                <?php else: ?>
                    <p class="text-caption c-subtle mt-2">Stack trace tidak tersedia untuk insiden ini.</p>
                <?php endif; ?>
            </div>

            <div class="card" style="padding:16px;">
                <p class="text-label mb-2">Informasi Request (Tersanitasi)</p>
                <div class="ec-kv-grid">
                    <div class="ec-kv"><span class="ec-kv__k">Method</span><span class="ec-kv__v font-mono"><?= htmlspecialchars((string) ($incident['request_method'] ?? '-'), ENT_QUOTES, 'UTF-8'); ?></span></div>
                    <div class="ec-kv"><span class="ec-kv__k">IP Address</span><span class="ec-kv__v font-mono"><?= htmlspecialchars((string) ($incident['ip_address'] ?? '-'), ENT_QUOTES, 'UTF-8'); ?></span></div>
                    <div class="ec-kv ec-kv--full"><span class="ec-kv__k">Request URI</span><span class="ec-kv__v font-mono" style="word-break:break-all;"><?= htmlspecialchars((string) ($incident['request_uri'] ?? '-'), ENT_QUOTES, 'UTF-8'); ?></span></div>
                    <div class="ec-kv ec-kv--full"><span class="ec-kv__k">User Agent</span><span class="ec-kv__v" style="word-break:break-word;"><?= htmlspecialchars((string) ($incident['user_agent'] ?? '-'), ENT_QUOTES, 'UTF-8'); ?></span></div>
                </div>
            </div>

            <div class="card" style="padding:16px;">
                <p class="text-label mb-2">Konteks Tambahan (Tersanitasi)</p>
                <?php if ($context_data === null && $context_json === ''): ?>
                    <p class="text-caption c-subtle">Tidak ada konteks tambahan.</p>
                <?php elseif ($context_data !== null): ?>
                    <pre class="ec-pre ec-pre--sm"><?= htmlspecialchars(json_encode($context_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), ENT_QUOTES, 'UTF-8'); ?></pre>
                <?php else: ?>
                    <pre class="ec-pre ec-pre--sm"><?= htmlspecialchars($context_json, ENT_QUOTES, 'UTF-8'); ?></pre>
                <?php endif; ?>
            </div>
        </div>

        <div style="grid-column:span 4;min-width:0;" class="space-y-4">
            <div class="card" style="padding:16px;">
                <p class="text-label mb-3">Status Penanganan</p>
                <form id="incident-status-form" class="space-y-3">
                    <input type="hidden" name="error_code" value="<?= htmlspecialchars($code, ENT_QUOTES, 'UTF-8'); ?>">
                    <div>
                        <label class="text-caption c-subtle" for="incident-status">Status</label>
                        <select id="incident-status" name="status" class="input" style="margin-top:6px;">
                            <option value="open" <?= $status === 'open' ? 'selected' : ''; ?>>Open</option>
                            <option value="investigating" <?= $status === 'investigating' ? 'selected' : ''; ?>>Investigating</option>
                            <option value="resolved" <?= $status === 'resolved' ? 'selected' : ''; ?>>Resolved</option>
                            <option value="ignored" <?= $status === 'ignored' ? 'selected' : ''; ?>>Ignored</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-caption c-subtle" for="incident-notes">Catatan Resolusi / Investigasi</label>
                        <textarea id="incident-notes" name="resolution_notes" class="input" rows="7" style="margin-top:6px;resize:vertical;" placeholder="Catat temuan, langkah perbaikan, atau alasan status..."><?= htmlspecialchars((string) ($incident['resolution_notes'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></textarea>
                        <p class="text-micro c-subtle mt-1">Catatan disimpan untuk kebutuhan investigasi internal.</p>
                    </div>
                    <button type="submit" class="btn btn-primary" id="incident-save-btn" style="width:100%;">Simpan Perubahan</button>
                </form>
            </div>

            <div class="card" style="padding:16px;">
                <p class="text-label mb-3">Riwayat Penanganan</p>
                <div class="ec-kv-grid">
                    <div class="ec-kv ec-kv--full"><span class="ec-kv__k">Resolved By</span><span class="ec-kv__v"><?= htmlspecialchars((string) ($incident['resolved_by'] ?? '-'), ENT_QUOTES, 'UTF-8'); ?></span></div>
                    <div class="ec-kv ec-kv--full"><span class="ec-kv__k">Resolved At</span><span class="ec-kv__v"><?= htmlspecialchars((string) ($incident['resolved_at'] ?? '-'), ENT_QUOTES, 'UTF-8'); ?></span></div>
                </div>
                <p class="text-caption c-subtle mt-3">Gunakan kode ini untuk menghubungkan laporan user dengan detail teknis insiden.</p>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    var copyBtn = document.getElementById('copy-incident-code-btn');
    var alertBox = document.getElementById('ec-alert');
    var form = document.getElementById('incident-status-form');
    var saveBtn = document.getElementById('incident-save-btn');
    var badge = document.getElementById('incident-status-badge');

    function showAlert(message, type) {
        if (!alertBox) return;
        alertBox.className = 'card';
        alertBox.style.padding = '12px 14px';
        alertBox.style.marginTop = '0';
        alertBox.style.border = '1px solid ' + (type === 'success' ? 'var(--color-success-border, rgba(34,197,94,0.25))' : 'var(--color-danger-border, rgba(239,68,68,0.25))');
        alertBox.style.background = type === 'success' ? 'var(--color-success-bg, rgba(34,197,94,0.08))' : 'var(--color-danger-bg, rgba(239,68,68,0.08))';
        alertBox.textContent = message;
    }

    function fallbackCopy(text) {
        var ta = document.createElement('textarea');
        ta.value = text;
        ta.setAttribute('readonly', '');
        ta.style.position = 'fixed';
        ta.style.opacity = '0';
        document.body.appendChild(ta);
        ta.select();
        var ok = false;
        try { ok = document.execCommand('copy'); } catch (e) { ok = false; }
        document.body.removeChild(ta);
        return ok;
    }

    if (copyBtn) {
        copyBtn.addEventListener('click', function() {
            var code = copyBtn.getAttribute('data-code') || '';
            if (!code) return;
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(code).then(function() {
                    showAlert('Kode insiden berhasil disalin.', 'success');
                }).catch(function() {
                    showAlert(fallbackCopy(code) ? 'Kode insiden berhasil disalin.' : 'Gagal menyalin kode.', fallbackCopy(code) ? 'success' : 'error');
                });
            } else {
                showAlert(fallbackCopy(code) ? 'Kode insiden berhasil disalin.' : 'Gagal menyalin kode.', fallbackCopy(code) ? 'success' : 'error');
            }
        });
    }

    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            if (saveBtn) { saveBtn.disabled = true; saveBtn.textContent = 'Menyimpan...'; }

            var params = new URLSearchParams();
            params.append('error_code', form.querySelector('[name="error_code"]').value || '');
            params.append('status', form.querySelector('[name="status"]').value || '');
            params.append('resolution_notes', form.querySelector('[name="resolution_notes"]').value || '');
            if (typeof getCsrfTokenName === 'function' && typeof getCsrfToken === 'function') {
                params.append(getCsrfTokenName(), getCsrfToken());
            }

            fetch('<?= base_url('admin/error_center_update_status'); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: params.toString(),
                credentials: 'same-origin'
            }).then(function(res) { return res.json().then(function(data){ return {ok: res.ok, data: data}; }); })
              .then(function(result) {
                var data = result.data || {};
                if (result.ok && data.status === 'success') {
                    showAlert(data.message || 'Status insiden diperbarui.', 'success');
                    var st = form.querySelector('[name="status"]').value;
                    if (badge) {
                        badge.textContent = st;
                        badge.className = 'badge ' + (st === 'resolved' ? 'badge-success' : (st === 'investigating' ? 'badge-warning' : (st === 'ignored' ? 'badge-default' : 'badge-danger')));
                    }
                } else {
                    showAlert((data && data.message) ? data.message : 'Gagal memperbarui status insiden.', 'error');
                }
              })
              .catch(function() {
                showAlert('Gagal memperbarui status insiden.', 'error');
              })
              .finally(function() {
                if (saveBtn) { saveBtn.disabled = false; saveBtn.textContent = 'Simpan Perubahan'; }
              });
        });
    }
})();
</script>
