<?php
require_once '../config/bootstrap.php';

checkRole('user');
$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: categories.php?error=Kategori tidak ditemukan.");
    exit;
}

// Cek apakah kategori sedang digunakan oleh transaksi
$checkQuery = "SELECT COUNT(*) as count FROM transactions WHERE category_id = ?";
$checkStmt = mysqli_prepare($conn, $checkQuery);
mysqli_stmt_bind_param($checkStmt, "i", $id);
mysqli_stmt_execute($checkStmt);
$result = mysqli_stmt_get_result($checkStmt);
$row = mysqli_fetch_assoc($result);

if ($row['count'] > 0) {
    header("Location: categories.php?error=Kategori tidak bisa dihapus karena masih digunakan oleh transaksi.");
    exit;
}

// Hapus kategori, pastikan hanya menghapus kategori milik user yang login
$deleteQuery = "DELETE FROM categories WHERE id = ? AND user_id = ?";
$deleteStmt = mysqli_prepare($conn, $deleteQuery);
mysqli_stmt_bind_param($deleteStmt, "ii", $id, $user_id);

if (mysqli_stmt_execute($deleteStmt)) {
    if (mysqli_stmt_affected_rows($deleteStmt) > 0) {
        header("Location: categories.php?success=Kategori berhasil dihapus.");
    } else {
        header("Location: categories.php?error=Kategori tidak ditemukan atau Anda tidak punya hak akses.");
    }
} else {
    header("Location: categories.php?error=Gagal menghapus kategori.");
}
exit;