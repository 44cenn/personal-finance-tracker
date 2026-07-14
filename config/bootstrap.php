<?php

/*
|--------------------------------------------------------------------------
| Session
|--------------------------------------------------------------------------
*/

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/*
|--------------------------------------------------------------------------
| File penting
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/../middleware/role_check.php';
require_once __DIR__ . '/database.php';

require_once __DIR__ . '/../user/format.php';
require_once __DIR__ . '/../user/i18n.php';

/*
|--------------------------------------------------------------------------
| Pengaturan pengguna dari session
|--------------------------------------------------------------------------
*/

$current_theme = $_SESSION['theme'] ?? 'default';
$current_currency = $_SESSION['currency'] ?? 'IDR';
$current_language = $_SESSION['language'] ?? 'id';

/*
|--------------------------------------------------------------------------
| Validasi nilai pengaturan
|--------------------------------------------------------------------------
*/

$allowedThemes = [
    'default',
    'dark'
];

$allowedCurrencies = [
    'IDR',
    'USD',
    'EUR',
    'JPY',
    'SGD',
    'MYR'
];

$allowedLanguages = [
    'id',
    'en'
];

if (!in_array($current_theme, $allowedThemes, true)) {
    $current_theme = 'default';
}

if (!in_array($current_currency, $allowedCurrencies, true)) {
    $current_currency = 'IDR';
}

if (!in_array($current_language, $allowedLanguages, true)) {
    $current_language = 'id';
}

/*
|--------------------------------------------------------------------------
| Muat file bahasa
|--------------------------------------------------------------------------
*/

load_language($current_language);

/*
|--------------------------------------------------------------------------
| Cek autentikasi
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION['user_id'])) {
    $errorMessage = urlencode(__('login_required'));

    header(
        "Location: /personal-finance-tracker/auth/login.php?error={$errorMessage}"
    );
    exit;
}

$user_id = (int) $_SESSION['user_id'];

/*
|--------------------------------------------------------------------------
| Ambil data user aktif
|--------------------------------------------------------------------------
*/

$queryUserBootstrap = "
    SELECT *
    FROM users
    WHERE id = ?
    LIMIT 1
";

$stmtUserBootstrap = mysqli_prepare($conn, $queryUserBootstrap);

if ($stmtUserBootstrap === false) {
    die(
        'Fatal Error: Gagal mempersiapkan statement database. Error: '
        . mysqli_error($conn)
    );
}

mysqli_stmt_bind_param(
    $stmtUserBootstrap,
    'i',
    $user_id
);

mysqli_stmt_execute($stmtUserBootstrap);

if (!function_exists('mysqli_stmt_get_result')) {
    die(
        "Fatal Error: Fungsi mysqli_stmt_get_result() tidak ditemukan. "
        . "Pastikan ekstensi PHP mysqlnd telah aktif."
    );
}

$resultUserBootstrap = mysqli_stmt_get_result($stmtUserBootstrap);

$user = mysqli_fetch_assoc($resultUserBootstrap);

mysqli_stmt_close($stmtUserBootstrap);

/*
|--------------------------------------------------------------------------
| Validasi user
|--------------------------------------------------------------------------
*/

if (!$user) {
    session_unset();
    session_destroy();

    $errorMessage = urlencode(__('invalid_session'));

    header(
        "Location: /personal-finance-tracker/auth/login.php?error={$errorMessage}"
    );
    exit;
}