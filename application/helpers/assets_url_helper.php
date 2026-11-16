<?php
if (!function_exists('assets_url')) {
    function assets_url($path = '') {
        // Local/dev: serve from this app so localhost works without CDN
        $is_local = (ENVIRONMENT === 'development')
            || php_sapi_name() === 'cli'
            || in_array($_SERVER['HTTP_HOST'] ?? '', ['localhost', '127.0.0.1', '::1'], true)
            || (strpos($_SERVER['HTTP_HOST'] ?? '', 'localhost:') === 0)
            || (strpos($_SERVER['HTTP_HOST'] ?? '', '127.0.0.1:') === 0);
        $cdn = $is_local ? rtrim(base_url(), '/') . '/' : 'https://cdn.paddockid.web.id/';
        if ($path === '') {
            return $cdn;
        }
        if (strpos($path, 'http://') === 0 || strpos($path, 'https://') === 0 || strpos($path, '//') === 0) {
            return $path;
        }
        if (strpos($path, 'uploads/') === 0 || strpos($path, 'assets/') === 0) {
            return $cdn . $path;
        }
        return $cdn . 'uploads/' . $path;
    }
}

if (!function_exists('avatar_url')) {
    function avatar_url($path) {
        $is_local = (ENVIRONMENT === 'development')
            || php_sapi_name() === 'cli'
            || in_array($_SERVER['HTTP_HOST'] ?? '', ['localhost', '127.0.0.1', '::1'], true)
            || (strpos($_SERVER['HTTP_HOST'] ?? '', 'localhost:') === 0)
            || (strpos($_SERVER['HTTP_HOST'] ?? '', '127.0.0.1:') === 0);
        $cdn = $is_local ? rtrim(base_url(), '/') . '/' : 'https://cdn.paddockid.web.id/';
        if (empty($path)) return $cdn . 'uploads/default.jpg';
        if (strpos($path, 'http://') === 0 || strpos($path, 'https://') === 0 || strpos($path, '//') === 0) return $path;
        if (strpos($path, 'uploads/') === 0) return $cdn . $path;
        if (strpos($path, 'assets/') === 0) return $cdn . $path;
        return $cdn . 'uploads/' . $path;
    }
}
