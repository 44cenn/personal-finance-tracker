<?php

include '../middleware/auth_check.php';
include '../middleware/role_check.php';
include '../config/database.php';

checkRole('user');

$query = "SELECT * FROM categories WHERE type = 'expense' ORDER BY name ASC";
$result = mysqli_query($conn, $query);

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Pengeluaran</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

<nav class="navbar navbar-dark bg-danger">
    <div class="container">
        <span class="navbar-brand">Personal Finance Tracker</span>
        <a href="expense.php" class="btn btn-outline-light btn-sm">Kembali</a>
    </div>
</nav>

<div class="container mt-4">
    <h3>Tambah Pengeluaran</h3>

    <div class="card mt-3">
        <div class="card-body">

            <?php if (isset($_GET['error'])) : ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($_GET['error']); ?>
                </div>
            <?php endif; ?>

            <form action="expense_store.php" method="POST" class="needs-validation-custom">

                <div class="mb-3">
                    <label class="form-label">Kategori</label>
                    <select name="category_id" class="form-select" required data-required="true">
                        <option value="">-- Pilih Kategori --</option>
                        <?php while ($category = mysqli_fetch_assoc($result)) : ?>
                            <option value="<?= $category['id']; ?>">
                                <?= htmlspecialchars($category['name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Jumlah Pengeluaran</label>
                    <input type="number" name="amount" class="form-control" required min="1" data-required="true" data-amount="true">
                </div>

                <div class="mb-3">
                    <label class="form-label">Tanggal</label>
                    <input type="date" name="transaction_date" class="form-control" required data-required="true">
                </div>

                <div class="mb-3">
                    <label class="form-label">Keterangan</label>
                    <textarea name="description" class="form-control" rows="3"></textarea>
                </div>

                <button type="submit" class="btn btn-danger">Simpan</button>
                <a href="expense.php" class="btn btn-secondary">Batal</a>

            </form>

        </div>
    </div>
</div>

<script src="../assets/js/validation.js"></script>

</body>
</html>