<?php

session_start();
include '../config/database.php';

// 1. Hanya izinkan metode POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: register.php");
    exit;
}

// 2. Ambil dan bersihkan input
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

// 3. Validasi Server-Side
if (empty($name) || empty($email) || empty($password)) {
    header("Location: register.php?error=Semua field wajib diisi");
    exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: register.php?error=Format email tidak valid");
    exit;
}
if (strlen($password) < 6) {
    header("Location: register.php?error=Password minimal 6 karakter");
    exit;
}

// 4. Cek apakah email sudah terdaftar (Gunakan Prepared Statement)
$checkQuery = "SELECT id FROM users WHERE email = ?";
$stmt = mysqli_prepare($conn, $checkQuery);
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) > 0) {
    header("Location: register.php?error=Email sudah terdaftar. Silakan gunakan email lain.");
    exit;
}

// 5. Hash password untuk keamanan
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// 6. Simpan pengguna baru ke database (Gunakan Prepared Statement)
$insertQuery = "INSERT INTO users (name, email, password) VALUES (?, ?, ?)";
$stmt = mysqli_prepare($conn, $insertQuery);
mysqli_stmt_bind_param($stmt, "sss", $name, $email, $hashedPassword);

if (mysqli_stmt_execute($stmt)) {
    header("Location: login.php?success=Registrasi berhasil. Silakan login.");
    exit;
} else {
    header("Location: register.php?error=Terjadi kesalahan. Registrasi gagal.");
    exit;
}