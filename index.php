<?php
require_once __DIR__ . '/config/bootstrap.php';
checkRole('user');
/*
    Ambil data summary (income, expense, count) dalam satu query
*/
$summaryQuery = "
    SELECT
        COALESCE(SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END), 0) AS total_income,
        COALESCE(SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END), 0) AS total_expense,
        COUNT(id) AS total_transactions
    FROM
        transactions
    WHERE
        user_id = ?
";
$stmtSummary = mysqli_prepare($conn, $summaryQuery);
mysqli_stmt_bind_param($stmtSummary, "i", $user_id);
mysqli_stmt_execute($stmtSummary);
$resultSummary = mysqli_stmt_get_result($stmtSummary);
$summaryData = mysqli_fetch_assoc($resultSummary);

$totalIncome = $summaryData['total_income'];
$totalExpense = $summaryData['total_expense'];
$totalTransactions = $summaryData['total_transactions'];

/*
    Hitung saldo
*/
$balance = $totalIncome - $totalExpense;

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
<html lang="<?= htmlspecialchars($current_language) ?>">
<head>
    <meta charset="UTF-8">
    <title><?= __('home') ?> - Dashboard Utama</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body data-theme="<?= htmlspecialchars($current_theme) ?>">
<?php
include __DIR__ . '/user/navbar.php';
?>

<div class="container mt-4 mb-5">

    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h3 class="page-title"><?= __('dashboard_title') ?></h3>
            <p class="page-subtitle">
                <?= str_replace('{name}', htmlspecialchars($_SESSION['name']), __('welcome_message')); ?>
            </p>
        </div>
        <a href="user/profile.php"><img src="<?= htmlspecialchars($user['profile_picture'] ?? 'assets/images/avatars/default.png'); ?>" alt="<?= __('profile') ?>" class="rounded-circle" style="width: 60px; height: 60px; object-fit: cover;"></a>
    </div>

    <!-- Kartu Statistik -->
    <div class="row">

        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-0 stat-card stat-income">
                <div class="card-body">
                    <h6><?= __('total_income') ?></h6>
                    <h4 class="text-success">
                        <?= format_currency($totalIncome, $current_currency) ?>
                    </h4>
                    <small class="text-muted"><?= __('all_money_in') ?></small>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-0 stat-card stat-expense">
                <div class="card-body">
                    <h6><?= __('total_expense') ?></h6>
                    <h4 class="text-danger">
                        <?= format_currency($totalExpense, $current_currency) ?>
                    </h4>
                    <small class="text-muted"><?= __('all_money_out') ?></small>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-0 stat-card stat-balance">
                <div class="card-body">
                    <h6><?= __('current_balance') ?></h6>
                    <h4 class="<?= $balance >= 0 ? 'text-primary' : 'text-danger'; ?>">
                        <?= format_currency($balance, $current_currency) ?>
                    </h4>
                    <small class="text-muted"><?= __('income_minus_expense') ?></small>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-0 stat-card stat-count">
                <div class="card-body">
                    <h6><?= __('total_transactions') ?></h6>
                    <h4 class="text-dark">
                        <?= $totalTransactions; ?>
                    </h4>
                    <small class="text-muted"><?= __('total_transaction_records') ?></small>
                </div>
            </div>
        </div>

    </div>

    <!-- Aksi Cepat -->
    <div class="card border-0 mb-4">
        <div class="card-body d-flex flex-wrap gap-2 justify-content-between align-items-center">
            <div>
                <strong><?= __('quick_actions') ?></strong>
                <div class="text-muted small"><?= __('quick_actions_desc') ?></div>
            </div>

            <div class="d-flex flex-wrap gap-2">
                <a href="user/income_create.php" class="btn btn-success btn-sm">+ <?= __('add_income') ?></a>
                <a href="user/expense_create.php" class="btn btn-danger btn-sm">+ <?= __('add_expense') ?></a>
                <a href="user/transactions.php" class="btn btn-primary btn-sm"><?= __('view_history') ?></a>
            </div>
        </div>
    </div>

    <!-- Grafik -->
    <div class="card border-0 mb-4">
        <div class="card-header bg-white">
            <?= __('expense_distribution_chart_monthly') ?>
        </div>

        <div class="card-body">
            <canvas id="expensePieChart" height="120"></canvas>
            <div id="noDataMessage" class="text-center text-muted" style="display: none;"><?= __('no_expense_data_this_month') ?></div>
        </div>
    </div>

    <!-- Transaksi Terbaru -->
    <div class="card border-0">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <span><?= __('latest_transactions') ?></span>
            <a href="user/transactions.php" class="btn btn-outline-primary btn-sm"><?= __('view_all') ?></a>
        </div>

        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-primary">
                        <tr>
                            <th><?= __('no') ?></th>
                            <th><?= __('date') ?></th>
                            <th><?= __('category') ?></th>
                            <th><?= __('type') ?></th>
                            <th><?= __('amount') ?></th>
                            <th><?= __('description') ?></th>
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
                                            <span class="badge bg-success"><?= __('income') ?></span>
                                        <?php else : ?>
                                            <span class="badge bg-danger"><?= __('expense') ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?= format_currency($row['amount'], $current_currency) ?>
                                    </td>
                                    <td><?= htmlspecialchars($row['description']); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="6">
                                    <div class="empty-state">
                                        <?= __('no_transactions_yet') ?>
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

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Pass PHP variables to JavaScript for dynamic formatting
    const appConfig = {
        // Use ISO 4217 currency code from PHP
        currency: '<?= htmlspecialchars($current_currency) ?>',
        // Use BCP 47 language tag from PHP
        language: '<?= htmlspecialchars($current_language) ?>'
    };
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('expensePieChart');
    const noDataMessage = document.getElementById('noDataMessage');

    fetch('user/chart_data.php')
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
                                    const label = context.label || '';
                                    const value = context.raw;
                                    // Use Intl.NumberFormat for proper, dynamic currency formatting
                                    const formattedValue = new Intl.NumberFormat(appConfig.language, {
                                        style: 'currency',
                                        currency: appConfig.currency,
                                        minimumFractionDigits: 0
                                    }).format(value);
                                    return label + ': ' + formattedValue;
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