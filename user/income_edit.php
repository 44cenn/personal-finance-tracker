<?php

include '../middleware/auth_check.php';
include '../middleware/role_check.php';
include '../config/database.php';

checkRole('user');

$user_id = $_SESSION['user_id'];
$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: income.php?error=Data tidak ditemukan");
    exit;
}

$query = "
    SELECT * FROM transactions
    WHERE id = ?
    AND user_id = ?
    AND type = 'income'
";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "ii", $id, $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) !== 1) {
    header("Location: income.php?error=Data tidak ditemukan atau bukan milik Anda");
    exit;
}

$income = mysqli_fetch_assoc($result);

$categoryQuery = "SELECT * FROM categories WHERE type = 'income' ORDER BY name ASC";
$categories = mysqli_query($conn, $categoryQuery);

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Pemasukan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

<nav class="navbar navbar-dark bg-primary">
    <div class="container">
        <span class="navbar-brand">Personal Finance Tracker</span>
        <a href="income.php" class="btn btn-outline-light btn-sm">Kembali</a>
    </div>
</nav>

<div class="container mt-4">
    <h3>Edit Pemasukan</h3>

    <div class="card mt-3">
        <div class="card-body">

            <?php if (isset($_GET['error'])) : ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($_GET['error']); ?>
                </div>
            <?php endif; ?>

            <form action="income_update.php" method="POST" class="needs-validation-custom">

                <input type="hidden" name="id" value="<?= $income['id']; ?>">

                <div class="mb-3">
                    <label class="form-label">Kategori</label>
                    <select name="category_id" class="form-select" required data-required="true"> 
                        <option value="">-- Pilih Kategori --</option>
                        <?php while ($category = mysqli_fetch_assoc($categories)) : ?>
                            <option value="<?= $category['id']; ?>"
                                <?= $category['id'] == $income['category_id'] ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($category['name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Jumlah Pemasukan</label>
                    <input type="number" name="amount" class="form-control"
                           value="<?= htmlspecialchars($income['amount']); ?>" required min="1">
                           data-required="true" data-amount="true">
                </div>

                <div class="mb-3">
                    <label class="form-label">Tanggal</label>
                    <input type="date" name="transaction_date" class="form-control"
                           value="<?= htmlspecialchars($income['transaction_date']); ?>" required>
                           data-required="true">
                </div>

                <div class="mb-3">
                    <label class="form-label">Keterangan</label>
                    <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($income['description']); ?></textarea>
                </div>

                <button type="submit" class="btn btn-warning">Update</button>
                <a href="income.php" class="btn btn-secondary">Batal</a>

            </form>

        </div>
    </div>
</div>

<script src="../assets/js/validation.js"></script>

</body>
</html>