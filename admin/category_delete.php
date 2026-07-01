<?php

include '../middleware/auth_check.php';
include '../middleware/role_check.php';
include '../config/database.php';

checkRole('admin');

$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: categories.php?error=Kategori tidak ditemukan");
    exit;
}

$query = "DELETE FROM categories WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $id);

if (mysqli_stmt_execute($stmt)) {
    header("Location: categories.php?success=Kategori berhasil dihapus");
    exit;
} else {
    header("Location: categories.php?error=Kategori gagal dihapus. Kemungkinan kategori masih digunakan oleh transaksi.");
    exit;
}