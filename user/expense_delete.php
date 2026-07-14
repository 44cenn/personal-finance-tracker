<?php
require_once '../config/bootstrap.php';

checkRole('user');
$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: expense.php?error=Data tidak ditemukan");
    exit;
}

$query = "
    DELETE FROM transactions
    WHERE id = ?
    AND user_id = ?
    AND type = 'expense'
";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "ii", $id, $user_id);

if (mysqli_stmt_execute($stmt)) {
    if (mysqli_stmt_affected_rows($stmt) > 0) {
        header("Location: expense.php?success=Data pengeluaran berhasil dihapus");
    } else {
        header("Location: expense.php?error=Data tidak ditemukan atau bukan milik Anda");
    }
    exit;
} else {
    header("Location: expense.php?error=Data pengeluaran gagal dihapus");
    exit;
}