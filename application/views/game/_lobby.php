<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$names_count = isset($names_count) ? (int) $names_count : 23;
$max_tries   = isset($max_tries) ? (int) $max_tries : 5;
$daily_limit = isset($daily_limit) ? (int) $daily_limit : 5;
$daily_used  = isset($daily_used) ? (int) $daily_used : 0;
$daily_remaining = isset($daily_remaining) ? (int) $daily_remaining : $daily_limit;
?>

<div class="card p-6 sm:p-10 text-center">
    <div class="section-title justify-center" style="margin-bottom:10px;">
        <span class="text-micro c-primary" style="letter-spacing:.16em;">Game F1 · Tebak-Tebakan</span>
    </div>
    <h1 class="text-heading c-white" style="font-size:26px;">Guess the Driver</h1>
    <p class="text-caption c-muted" style="margin-top:8px;max-width:460px;margin-left:auto;margin-right:auto;line-height:1.7;">
        Bisa menebak pembalap F1 hanya dari petunjuk? Sebuah pembalap dipilih secara
        rahasia. Kamu punya <strong class="c-primary"><?= $max_tries; ?> percobaan</strong> untuk
        menemukannya dari salah satu dari <?= $names_count; ?> pembalap aktif.
    </p>

    <div class="flex-row justify-center wrap gap-3" style="margin-top:24px;">
        <div class="gd-lobby-item">
            <div class="gd-lobby-item__icon"><i data-lucide="list" style="width:22px;height:22px;"></i></div>
            <div class="gd-lobby-item__text">
                <strong>5 Petunjuk</strong>
                <span>Nomor, win, gelar, tim, lahir, debut, poin, negara — acak tiap game</span>
            </div>
        </div>
        <div class="gd-lobby-item">
            <div class="gd-lobby-item__icon"><i data-lucide="gamepad-2" style="width:22px;height:22px;"></i></div>
            <div class="gd-lobby-item__text">
                <strong>± Cepat</strong>
                <span>Feedback langsung setelah tebak, tanpa tunggu reload</span>
            </div>
        </div>
        <div class="gd-lobby-item">
            <div class="gd-lobby-item__icon"><i data-lucide="trophy" style="width:22px;height:22px;"></i></div>
            <div class="gd-lobby-item__text">
                <strong>Menang = Semua ✓</strong>
                <span>Semua petunjuk cocok dalam ≤5 percobaan dan kamu juara</span>
            </div>
        </div>
    </div>

    <div id="gd-alert" class="gd-alert hidden"></div>

    <button type="button" id="gd-play" class="btn c-primary" style="margin-top:28px;padding:12px 34px;border:1px solid var(--color-primary-border);background:var(--color-primary-bg);font-weight:700;" <?= $daily_remaining <= 0 ? 'disabled' : ''; ?>>
        <i data-lucide="play" style="width:16px;height:16px;" class="inline-block"></i>
        &nbsp;Play
    </button>
    <?php if ($daily_remaining > 0): ?>
        <p class="text-caption c-subtle" style="margin-top:12px;">Kesempatan main hari ini: <strong class="c-primary"><?= $daily_limit - $daily_remaining; ?>/<?= $daily_limit; ?></strong> terpakai.</p>
    <?php else: ?>
        <p class="text-caption c-danger" style="margin-top:12px;font-weight:600;">Batas main harian <?= $daily_limit; ?>x sudah tercapai. Coba lagi besok!</p>
    <?php endif; ?>
    <p class="text-caption c-subtle" style="margin-top:4px;">Butuh akun PaddockID untuk bermain. Cocok buat penggemar sejati.</p>
</div>