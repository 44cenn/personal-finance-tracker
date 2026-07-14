<?php

require_once __DIR__ . '/../config/bootstrap.php';

/** @var mysqli $conn */
/** @var int $user_id */

checkRole('user');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: expense.php');
    exit;
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$categoryId = filter_input(INPUT_POST, 'category_id', FILTER_VALIDATE_INT);

$amountInput = $_POST['amount'] ?? '';
$transactionDate = trim((string) ($_POST['transaction_date'] ?? ''));
$description = trim((string) ($_POST['description'] ?? ''));

$amountInput = str_replace(['.', ',', ' '], '', (string) $amountInput);

if (!$id || !$categoryId || $amountInput === '' || $transactionDate === '') {
    $targetId = $id ?: '';

    header('Location: expense_edit.php?id=' . urlencode((string) $targetId) . '&error=' . urlencode('error_all_fields_required'));
    exit;
}

if (!is_numeric($amountInput)) {
    header('Location: expense_edit.php?id=' . urlencode((string) $id) . '&error=' . urlencode('amount_must_be_numeric'));
    exit;
}

$amount = (float) $amountInput;

if ($amount <= 0) {
    header('Location: expense_edit.php?id=' . urlencode((string) $id) . '&error=' . urlencode('error_amount_must_be_positive'));
    exit;
}

$dateCheck = DateTime::createFromFormat('Y-m-d', $transactionDate);

if (!$dateCheck || $dateCheck->format('Y-m-d') !== $transactionDate) {
    header('Location: expense_edit.php?id=' . urlencode((string) $id) . '&error=' . urlencode('invalid_date_format'));
    exit;
}

/*
|--------------------------------------------------------------------------
| Validasi kategori
|--------------------------------------------------------------------------
*/

$categoryCheckQuery = "
    SELECT id
    FROM categories
    WHERE id = ?
        AND type = 'expense'
        AND (
            user_id IS NULL
            OR user_id = ?
        )
    LIMIT 1
";

$categoryStmt = mysqli_prepare($conn, $categoryCheckQuery);

if ($categoryStmt === false) {
    header('Location: expense_edit.php?id=' . urlencode((string) $id) . '&error=' . urlencode('generic_error'));
    exit;
}

mysqli_stmt_bind_param($categoryStmt, 'ii', $categoryId, $user_id);
mysqli_stmt_execute($categoryStmt);

$categoryResult = mysqli_stmt_get_result($categoryStmt);

if (mysqli_num_rows($categoryResult) !== 1) {
    mysqli_stmt_close($categoryStmt);

    header('Location: expense_edit.php?id=' . urlencode((string) $id) . '&error=' . urlencode('invalid_category'));
    exit;
}

mysqli_stmt_close($categoryStmt);

/*
|--------------------------------------------------------------------------
| Update transaksi
|--------------------------------------------------------------------------
*/

$query = "
    UPDATE transactions
    SET
        category_id = ?,
        amount = ?,
        description = ?,
        transaction_date = ?
    WHERE id = ?
        AND user_id = ?
        AND type = 'expense'
";

$stmt = mysqli_prepare($conn, $query);

if ($stmt === false) {
    header('Location: expense_edit.php?id=' . urlencode((string) $id) . '&error=' . urlencode('failed_to_update_data'));
    exit;
}

mysqli_stmt_bind_param(
    $stmt,
    'idssii',
    $categoryId,
    $amount,
    $description,
    $transactionDate,
    $id,
    $user_id
);

if (!mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);

    header('Location: expense_edit.php?id=' . urlencode((string) $id) . '&error=' . urlencode('failed_to_update_data'));
    exit;
}

$affectedRows = mysqli_stmt_affected_rows($stmt);

mysqli_stmt_close($stmt);

if ($affectedRows > 0) {
    header('Location: expense.php?success=' . urlencode('expense_updated_successfully'));
    exit;
}

header('Location: expense.php?success=' . urlencode('no_data_changed'));
exit;