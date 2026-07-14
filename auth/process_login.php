<?php

session_start();
include '../config/database.php';

// 1. Hanya izinkan metode POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.php");
    exit;
}

// 2. Ambil dan bersihkan input
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

// 3. Validasi dasar
if (empty($email) || empty($password)) {
    header("Location: login.php?error=Email dan password wajib diisi");
    exit;
}

// 4. Cari pengguna berdasarkan email (Gunakan Prepared Statement)
$query = "SELECT * FROM users WHERE email = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// 5. Jika email tidak ditemukan, berikan pesan error yang umum
if (mysqli_num_rows($result) !== 1) {
    header("Location: login.php?error=Email atau password salah");
    exit;
}

$user = mysqli_fetch_assoc($result);

// 6. Verifikasi password dengan hash di database
if (!password_verify($password, $user['password'])) {
    // Jika password salah, berikan pesan error yang sama agar aman
    header("Location: login.php?error=Email atau password salah");
    exit;
}

// 7. Login berhasil! Buat session untuk pengguna
$_SESSION['user_id'] = $user['id'];
$_SESSION['name'] = $user['name'];
$_SESSION['email'] = $user['email'];
$_SESSION['role'] = $user['role'];
$_SESSION['theme'] = $user['theme'];
$_SESSION['currency'] = $user['currency'];
$_SESSION['language'] = $user['language'];

// 8. Arahkan ke dashboard
if ($user['role'] === 'admin') {
    header("Location: ../admin/dashboard.php");
} else {
    header("Location: ../index.php");
}
exit;