<?php

include '../middleware/auth_check.php';
include '../middleware/role_check.php';
include '../config/database.php';

checkRole('user');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: income.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$id = $_POST['id'];
$category_id = $_POST['category_id'];
$amount = $_POST['amount'];
$transaction_date = $_POST['transaction_date'];
$description = trim($_POST['description']);

if (empty($id) || empty($category_id) || empty($amount) || empty($transaction_date)) {
    header("Location: income.php?error=Data tidak lengkap");
    exit;
}

if ($amount <= 0) {
    header("Location: income_edit.php?id=$id&error=Jumlah pemasukan harus lebih dari 0");
    exit;
}

$query = "
    UPDATE transactions
    SET category_id = ?, amount = ?, description = ?, transaction_date = ?
    WHERE id = ?
    AND user_id = ?
    AND type = 'income'
";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param(
    $stmt,
    "idssii",
    $category_id,
    $amount,
    $description,
    $transaction_date,
    $id,
    $user_id
);

if (mysqli_stmt_execute($stmt)) {
    header("Location: income.php?success=Data pemasukan berhasil diperbarui");
    exit;
} else {
    header("Location: income_edit.php?id=$id&error=Data pemasukan gagal diperbarui");
    exit;
}