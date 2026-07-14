<?php

include '../middleware/auth_check.php';
include '../middleware/role_check.php';
include '../config/database.php';

checkRole('admin');

$query = "
    SELECT *
    FROM categories
    WHERE user_id IS NULL
    ORDER BY type, name
";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Kategori Default</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

<nav class="navbar navbar-expand-lg navbar-dark admin-navbar">
    <div class="container">
        <a class="navbar-brand" href="dashboard.php">
            Admin Panel
        </a>

        <div class="d-flex gap-2">
            <a href="dashboard.php" class="btn btn-outline-light btn-sm">
                Dashboard
            </a>

            <a href="../auth/logout.php" class="btn btn-danger btn-sm">
                Logout
            </a>
        </div>
    </div>
</nav>

<div class="container mt-4">

    <div class="page-header">
        <h3 class="page-title">Manajemen Kategori Default</h3>

        <p class="page-subtitle">
            Kelola kategori default yang tersedia untuk semua pengguna.
        </p>
    </div>

    <?php if (isset($_GET['success'])) : ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($_GET['success']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])) : ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($_GET['error']); ?>
        </div>
    <?php endif; ?>

    <div class="row">

        <div class="col-md-4">
            <div class="card">

                <div class="card-header">
                    Tambah Kategori Baru
                </div>

                <div class="card-body">
                    <form action="category_store.php" method="POST">

                        <div class="mb-3">
                            <label for="name" class="form-label">
                                Nama Kategori
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                id="name"
                                name="name"
                                required
                            >
                        </div>

                        <div class="mb-3">
                            <label for="type" class="form-label">
                                Tipe Kategori
                            </label>

                            <select
                                class="form-select"
                                id="type"
                                name="type"
                                required
                            >
                                <option value="income">Pemasukan</option>
                                <option value="expense">Pengeluaran</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            Tambah
                        </button>

                    </form>
                </div>

            </div>
        </div>

        <div class="col-md-8 mt-4 mt-md-0">
            <div class="card">

                <div class="card-header">
                    Daftar Kategori Default
                </div>

                <div class="card-body">
                    <div class="table-responsive">

                        <table class="table table-striped">

                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Tipe</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>

                            <tbody>

                            <?php if (mysqli_num_rows($result) > 0) : ?>

                                <?php while ($row = mysqli_fetch_assoc($result)) : ?>

                                    <tr>
                                        <td>
                                            <?= htmlspecialchars($row['name']); ?>
                                        </td>

                                        <td>
                                            <span class="badge bg-<?= $row['type'] === 'income' ? 'success' : 'danger'; ?>">
                                                <?= $row['type'] === 'income' ? 'Pemasukan' : 'Pengeluaran'; ?>
                                            </span>
                                        </td>

                                        <td class="action-buttons">
                                            <a
                                                href="category_edit.php?id=<?= (int) $row['id']; ?>"
                                                class="btn btn-warning btn-sm"
                                            >
                                                Edit
                                            </a>

                                            <a
                                                href="category_delete.php?id=<?= (int) $row['id']; ?>"
                                                class="btn btn-danger btn-sm"
                                                onclick="return confirm('Yakin ingin menghapus kategori ini? Ini akan memengaruhi semua pengguna.')"
                                            >
                                                Hapus
                                            </a>
                                        </td>
                                    </tr>

                                <?php endwhile; ?>

                            <?php else : ?>

                                <tr>
                                    <td colspan="3" class="text-center">
                                        Belum ada kategori default.
                                    </td>
                                </tr>

                            <?php endif; ?>

                            </tbody>

                        </table>

                    </div>
                </div>

            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>