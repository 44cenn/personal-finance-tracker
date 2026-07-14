<?php

require_once __DIR__ . '/../config/bootstrap.php';

/** @var mysqli $conn */
/** @var int $user_id */
/** @var string $current_theme */
/** @var string $current_currency */
/** @var string $current_language */

checkRole('user');

/*
|--------------------------------------------------------------------------
| Filter
|--------------------------------------------------------------------------
*/

$startDate = trim((string) ($_GET['start_date'] ?? ''));
$endDate = trim((string) ($_GET['end_date'] ?? ''));
$type = trim((string) ($_GET['type'] ?? ''));
$categoryId = isset($_GET['category_id']) ? (int) $_GET['category_id'] : 0;
$keyword = trim((string) ($_GET['keyword'] ?? ''));

$allowedTypes = ['', 'income', 'expense'];

if (!in_array($type, $allowedTypes, true)) {
    $type = '';
}

/*
|--------------------------------------------------------------------------
| Pagination
|--------------------------------------------------------------------------
*/

$limit = 5;

$page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);

if (!$page || $page < 1) {
    $page = 1;
}

$offset = ($page - 1) * $limit;

/*
|--------------------------------------------------------------------------
| Daftar kategori
|--------------------------------------------------------------------------
*/

$categoryQuery = "
    SELECT *
    FROM categories
    WHERE user_id IS NULL
        OR user_id = ?
    ORDER BY type ASC, name ASC
";

$categoryStmt = mysqli_prepare($conn, $categoryQuery);

if ($categoryStmt === false) {
    die(htmlspecialchars(__('generic_error'), ENT_QUOTES, 'UTF-8'));
}

mysqli_stmt_bind_param($categoryStmt, 'i', $user_id);
mysqli_stmt_execute($categoryStmt);

$categoryResult = mysqli_stmt_get_result($categoryStmt);

/*
|--------------------------------------------------------------------------
| Query filter
|--------------------------------------------------------------------------
*/

$whereQuery = "
    FROM transactions
    INNER JOIN categories
        ON transactions.category_id = categories.id
    WHERE transactions.user_id = ?
";

$params = [$user_id];
$types = 'i';

if ($startDate !== '') {
    $whereQuery .= " AND transactions.transaction_date >= ? ";
    $params[] = $startDate;
    $types .= 's';
}

if ($endDate !== '') {
    $whereQuery .= " AND transactions.transaction_date <= ? ";
    $params[] = $endDate;
    $types .= 's';
}

if ($type !== '') {
    $whereQuery .= " AND transactions.type = ? ";
    $params[] = $type;
    $types .= 's';
}

if ($categoryId > 0) {
    $whereQuery .= " AND transactions.category_id = ? ";
    $params[] = $categoryId;
    $types .= 'i';
}

if ($keyword !== '') {
    $whereQuery .= " AND transactions.description LIKE ? ";
    $params[] = '%' . $keyword . '%';
    $types .= 's';
}

/*
|--------------------------------------------------------------------------
| Hitung jumlah data
|--------------------------------------------------------------------------
*/

$countQuery = "
    SELECT COUNT(*) AS total_data
    {$whereQuery}
";

$countStmt = mysqli_prepare($conn, $countQuery);

if ($countStmt === false) {
    die(htmlspecialchars(__('generic_error'), ENT_QUOTES, 'UTF-8'));
}

mysqli_stmt_bind_param($countStmt, $types, ...$params);
mysqli_stmt_execute($countStmt);

$countResult = mysqli_stmt_get_result($countStmt);
$countData = mysqli_fetch_assoc($countResult);

$totalData = (int) ($countData['total_data'] ?? 0);
$totalPages = (int) ceil($totalData / $limit);

if ($totalPages < 1) {
    $totalPages = 1;
}

if ($page > $totalPages) {
    $page = $totalPages;
    $offset = ($page - 1) * $limit;
}

/*
|--------------------------------------------------------------------------
| Ambil data transaksi
|--------------------------------------------------------------------------
*/

$query = "
    SELECT
        transactions.*,
        categories.name AS category_name
    {$whereQuery}
    ORDER BY
        transactions.transaction_date DESC,
        transactions.id DESC
    LIMIT ? OFFSET ?
";

$dataParams = $params;
$dataTypes = $types;

$dataParams[] = $limit;
$dataParams[] = $offset;
$dataTypes .= 'ii';

$stmt = mysqli_prepare($conn, $query);

if ($stmt === false) {
    die(htmlspecialchars(__('generic_error'), ENT_QUOTES, 'UTF-8'));
}

mysqli_stmt_bind_param($stmt, $dataTypes, ...$dataParams);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

/*
|--------------------------------------------------------------------------
| Query string pagination
|--------------------------------------------------------------------------
*/

$queryString = http_build_query([
    'start_date' => $startDate,
    'end_date' => $endDate,
    'type' => $type,
    'category_id' => $categoryId > 0 ? $categoryId : '',
    'keyword' => $keyword,
]);

?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($current_language, ENT_QUOTES, 'UTF-8') ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars(__('transaction_history'), ENT_QUOTES, 'UTF-8') ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body data-theme="<?= htmlspecialchars($current_theme, ENT_QUOTES, 'UTF-8') ?>">

<?php require __DIR__ . '/navbar.php'; ?>

<div class="container mt-4">

    <div class="d-flex align-items-center mb-3">
        <a href="../index.php" class="btn btn-outline-secondary me-2 btn-back-icon"
           title="<?= htmlspecialchars(__('back_to_home'), ENT_QUOTES, 'UTF-8') ?>"
           aria-label="<?= htmlspecialchars(__('back_to_home'), ENT_QUOTES, 'UTF-8') ?>">
            ‹
        </a>

        <h3 class="mb-0"><?= htmlspecialchars(__('transaction_history'), ENT_QUOTES, 'UTF-8') ?></h3>
    </div>

    <p class="text-muted"><?= htmlspecialchars(__('transactions_subtitle'), ENT_QUOTES, 'UTF-8') ?></p>

    <div class="card shadow-sm mb-4">

        <div class="card-header bg-white">
            <strong><?= htmlspecialchars(__('filter_transactions'), ENT_QUOTES, 'UTF-8') ?></strong>
        </div>

        <div class="card-body">

            <form method="GET" action="transactions.php">

                <div class="row">

                    <div class="col-md-3 mb-3">
                        <label for="start_date" class="form-label"><?= htmlspecialchars(__('start_date')) ?></label>
                        <input id="start_date" type="date" name="start_date" class="form-control"
                               value="<?= htmlspecialchars($startDate, ENT_QUOTES, 'UTF-8') ?>">
                    </div>

                    <div class="col-md-3 mb-3">
                        <label for="end_date" class="form-label"><?= htmlspecialchars(__('end_date')) ?></label>
                        <input id="end_date" type="date" name="end_date" class="form-control"
                               value="<?= htmlspecialchars($endDate, ENT_QUOTES, 'UTF-8') ?>">
                    </div>

                    <div class="col-md-3 mb-3">
                        <label for="type" class="form-label"><?= htmlspecialchars(__('type')) ?></label>

                        <select id="type" name="type" class="form-select">
                            <option value=""><?= htmlspecialchars(__('all_types')) ?></option>
                            <option value="income" <?= $type === 'income' ? 'selected' : '' ?>>
                                <?= htmlspecialchars(__('income')) ?>
                            </option>
                            <option value="expense" <?= $type === 'expense' ? 'selected' : '' ?>>
                                <?= htmlspecialchars(__('expense')) ?>
                            </option>
                        </select>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label for="category_id" class="form-label"><?= htmlspecialchars(__('category')) ?></label>

                        <select id="category_id" name="category_id" class="form-select">
                            <option value=""><?= htmlspecialchars(__('all_categories')) ?></option>

                            <?php while ($category = mysqli_fetch_assoc($categoryResult)) : ?>
                                <option value="<?= (int) $category['id'] ?>"
                                    <?= $categoryId === (int) $category['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars((string) $category['name'], ENT_QUOTES, 'UTF-8') ?>
                                    <?= $category['type'] === 'income'
                                        ? '(' . htmlspecialchars(__('income')) . ')'
                                        : '(' . htmlspecialchars(__('expense')) . ')' ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="keyword" class="form-label"><?= htmlspecialchars(__('search_description')) ?></label>
                        <input id="keyword" type="text" name="keyword" class="form-control"
                               placeholder="<?= htmlspecialchars(__('search_placeholder'), ENT_QUOTES, 'UTF-8') ?>"
                               value="<?= htmlspecialchars($keyword, ENT_QUOTES, 'UTF-8') ?>">
                    </div>

                    <div class="col-md-6 mb-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <?= htmlspecialchars(__('filter_transactions')) ?>
                        </button>

                        <a href="transactions.php" class="btn btn-secondary">
                            <?= htmlspecialchars(__('reset')) ?>
                        </a>
                    </div>

                </div>

            </form>

        </div>

    </div>

    <div class="card shadow-sm">

        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <strong><?= htmlspecialchars(__('transaction_data')) ?></strong>

            <small class="text-muted">
                <?= htmlspecialchars(__('total_data')) ?>: <?= $totalData ?>
            </small>
        </div>

        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-primary">
                        <tr>
                            <th><?= htmlspecialchars(__('no')) ?></th>
                            <th><?= htmlspecialchars(__('date')) ?></th>
                            <th><?= htmlspecialchars(__('category')) ?></th>
                            <th><?= htmlspecialchars(__('type')) ?></th>
                            <th><?= htmlspecialchars(__('amount')) ?></th>
                            <th><?= htmlspecialchars(__('description')) ?></th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php if (mysqli_num_rows($result) > 0) : ?>
                        <?php $number = $offset + 1; ?>
                        <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                            <tr>
                                <td><?= $number++; ?></td>
                                <td><?= htmlspecialchars((string) $row['transaction_date'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars((string) $row['category_name'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td>
                                    <?php if ($row['type'] === 'income') : ?>
                                        <span class="badge bg-success"><?= htmlspecialchars(__('income')) ?></span>
                                    <?php else : ?>
                                        <span class="badge bg-danger"><?= htmlspecialchars(__('expense')) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars(format_currency($row['amount'], $current_currency), ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars((string) ($row['description'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="6" class="text-center">
                                <?= htmlspecialchars(__('no_transactions_found'), ENT_QUOTES, 'UTF-8') ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <nav class="mt-3" aria-label="<?= htmlspecialchars(__('transaction_history'), ENT_QUOTES, 'UTF-8') ?>">
                <ul class="pagination justify-content-center">

                    <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link"
                           href="transactions.php?<?= htmlspecialchars($queryString, ENT_QUOTES, 'UTF-8') ?>&page=<?= max(1, $page - 1) ?>">
                            <?= htmlspecialchars(__('previous')) ?>
                        </a>
                    </li>

                    <?php
                    $pageWindow = 2;
                    $startPage = max(1, $page - $pageWindow);
                    $endPage = min($totalPages, $page + $pageWindow);
                    ?>

                    <?php if ($startPage > 1) : ?>
                        <li class="page-item">
                            <a class="page-link"
                               href="transactions.php?<?= htmlspecialchars($queryString, ENT_QUOTES, 'UTF-8') ?>&page=1">
                                1
                            </a>
                        </li>

                        <?php if ($startPage > 2) : ?>
                            <li class="page-item disabled">
                                <span class="page-link">...</span>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php for ($i = $startPage; $i <= $endPage; $i++) : ?>
                        <li class="page-item <?= $page === $i ? 'active' : '' ?>">
                            <a class="page-link"
                               href="transactions.php?<?= htmlspecialchars($queryString, ENT_QUOTES, 'UTF-8') ?>&page=<?= $i ?>">
                                <?= $i ?>
                            </a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($endPage < $totalPages) : ?>
                        <?php if ($endPage < $totalPages - 1) : ?>
                            <li class="page-item disabled">
                                <span class="page-link">...</span>
                            </li>
                        <?php endif; ?>

                        <li class="page-item">
                            <a class="page-link"
                               href="transactions.php?<?= htmlspecialchars($queryString, ENT_QUOTES, 'UTF-8') ?>&page=<?= $totalPages ?>">
                                <?= $totalPages ?>
                            </a>
                        </li>
                    <?php endif; ?>

                    <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                        <a class="page-link"
                           href="transactions.php?<?= htmlspecialchars($queryString, ENT_QUOTES, 'UTF-8') ?>&page=<?= min($totalPages, $page + 1) ?>">
                            <?= htmlspecialchars(__('next')) ?>
                        </a>
                    </li>

                </ul>
            </nav>

        </div>

    </div>

</div>

<?php
mysqli_stmt_close($categoryStmt);
mysqli_stmt_close($countStmt);
mysqli_stmt_close($stmt);
?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>