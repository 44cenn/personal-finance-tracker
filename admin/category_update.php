<?php

include '../middleware/auth_check.php';
include '../middleware/role_check.php';
include '../config/database.php';

checkRole('admin');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: categories.php");
    exit;
}

$id = $_POST['id'];
$name = trim($_POST['name']);
$type = $_POST['type'];

if (empty($id) || empty($name) || empty($type)) {
    header("Location: categories.php?error=Data kategori tidak lengkap");
    exit;
}

if ($type !== 'income' && $type !== 'expense') {
    header("Location: category_edit.php?id=$id&error=Tipe kategori tidak valid");
    exit;
}

$query = "UPDATE categories SET name = ?, type = ? WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "ssi", $name, $type, $id);

if (mysqli_stmt_execute($stmt)) {
    header("Location: categories.php?success=Kategori berhasil diperbarui");
    exit;
} else {
    header("Location: category_edit.php?id=$id&error=Kategori gagal diperbarui");
    exit;
}