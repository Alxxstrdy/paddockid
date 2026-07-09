<?php
if (!function_exists('assets_url')) {
    function assets_url($path = '') {
        if (strpos($path, 'uploads/') === 0 || strpos($path, 'http') === 0) {
            return base_url($path);
        }
        return base_url('uploads/' . $path);
    }
}

if (!function_exists('avatar_url')) {
    function avatar_url($path) {
        if (empty($path)) return base_url('uploads/default.jpg');
        if (strpos($path, 'http') === 0) return $path;
        if (strpos($path, 'uploads/') === 0) return base_url($path);
        return base_url('uploads/' . $path);
    }
}
