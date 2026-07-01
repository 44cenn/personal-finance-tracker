<?php

include '../middleware/auth_check.php';
include '../middleware/role_check.php';
include '../config/database.php';

checkRole('user');

$user_id = $_SESSION['user_id'];

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

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="bg-light">

<nav class="navbar navbar-dark bg-primary">
    <div class="container">
        <span class="navbar-brand">Personal Finance Tracker</span>

        <div>
            <a href="income.php" class="btn btn-outline-light btn-sm">Pemasukan</a>
            <a href="expense.php" class="btn btn-outline-light btn-sm">Pengeluaran</a>
            <a href="../auth/logout.php" class="btn btn-outline-light btn-sm">Logout</a>
        </div>
    </div>
</nav>

<div class="container mt-4">

    <h3>Dashboard User</h3>
    <p>Selamat datang, <?= htmlspecialchars($_SESSION['name']); ?>.</p>

    <!-- Kartu Statistik -->
    <div class="row mt-4">

        <div class="col-md-3 mb-3">
            <div class="card border-success shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted">Total Pemasukan</h6>
                    <h4 class="text-success">
                        Rp <?= number_format($totalIncome, 0, ',', '.'); ?>
                    </h4>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-danger shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted">Total Pengeluaran</h6>
                    <h4 class="text-danger">
                        Rp <?= number_format($totalExpense, 0, ',', '.'); ?>
                    </h4>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-primary shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted">Saldo</h6>
                    <h4 class="text-primary">
                        Rp <?= number_format($balance, 0, ',', '.'); ?>
                    </h4>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-dark shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted">Jumlah Transaksi</h6>
                    <h4>
                        <?= $totalTransactions; ?>
                    </h4>
                </div>
            </div>
        </div>

    </div>

    <!-- Grafik Keuangan -->
    <div class="card mt-4 shadow-sm">
        <div class="card-header bg-white">
            <strong>Grafik Pemasukan dan Pengeluaran</strong>
        </div>

        <div class="card-body">
            <canvas id="financeChart" height="100"></canvas>
        </div>
    </div>

    <!-- Transaksi Terbaru -->
    <div class="card mt-4 shadow-sm">
        <div class="card-header bg-white">
            <strong>Transaksi Terbaru</strong>
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
                                <td colspan="6" class="text-center">
                                    Belum ada transaksi.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                <a href="income.php" class="btn btn-success btn-sm">Tambah Pemasukan</a>
                <a href="expense.php" class="btn btn-danger btn-sm">Tambah Pengeluaran</a>
                <a href="transactions.php" class="btn btn-primary btn-sm">Lihat Semua Transaksi</a>
                <a href="../auth/logout.php" class="btn btn-secondary btn-sm">Logout</a>
            </div>

        </div>
    </div>

</div>

<!-- Script Grafik -->
<script>
    const ctx = document.getElementById('financeChart');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Pemasukan', 'Pengeluaran'],
            datasets: [{
                label: 'Jumlah Rupiah',
                data: [
                    <?= (float) $totalIncome; ?>,
                    <?= (float) $totalExpense; ?>
                ],
                backgroundColor: [
                    'rgba(25, 135, 84, 0.7)',
                    'rgba(220, 53, 69, 0.7)'
                ],
                borderColor: [
                    'rgba(25, 135, 84, 1)',
                    'rgba(220, 53, 69, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: true
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let value = context.raw;
                            return 'Rp ' + value.toLocaleString('id-ID');
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + value.toLocaleString('id-ID');
                        }
                    }
                }
            }
        }
    });
</script>

</body>
</html>