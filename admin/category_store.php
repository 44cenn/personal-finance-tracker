<?php

include '../middleware/auth_check.php';
include '../middleware/role_check.php';
include '../config/database.php';

checkRole('admin');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: categories.php");
    exit;
}

$name = trim($_POST['name'] ?? '');
$type = $_POST['type'] ?? '';

if (empty($name) || empty($type)) {
    header("Location: category_create.php?error=Nama kategori dan tipe wajib diisi");
    exit;
}

if (strlen($name) < 3) {
    header("Location: category_create.php?error=Nama kategori minimal 3 karakter");
    exit;
}

if ($type !== 'income' && $type !== 'expense') {
    header("Location: category_create.php?error=Tipe kategori tidak valid");
    exit;
}

/*
    Cek kategori duplikat
*/
$checkQuery = "SELECT id FROM categories WHERE name = ? AND type = ?";
$checkStmt = mysqli_prepare($conn, $checkQuery);
mysqli_stmt_bind_param($checkStmt, "ss", $name, $type);
mysqli_stmt_execute($checkStmt);
$checkResult = mysqli_stmt_get_result($checkStmt);

if (mysqli_num_rows($checkResult) > 0) {
    header("Location: category_create.php?error=Kategori dengan tipe tersebut sudah ada");
    exit;
}

$query = "INSERT INTO categories (name, type) VALUES (?, ?)";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "ss", $name, $type);

if (mysqli_stmt_execute($stmt)) {
    header("Location: categories.php?success=Kategori berhasil ditambahkan");
    exit;
} else {
    header("Location: category_create.php?error=Kategori gagal ditambahkan");
    exit;
}