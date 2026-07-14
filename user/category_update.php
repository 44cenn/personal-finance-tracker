<?php
require_once '../config/bootstrap.php';

checkRole('user');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: categories.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$id = $_POST['id'] ?? '';
$name = trim($_POST['name'] ?? '');
$type = $_POST['type'] ?? '';

if (empty($id) || empty($name) || !in_array($type, ['income', 'expense'])) {
    header("Location: categories.php?error=Data tidak lengkap atau tidak valid.");
    exit;
}

// Update kategori, pastikan hanya mengubah kategori milik user yang login
$query = "UPDATE categories SET name = ?, type = ? WHERE id = ? AND user_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "ssii", $name, $type, $id, $user_id);

if (mysqli_stmt_execute($stmt) && mysqli_stmt_affected_rows($stmt) > 0) {
    header("Location: categories.php?success=Kategori berhasil diperbarui.");
} else {
    header("Location: categories.php?success=Tidak ada perubahan data atau gagal memperbarui.");
}
exit;