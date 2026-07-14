<?php
require_once '../config/bootstrap.php';

checkRole('user');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: profile.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');

if (empty($name) || empty($email)) {
    header("Location: profile.php?error=Nama dan email tidak boleh kosong");
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: profile.php?error=Format email tidak valid");
    exit;
}

// Cek apakah email baru sudah digunakan oleh user lain
$checkEmailQuery = "SELECT id FROM users WHERE email = ? AND id != ?";
$stmt = mysqli_prepare($conn, $checkEmailQuery);
mysqli_stmt_bind_param($stmt, "si", $email, $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) > 0) {
    header("Location: profile.php?error=Email sudah digunakan oleh akun lain");
    exit;
}

// Update data user
$updateQuery = "UPDATE users SET name = ?, email = ? WHERE id = ?";
$stmt = mysqli_prepare($conn, $updateQuery);
mysqli_stmt_bind_param($stmt, "ssi", $name, $email, $user_id);

if (mysqli_stmt_execute($stmt)) {
    // Update session
    $_SESSION['name'] = $name;
    $_SESSION['email'] = $email;
    header("Location: profile.php?success=Informasi profil berhasil diperbarui");
} else {
    header("Location: profile.php?error=Gagal memperbarui profil");
}
exit;