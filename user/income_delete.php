<?php

require_once __DIR__ . '/../config/bootstrap.php';

/** @var mysqli $conn */
/** @var int $user_id */

checkRole('user');

$id = filter_input(
    INPUT_GET,
    'id',
    FILTER_VALIDATE_INT
);

if (!$id) {
    header(
        'Location: expense.php?error='
        . urlencode('data_not_found')
    );
    exit;
}

$query = "
    DELETE FROM transactions
    WHERE id = ?
        AND user_id = ?
        AND type = 'expense'
";

$stmt = mysqli_prepare($conn, $query);

if ($stmt === false) {
    header(
        'Location: expense.php?error='
        . urlencode('failed_to_delete_data')
    );
    exit;
}

mysqli_stmt_bind_param(
    $stmt,
    'ii',
    $id,
    $user_id
);

if (!mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);

    header(
        'Location: expense.php?error='
        . urlencode('failed_to_delete_data')
    );
    exit;
}

$affectedRows = mysqli_stmt_affected_rows($stmt);

mysqli_stmt_close($stmt);

if ($affectedRows > 0) {
    header(
        'Location: expense.php?success='
        . urlencode('expense_deleted_successfully')
    );
    exit;
}

header(
    'Location: expense.php?error='
    . urlencode('data_not_found_or_not_yours')
);
exit;