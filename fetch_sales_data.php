<?php
require '../motor-parts/backend/connection.php';

// Check if the period parameter is set
if (!isset($_GET['period'])) {
    echo json_encode(['labels' => [], 'data' => []]);
    exit;
}

$period = $_GET['period'];
$salesData = [];
$labels = [];

switch ($period) {
    case 'daily':
        // Get sales data for each sale with separate date and time
        $query = "SELECT DATE(sales_date) AS sale_date, TIME(sales_date) AS sale_time, SUM(total_price) AS total_sales
                  FROM sales_data
                  WHERE sales_date >= CURDATE()
                  GROUP BY sale_date, sale_time
                  ORDER BY sale_date, sale_time";
        break;
    case 'weekly':
        // Get sales data for the current week (Monday to Sunday)
        $query = "SELECT DATE(sales_date) AS date, SUM(total_price) AS total_sales
                  FROM sales_data
                  WHERE sales_date >= DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY)
                  AND sales_date < DATE_ADD(DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY), INTERVAL 7 DAY)
                  GROUP BY DATE(sales_date);";
        break;
    case 'monthly':
        // Get sales data for each month and format the month name
        $query = "SELECT DATE_FORMAT(sales_date, '%M') AS month, SUM(total_price) AS total_sales
                  FROM sales_data
                  GROUP BY month";
        break;
    case 'yearly':
        // Get sales data for each year
        $query = "SELECT YEAR(sales_date) AS year, SUM(total_price) AS total_sales
                  FROM sales_data
                  GROUP BY year";
        break;
    default:
        echo json_encode(['labels' => [], 'data' => []]);
        exit;
}

// Execute the query
$result = $conn->query($query);

// Check for query execution errors
if (!$result) {
    echo json_encode(['error' => 'Query error: ' . $conn->error]);
    exit;
}

// Fetch the results
while ($row = $result->fetch_assoc()) {
    if ($period === 'daily') {
        // Create a label for each sale with separate date and time
        $labels[] = $row['sale_date'] . ' ' . $row['sale_time']; // Combine date and time
        $salesData[] = (float)$row['total_sales']; // Total sales for that date and time
    } elseif ($period === 'weekly') {
        $labels[] = date('l', strtotime($row['date'])); // Get the day of the week
        $salesData[] = (float)$row['total_sales']; // Total sales for that day
    } elseif ($period === 'monthly') {
        $labels[] = $row['month']; // Month name (e.g., December)
        $salesData[] = (float)$row['total_sales']; // Total sales for that month
    } elseif ($period === 'yearly') {
        $labels[] = $row['year']; // Year
        $salesData[] = (float)$row['total_sales']; // Total sales for that year
    }
}

// Return the data as JSON
echo json_encode(['labels' => $labels, 'data' => $salesData]);

// Close the database connection
$conn->close();
?>