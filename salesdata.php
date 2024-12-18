<?php
require '../motor-parts/backend/connection.php';

// Fetch sales data
$query_sales_data = "
    SELECT 
        sales_data.id,
        sales_data.transaction_no,  -- Fetch transaction number
        sales_data.quantity,
        sales_data.sales_price,
        sales_data.total_price,
        sales_data.sales_date,
        inventories.name AS item_name
    FROM sales_data
    INNER JOIN inventories ON sales_data.item_id = inventories.id
    ORDER BY sales_data.id ASC";

$result_sales_data = $conn->query($query_sales_data);

// Fetch today's sales
$query_today_sales = "SELECT SUM(total_price) AS total_today FROM sales_data WHERE DATE(sales_date) = CURDATE()";
$result_today_sales = $conn->query($query_today_sales);
$row_today_sales = $result_today_sales->fetch_assoc();
$total_today_sales = $row_today_sales['total_today'] ? number_format($row_today_sales['total_today'], 2) : '0.00';

// Fetch this month's sales
$query_month_sales = "SELECT SUM(total_price) AS total_month FROM sales_data WHERE MONTH(sales_date) = MONTH(CURDATE()) AND YEAR(sales_date) = YEAR(CURDATE())";
$result_month_sales = $conn->query($query_month_sales);
$row_month_sales = $result_month_sales->fetch_assoc();
$total_month_sales = $row_month_sales['total_month'] ? number_format($row_month_sales['total_month'], 2) : '0.00';

// Fetch this year's sales
$query_year_sales = "SELECT SUM(total_price) AS total_year FROM sales_data WHERE YEAR(sales_date) = YEAR(CURDATE())";
$result_year_sales = $conn->query($query_year_sales);
$row_year_sales = $result_year_sales->fetch_assoc();
$total_year_sales = $row_year_sales['total_year'] ? number_format($row_year_sales['total_year'], 2) : '0.00';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Data</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/cdbootstrap/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/cdbootstrap/css/cdb.min.css" />
    <link rel="stylesheet" href="../motor-parts/src/css/styles.css">
    <style>
        body {
            background-color: #f8f9fa;
            /* Light background color */
        }

        .card {
            border-radius: 10px;
            /* Rounded corners */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            /* Subtle shadow */
        }

        .table th,
        .table td {
            vertical-align: middle;
            /* Center align text */
        }

        .table-hover tbody tr:hover {
            background-color: #e9ecef;
            /* Light gray on hover */
        }

        .btn-danger {
            display: flex;
            align-items: center;
        }

        .btn-danger i {
            margin-right: 5px;
            /* Space between icon and text */
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg bg-primary-subtle">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php">SALES AND INVENTORY</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavDropdown">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="user.php">Account Management</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="items.php">Items</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="inventory.php">Inventory</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="pos.php">Add Sales</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="salesdata.php">Sales</a>
                    </li>
                </ul>
            </div>
            <button class="btn btn-outline-success" type="submit"><a class="logout" href="login.php">Logout</a></button>
        </div>
    </nav>

    <div class="container mt-4">
        <h1 class="text-center mb-4">Sales</h1>

        <!-- Display Sales Summary -->
        <div class="row mb-3">
            <div class="col-md-4">
                <div class="card bg-success-subtle text-dark">
                    <div class="card-body">
                        <h5 class="card-title">Today's Sales</h5>
                        <h6 class="card-subtitle mb-2 text-dark">₱ <?php echo $total_today_sales; ?></h6>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-info-subtle text-dark">
                    <div class="card-body">
                        <h5 class="card-title">This Month's Sales</h5>
                        <h6 class="card-subtitle mb-2 text-dark">₱ <?php echo $total_month_sales; ?></h6>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-danger-subtle text-dark">
                    <div class="card-body">
                        <h5 class="card-title">This Year's Sales</h5>
                        <h6 class="card-subtitle mb-2 text-dark">₱ <?php echo $total_year_sales; ?></h6>
                    </div>
                </div>
            </div>
        </div>

        <button id="print-btn" class="btn btn-primary mb-3">Print Sales Data</button>
        <table class="table table-bordered text-center">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Transaction No</th> <!-- Add this line -->
                    <th>Item Name</th>
                    <th>Quantity</th>
                    <th>Sales Price</th>
                    <th>Total Price</th>
                    <th>Sales Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result_sales_data->num_rows > 0) {
                    while ($row = $result_sales_data->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["id"] . "</td>";
                        echo "<td>" . $row["transaction_no"] . "</td>"; // Display transaction number
                        echo "<td>" . $row["item_name"] . "</td>"; // Change here
                        echo "<td>" . $row["quantity"] . "</td>";
                        echo "<td>" . number_format($row["sales_price"], 2) . "</td>";
                        echo "<td>" . number_format($row["total_price"], 2) . "</td>";
                        echo "<td>" . date("Y-m-d H:i:s", strtotime($row["sales_date"])) . "</td>";
                        echo "<td class='d-flex justify-content-center'><button class='btn btn-danger btn-sm' onclick='deleteSale(" . $row["id"] . ")'><i class='bi bi-trash'></i> Delete</button></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='8'>No sales data available</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function deleteSale(id) {
            if (confirm("Are you sure you want to delete this sale?")) {
                window.location.href = "delete_sale.php?id=" + id;
            }
        }

        document.getElementById("print-btn").onclick = function() {
            window.print();
        };
    </script>
</body>

</html>