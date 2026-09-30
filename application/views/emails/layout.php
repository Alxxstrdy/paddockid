<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/*
 * Layout HTML email PaddockID — satu-satunya sumber template untuk semua email.
 * Desain menyelaraskan tema platform: navy gelap solid + biru aksen #2f6bff,
 * font Syne (judul) & Plus Jakarta Sans (body).
 * - Table-based + semua style inline (compat Gmail/Outlook).
 * - Variabel:
 *     $heading   (string)  Judul email.
 *     $body      (string)  Isi HTML.
 *     $button    (array)   opsional: ['url' => ..., 'text' => ...] CTA tombol.
 *     $preheader (string)  opsional: teks preview di inbox.
 */
$logo = (string) getenv('SMTP_LOGO');
if ($logo === '') {
    $logo = base_url('uploads/Logo_PaddockID.png');
}
$pre = !empty($preheader) ? $preheader : $heading;
$has_btn = !empty($button) && !empty($button['url']) && !empty($button['text']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="color-scheme" content="dark">
<meta name="supported-color-schemes" content="dark">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@600;700&family=Plus+Jakarta+Sans:wght@400;600&display=swap" rel="stylesheet">
<title><?= htmlspecialchars($heading, ENT_QUOTES, 'UTF-8') ?></title>
</head>
<body style="margin:0;padding:0;background:#090c13;word-spacing:normal;">
  <div style="display:none;max-height:0;overflow:hidden;mso-hide:all;"><?= htmlspecialchars($pre, ENT_QUOTES, 'UTF-8') ?></div>
  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#090c13;">
    <tr>
      <td align="center" style="padding:36px 16px;">
        <table role="presentation" width="560" cellpadding="0" cellspacing="0" style="width:100%;max-width:560px;">

          <!-- HEADER: logo -->
          <tr>
            <td align="center" style="padding:0 16px 24px;">
              <img src="<?= htmlspecialchars($logo, ENT_QUOTES, 'UTF-8') ?>"
                   alt="PaddockID" width="200" height="49"
                   style="display:block;width:200px;height:auto;max-width:200px;border:0;outline:none;text-decoration:none;">
            </td>
          </tr>

          <!-- KARTU KONTEN -->
          <tr>
            <td style="background:#10141e;border:1px solid rgba(255,255,255,0.075);border-radius:12px;padding:32px 36px;">
              <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                <tr>
                  <td width="44" style="background:#2f6bff;border-radius:3px;height:4px;">&nbsp;</td>
                </tr>
              </table>
              <h1 style="margin:18px 0 14px;font-family:'Syne','Plus Jakarta Sans',Arial,Helvetica,sans-serif;font-size:20px;line-height:1.35;font-weight:700;color:#eceef4;">
                <?= htmlspecialchars($heading, ENT_QUOTES, 'UTF-8') ?>
              </h1>
              <div style="font-family:'Plus Jakarta Sans',Arial,Helvetica,sans-serif;font-size:14px;line-height:1.7;color:#c3c9d7;">
                <?= $body ?>
              </div>
              <?php if ($has_btn): ?>
              <table role="presentation" cellpadding="0" cellspacing="0" style="margin:26px 0 2px;">
                <tr>
                  <td align="center" bgcolor="#2f6bff" style="border-radius:8px;">
                    <a href="<?= htmlspecialchars($button['url'], ENT_QUOTES, 'UTF-8') ?>"
                       style="display:inline-block;padding:10px 20px;font-family:'Plus Jakarta Sans',Arial,Helvetica,sans-serif;font-size:13px;line-height:1;font-weight:600;color:#ffffff;text-decoration:none;background:#2f6bff;border-radius:8px;">
                      <?= htmlspecialchars($button['text'], ENT_QUOTES, 'UTF-8') ?>
                    </a>
                  </td>
                </tr>
              </table>
              <?php endif; ?>
            </td>
          </tr>

          <!-- FOOTER -->
          <tr>
            <td align="center" style="padding:20px 16px 4px;font-family:'Plus Jakarta Sans',Arial,Helvetica,sans-serif;font-size:11px;line-height:1.6;color:#677084;">
              PaddockID &middot; Forum komunitas otomotif.<br>
              Email ini dikirim otomatis oleh sistem. Jika bukan kamu, abaikan saja.
            </td>
          </tr>

        </table>
      </td>
    </tr>
  </table>
</body>
</html>