<?php

include '../middleware/auth_check.php';
include '../middleware/role_check.php';

checkRole('admin');

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

<nav class="navbar navbar-dark bg-dark">
    <div class="container">
        <span class="navbar-brand">Admin Panel</span>
        <a href="../auth/logout.php" class="btn btn-outline-light btn-sm">Logout</a>
    </div>
</nav>

<div class="container mt-4">
    <h3>Dashboard Admin</h3>
    <p>Selamat datang, <?= htmlspecialchars($_SESSION['name']); ?>.</p>

    <div class="alert alert-info">
        Ini adalah halaman khusus admin.
    </div>

    <a href="categories.php" class="btn btn-primary">Kelola Kategori</a>
</div>

</body>
</html>