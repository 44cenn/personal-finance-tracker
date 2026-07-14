<?php

/*
|--------------------------------------------------------------------------
| Penyimpanan terjemahan aktif
|--------------------------------------------------------------------------
*/

$translations = [];

/*
|--------------------------------------------------------------------------
| Memuat file bahasa
|--------------------------------------------------------------------------
*/

function load_language(string $language = 'id'): void
{
    global $translations;

    $supportedLanguages = [
        'id',
        'en'
    ];

    if (!in_array($language, $supportedLanguages, true)) {
        $language = 'id';
    }

    $languageFile = __DIR__ . '/' . $language . '.php';

    if (is_file($languageFile)) {
        $loadedTranslations = require $languageFile;

        if (is_array($loadedTranslations)) {
            $translations = $loadedTranslations;
            return;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Fallback Bahasa Indonesia
    |--------------------------------------------------------------------------
    */

    $fallbackFile = __DIR__ . '/id.php';

    if (is_file($fallbackFile)) {
        $fallbackTranslations = require $fallbackFile;

        if (is_array($fallbackTranslations)) {
            $translations = $fallbackTranslations;
            return;
        }
    }

    $translations = [];
}

/*
|--------------------------------------------------------------------------
| Helper terjemahan
|--------------------------------------------------------------------------
|
| Contoh:
| __('income_data')
|
| Dengan parameter:
| __('welcome_message', null, ['name' => 'Acenn'])
|
*/

function __(
    string $key,
    ?string $default = null,
    array $replacements = []
): string {
    global $translations;

    $fallbackText = $default;

    if ($fallbackText === null) {
        $fallbackText = ucfirst(
            str_replace('_', ' ', $key)
        );
    }

    $text = $translations[$key] ?? $fallbackText;

    foreach ($replacements as $placeholder => $value) {
        $text = str_replace(
            '{' . $placeholder . '}',
            (string) $value,
            $text
        );
    }

    return $text;
}