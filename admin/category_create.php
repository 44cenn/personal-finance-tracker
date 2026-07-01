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
</head>

<body>

<nav class="navbar navbar-dark bg-dark">
    <div class="container">
        <span class="navbar-brand">Admin Panel</span>
        <a href="categories.php" class="btn btn-outline-light btn-sm">Kembali</a>
    </div>
</nav>

<div class="container mt-4">
    <h3>Tambah Kategori</h3>

    <div class="card mt-3">
        <div class="card-body">

            <?php if (isset($_GET['error'])) : ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($_GET['error']); ?>
                </div>
            <?php endif; ?>

            <form action="category_store.php" method="POST" class="needs-validation-custom">

                <div class="mb-3">
                    <label class="form-label">Nama Kategori</label>
                    <input type="text" name="name" class="form-control" required data-required="true">
                </div>

                <div class="mb-3">
                    <label class="form-label">Tipe Kategori</label>
                    <select name="type" class="form-select" required data-required="true">
                        <option value="">-- Pilih Tipe --</option>
                        <option value="income">Pemasukan</option>
                        <option value="expense">Pengeluaran</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="categories.php" class="btn btn-secondary">Batal</a>

            </form>

        </div>
    </div>
</div>

<script src="../assets/js/validation.js"></script>

</body>
</html>