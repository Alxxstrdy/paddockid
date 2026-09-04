<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$config['pusher_app_id']  = getenv('PUSHER_APP_ID');
$config['pusher_key']     = getenv('PUSHER_KEY');
$config['pusher_secret']  = getenv('PUSHER_SECRET');
$config['pusher_cluster'] = getenv('PUSHER_CLUSTER') ?: 'ap1';
