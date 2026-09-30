<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('link_mentions')) {
    function link_mentions($content) {
        $content = preg_replace(
            '/@(\w+)/',
            '<a href="' . base_url('user/$1') . '" class="text-red-400 hover:text-red-300 font-medium transition-colors">@$1</a>',
            $content
        );
        return $content;
    }
}

if (!function_exists('extract_mentions')) {
    function extract_mentions($content) {
        preg_match_all('/@(\w+)/', $content, $matches);
        return array_unique($matches[1]);
    }
}

if (!function_exists('extract_hashtags')) {
    function extract_hashtags($content) {
        preg_match_all('/(?<!&)#(\w+)/', $content, $matches);
        return array_unique($matches[1]);
    }
}

/**
 * Ubah @mention dan #hashtag menjadi link klik.
 * WAJIB dipanggil SETELAH konten di-htmlspecialchars() supaya aman XSS.
 *
 * - @username  -> /user/{username}
 * - #topik     -> /search?q={topik}
 *
 * Regex hashtag memakai negative lookbehind (?<!&) agar entitas HTML
 * seperti &#039; / &amp; / &#38; TIDAK ikut ter-link (hanya cocok untuk
 * hashtag asli yang dimulai user).
 */
if (!function_exists('linkify_content')) {
    function linkify_content($content) {
        $content = preg_replace(
            '/@([A-Za-z0-9_]+)/',
            '<a href="' . base_url('user/$1') . '" class="post-mention">@$1</a>',
            $content
        );
        $content = preg_replace(
            '/(?<!&)#([A-Za-z0-9_]+)/',
            '<a href="' . base_url('search?q=$1') . '" class="post-hashtag">#$1</a>',
            $content
        );
        return $content;
    }
}