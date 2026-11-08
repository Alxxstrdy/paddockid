<?php defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('is_private_ip')) {
    function is_private_ip($ip) {
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            if ($ip === '::1') return true;
            $first = strtolower(substr($ip, 0, 2));
            if ($first === 'fc' || $first === 'fd') return true;
            if (strtolower(substr($ip, 0, 4)) === 'fe80') return true;
            return false;
        }
        $long = ip2long($ip);
        if ($long === false) return true;
        if (($long >= ip2long('10.0.0.0') && $long <= ip2long('10.255.255.255')) ||
            ($long >= ip2long('172.16.0.0') && $long <= ip2long('172.31.255.255')) ||
            ($long >= ip2long('192.168.0.0') && $long <= ip2long('192.168.255.255')) ||
            ($long >= ip2long('127.0.0.0') && $long <= ip2long('127.255.255.255')) ||
            ($long >= ip2long('169.254.0.0') && $long <= ip2long('169.254.255.255')) ||
            ($long >= ip2long('100.64.0.0') && $long <= ip2long('100.127.255.255'))) {
            return true;
        }
        return false;
    }
}

if (!function_exists('safe_remote_get')) {
    function safe_remote_get($url, $allowed_hosts = array('api.jolpi.ca'), $timeout = 10) {
        $parts = parse_url($url);
        if (!$parts || empty($parts['scheme']) || empty($parts['host'])) return false;
        $scheme = strtolower($parts['scheme']);
        if ($scheme !== 'http' && $scheme !== 'https') return false;
        $host = strtolower($parts['host']);
        $allowed = array_map('strtolower', $allowed_hosts);
        if (!in_array($host, $allowed, true)) return false;
        $ip = gethostbyname($host);
        if ($ip === $host) return false;
        if (is_private_ip($ip)) return false;
        $ctx = stream_context_create(array(
            'http' => array('method' => 'GET', 'timeout' => $timeout, 'header' => "User-Agent: PaddockID/1.0\r\nAccept: application/json\r\n", 'follow_location' => 0, 'max_redirects' => 0),
            'ssl'  => array('verify_peer' => true, 'verify_peer_name' => true)
        ));
        return @file_get_contents($url, false, $ctx);
    }
}
