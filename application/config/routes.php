<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$route['default_controller'] = 'home';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
$route['post/report/(:num)'] = 'post/report/$1';
$route['post/(:any)/(:num)'] = 'post/index/$1/$2';
