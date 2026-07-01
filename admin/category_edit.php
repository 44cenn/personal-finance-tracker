<?php

include '../middleware/auth_check.php';
include '../middleware/role_check.php';
include '../config/database.php';

checkRole('admin');

$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: categories.php?error=Kategori tidak ditemukan");
    exit;
}

$query = "SELECT * FROM categories WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) !== 1) {
    header("Location: categories.php?error=Kategori tidak ditemukan");
    exit;
}

$category = mysqli_fetch_assoc($result);

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Kategori</title>
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
    <h3>Edit Kategori</h3>

    <div class="card mt-3">
        <div class="card-body">

            <?php if (isset($_GET['error'])) : ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($_GET['error']); ?>
                </div>
            <?php endif; ?>

            <form action="category_update.php" method="POST" class="needs-validation-custom">

                <input type="hidden" name="id" value="<?= $category['id']; ?>">

                <div class="mb-3">
                    <label class="form-label">Nama Kategori</label>
                    <input type="text" name="name" class="form-control"
                           value="<?= htmlspecialchars($category['name']); ?>" required data-required="true">
                </div>

                <div class="mb-3">
                    <label class="form-label">Tipe Kategori</label>
                    <select name="type" class="form-select" required data-required="true">
                        <option value="">-- Pilih Tipe --</option>
                        <option value="income" <?= $category['type'] == 'income' ? 'selected' : ''; ?>>
                            Pemasukan
                        </option>
                        <option value="expense" <?= $category['type'] == 'expense' ? 'selected' : ''; ?>>
                            Pengeluaran
                        </option>
                    </select>
                </div>

                <button type="submit" class="btn btn-warning">Update</button>
                <a href="categories.php" class="btn btn-secondary">Batal</a>

            </form>

        </div>
    </div>
</div>

<script src="../assets/js/validation.js"></script>

</body>
</html>