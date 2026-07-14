<?php
require_once '../config/bootstrap.php';
checkRole('user');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: settings.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// --- Validasi Input ---
$theme = $_POST['theme'] ?? 'default';
$currency = $_POST['currency'] ?? 'IDR';
$language = $_POST['language'] ?? 'id';

// Daftar nilai yang diizinkan untuk keamanan
$allowed_themes = [
    'default', 'dark', 'ocean', 'forest', 'sunset', 
    'royal', 'mono', 'pastel', 'crimson', 'golden'
];
$allowed_currencies = [
    'IDR', 'USD', 'EUR', 'JPY', 'GBP', 'AUD', 
    'CAD', 'CHF', 'CNY', 'INR'
];
$allowed_languages = [
    'id', 'en' // Tambahkan kode bahasa lain jika sudah diimplementasikan
];

if (!in_array($theme, $allowed_themes)) {
    header("Location: settings.php?error=Tema tidak valid.");
    exit;
}
if (!in_array($currency, $allowed_currencies)) {
    header("Location: settings.php?error=Mata uang tidak valid.");
    exit;
}
if (!in_array($language, $allowed_languages)) {
    header("Location: settings.php?error=Bahasa tidak valid.");
    exit;
}

// --- Update Database ---
$query = "UPDATE users SET theme = ?, currency = ?, language = ? WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);

if (!$stmt) {
    header("Location: settings.php?error=Gagal mempersiapkan statement database.");
    exit;
}

mysqli_stmt_bind_param($stmt, "sssi", $theme, $currency, $language, $user_id);

if (mysqli_stmt_execute($stmt)) {
    // --- Update Session ---
    $_SESSION['theme'] = $theme;
    $_SESSION['currency'] = $currency;
    $_SESSION['language'] = $language;

    header("Location: settings.php?success=Pengaturan berhasil disimpan.");
} else {
    header("Location: settings.php?error=Gagal menyimpan pengaturan ke database.");
}

exit;