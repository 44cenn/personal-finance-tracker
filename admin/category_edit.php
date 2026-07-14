<?php

include '../middleware/auth_check.php';
include '../middleware/role_check.php';
include '../config/database.php';

checkRole('admin');

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    header('Location: categories.php?error=' . urlencode('Kategori tidak ditemukan.'));
    exit;
}

$query = "
    SELECT *
    FROM categories
    WHERE id = ?
    AND user_id IS NULL
    LIMIT 1
";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) !== 1) {
    header('Location: categories.php?error=' . urlencode('Kategori default tidak ditemukan.'));
    exit;
}

$category = mysqli_fetch_assoc($result);

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Kategori Default</title>
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
                Kembali ke Kategori
            </a>

            <a href="../auth/logout.php" class="btn btn-danger btn-sm">
                Logout
            </a>
        </div>
    </div>
</nav>

<div class="container mt-4">

    <div class="row justify-content-center">
        <div class="col-md-6">

            <div class="page-header">
                <h3 class="page-title">Edit Kategori Default</h3>

                <p class="page-subtitle">
                    Perbarui nama atau tipe kategori default.
                </p>
            </div>

            <div class="card">

                <div class="card-header">
                    Edit Kategori
                    “<?= htmlspecialchars($category['name']); ?>”
                </div>

                <div class="card-body">

                    <form action="category_update.php" method="POST">

                        <input
                            type="hidden"
                            name="id"
                            value="<?= (int) $category['id']; ?>"
                        >

                        <div class="mb-3">
                            <label for="name" class="form-label">
                                Nama Kategori
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                id="name"
                                name="name"
                                value="<?= htmlspecialchars($category['name']); ?>"
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
                                <option
                                    value="income"
                                    <?= $category['type'] === 'income' ? 'selected' : ''; ?>
                                >
                                    Pemasukan
                                </option>

                                <option
                                    value="expense"
                                    <?= $category['type'] === 'expense' ? 'selected' : ''; ?>
                                >
                                    Pengeluaran
                                </option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-warning">
                            Update
                        </button>

                        <a href="categories.php" class="btn btn-secondary">
                            Batal
                        </a>

                    </form>

                </div>

            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>