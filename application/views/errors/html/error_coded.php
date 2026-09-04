<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$is_dev = $show_detail;
$has_info = is_array($info) && !empty($info['title']);
?><!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Kendala Teknis | PaddockID</title>
<style type="text/css">
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
        background: #05070c;
        color: #e2e8f0;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 24px;
    }
    .card {
        max-width: 560px;
        width: 100%;
        background: rgba(15, 22, 38, 0.9);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 20px;
        padding: 36px 32px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
    }
    .badge {
        display: inline-block;
        background: rgba(239, 68, 68, 0.12);
        border: 1px solid rgba(239, 68, 68, 0.3);
        color: #f87171;
        font-family: "SFMono-Regular", Consolas, "Liberation Mono", Menlo, monospace;
        font-size: 13px;
        font-weight: 700;
        padding: 6px 12px;
        border-radius: 8px;
        letter-spacing: 0.5px;
    }
    h1 {
        font-size: 20px;
        font-weight: 700;
        margin: 18px 0 8px;
        color: #f8fafc;
    }
    p { font-size: 14px; line-height: 1.7; color: #94a3b8; }
    .detail {
        margin-top: 20px;
        padding: 16px;
        background: rgba(0, 0, 0, 0.3);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 12px;
        font-family: "SFMono-Regular", Consolas, "Liberation Mono", Menlo, monospace;
        font-size: 12px;
        color: #cbd5e1;
        word-break: break-word;
        white-space: pre-wrap;
    }
    .fix {
        margin-top: 20px;
        border-left: 3px solid rgba(239, 68, 68, 0.5);
        padding: 4px 0 4px 16px;
    }
    .fix strong { display: block; color: #f87171; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
    .fix p { font-size: 13px; color: #cbd5e1; }
    .hint { margin-top: 24px; font-size: 12px; color: #64748b; }
    a { color: #f87171; text-decoration: none; }
    a:hover { text-decoration: underline; }
</style>
</head>
<body>
<div class="card">
    <span class="badge"><?php echo htmlspecialchars($code, ENT_QUOTES, 'UTF-8'); ?></span>
    <h1><?php echo htmlspecialchars($heading, ENT_QUOTES, 'UTF-8'); ?></h1>
    <?php if ($is_dev && $has_info): ?>
        <p><?php echo htmlspecialchars($info['cause'], ENT_QUOTES, 'UTF-8'); ?></p>
    <?php else: ?>
        <p>Terjadi kendala teknis. Simpan kode di atas lalu hubungi admin untuk bantuan.</p>
    <?php endif; ?>

    <?php if ($is_dev): ?>
        <?php if (!empty($detail)): ?>
            <div class="detail"><?php echo htmlspecialchars($detail, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>
        <?php if ($has_info): ?>
            <div class="fix">
                <strong>Cara memperbaiki</strong>
                <p><?php echo htmlspecialchars($info['fix'], ENT_QUOTES, 'UTF-8'); ?></p>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <p class="hint">Kembali ke <a href="<?php echo isset($base) ? $base : '/'; ?>">beranda</a>.</p>
</div>
</body>
</html>
