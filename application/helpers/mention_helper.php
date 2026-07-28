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
