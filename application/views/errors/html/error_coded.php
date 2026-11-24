<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$incident_code = isset($incident_code) && $incident_code ? $incident_code : (isset($code) ? $code : 'PDK-UNKNOWN');
$heading_text = isset($heading) && $heading ? $heading : 'Terjadi Kendala';
$message_text = isset($user_message) && $user_message
    ? $user_message
    : 'Kami tidak dapat memproses permintaan Anda saat ini. Silakan coba kembali beberapa saat.';
$hint_text = isset($support_hint) && $support_hint
    ? $support_hint
    : 'Jika masalah berlanjut, berikan kode laporan tersebut kepada tim PaddockID.';
$home_url = function_exists('base_url') ? base_url() : '/';
?><!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= htmlspecialchars($heading_text, ENT_QUOTES, 'UTF-8'); ?> | PaddockID</title>
<style type="text/css">
    :root {
        color-scheme: dark;
        --bg: #05070c;
        --surface: rgba(15, 22, 38, 0.92);
        --border: rgba(255, 255, 255, 0.08);
        --text: #e2e8f0;
        --muted: #94a3b8;
        --subtle: #64748b;
        --primary: #dc2626;
        --primary-soft: rgba(220, 38, 38, 0.12);
        --primary-border: rgba(220, 38, 38, 0.28);
    }
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 24px;
        background:
            radial-gradient(900px 420px at 50% -10%, rgba(220, 38, 38, 0.12), transparent 60%),
            var(--bg);
        color: var(--text);
        font-family: "Plus Jakarta Sans", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        line-height: 1.6;
    }
    .card {
        width: 100%;
        max-width: 560px;
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 20px;
        padding: 32px 28px;
        box-shadow: 0 24px 64px rgba(0, 0, 0, 0.45);
    }
    .eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 14px;
        color: #f87171;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }
    .dot {
        width: 8px;
        height: 8px;
        border-radius: 999px;
        background: var(--primary);
        box-shadow: 0 0 0 6px rgba(220, 38, 38, 0.12);
    }
    h1 {
        font-size: clamp(22px, 3vw, 28px);
        line-height: 1.2;
        font-weight: 700;
        color: #fff;
        margin-bottom: 10px;
        letter-spacing: -0.02em;
    }
    .message {
        color: var(--muted);
        font-size: 14px;
        margin-bottom: 22px;
    }
    .code-box {
        border: 1px solid var(--primary-border);
        background: var(--primary-soft);
        border-radius: 14px;
        padding: 14px;
        margin-bottom: 14px;
    }
    .code-label {
        font-size: 11px;
        font-weight: 600;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: #fca5a5;
        margin-bottom: 8px;
    }
    .code-row {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }
    .incident-code {
        font-family: "JetBrains Mono", "SFMono-Regular", Consolas, "Liberation Mono", Menlo, monospace;
        font-size: 15px;
        font-weight: 700;
        color: #fff;
        background: rgba(5, 7, 12, 0.45);
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: 10px;
        padding: 10px 12px;
        word-break: break-all;
        flex: 1 1 220px;
    }
    .copy-btn {
        appearance: none;
        border: 1px solid rgba(255,255,255,0.12);
        background: rgba(255,255,255,0.04);
        color: #fff;
        border-radius: 10px;
        padding: 10px 14px;
        font: inherit;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.15s ease, border-color 0.15s ease, transform 0.15s ease;
    }
    .copy-btn:hover {
        background: rgba(255,255,255,0.08);
        border-color: rgba(255,255,255,0.18);
    }
    .copy-btn:active { transform: translateY(1px); }
    .copy-status {
        min-height: 18px;
        margin-top: 8px;
        font-size: 12px;
        color: #86efac;
    }
    .copy-status.is-error { color: #fda4af; }
    .hint {
        color: var(--subtle);
        font-size: 13px;
        margin-bottom: 22px;
    }
    .actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }
    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        border-radius: 10px;
        padding: 10px 14px;
        font-size: 13px;
        font-weight: 600;
        border: 1px solid rgba(255,255,255,0.12);
        color: #e2e8f0;
        background: rgba(255,255,255,0.03);
    }
    .btn:hover { background: rgba(255,255,255,0.07); }
    .btn-primary {
        background: #dc2626;
        border-color: #dc2626;
        color: #fff;
    }
    .btn-primary:hover { background: #ef4444; border-color: #ef4444; }
    @media (max-width: 480px) {
        body { padding: 16px; }
        .card { padding: 24px 18px; border-radius: 16px; }
        .actions { flex-direction: column; }
        .btn, .copy-btn { width: 100%; }
    }
</style>
</head>
<body>
    <main class="card" role="main" aria-labelledby="error-title">
        <div class="eyebrow"><span class="dot" aria-hidden="true"></span>Laporan Kendala</div>
        <h1 id="error-title"><?= htmlspecialchars($heading_text, ENT_QUOTES, 'UTF-8'); ?></h1>
        <p class="message"><?= htmlspecialchars($message_text, ENT_QUOTES, 'UTF-8'); ?></p>

        <section class="code-box" aria-label="Kode laporan error">
            <p class="code-label">Kode laporan</p>
            <div class="code-row">
                <code id="incident-code" class="incident-code"><?= htmlspecialchars($incident_code, ENT_QUOTES, 'UTF-8'); ?></code>
                <button type="button" id="copy-incident-code" class="copy-btn">Salin kode</button>
            </div>
            <p id="copy-status" class="copy-status" aria-live="polite"></p>
        </section>

        <p class="hint"><?= htmlspecialchars($hint_text, ENT_QUOTES, 'UTF-8'); ?></p>

        <div class="actions">
            <a class="btn btn-primary" href="<?= htmlspecialchars($home_url, ENT_QUOTES, 'UTF-8'); ?>">Kembali ke Beranda</a>
            <button type="button" class="btn" onclick="if (history.length > 1) { history.back(); } else { window.location.href='<?= htmlspecialchars($home_url, ENT_QUOTES, 'UTF-8'); ?>'; }">Muat Ulang / Kembali</button>
        </div>
    </main>

<script>
(function () {
    var button = document.getElementById('copy-incident-code');
    var codeEl = document.getElementById('incident-code');
    var statusEl = document.getElementById('copy-status');
    if (!button || !codeEl) return;

    function setStatus(message, isError) {
        if (!statusEl) return;
        statusEl.textContent = message || '';
        statusEl.className = 'copy-status' + (isError ? ' is-error' : '');
    }

    function fallbackCopy(text) {
        var textarea = document.createElement('textarea');
        textarea.value = text;
        textarea.setAttribute('readonly', '');
        textarea.style.position = 'fixed';
        textarea.style.opacity = '0';
        textarea.style.pointerEvents = 'none';
        document.body.appendChild(textarea);
        textarea.focus();
        textarea.select();
        textarea.setSelectionRange(0, textarea.value.length);
        var ok = false;
        try { ok = document.execCommand('copy'); } catch (e) { ok = false; }
        document.body.removeChild(textarea);
        return ok;
    }

    button.addEventListener('click', function () {
        var text = (codeEl.textContent || '').trim();
        if (!text) return;

        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text).then(function () {
                setStatus('Kode laporan berhasil disalin.', false);
            }).catch(function () {
                setStatus(fallbackCopy(text) ? 'Kode laporan berhasil disalin.' : 'Gagal menyalin kode. Salin secara manual.', !fallbackCopy(text));
            });
            return;
        }

        if (fallbackCopy(text)) {
            setStatus('Kode laporan berhasil disalin.', false);
        } else {
            setStatus('Gagal menyalin kode. Salin secara manual.', true);
        }
    });
})();
</script>
</body>
</html>
