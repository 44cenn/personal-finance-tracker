<?php

require_once __DIR__ . '/../config/bootstrap.php';

/** @var mysqli $conn */
/** @var int $user_id */
/** @var string $current_theme */
/** @var string $current_currency */
/** @var string $current_language */

checkRole('user');

$query = "
    SELECT
        transactions.*,
        categories.name AS category_name
    FROM transactions
    INNER JOIN categories
        ON transactions.category_id = categories.id
    WHERE transactions.user_id = ?
        AND transactions.type = 'expense'
    ORDER BY transactions.transaction_date DESC
";

$stmt = mysqli_prepare($conn, $query);

if ($stmt === false) {
    die(htmlspecialchars(__('generic_error'), ENT_QUOTES, 'UTF-8'));
}

mysqli_stmt_bind_param($stmt, 'i', $user_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

/*
|--------------------------------------------------------------------------
| Pesan sukses dan error
|--------------------------------------------------------------------------
|
| Mendukung query parameter berupa translation key:
| ?success=expense_added_successfully
|
| Jika parameter bukan translation key, teks aslinya tetap ditampilkan.
|
*/

$successMessage = null;
$errorMessage = null;

if (isset($_GET['success']) && is_string($_GET['success']) && $_GET['success'] !== '') {
    $successKey = $_GET['success'];
    $successMessage = __($successKey, $successKey);
}

if (isset($_GET['error']) && is_string($_GET['error']) && $_GET['error'] !== '') {
    $errorKey = $_GET['error'];
    $errorMessage = __($errorKey, $errorKey);
}

?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($current_language, ENT_QUOTES, 'UTF-8') ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars(__('expense_data'), ENT_QUOTES, 'UTF-8') ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body data-theme="<?= htmlspecialchars($current_theme, ENT_QUOTES, 'UTF-8') ?>">

<?php require __DIR__ . '/navbar.php'; ?>

<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-3">

        <div class="d-flex align-items-center">
            <a href="../index.php" class="btn btn-outline-secondary me-2 btn-back-icon"
               title="<?= htmlspecialchars(__('back_to_home'), ENT_QUOTES, 'UTF-8') ?>"
               aria-label="<?= htmlspecialchars(__('back_to_home'), ENT_QUOTES, 'UTF-8') ?>">
                ‹
            </a>

            <h3 class="mb-0"><?= htmlspecialchars(__('expense_data'), ENT_QUOTES, 'UTF-8') ?></h3>
        </div>

        <a href="expense_create.php" class="btn btn-danger">
            + <?= htmlspecialchars(__('add'), ENT_QUOTES, 'UTF-8') ?>
        </a>

    </div>

    <?php if ($successMessage !== null) : ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if ($errorMessage !== null) : ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-primary">
                        <tr>
                            <th><?= htmlspecialchars(__('no')) ?></th>
                            <th><?= htmlspecialchars(__('date')) ?></th>
                            <th><?= htmlspecialchars(__('category')) ?></th>
                            <th><?= htmlspecialchars(__('amount')) ?></th>
                            <th><?= htmlspecialchars(__('description')) ?></th>
                            <th><?= htmlspecialchars(__('actions')) ?></th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php if (mysqli_num_rows($result) > 0) : ?>
                        <?php $number = 1; ?>
                        <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                            <tr>
                                <td><?= $number++; ?></td>
                                <td><?= htmlspecialchars((string) $row['transaction_date'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars((string) $row['category_name'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars(format_currency($row['amount'], $current_currency), ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars((string) ($row['description'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                <td>
                                    <a href="expense_edit.php?id=<?= (int) $row['id'] ?>" class="btn btn-warning btn-sm">
                                        <?= htmlspecialchars(__('edit')) ?>
                                    </a>

                                    <a href="expense_delete.php?id=<?= (int) $row['id'] ?>" class="btn btn-danger btn-sm"
                                       onclick='return confirm(<?= json_encode(__('confirm_delete_expense'), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'>
                                        <?= htmlspecialchars(__('delete')) ?>
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="6" class="text-center">
                                <?= htmlspecialchars(__('no_expense_data'), ENT_QUOTES, 'UTF-8') ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<?php mysqli_stmt_close($stmt); ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>