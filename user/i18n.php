<?php
$translations = [];

function load_language($lang = 'id') {
    global $translations;
    $lang_file = __DIR__ . "/{$lang}.php";

    if (file_exists($lang_file)) {
        $translations = include $lang_file;
    } else {
        // Fallback ke Bahasa Inggris jika file terjemahan tidak ditemukan
        $fallback_file = __DIR__ . "/en.php";
        if(file_exists($fallback_file)) {
            $translations = include $fallback_file;
        }
    }
}

function __($key, $default = null) {
    global $translations;
    $default_value = $default ?? str_replace('_', ' ', ucfirst($key));
    return $translations[$key] ?? $default_value;
}