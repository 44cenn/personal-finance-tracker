<?php
// 1. Mulai session jika belum ada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Cek otentikasi pengguna
if (!isset($_SESSION['user_id'])) {
    // Gunakan path absolut dari root web untuk pengalihan yang andal
    header("Location: /personal-finance-tracker/auth/login.php?error=Anda harus login untuk mengakses halaman ini");
    exit;
}

// Make user_id available globally for pages that include bootstrap.php
$user_id = $_SESSION['user_id'];

// 3. Sertakan file-file penting
require_once __DIR__ . '/../middleware/role_check.php';
require_once __DIR__ . '/database.php';

// 4. Muat Helper (Adjusted path based on context)
require_once __DIR__ . '/../user/format.php';
require_once __DIR__ . '/../user/i18n.php';

// 5. Muat pengaturan pengguna dari session
// Pengaturan ini diatur saat login (process_login.php) dan saat diupdate (settings_update.php)
$current_theme = $_SESSION['theme'] ?? 'default';
$current_currency = $_SESSION['currency'] ?? 'IDR';
$current_language = $_SESSION['language'] ?? 'id';

// 6. Muat file bahasa yang sesuai dengan pilihan pengguna
load_language($current_language);

// 7. Ambil data user yang sedang login untuk digunakan di banyak halaman
$queryUserBootstrap = "SELECT * FROM users WHERE id = ?";
$stmtUserBootstrap = mysqli_prepare($conn, $queryUserBootstrap);

// Pengecekan error dasar untuk mysqli_prepare
if ($stmtUserBootstrap === false) {
    // Kemungkinan besar masalah koneksi atau SQL.
    die("Fatal Error: Gagal mempersiapkan statement database. Error: " . mysqli_error($conn));
}

mysqli_stmt_bind_param($stmtUserBootstrap, "i", $_SESSION['user_id']);
mysqli_stmt_execute($stmtUserBootstrap);

// Pengecekan penting: fungsi mysqli_stmt_get_result() memerlukan driver mysqlnd.
if (!function_exists('mysqli_stmt_get_result')) {
    die("Fatal Error: Fungsi mysqli_stmt_get_result() tidak ditemukan. Harap pastikan ekstensi PHP 'mysqlnd' telah terinstal dan aktif di file php.ini Anda.");
}

$resultUserBootstrap = mysqli_stmt_get_result($stmtUserBootstrap);
$user = mysqli_fetch_assoc($resultUserBootstrap);

if (!$user) {
    // Jika user tidak ditemukan di database (misalnya sudah dihapus), hancurkan sesi dan paksa login ulang.
    session_unset();
    session_destroy();
    header("Location: /personal-finance-tracker/auth/login.php?error=Sesi tidak valid, silakan login kembali.");
    exit;
}