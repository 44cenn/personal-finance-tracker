<?php

require_once __DIR__ . '/../config/bootstrap.php';

/** @var mysqli $conn */
/** @var int $user_id */
/** @var string $current_theme */
/** @var string $current_language */

checkRole('user');

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    header('Location: expense.php?error=' . urlencode('data_not_found'));
    exit;
}

/*
|--------------------------------------------------------------------------
| Ambil data pengeluaran
|--------------------------------------------------------------------------
*/

$query = "
    SELECT *
    FROM transactions
    WHERE id = ?
        AND user_id = ?
        AND type = 'expense'
    LIMIT 1
";

$stmt = mysqli_prepare($conn, $query);

if ($stmt === false) {
    header('Location: expense.php?error=' . urlencode('generic_error'));
    exit;
}

mysqli_stmt_bind_param($stmt, 'ii', $id, $user_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) !== 1) {
    mysqli_stmt_close($stmt);

    header('Location: expense.php?error=' . urlencode('data_not_found_or_not_yours'));
    exit;
}

$expense = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

/*
|--------------------------------------------------------------------------
| Ambil daftar kategori
|--------------------------------------------------------------------------
*/

$categoryQuery = "
    SELECT *
    FROM categories
    WHERE type = 'expense'
        AND (
            user_id IS NULL
            OR user_id = ?
        )
    ORDER BY name ASC
";

$categoryStmt = mysqli_prepare($conn, $categoryQuery);

if ($categoryStmt === false) {
    header('Location: expense.php?error=' . urlencode('generic_error'));
    exit;
}

mysqli_stmt_bind_param($categoryStmt, 'i', $user_id);
mysqli_stmt_execute($categoryStmt);

$categories = mysqli_stmt_get_result($categoryStmt);

$errorMessage = null;

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
    <title><?= htmlspecialchars(__('edit_expense'), ENT_QUOTES, 'UTF-8') ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body data-theme="<?= htmlspecialchars($current_theme, ENT_QUOTES, 'UTF-8') ?>">

<?php require __DIR__ . '/navbar.php'; ?>

<div class="container mt-4">

    <div class="d-flex align-items-center mb-3">
        <a href="expense.php" class="btn btn-outline-secondary me-2 btn-back-icon"
           title="<?= htmlspecialchars(__('back_to_expense'), ENT_QUOTES, 'UTF-8') ?>"
           aria-label="<?= htmlspecialchars(__('back_to_expense'), ENT_QUOTES, 'UTF-8') ?>">
            ‹
        </a>

        <h3 class="mb-0"><?= htmlspecialchars(__('edit_expense'), ENT_QUOTES, 'UTF-8') ?></h3>
    </div>

    <div class="card mt-3">
        <div class="card-body">

            <?php if ($errorMessage !== null) : ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <form action="expense_update.php" method="POST" class="needs-validation-custom">

                <input type="hidden" name="id" value="<?= (int) $expense['id'] ?>">

                <div class="mb-3">
                    <label for="category_id" class="form-label"><?= htmlspecialchars(__('category')) ?></label>

                    <select id="category_id" name="category_id" class="form-select" required data-required="true">
                        <option value=""><?= htmlspecialchars(__('choose_option')) ?></option>

                        <?php while ($category = mysqli_fetch_assoc($categories)) : ?>
                            <option value="<?= (int) $category['id'] ?>"
                                <?= (int) $category['id'] === (int) $expense['category_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars((string) $category['name'], ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="amount" class="form-label"><?= htmlspecialchars(__('expense_amount')) ?></label>

                    <input id="amount" type="text" name="amount" class="form-control" data-format="numeric"
                           value="<?= htmlspecialchars((string) $expense['amount'], ENT_QUOTES, 'UTF-8') ?>"
                           required data-required="true" data-amount="true">
                </div>

                <div class="mb-3">
                    <label for="transaction_date" class="form-label"><?= htmlspecialchars(__('date')) ?></label>

                    <input id="transaction_date" type="date" name="transaction_date" class="form-control"
                           value="<?= htmlspecialchars((string) $expense['transaction_date'], ENT_QUOTES, 'UTF-8') ?>"
                           required data-required="true">
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label"><?= htmlspecialchars(__('description')) ?></label>

                    <textarea id="description" name="description" class="form-control" rows="3"
                    ><?= htmlspecialchars((string) ($expense['description'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
                </div>

                <button type="submit" class="btn btn-warning">
                    <?= htmlspecialchars(__('update')) ?>
                </button>

            </form>

        </div>
    </div>

</div>

<?php mysqli_stmt_close($categoryStmt); ?>

<script src="../assets/js/validation.js"></script>
<script src="../assets/js/input-formatter.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>