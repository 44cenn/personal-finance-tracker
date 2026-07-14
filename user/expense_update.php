<?php
require_once '../config/bootstrap.php';

checkRole('user');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: expense.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$id = $_POST['id'] ?? '';
$category_id = $_POST['category_id'] ?? '';
$amount = $_POST['amount'] ?? '';
$transaction_date = $_POST['transaction_date'] ?? '';
$description = trim($_POST['description'] ?? '');

if (empty($id) || empty($category_id) || empty($amount) || empty($transaction_date)) {
    header("Location: expense_edit.php?id=$id&error=Semua field wajib diisi kecuali keterangan");
    exit;
}

if (!is_numeric($amount) || $amount <= 0) {
    header("Location: expense_edit.php?id=$id&error=Jumlah pengeluaran harus angka dan lebih dari 0");
    exit;
}

$dateCheck = DateTime::createFromFormat('Y-m-d', $transaction_date);
if (!$dateCheck || $dateCheck->format('Y-m-d') !== $transaction_date) {
    header("Location: expense_edit.php?id=$id&error=Format tanggal tidak valid");
    exit;
}

$categoryCheckQuery = "SELECT id FROM categories WHERE id = ? AND type = 'expense' AND (user_id IS NULL OR user_id = ?)";
$categoryStmt = mysqli_prepare($conn, $categoryCheckQuery);
mysqli_stmt_bind_param($categoryStmt, "ii", $category_id, $user_id);
mysqli_stmt_execute($categoryStmt);
$categoryResult = mysqli_stmt_get_result($categoryStmt);

if (mysqli_num_rows($categoryResult) !== 1) {
    header("Location: expense_edit.php?id=$id&error=Kategori pengeluaran tidak valid");
    exit;
}

$query = "
    UPDATE transactions
    SET category_id = ?, amount = ?, description = ?, transaction_date = ?
    WHERE id = ? AND user_id = ? AND type = 'expense'
";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "idssii", $category_id, $amount, $description, $transaction_date, $id, $user_id);

if (mysqli_stmt_execute($stmt)) {
    if (mysqli_stmt_affected_rows($stmt) > 0) {
        header("Location: expense.php?success=Data pengeluaran berhasil diperbarui");
    } else {
        header("Location: expense.php?success=Tidak ada perubahan data");
    }
    exit;
} else {
    header("Location: expense_edit.php?id=$id&error=Data pengeluaran gagal diperbarui");
    exit;
}