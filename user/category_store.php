<?php
require_once '../config/bootstrap.php';

checkRole('user');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: categories.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$name = trim($_POST['name'] ?? '');
$type = $_POST['type'] ?? '';

if (empty($name) || !in_array($type, ['income', 'expense'])) {
    header("Location: categories.php?error=Nama dan tipe kategori tidak valid.");
    exit;
}

// Simpan kategori baru
$query = "INSERT INTO categories (name, type, user_id) VALUES (?, ?, ?)";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "ssi", $name, $type, $user_id);

if (mysqli_stmt_execute($stmt)) {
    header("Location: categories.php?success=Kategori baru berhasil ditambahkan.");
} else {
    header("Location: categories.php?error=Gagal menambahkan kategori.");
}
exit;