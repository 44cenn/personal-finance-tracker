<?php
require_once '../config/bootstrap.php';

checkRole('user');
$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: categories.php?error=Kategori tidak ditemukan.");
    exit;
}

// Ambil data kategori yang akan diedit, pastikan milik user yang login
$query = "SELECT * FROM categories WHERE id = ? AND user_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "ii", $id, $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) !== 1) {
    header("Location: categories.php?error=Kategori tidak ditemukan atau Anda tidak punya hak akses.");
    exit;
}

$category = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html> 
<html lang="<?= htmlspecialchars($current_language) ?>">
<head>
    <meta charset="UTF-8">
    <title><?= __('settings') ?> - Personal Finance Tracker</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="../welcome.php">Finance Tracker</a>
            <a href="categories.php" class="btn btn-outline-light btn-sm">Kembali</a>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Edit Kategori "<?= htmlspecialchars($category['name']) ?>"</div>
                    <div class="card-body">
                        <form action="category_update.php" method="POST">
                            <input type="hidden" name="id" value="<?= $category['id']; ?>">
                            <div class="mb-3">
                                <label for="name" class="form-label">Nama Kategori</label>
                                <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($category['name']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="type" class="form-label">Tipe Kategori</label>
                                <select class="form-select" id="type" name="type" required>
                                    <option value="income" <?= $category['type'] == 'income' ? 'selected' : ''; ?>>Pemasukan</option>
                                    <option value="expense" <?= $category['type'] == 'expense' ? 'selected' : ''; ?>>Pengeluaran</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-warning">Update</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>