<?php
require_once '../config/bootstrap.php';

checkRole('user');

// Ambil semua kategori (default dan milik user)
$query = "
    SELECT * FROM categories 
    WHERE user_id IS NULL OR user_id = ? 
    ORDER BY type, name
";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Kategori</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body data-theme="<?= htmlspecialchars($current_theme) ?>">
    <?php include __DIR__ . '/navbar.php'; ?>

    <div class="container mt-4">
        <div class="d-flex align-items-center mb-3">
            <a href="../index.php" class="btn btn-outline-secondary me-2 btn-back-icon" title="Kembali ke Home">‹</a>
            <h3>Manajemen Kategori</h3>
        </div>
        <p class="text-muted">Tambah atau kelola kategori pemasukan dan pengeluaran Anda.</p>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success"><?= htmlspecialchars($_GET['success']); ?></div>
        <?php endif; ?>
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']); ?></div>
        <?php endif; ?>

        <div class="row">
            <!-- Form Tambah Kategori -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">Tambah Kategori Baru</div>
                    <div class="card-body">
                        <form action="category_store.php" method="POST">
                            <div class="mb-3">
                                <label for="name" class="form-label">Nama Kategori</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="type" class="form-label">Tipe Kategori</label>
                                <select class="form-select" id="type" name="type" required>
                                    <option value="income">Pemasukan</option>
                                    <option value="expense">Pengeluaran</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Tambah</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Daftar Kategori -->
            <div class="col-md-8 mt-4 mt-md-0">
                <div class="card">
                    <div class="card-header">Daftar Kategori</div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Nama</th>
                                        <th>Tipe</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($row = mysqli_fetch_assoc($result)): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($row['name']); ?></td>
                                        <td>
                                            <span class="badge bg-<?= $row['type'] == 'income' ? 'success' : 'danger'; ?>">
                                                <?= ucfirst($row['type']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?= $row['user_id'] == NULL ? 'secondary' : 'info'; ?>">
                                                <?= $row['user_id'] == NULL ? 'Default' : 'Custom'; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if($row['user_id'] != NULL): // Hanya kategori custom yang bisa diedit/dihapus ?>
                                                <a href="category_edit.php?id=<?= $row['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                                <a href="category_delete.php?id=<?= $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus kategori ini?')">Hapus</a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>