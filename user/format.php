<?php
function format_currency($amount, $currency_code = 'IDR') {
    // Fallback untuk sistem tanpa ekstensi intl
    if (!class_exists('NumberFormatter')) {
        if ($currency_code === 'IDR') {
            return 'Rp ' . number_format($amount, 0, ',', '.');
        }
        // Fallback sederhana untuk mata uang lain
        return $currency_code . ' ' . number_format($amount, 2, '.', ',');
    }

    // Menggunakan locale 'en' agar simbol mata uang (seperti $) berada di depan
    // dan menggunakan format yang umum secara internasional.
    $formatter = new NumberFormatter('en_US', NumberFormatter::CURRENCY);
    // Fungsi formatCurrency akan menggunakan simbol yang benar berdasarkan kode mata uang.
    return $formatter->formatCurrency($amount, $currency_code);
}