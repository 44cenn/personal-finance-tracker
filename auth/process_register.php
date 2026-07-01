<?php

session_start();
include '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: register.php");
    exit;
}

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($name) || empty($email) || empty($password)) {
    header("Location: register.php?error=Semua field wajib diisi");
    exit;
}

if (strlen($name) < 3) {
    header("Location: register.php?error=Nama minimal 3 karakter");
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

$checkQuery = "SELECT id FROM users WHERE email = ?";
$stmt = mysqli_prepare($conn, $checkQuery);
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) > 0) {
    header("Location: register.php?error=Email sudah terdaftar");
    exit;
}

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
$role = "user";

$insertQuery = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)";
$stmt = mysqli_prepare($conn, $insertQuery);
mysqli_stmt_bind_param($stmt, "ssss", $name, $email, $hashedPassword, $role);

if (mysqli_stmt_execute($stmt)) {
    header("Location: login.php?success=Registrasi berhasil, silakan login");
    exit;
} else {
    header("Location: register.php?error=Registrasi gagal");
    exit;
}