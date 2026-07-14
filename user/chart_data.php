<?php
header('Content-Type: application/json');

require_once '../config/bootstrap.php';
checkRole('user');

// Query untuk mengambil total pengeluaran per kategori di bulan ini
$query = "
    SELECT c.name AS category_name, SUM(t.amount) AS total_amount
    FROM transactions t
    JOIN categories c ON t.category_id = c.id
    WHERE t.user_id = ?
    AND t.type = 'expense'
    AND MONTH(t.transaction_date) = MONTH(CURDATE())
    AND YEAR(t.transaction_date) = YEAR(CURDATE())
    GROUP BY c.name
    ORDER BY total_amount DESC
";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$labels = [];
$data = [];

while ($row = mysqli_fetch_assoc($result)) {
    $labels[] = $row['category_name'];
    $data[] = (float) $row['total_amount'];
}

echo json_encode(['labels' => $labels, 'data' => $data]);

exit;