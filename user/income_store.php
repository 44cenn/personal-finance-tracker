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
$category_id = $_POST['category_id'] ?? '';
$amount = $_POST['amount'] ?? '';
$transaction_date = $_POST['transaction_date'] ?? '';
$description = trim($_POST['description'] ?? '');
$type = 'income';

/*
    Validasi input kosong
*/
if (empty($category_id) || empty($amount) || empty($transaction_date)) {
    header("Location: income_create.php?error=Kategori, jumlah, dan tanggal wajib diisi");
    exit;
}

/*
    Validasi jumlah harus angka
*/
if (!is_numeric($amount)) {
    header("Location: income_create.php?error=Jumlah pemasukan harus berupa angka");
    exit;
}

/*
    Validasi jumlah harus lebih dari 0
*/
if ($amount <= 0) {
    header("Location: income_create.php?error=Jumlah pemasukan harus lebih dari 0");
    exit;
}

/*
    Validasi format tanggal
*/
$dateCheck = DateTime::createFromFormat('Y-m-d', $transaction_date);

if (!$dateCheck || $dateCheck->format('Y-m-d') !== $transaction_date) {
    header("Location: income_create.php?error=Format tanggal tidak valid");
    exit;
}

/*
    Validasi kategori harus kategori income yang ada di database
*/
$categoryCheckQuery = "SELECT id FROM categories WHERE id = ? AND type = 'income' AND (user_id IS NULL OR user_id = ?)";
$categoryStmt = mysqli_prepare($conn, $categoryCheckQuery);
mysqli_stmt_bind_param($categoryStmt, "ii", $category_id, $user_id);
mysqli_stmt_execute($categoryStmt);
$categoryResult = mysqli_stmt_get_result($categoryStmt);

if (mysqli_num_rows($categoryResult) !== 1) {
    header("Location: income_create.php?error=Kategori pemasukan tidak valid");
    exit;
}

/*
    Simpan data pemasukan
*/
$query = "INSERT INTO transactions (user_id, category_id, type, amount, description, transaction_date) VALUES (?, ?, ?, ?, ?, ?)";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "iisdss", $user_id, $category_id, $type, $amount, $description, $transaction_date);

if (mysqli_stmt_execute($stmt)) {
    header("Location: income.php?success=Data pemasukan berhasil ditambahkan");
    exit;
} else {
    header("Location: income_create.php?error=Data pemasukan gagal ditambahkan");
    exit;
}