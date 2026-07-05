<?php
if (!function_exists('js_url')) {
    function js_url($path = '') {
        $CI =& get_instance();
        return $CI->config->item('js_url') . $path;
    }
}