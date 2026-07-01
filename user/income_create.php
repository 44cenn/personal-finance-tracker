<?php

include '../middleware/auth_check.php';
include '../middleware/role_check.php';
include '../config/database.php';

checkRole('user');

$query = "SELECT * FROM categories WHERE type = 'income' ORDER BY name ASC";
$result = mysqli_query($conn, $query);

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Pemasukan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<nav class="navbar navbar-dark bg-primary">
    <div class="container">
        <span class="navbar-brand">Personal Finance Tracker</span>
        <a href="income.php" class="btn btn-outline-light btn-sm">Kembali</a>
    </div>
</nav>

<div class="container mt-4">
    <h3>Tambah Pemasukan</h3>

    <div class="card mt-3 shadow-sm">
        <div class="card-body">

            <?php if (isset($_GET['error'])) : ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($_GET['error']); ?>
                </div>
            <?php endif; ?>

            <form action="income_store.php" method="POST" class="needs-validation-custom">

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
                    <label class="form-label">Jumlah Pemasukan</label>
                    <input 
                        type="number" 
                        name="amount" 
                        class="form-control" 
                        required 
                        min="1"
                        data-required="true"
                        data-amount="true"
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label">Tanggal</label>
                    <input 
                        type="date" 
                        name="transaction_date" 
                        class="form-control" 
                        required
                        data-required="true"
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label">Keterangan</label>
                    <textarea 
                        name="description" 
                        class="form-control" 
                        rows="3"
                        placeholder="Contoh: Uang saku bulanan"
                    ></textarea>
                </div>

                <button type="submit" class="btn btn-success">Simpan</button>
                <a href="income.php" class="btn btn-secondary">Batal</a>

            </form>

        </div>
    </div>
</div>

<!-- Custom Validation JavaScript -->
<script src="../assets/js/validation.js"></script>

</body>
</html>