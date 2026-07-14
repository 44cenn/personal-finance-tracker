<?php

require_once __DIR__ . '/../config/bootstrap.php';

/** @var mysqli $conn */
/** @var int $user_id */

checkRole('user');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: expense.php');
    exit;
}

$categoryId = filter_input(
    INPUT_POST,
    'category_id',
    FILTER_VALIDATE_INT
);

$amountInput = $_POST['amount'] ?? '';
$transactionDate = trim(
    (string) ($_POST['transaction_date'] ?? '')
);
$description = trim(
    (string) ($_POST['description'] ?? '')
);

$amountInput = str_replace(
    ['.', ',', ' '],
    '',
    (string) $amountInput
);

if (
    !$categoryId
    || $amountInput === ''
    || $transactionDate === ''
) {
    header(
        'Location: expense_create.php?error='
        . urlencode('error_all_fields_required')
    );
    exit;
}

if (!is_numeric($amountInput)) {
    header(
        'Location: expense_create.php?error='
        . urlencode('amount_must_be_numeric')
    );
    exit;
}

$amount = (float) $amountInput;

if ($amount <= 0) {
    header(
        'Location: expense_create.php?error='
        . urlencode('error_amount_must_be_positive')
    );
    exit;
}

$dateCheck = DateTime::createFromFormat(
    'Y-m-d',
    $transactionDate
);

if (
    !$dateCheck
    || $dateCheck->format('Y-m-d') !== $transactionDate
) {
    header(
        'Location: expense_create.php?error='
        . urlencode('invalid_date_format')
    );
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

$categoryStmt = mysqli_prepare(
    $conn,
    $categoryCheckQuery
);

if ($categoryStmt === false) {
    header(
        'Location: expense_create.php?error='
        . urlencode('generic_error')
    );
    exit;
}

mysqli_stmt_bind_param(
    $categoryStmt,
    'ii',
    $categoryId,
    $user_id
);

mysqli_stmt_execute($categoryStmt);

$categoryResult = mysqli_stmt_get_result(
    $categoryStmt
);

if (mysqli_num_rows($categoryResult) !== 1) {
    mysqli_stmt_close($categoryStmt);

    header(
        'Location: expense_create.php?error='
        . urlencode('invalid_category')
    );
    exit;
}

mysqli_stmt_close($categoryStmt);

/*
|--------------------------------------------------------------------------
| Simpan transaksi
|--------------------------------------------------------------------------
*/

$type = 'expense';

$query = "
    INSERT INTO transactions (
        user_id,
        category_id,
        type,
        amount,
        description,
        transaction_date
    )
    VALUES (?, ?, ?, ?, ?, ?)
";

$stmt = mysqli_prepare($conn, $query);

if ($stmt === false) {
    header(
        'Location: expense_create.php?error='
        . urlencode('failed_to_add_data')
    );
    exit;
}

mysqli_stmt_bind_param(
    $stmt,
    'iisdss',
    $user_id,
    $categoryId,
    $type,
    $amount,
    $description,
    $transactionDate
);

if (!mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);

    header(
        'Location: expense_create.php?error='
        . urlencode('failed_to_add_data')
    );
    exit;
}

mysqli_stmt_close($stmt);

header(
    'Location: expense.php?success='
    . urlencode('expense_added_successfully')
);
exit;