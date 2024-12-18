<?php
require '../motor-parts/backend/connection.php';

$query = "
    SELECT 
        DATE_FORMAT(pos_data.created_at, '%Y-%m') AS month,
        inventories.name AS item_name,
        SUM(pos_data.quantity) AS total_quantity,
        SUM(pos_data.quantity * inventories.price) AS total_sales
    FROM pos_data
    INNER JOIN inventories ON pos_data.item_id = inventories.id
    GROUP BY YEAR(pos_data.created_at), MONTH(pos_data.created_at), inventories.name
    ORDER BY pos_data.created_at ASC";
$result = $conn->query($query);

$transactions = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $transactions[] = [
            'month' => $row['month'],
            'item_name' => $row['item_name'],
            'total_quantity' => $row['total_quantity'],
            'total_sales' => number_format($row['total_sales'], 2)
        ];
    }
}

echo json_encode(['success' => true, 'transactions' => $transactions]);
?>
