<?php
require '../motor-parts/backend/connection.php';

// Handle form submission for recording sales
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['record'])) {
    $item_name = $_POST['item_name'];
    $sales_date = $_POST['sales_dates'];
    $sales_quantity = $_POST['sales_quantity'];
    $sales_price = $_POST['sales_price'];

    // Insert the record into the database
    $query_insert = "INSERT INTO pos_data (item_id, quantity, created_at) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query_insert);
    $stmt->bind_param("iis", $item_id, $sales_quantity, $sales_date);

    if ($stmt->execute()) {
        echo "<script>alert('Record successfully added!');</script>";
    } else {
        echo "<script>alert('Failed to record the sale.');</script>";
    }
    $stmt->close();
}

// Fetch daily sales with item names
$query_pos_data = "
    SELECT 
        pos_data.pos_id,
        pos_data.created_at,
        pos_data.quantity,
        inventories.name AS item_name
    FROM pos_data
    INNER JOIN inventories ON pos_data.item_id = inventories.id";
$result_pos_data = $conn->query($query_pos_data);

// Display the fetched data in a table
if ($result_pos_data->num_rows > 0) {
    echo "<table class='table'>";
    echo "<thead><tr><th>ID</th><th>Item Name</th><th>Quantity</th><th>Sales Date</th></tr></thead>";
    echo "<tbody>";
    while ($row = $result_pos_data->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row["pos_id"] . "</td>"; // Display pos_id
        echo "<td>" . $row["item_name"] . "</td>";
        echo "<td>" . $row["quantity"] . "</td>";
        echo "<td>" . date("Y-m-d H:i:s", strtotime($row["created_at"])) . "</td>";
        echo "</tr>";
    }
    echo "</tbody></table>";
} else {
    echo "No sales data available.";
}

// Fetch monthly sales with item names
$query_monthly_sales = "
    SELECT 
        DATE_FORMAT(pos_data.created_at, '%Y-%m') AS month,
        inventories.name AS item_name,
        SUM(pos_data.quantity) AS total_quantity,
        SUM(pos_data.quantity * inventories.price) AS total_sales
    FROM pos_data
    INNER JOIN inventories ON pos_data.item_id = inventories.id
    GROUP BY YEAR(pos_data.created_at), MONTH(pos_data.created_at), inventories.name
    ORDER BY pos_data.created_at ASC";

$result_monthly_sales = $conn->query($query_monthly_sales);
?>