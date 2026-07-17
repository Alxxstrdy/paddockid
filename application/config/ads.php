<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$config['ads_enabled'] = TRUE;

// Google AdSense
$config['adsense_enabled'] = TRUE;
$config['adsense_pub_id'] = 'pub-8662865440698442';

// Custom Ads settings
$config['ads_max_sidebar'] = 1;     // Max ads di sidebar
$config['ads_max_feed'] = 5;        // Max ads di feed (per load)
$config['ads_feed_min_gap'] = 5;    // Minimum posts antar iklan
$config['ads_feed_chance'] = 15;    // Peluang % muncul iklan per post (setelah min_gap tercapai)
