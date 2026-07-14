<?php
include '../middleware/auth_check.php';
include '../middleware/role_check.php';

checkRole('admin');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="dashboard.php">Admin Panel</a>
        <div>
            <a href="categories.php" class="btn btn-outline-light btn-sm">Manajemen Kategori</a>
            <a href="../auth/logout.php" class="btn btn-danger btn-sm">Logout</a>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <div class="page-header">
        <h3 class="page-title">Dashboard Admin</h3>
        <p class="page-subtitle">
            Selamat datang, <?= htmlspecialchars($_SESSION['name']); ?>.
        </p>
    </div>
    <p>Pilih menu di atas untuk mengelola aplikasi.</p>
</div>

</body>
</html>