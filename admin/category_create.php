<?php

include '../middleware/auth_check.php';
include '../middleware/role_check.php';

checkRole('admin');

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Kategori</title>
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
            <a href="categories.php" class="btn btn-outline-light btn-sm">
                Kembali
            </a>

            <a href="../auth/logout.php" class="btn btn-danger btn-sm">
                Logout
            </a>
        </div>
    </div>
</nav>

<div class="container mt-4">

    <div class="page-header">
        <h3 class="page-title">Tambah Kategori</h3>

        <p class="page-subtitle">
            Tambahkan kategori default baru untuk seluruh pengguna.
        </p>
    </div>

    <div class="card">
        <div class="card-body">

            <?php if (isset($_GET['error'])) : ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($_GET['error']); ?>
                </div>
            <?php endif; ?>

            <form
                action="category_store.php"
                method="POST"
                class="needs-validation-custom"
            >

                <div class="mb-3">
                    <label for="name" class="form-label">
                        Nama Kategori
                    </label>

                    <input
                        type="text"
                        id="name"
                        name="name"
                        class="form-control"
                        required
                        data-required="true"
                    >
                </div>

                <div class="mb-3">
                    <label for="type" class="form-label">
                        Tipe Kategori
                    </label>

                    <select
                        id="type"
                        name="type"
                        class="form-select"
                        required
                        data-required="true"
                    >
                        <option value="">-- Pilih Tipe --</option>
                        <option value="income">Pemasukan</option>
                        <option value="expense">Pengeluaran</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">
                    Simpan
                </button>

                <a href="categories.php" class="btn btn-secondary">
                    Batal
                </a>

            </form>

        </div>
    </div>

</div>

<script src="../assets/js/validation.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>