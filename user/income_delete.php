<?php
require_once '../config/bootstrap.php';

checkRole('user');
$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: income.php?error=Data tidak ditemukan");
    exit;
}

$query = "
    DELETE FROM transactions
    WHERE id = ?
    AND user_id = ?
    AND type = 'income'
";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "ii", $id, $user_id);

if (mysqli_stmt_execute($stmt)) {
    if (mysqli_stmt_affected_rows($stmt) > 0) {
        header("Location: income.php?success=Data pemasukan berhasil dihapus");
    } else {
        header("Location: income.php?error=Data tidak ditemukan atau bukan milik Anda");
    }
    exit;
} else {
    header("Location: income.php?error=Data pemasukan gagal dihapus");
    exit;
}