<?php
require_once '../config/bootstrap.php';

checkRole('user');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: profile.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$current_password = $_POST['current_password'] ?? '';
$new_password = $_POST['new_password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
    header("Location: profile.php?error=Semua field password wajib diisi");
    exit;
}

if (strlen($new_password) < 6) {
    header("Location: profile.php?error=Password baru minimal 6 karakter");
    exit;
}

if ($new_password !== $confirm_password) {
    header("Location: profile.php?error=Konfirmasi password baru tidak cocok");
    exit;
}

// Ambil password hash saat ini dari database
$query = "SELECT password FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

// Verifikasi password saat ini
if (!password_verify($current_password, $user['password'])) {
    header("Location: profile.php?error=Password saat ini salah");
    exit;
}

// Hash password baru
$hashedPassword = password_hash($new_password, PASSWORD_DEFAULT);

// Update password di database
$updateQuery = "UPDATE users SET password = ? WHERE id = ?";
$stmt = mysqli_prepare($conn, $updateQuery);
mysqli_stmt_bind_param($stmt, "si", $hashedPassword, $user_id);

if (mysqli_stmt_execute($stmt)) {
    header("Location: profile.php?success=Password berhasil diubah");
} else {
    header("Location: profile.php?error=Gagal mengubah password");
}
exit;