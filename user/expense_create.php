<?php
require_once '../config/bootstrap.php';

checkRole('user');

$query = "SELECT * FROM categories WHERE type = 'expense' AND (user_id IS NULL OR user_id = ?) ORDER BY name ASC";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

?>

<!DOCTYPE html>
<html lang="<?= htmlspecialchars($current_language) ?>">
<head>
    <meta charset="UTF-8">
    <title><?= __('expense') ?> - Personal Finance Tracker</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body data-theme="<?= htmlspecialchars($current_theme) ?>">
<?php include __DIR__ . '/navbar.php'; ?>

<div class="container mt-4">
    <div class="d-flex align-items-center mb-3">
        <a href="expense.php" class="btn btn-outline-secondary me-2 btn-back-icon" title="Kembali ke Pengeluaran">‹</a>
        <h3>Tambah Pengeluaran</h3>
    </div>

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
                    <input type="text" name="amount" class="form-control" required data-required="true" data-amount="true" data-format="numeric" placeholder="Contoh: 150000">
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

            </form>

        </div>
    </div>
</div>

<script src="../assets/js/validation.js"></script>
<script src="../assets/js/input-formatter.js"></script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>