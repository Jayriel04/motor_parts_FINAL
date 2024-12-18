<?php
// Establish a connection to your database
require '../motor-parts/backend/connection.php';

// Check if the connection is successful
if (!isset($conn) || $conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize total sales   
$totalSales = 0;
$query = "SELECT SUM(pos_data.quantity * items.price) AS total_sales
          FROM pos_data
          INNER JOIN items ON pos_data.item_id = items.item_id";
$result = $conn->query($query);
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $totalSales = $row["total_sales"] !== null ? $row["total_sales"] : 0; // Ensure it's not null
}

// Initialize total items count
$totalItems = 0;
$query = "SELECT COUNT(*) AS total_items FROM items";
$result = $conn->query($query);
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $totalItems = $row["total_items"];
}

// Initialize total inventory count
$totalInventory = 0;
$query = "SELECT COUNT(id) AS total_inventory FROM inventories";
$result = $conn->query($query);
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $totalInventory = $row["total_inventory"];
}

// Chart data for daily sales
$sql = "SELECT pos_data.created_at, pos_data.quantity, items.price 
        FROM pos_data 
        JOIN items ON pos_data.item_id = items.item_id";
$result = $conn->query($sql);
$chartData = array();
while ($row = $result->fetch_assoc()) {
    $chartData[] = array(
        'created_at' => $row['created_at'],
        'sales' => $row['quantity'] * $row['price']
    );
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/cdbootstrap/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/cdbootstrap/css/cdb.min.css" />
    <link rel="stylesheet" href="../motor-parts/src/css/styles.css" />
    <script src="https://cdn.jsdelivr.net/npm/cdbootstrap/js/cdb.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/cdbootstrap/js/bootstrap.min.js"></script>
    <script src="https://kit.fontawesome.com/9d1d9a82d2.js" crossorigin="anonymous"></script>
    <style>
        .chart-container {
            width: 70%;
            height: 50%;
            margin: auto;
        }

        body {
            background-color: lightgrey;
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
                    <li class="nav-item"><a class="nav-link" href="user.php">Account Management</a></li>
                    <li class="nav-item"><a class="nav-link" href="items.php">Items</a></li>
                    <li class="nav-item"><a class="nav-link" href="inventory.php">Inventory</a></li>
                    <li class="nav-item"><a class="nav-link" href="pos.php">Add Sales</a></li>
                    <li class="nav-item"><a class="nav-link" href="salesdata.php">Sales</a></li>
                </ul>
            </div>
            <button class="btn btn-outline-success" type="submit"><a class="logout" href="login.php">Logout</a></button>
        </div>
    </nav>

    <h1>Dashboard</h1>
    <div class="row mb-3 w-75 m-auto">
        <div class="col">
            <div class="card w-75 p-3 mb-2 bg-warning-subtle text-warning-emphasis">
                <div class="card-body">
                    <h5 class="card-title">TOTAL SALES</h5>
                    <h6 class="card-subtitle mb-2 text-body-secondary">₱ <?php echo number_format($totalSales, 2); ?></h6>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card w-75 p-3 mb-2 bg-info-subtle text-info-emphasis">
                <div class="card-body">
                    <h5 class="card-title">ITEMS</h5>
                    <h6 class="card-subtitle mb-2 text-body-secondary"><?php echo $totalItems; ?></h6>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card w-75 p-3 mb-2 bg-danger-subtle text-danger-emphasis">
                <div class="card-body">
                    <h5 class="card-title">INVENTORY</h5>
                    <h6 class="card-subtitle mb-2 text-body-secondary"><?php echo $totalInventory; ?></h6>
                </div>
            </div>
        </div>
    </div>

    <div class="text-center mb-3">
        <select id="sales-period" class="form-select w-25 m-auto">
            <option value="daily">Daily Sales</option>
            <option value="weekly">Weekly Sales</option>
            <option value="monthly">Monthly Sales</option>
            <option value="yearly">Yearly Sales</option>
        </select>
    </div>

    <style>
        .form-select {
            max-width: 150px;
            font-size: 0.9rem;
        }
    </style>


    <div class="card chart-container">
        <h3>Sales Overview</h3>
        <canvas id="sales-chart"></canvas>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.js"></script>

    <script>
        const data = <?php echo json_encode($chartData); ?>;

        // Function to prepare data based on the selected period
        async function prepareChartData(period) {
            const response = await fetch(`fetch_sales_data.php?period=${period}`);
            const data = await response.json();
            console.log(data); // Debugging line to check the response
            return {
                labels: data.labels,
                data: data.data
            };
        }

        // Initial chart setup
        async function initializeChart() {
            const salesCtx = document.getElementById("sales-chart").getContext('2d');
            let currentPeriod = 'daily';
            let chartData = await prepareChartData(currentPeriod);

            let salesChart = new Chart(salesCtx, {
                type: 'bar',
                data: {
                    labels: chartData.labels,
                    datasets: [{
                        label: 'SALES',
                        backgroundColor: 'rgba(161, 198, 247, 1)',
                        borderColor: 'rgb(47, 128, 237)',
                        data: chartData.data,
                        indexAxis: 'x'
                    }]
                },
                options: {
                    scales: {
                        x: {
                            beginAtZero: true
                        },
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // Event listener for the dropdown
            document.getElementById("sales-period").addEventListener("change", async function() {
                currentPeriod = this.value;
                let newChartData = await prepareChartData(currentPeriod);
                salesChart.data.labels = newChartData.labels;
                salesChart.data.datasets[0].data = newChartData.data;
                salesChart.update();
            });
        }

        // Call the initializeChart function to set up the chart
        initializeChart();
    </script>
</body>

</html>