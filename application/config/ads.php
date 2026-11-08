<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// PaddockID berjalan TANPA iklan.
//
// Alasannya lisensi: data F1 yang dipakai situs ini berasal dari
// Jolpica/Ergast yang berlisensi CC BY-NC-SA (non-komersial).
// Menampilkan iklan akan bertentangan dengan ketentuan lisensi itu.
//
// Kode iklan pada halaman sudah dihapus total, bukan sekadar dimatikan
// lewat flag. Kalau suatu saat situs ini menjadi komersial, ganti
// sumber pemasannya dengan donasi atau sponsor yang tidak melanggar
// ketentuan non-komersial.

$config['ads_enabled'] = FALSE;

// Google AdSense - dimatikan dan tidak lagi dipakai di view mana pun.
$config['adsense_enabled'] = FALSE;

// Batas per slot, masih dibaca Ads::get_active() supaya endpoint ini
// tidak error kalau diakses langsung.
$config['ads_max_sidebar'] = 1;
$config['ads_max_feed'] = 5;
