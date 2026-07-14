<?php

include '../middleware/auth_check.php';
include '../middleware/role_check.php';
include '../config/database.php';

checkRole('user');

$user_id = $_SESSION['user_id'];

/*
    Ambil data user untuk foto profil
*/
$queryUser = "SELECT profile_picture FROM users WHERE id = ?";
$stmtUser = mysqli_prepare($conn, $queryUser);
mysqli_stmt_bind_param($stmtUser, "i", $user_id);
mysqli_stmt_execute($stmtUser);
$resultUser = mysqli_stmt_get_result($stmtUser);
$user = mysqli_fetch_assoc($resultUser);

/*
    Ambil total pemasukan user
*/
$queryIncome = "
    SELECT COALESCE(SUM(amount), 0) AS total_income
    FROM transactions
    WHERE user_id = ?
    AND type = 'income'
";

$stmtIncome = mysqli_prepare($conn, $queryIncome);
mysqli_stmt_bind_param($stmtIncome, "i", $user_id);
mysqli_stmt_execute($stmtIncome);
$resultIncome = mysqli_stmt_get_result($stmtIncome);
$incomeData = mysqli_fetch_assoc($resultIncome);
$totalIncome = $incomeData['total_income'];

/*
    Ambil total pengeluaran user
*/
$queryExpense = "
    SELECT COALESCE(SUM(amount), 0) AS total_expense
    FROM transactions
    WHERE user_id = ?
    AND type = 'expense'
";

$stmtExpense = mysqli_prepare($conn, $queryExpense);
mysqli_stmt_bind_param($stmtExpense, "i", $user_id);
mysqli_stmt_execute($stmtExpense);
$resultExpense = mysqli_stmt_get_result($stmtExpense);
$expenseData = mysqli_fetch_assoc($resultExpense);
$totalExpense = $expenseData['total_expense'];

/*
    Hitung saldo
*/
$balance = $totalIncome - $totalExpense;

/*
    Hitung jumlah transaksi user
*/
$queryCount = "
    SELECT COUNT(*) AS total_transactions
    FROM transactions
    WHERE user_id = ?
";

$stmtCount = mysqli_prepare($conn, $queryCount);
mysqli_stmt_bind_param($stmtCount, "i", $user_id);
mysqli_stmt_execute($stmtCount);
$resultCount = mysqli_stmt_get_result($stmtCount);
$countData = mysqli_fetch_assoc($resultCount);
$totalTransactions = $countData['total_transactions'];

/*
    Ambil 5 transaksi terbaru
*/
$queryLatest = "
    SELECT transactions.*, categories.name AS category_name
    FROM transactions
    JOIN categories ON transactions.category_id = categories.id
    WHERE transactions.user_id = ?
    ORDER BY transactions.transaction_date DESC, transactions.id DESC
    LIMIT 5
";

$stmtLatest = mysqli_prepare($conn, $queryLatest);
mysqli_stmt_bind_param($stmtLatest, "i", $user_id);
mysqli_stmt_execute($stmtLatest);
$latestTransactions = mysqli_stmt_get_result($stmtLatest);

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard User</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/style.css">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="../index.php">Finance Tracker</a>

        <div class="d-flex gap-2">
            <a href="dashboard.php" class="btn btn-light btn-sm">Dashboard</a>
            <a href="income.php" class="btn btn-outline-light btn-sm">Pemasukan</a>
            <a href="expense.php" class="btn btn-outline-light btn-sm">Pengeluaran</a>
            <a href="transactions.php" class="btn btn-outline-light btn-sm">Riwayat</a>
            <a href="categories.php" class="btn btn-outline-light btn-sm">Kategori</a>
            <a href="profile.php" class="btn btn-outline-light btn-sm">Profil</a>
            <a href="../auth/logout.php" class="btn btn-danger btn-sm">Logout</a>
        </div>
    </div>
</nav>

<div class="container mt-4 mb-5">

    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h3 class="page-title">Dashboard Keuangan</h3>
            <p class="page-subtitle">
                Selamat datang, <?= htmlspecialchars($_SESSION['name']); ?>. Berikut ringkasan keuangan pribadi Anda.
            </p>
        </div>
        <a href="profile.php"><img src="../<?= htmlspecialchars($user['profile_picture'] ?? 'assets/images/avatars/default.png'); ?>" alt="Foto Profil" class="rounded-circle" style="width: 60px; height: 60px; object-fit: cover;"></a>
    </div>

    <!-- Kartu Statistik -->
    <div class="row">

        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-0 stat-card stat-income">
                <div class="card-body">
                    <h6>Total Pemasukan</h6>
                    <h4 class="text-success">
                        Rp <?= number_format($totalIncome, 0, ',', '.'); ?>
                    </h4>
                    <small class="text-muted">Seluruh uang masuk</small>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-0 stat-card stat-expense">
                <div class="card-body">
                    <h6>Total Pengeluaran</h6>
                    <h4 class="text-danger">
                        Rp <?= number_format($totalExpense, 0, ',', '.'); ?>
                    </h4>
                    <small class="text-muted">Seluruh uang keluar</small>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-0 stat-card stat-balance">
                <div class="card-body">
                    <h6>Saldo Saat Ini</h6>
                    <h4 class="<?= $balance >= 0 ? 'text-primary' : 'text-danger'; ?>">
                        Rp <?= number_format($balance, 0, ',', '.'); ?>
                    </h4>
                    <small class="text-muted">Pemasukan - pengeluaran</small>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-0 stat-card stat-count">
                <div class="card-body">
                    <h6>Jumlah Transaksi</h6>
                    <h4 class="text-dark">
                        <?= $totalTransactions; ?>
                    </h4>
                    <small class="text-muted">Total catatan transaksi</small>
                </div>
            </div>
        </div>

    </div>

    <!-- Aksi Cepat -->
    <div class="card border-0 mb-4">
        <div class="card-body d-flex flex-wrap gap-2 justify-content-between align-items-center">
            <div>
                <strong>Aksi Cepat</strong>
                <div class="text-muted small">Tambah transaksi atau lihat riwayat keuangan Anda.</div>
            </div>

            <div class="d-flex flex-wrap gap-2">
                <a href="income_create.php" class="btn btn-success btn-sm">+ Tambah Pemasukan</a>
                <a href="expense_create.php" class="btn btn-danger btn-sm">+ Tambah Pengeluaran</a>
                <a href="transactions.php" class="btn btn-primary btn-sm">Lihat Riwayat</a>
            </div>
        </div>
    </div>

    <!-- Grafik -->
    <div class="card border-0 mb-4">
        <div class="card-header bg-white">
            Grafik Distribusi Pengeluaran (Bulan Ini)
        </div>

        <div class="card-body">
            <canvas id="expensePieChart" height="120"></canvas>
            <div id="noDataMessage" class="text-center text-muted" style="display: none;">Belum ada data pengeluaran bulan ini.</div>
        </div>
    </div>

    <!-- Transaksi Terbaru -->
    <div class="card border-0">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <span>Transaksi Terbaru</span>
            <a href="transactions.php" class="btn btn-outline-primary btn-sm">Lihat Semua</a>
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
                        <?php if (mysqli_num_rows($latestTransactions) > 0) : ?>
                            <?php $no = 1; ?>
                            <?php while ($row = mysqli_fetch_assoc($latestTransactions)) : ?>
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
                                <td colspan="6">
                                    <div class="empty-state">
                                        Belum ada transaksi. Silakan tambah pemasukan atau pengeluaran terlebih dahulu.
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('expensePieChart');
    const noDataMessage = document.getElementById('noDataMessage');

    fetch('chart_data.php')
        .then(response => response.json())
        .then(chartData => {

            if (chartData.data.length === 0) {
                ctx.style.display = 'none';
                noDataMessage.style.display = 'block';
                return;
            }

            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: chartData.labels,
                    datasets: [{
                        label: 'Pengeluaran',
                        data: chartData.data,
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.8)',
                            'rgba(54, 162, 235, 0.8)',
                            'rgba(255, 206, 86, 0.8)',
                            'rgba(75, 192, 192, 0.8)',
                            'rgba(153, 102, 255, 0.8)',
                            'rgba(255, 159, 64, 0.8)',
                            'rgba(201, 203, 207, 0.8)'
                        ],
                        borderColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)',
                            'rgba(255, 159, 64, 1)',
                            'rgba(201, 203, 207, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    let value = context.raw;
                                    return label + ': Rp ' + value.toLocaleString('id-ID');
                                }
                            }
                        }
                    }
                }
            });
        })
        .catch(error => console.error('Error fetching chart data:', error));
});
</script>

</body>
</html>