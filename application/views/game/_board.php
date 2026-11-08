<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$game       = isset($game) ? $game : [];
$clues_def  = isset($clues_def) ? $clues_def : [];
$names      = isset($names) ? (array) $names : [];
$max_tries  = isset($max_tries) ? (int) $max_tries : 5;
$used_tries = isset($used_tries) ? (int) $used_tries : count($game['tries'] ?? []);
$remaining  = max(0, $max_tries - $used_tries);
$clues      = isset($game['clues']) ? $game['clues'] : [];

$fb_icon = ['same' => '✓', 'diff' => '✗', 'higher' => '↑', 'lower' => '↓'];
$fb_tip  = [
    'same'   => 'Sama dengan jawaban',
    'diff'   => 'Berbeda dari jawaban',
    'higher' => 'Jawaban lebih tinggi',
    'lower'  => 'Jawaban lebih rendah',
];
?>

<div class="card p-4 sm:p-8">
    <div class="flex-row justify-between items-start gap-3 mb-2">
        <div class="min-w-0">
            <div class="section-title" style="margin-bottom:6px;">
                <span class="text-micro c-primary" style="letter-spacing:.14em;">Game F1</span>
            </div>
            <h1 class="text-heading c-white" style="font-size:20px;">Guess the Driver</h1>
            <p class="text-caption c-muted" style="margin-top:4px;">Tebak pembalap F1 dari petunjuk acak. Jawabannya satu dari <?= isset($names_count) ? (int) $names_count : count($names); ?> pembalap aktif. Gunakan ↑/↓ untuk mempersempit pilihanmu.</p>
        </div>
        <span class="gd-badge" style="flex-shrink:0;">
            <span class="gd-dot"></span>
            <?= $remaining; ?>/<?= $max_tries; ?> percobaan
        </span>
    </div>

    <div id="gd-alert" class="gd-alert hidden"></div>

    <?php if (!empty($game['over'])): ?>
        <?php if (!empty($game['won'])): ?>
            <div class="gd-result gd-result--win" id="gd-result">
                <div class="gd-result__icon">🏆</div>
                <h2 class="text-heading c-white" style="font-size:18px;margin-top:8px;">Benar! Menang dalam <?= $used_tries; ?>/<?= $max_tries; ?> percobaan</h2>
                <p class="text-caption c-success mt-2 font-semibold">Pembalapnya: <?= htmlspecialchars($game['answer']['answer'], ENT_QUOTES, 'UTF-8'); ?></p>
                <?php if (!empty($game['reward'])): ?>
                    <div class="gd-reward mt-3 p-3 flex-row justify-center items-center gap-2" style="background:var(--color-success-bg);border:1px solid var(--color-success-border);border-radius:var(--radius-md);">
                        <span class="text-caption font-semibold c-success">+<?= (int) $game['reward']; ?> koin</span>
                        <span class="text-micro c-subtle">(Clue <?= $used_tries; ?>)</span>
                    </div>
                <?php endif; ?>
                <div class="flex-row justify-center gap-2 mt-4 flex-wrap">
                    <button type="button" class="btn c-primary" data-gd-action="restart" style="border:1px solid var(--color-primary-border);background:var(--color-primary-bg);">
                        <i data-lucide="play" style="width:14px;height:14px;" class="inline-block"></i> Main Lagi
                    </button>
                    <button type="button" class="btn" data-gd-action="quit">Kembali ke Lobby</button>
                </div>
            </div>
        <?php else: ?>
            <div class="gd-result gd-result--lose" id="gd-result">
                <h2 class="text-heading c-white" style="font-size:18px;">Game over — percobaanmu habis</h2>
                <p class="text-caption c-danger mt-2 font-semibold">Jawabannya: <?= htmlspecialchars($game['answer']['answer'], ENT_QUOTES, 'UTF-8'); ?></p>
                <div class="flex-row justify-center gap-2 mt-4 flex-wrap">
                    <button type="button" class="btn c-primary" data-gd-action="restart" style="border:1px solid var(--color-primary-border);background:var(--color-primary-bg);">
                        <i data-lucide="rotate-ccw" style="width:14px;height:14px;" class="inline-block"></i> Coba Lagi
                    </button>
                    <button type="button" class="btn" data-gd-action="quit">Kembali ke Lobby</button>
                </div>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <form id="gd-guess-form" class="mt-5" novalidate>
            <div class="gd-guess-row">
                <div class="gd-guess-input-wrap">
                    <input list="gd-drivers" name="driver" id="gd-guess-input" class="input" placeholder="Ketik nama pembalap…" autocomplete="off" required>
                    <datalist id="gd-drivers">
                        <?php foreach ($names as $n): ?>
                            <option value="<?= htmlspecialchars($n, ENT_QUOTES, 'UTF-8'); ?>"></option>
                        <?php endforeach; ?>
                    </datalist>
                </div>
                <button type="submit" class="btn c-primary" style="border:1px solid var(--color-primary-border);background:var(--color-primary-bg);flex:0 0 auto;">
                    Tebak <i data-lucide="target" style="width:14px;height:14px;" class="inline-block"></i>
                </button>
            </div>
        </form>
    <?php endif; ?>

    <?php if (!empty($clues)): ?>
        <div class="mt-6">
            <p class="text-micro c-subtle mb-2" style="letter-spacing:.14em;text-transform:uppercase;">Petunjuk</p>
            <div class="flex-row wrap gap-2">
                <?php foreach ($clues as $k): ?>
                    <span class="gd-badge gd-badge--ghost">
                        <?= isset($clues_def[$k]['label']) ? $clues_def[$k]['label'] : $k; ?>
                    </span>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if (!empty($game['tries'])): ?>
        <div class="custom-scrollbar" style="overflow-x:auto;margin-top:20px;">
            <table class="table-auto w-full text-sm" style="min-width:520px;border-collapse:collapse;">
                <thead>
                    <tr>
                        <th style="text-align:left;padding:8px 10px;font-size:11px;letter-spacing:.1em;text-transform:uppercase;color:var(--text-subtle);border-bottom:1px solid var(--color-border);">#</th>
                        <th style="text-align:left;padding:8px 10px;font-size:11px;letter-spacing:.1em;text-transform:uppercase;color:var(--text-subtle);border-bottom:1px solid var(--color-border);">Tebakan</th>
                        <?php foreach ($clues as $k): ?>
                            <th style="text-align:center;padding:8px 10px;font-size:11px;letter-spacing:.1em;text-transform:uppercase;color:var(--text-subtle);border-bottom:1px solid var(--color-border);">
                                <?= isset($clues_def[$k]['label']) ? $clues_def[$k]['label'] : $k; ?>
                            </th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($game['tries'] as $i => $t): ?>
                        <tr style="border-bottom:1px solid var(--color-border);">
                            <td style="padding:10px;color:var(--text-subtle);font-weight:600;"><?= $i + 1; ?></td>
                            <td style="padding:10px;font-weight:700;color:var(--text-primary);white-space:nowrap;"><?= htmlspecialchars($t['name'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <?php foreach ($clues as $k): ?>
                                <?php
                                    $fb = isset($t['fb'][$k]) ? $t['fb'][$k] : 'diff';
                                    $color = $fb === 'same' ? 'var(--color-success)'
                                            : ($fb === 'diff' ? 'var(--text-subtle)'
                                            : 'var(--color-warning)');
                                ?>
                                <td style="text-align:center;padding:10px;">
                                    <span title="<?= isset($fb_tip[$fb]) ? $fb_tip[$fb] : ''; ?>" style="color:<?= $color; ?>;font-weight:700;"><?= $fb_icon[$fb] ?? '·'; ?></span>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <div class="mt-6" style="border-top:1px solid var(--color-border);padding-top:16px;">
        <p class="text-caption c-muted" style="line-height:1.6;">
            <strong class="c-primary">↑ Jawaban lebih tinggi</strong> &nbsp;·&nbsp;
            <strong class="c-primary">↓ Jawaban lebih rendah</strong> &nbsp;·&nbsp;
            <strong class="c-success">✓ Sama</strong> &nbsp;·&nbsp;
            <strong class="c-subtle">✗ Beda</strong>
            &nbsp;— untuk petunjuk teks, hanya ✓/✗ yang muncul.
        </p>
        <p class="text-caption c-muted mt-2">Nilai pasti jawaban tidak pernah ditampilkan — kamu menebak dari angka perbandingan. Selamat mencoba!</p>
    </div>
</div>