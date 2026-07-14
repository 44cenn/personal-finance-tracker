<?php

include '../middleware/auth_check.php';
include '../middleware/role_check.php';
include '../config/database.php';

checkRole('user');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: expense.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$category_id = $_POST['category_id'] ?? '';
$amount = $_POST['amount'] ?? '';
$transaction_date = $_POST['transaction_date'] ?? '';
$description = trim($_POST['description'] ?? '');
$type = 'expense';

if (empty($category_id) || empty($amount) || empty($transaction_date)) {
    header("Location: expense_create.php?error=Kategori, jumlah, dan tanggal wajib diisi");
    exit;
}

if (!is_numeric($amount)) {
    header("Location: expense_create.php?error=Jumlah pengeluaran harus berupa angka");
    exit;
}

if ($amount <= 0) {
    header("Location: expense_create.php?error=Jumlah pengeluaran harus lebih dari 0");
    exit;
}

$dateCheck = DateTime::createFromFormat('Y-m-d', $transaction_date);

if (!$dateCheck || $dateCheck->format('Y-m-d') !== $transaction_date) {
    header("Location: expense_create.php?error=Format tanggal tidak valid");
    exit;
}

$categoryCheckQuery = "SELECT id FROM categories WHERE id = ? AND type = 'expense' AND (user_id IS NULL OR user_id = ?)";
$categoryStmt = mysqli_prepare($conn, $categoryCheckQuery);
mysqli_stmt_bind_param($categoryStmt, "ii", $category_id, $user_id);
mysqli_stmt_execute($categoryStmt);
$categoryResult = mysqli_stmt_get_result($categoryStmt);

if (mysqli_num_rows($categoryResult) !== 1) {
    header("Location: expense_create.php?error=Kategori pengeluaran tidak valid");
    exit;
}

$query = "
    INSERT INTO transactions 
    (user_id, category_id, type, amount, description, transaction_date)
    VALUES (?, ?, ?, ?, ?, ?)
";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param(
    $stmt,
    "iisdss",
    $user_id,
    $category_id,
    $type,
    $amount,
    $description,
    $transaction_date
);

if (mysqli_stmt_execute($stmt)) {
    header("Location: expense.php?success=Data pengeluaran berhasil ditambahkan");
    exit;
} else {
    header("Location: expense_create.php?error=Data pengeluaran gagal ditambahkan");
    exit;
}