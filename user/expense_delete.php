<?php

include '../middleware/auth_check.php';
include '../middleware/role_check.php';
include '../config/database.php';

checkRole('user');

$user_id = $_SESSION['user_id'];
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
    header("Location: expense.php?success=Data pengeluaran berhasil dihapus");
    exit;
} else {
    header("Location: expense.php?error=Data pengeluaran gagal dihapus");
    exit;
}