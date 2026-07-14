<?php
require_once '../config/bootstrap.php';

checkRole('user');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: income.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$id = $_POST['id'] ?? '';
$category_id = $_POST['category_id'] ?? '';
$amount = $_POST['amount'] ?? '';
$transaction_date = $_POST['transaction_date'] ?? '';
$description = trim($_POST['description'] ?? '');

if (empty($id) || empty($category_id) || empty($amount) || empty($transaction_date)) {
    header("Location: income_edit.php?id=$id&error=Semua field wajib diisi kecuali keterangan");
    exit;
}

if (!is_numeric($amount) || $amount <= 0) {
    header("Location: income_edit.php?id=$id&error=Jumlah pemasukan harus angka dan lebih dari 0");
    exit;
}

$dateCheck = DateTime::createFromFormat('Y-m-d', $transaction_date);
if (!$dateCheck || $dateCheck->format('Y-m-d') !== $transaction_date) {
    header("Location: income_edit.php?id=$id&error=Format tanggal tidak valid");
    exit;
}

$categoryCheckQuery = "SELECT id FROM categories WHERE id = ? AND type = 'income' AND (user_id IS NULL OR user_id = ?)";
$categoryStmt = mysqli_prepare($conn, $categoryCheckQuery);
mysqli_stmt_bind_param($categoryStmt, "ii", $category_id, $user_id);
mysqli_stmt_execute($categoryStmt);
$categoryResult = mysqli_stmt_get_result($categoryStmt);

if (mysqli_num_rows($categoryResult) !== 1) {
    header("Location: income_edit.php?id=$id&error=Kategori pemasukan tidak valid");
    exit;
}

$query = "
    UPDATE transactions
    SET category_id = ?, amount = ?, description = ?, transaction_date = ?
    WHERE id = ? AND user_id = ? AND type = 'income'
";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "idssii", $category_id, $amount, $description, $transaction_date, $id, $user_id);

if (mysqli_stmt_execute($stmt)) {
    if (mysqli_stmt_affected_rows($stmt) > 0) {
        header("Location: income.php?success=Data pemasukan berhasil diperbarui");
    } else {
        header("Location: income.php?success=Tidak ada perubahan data");
    }
    exit;
} else {
    header("Location: income_edit.php?id=$id&error=Data pemasukan gagal diperbarui");
    exit;
}