<?php if (!function_exists('assets_url')) {
    function assets_url($path = '') {
        $cdn = 'https://cdn.paddockid.web.id/';
        if ($path === '') {
            return $cdn;
        }
        if (strpos($path, 'http') === 0) {
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
        $cdn = 'https://cdn.paddockid.web.id/';
        if (empty($path)) return $cdn . 'uploads/default.jpg';
        if (strpos($path, 'http') === 0) return $path;
        if (strpos($path, 'uploads/') === 0) return $cdn . $path;
        if (strpos($path, 'assets/') === 0) return $cdn . $path;
        return $cdn . 'uploads/' . $path;
    }
}
