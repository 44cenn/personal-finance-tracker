<?php

include '../middleware/auth_check.php';
include '../middleware/role_check.php';
include '../config/database.php';

checkRole('user');

$user_id = $_SESSION['user_id'];

/*
    Ambil data filter dari URL
*/
$start_date = $_GET['start_date'] ?? '';
$end_date = $_GET['end_date'] ?? '';
$type = $_GET['type'] ?? '';
$category_id = $_GET['category_id'] ?? '';
$keyword = $_GET['keyword'] ?? '';

/*
    Pagination
*/
$limit = 5;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;

if ($page < 1) {
    $page = 1;
}

$offset = ($page - 1) * $limit;

/*
    Ambil semua kategori untuk dropdown filter
*/
$categoryQuery = "SELECT * FROM categories ORDER BY type ASC, name ASC";
$categoryResult = mysqli_query($conn, $categoryQuery);

/*
    Query dasar untuk filter
*/
$whereQuery = "
    FROM transactions
    JOIN categories ON transactions.category_id = categories.id
    WHERE transactions.user_id = ?
";

$params = [$user_id];
$types = "i";

/*
    Filter tanggal awal
*/
if (!empty($start_date)) {
    $whereQuery .= " AND transactions.transaction_date >= ?";
    $params[] = $start_date;
    $types .= "s";
}

/*
    Filter tanggal akhir
*/
if (!empty($end_date)) {
    $whereQuery .= " AND transactions.transaction_date <= ?";
    $params[] = $end_date;
    $types .= "s";
}

/*
    Filter tipe income / expense
*/
if (!empty($type)) {
    $whereQuery .= " AND transactions.type = ?";
    $params[] = $type;
    $types .= "s";
}

/*
    Filter kategori
*/
if (!empty($category_id)) {
    $whereQuery .= " AND transactions.category_id = ?";
    $params[] = $category_id;
    $types .= "i";
}

/*
    Filter keyword berdasarkan keterangan
*/
if (!empty($keyword)) {
    $whereQuery .= " AND transactions.description LIKE ?";
    $params[] = "%" . $keyword . "%";
    $types .= "s";
}

/*
    Hitung total data setelah filter
*/
$countQuery = "SELECT COUNT(*) AS total_data " . $whereQuery;

$countStmt = mysqli_prepare($conn, $countQuery);
mysqli_stmt_bind_param($countStmt, $types, ...$params);
mysqli_stmt_execute($countStmt);
$countResult = mysqli_stmt_get_result($countStmt);
$countData = mysqli_fetch_assoc($countResult);

$totalData = $countData['total_data'];
$totalPages = ceil($totalData / $limit);

if ($totalPages < 1) {
    $totalPages = 1;
}

if ($page > $totalPages) {
    $page = $totalPages;
    $offset = ($page - 1) * $limit;
}

/*
    Query utama transaksi dengan LIMIT dan OFFSET
*/
$query = "
    SELECT transactions.*, categories.name AS category_name
    " . $whereQuery . "
    ORDER BY transactions.transaction_date DESC, transactions.id DESC
    LIMIT ? OFFSET ?
";

$dataParams = $params;
$dataTypes = $types;

$dataParams[] = $limit;
$dataParams[] = $offset;
$dataTypes .= "ii";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, $dataTypes, ...$dataParams);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

/*
    Buat query string agar filter tetap terbawa saat pindah halaman
*/
$queryString = http_build_query([
    'start_date' => $start_date,
    'end_date' => $end_date,
    'type' => $type,
    'category_id' => $category_id,
    'keyword' => $keyword
]);

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Transaksi</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<nav class="navbar navbar-dark bg-primary">
    <div class="container">
        <span class="navbar-brand">Personal Finance Tracker</span>

        <div>
            <a href="dashboard.php" class="btn btn-outline-light btn-sm">Dashboard</a>
            <a href="income.php" class="btn btn-outline-light btn-sm">Pemasukan</a>
            <a href="expense.php" class="btn btn-outline-light btn-sm">Pengeluaran</a>
            <a href="../auth/logout.php" class="btn btn-outline-light btn-sm">Logout</a>
        </div>
    </div>
</nav>

<div class="container mt-4">

    <h3>Riwayat Transaksi</h3>
    <p class="text-muted">Gunakan filter untuk mencari transaksi tertentu.</p>

    <!-- Filter -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white">
            <strong>Filter Transaksi</strong>
        </div>

        <div class="card-body">
            <form method="GET" action="transactions.php">
                <div class="row">

                    <div class="col-md-3 mb-3">
                        <label class="form-label">Tanggal Awal</label>
                        <input type="date" name="start_date" class="form-control"
                               value="<?= htmlspecialchars($start_date); ?>">
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label">Tanggal Akhir</label>
                        <input type="date" name="end_date" class="form-control"
                               value="<?= htmlspecialchars($end_date); ?>">
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label">Tipe</label>
                        <select name="type" class="form-select">
                            <option value="">Semua Tipe</option>
                            <option value="income" <?= $type == 'income' ? 'selected' : ''; ?>>
                                Pemasukan
                            </option>
                            <option value="expense" <?= $type == 'expense' ? 'selected' : ''; ?>>
                                Pengeluaran
                            </option>
                        </select>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label">Kategori</label>
                        <select name="category_id" class="form-select">
                            <option value="">Semua Kategori</option>

                            <?php while ($category = mysqli_fetch_assoc($categoryResult)) : ?>
                                <option value="<?= $category['id']; ?>"
                                    <?= $category_id == $category['id'] ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($category['name']); ?>
                                    <?= $category['type'] == 'income' ? '(Pemasukan)' : '(Pengeluaran)'; ?>
                                </option>
                            <?php endwhile; ?>

                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Cari Keterangan</label>
                        <input type="text" name="keyword" class="form-control"
                               placeholder="Contoh: makan, bensin, uang saku"
                               value="<?= htmlspecialchars($keyword); ?>">
                    </div>

                    <div class="col-md-6 mb-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">Filter</button>
                        <a href="transactions.php" class="btn btn-secondary">Reset</a>
                    </div>

                </div>
            </form>
        </div>
    </div>

    <!-- Tabel Transaksi -->
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <strong>Data Transaksi</strong>
            <small class="text-muted">
                Total data: <?= $totalData; ?>
            </small>
        </div>

        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-primary">
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Kategori</th>
                            <th>Tipe</th>
                            <th>Jumlah</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (mysqli_num_rows($result) > 0) : ?>
                            <?php $no = $offset + 1; ?>
                            <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><?= htmlspecialchars($row['transaction_date']); ?></td>
                                    <td><?= htmlspecialchars($row['category_name']); ?></td>
                                    <td>
                                        <?php if ($row['type'] == 'income') : ?>
                                            <span class="badge bg-success">Pemasukan</span>
                                        <?php else : ?>
                                            <span class="badge bg-danger">Pengeluaran</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        Rp <?= number_format($row['amount'], 0, ',', '.'); ?>
                                    </td>
                                    <td><?= htmlspecialchars($row['description']); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="6" class="text-center">
                                    Data transaksi tidak ditemukan.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <nav class="mt-3">
                <ul class="pagination justify-content-center">

                    <li class="page-item <?= $page <= 1 ? 'disabled' : ''; ?>">
                        <a class="page-link"
                           href="transactions.php?<?= $queryString; ?>&page=<?= $page - 1; ?>">
                            Previous
                        </a>
                    </li>

                    <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
                        <li class="page-item <?= $page == $i ? 'active' : ''; ?>">
                            <a class="page-link"
                               href="transactions.php?<?= $queryString; ?>&page=<?= $i; ?>">
                                <?= $i; ?>
                            </a>
                        </li>
                    <?php endfor; ?>

                    <li class="page-item <?= $page >= $totalPages ? 'disabled' : ''; ?>">
                        <a class="page-link"
                           href="transactions.php?<?= $queryString; ?>&page=<?= $page + 1; ?>">
                            Next
                        </a>
                    </li>

                </ul>
            </nav>

        </div>
    </div>

</div>

</body>
</html>