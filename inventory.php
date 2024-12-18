<?php
require '../motor-parts/backend/connection.php';

// Add inventory item
if (isset($_POST['add'])) {
    $name = $_POST['name'];
    $attributes = $_POST['attributes'];
    $price = $_POST['price'];
    $inventory = $_POST['inventory'];

    $sql = "INSERT INTO inventories (name, attributes, price, inventory) 
            VALUES ('$name', '$attributes', '$price', '$inventory')";

    if ($conn->query($sql) === TRUE) {
        header('location: inventory.php'); // Redirect after success
        exit;
    } else {
        echo "<script>alert('Error: " . $conn->error . "');</script>";
    }
}

// Edit inventory item
if (isset($_POST['edit'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $attributes = $_POST['attributes'];
    $price = $_POST['price'];
    $inventory = $_POST['inventory'];

    $sql = "UPDATE inventories SET name='$name', attributes='$attributes', price='$price', inventory='$inventory' WHERE id='$id'";

    if ($conn->query($sql) === TRUE) {
        header('location: inventory.php');
        exit;
    } else {
        echo "<script>alert('Error: " . $conn->error . "');</script>";
    }
}

// Handle deletion via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    $id = $_POST['id'];
    $sql = "DELETE FROM inventories WHERE id='$id'";

    if ($conn->query($sql) === TRUE) {
        header('Location: inventory.php');
        exit;
    } else {
        echo "<script>alert('Error: " . $conn->error . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Items</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/cdbootstrap/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/cdbootstrap/css/cdb.min.css" />
    <link rel="stylesheet" href="../motor-parts/src/css/styles.css">
    <style>
        .table-warning {
            background-color: #ffcc00;
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

    <div class="row">
        <div class="col-auto me-auto mx-3">
            <h1>Inventory</h1>
        </div>
        <div class="col-auto mx-4 mt-2"> 
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addModal">Add</button>
        </div>

        <table class="table table-bordered text-center">
            <thead class="table-dark">
                <tr>
                    <th>Id</th>
                    <th>Name</th>
                    <th>Attributes</th>
                    <th>Price</th>
                    <th>Inventory</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql_select = "SELECT * FROM inventories"; 
                $result = $conn->query($sql_select);

                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        $lowStockClass = $row['inventory'] < 20 ? 'table-warning' : '';
                        echo "<tr class='$lowStockClass' onmouseover='checkStock({$row['id']}, {$row['inventory']})'>";
                        echo "<th scope='row'>{$row['id']}</th>";
                        echo "<td>{$row['name']}</td>";
                        echo "<td>{$row['attributes']}</td>";
                        echo "<td>{$row['price']}</td>";
                        echo "<td>{$row['inventory']}</td>";
                        echo "<td>
                                <button class='btn btn-primary' onclick=\"test('{$row['id']}', '{$row['name']}', '{$row['attributes']}', '{$row['price']}', '{$row['inventory']}')\">Edit</button>
                                <button class='btn btn-danger' onclick='deleteInventory({$row['id']})'>Delete</button>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>No results found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Add Modal -->
    <div id="addModal" class="modal fade" tabindex="-1">
        <form method="post">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">Add Inventory</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" id="name" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="attributes" class="form-label">Attributes</label>
                            <input type="text" id="attributes" name="attributes" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="price" class="form-label">Price</label>
                            <input type="number" id="price" name="price" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="inventory" class="form-label">Inventory</label>
                            <input type="number" id="inventory" name="inventory" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="add" class="btn btn-success">Add</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="modal fade" tabindex="-1">
        <form method="post">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-warning text-white">
                        <h5 class="modal-title">Edit Inventory</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="itemId" name="id">
                        <div class="mb-3">
                            <label for="itemName" class="form-label">Name</label>
                            <input type="text" id="itemName" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="itemAttributes" class="form-label">Attributes</label>
                            <input type="text" id="itemAttributes" name="attributes" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="itemPrice" class="form-label">Price</label>
                            <input type="number" id="itemPrice" name="price" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="itemInventory" class="form-label">Inventory</label>
                            <input type="number" id="itemInventory" name="inventory" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="edit" class="btn btn-warning">Save Changes</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        const alertedStocks = new Set(); // Track alerted stock IDs

        function test(id, name, attributes, price, inventory) {
            document.getElementById('itemId').value = id;
            document.getElementById('itemName').value = name;
            document.getElementById('itemAttributes').value = attributes;
            document.getElementById('itemPrice').value = price;
            document.getElementById('itemInventory').value = inventory;
            const editModal = new bootstrap.Modal(document.getElementById('editModal'));
            editModal.show();
        }

        function deleteInventory(id) {
            if (confirm("Are you sure you want to delete this inventory item?")) {
                const formData = new FormData();
                formData.append('delete', true);
                formData.append('id', id);

                fetch('inventory.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (response.redirected) {
                        window.location.href = response.url; // Redirect to inventory page
                    } else {
                        return response.text();
                    }
                })
                .then(data => {
                    if (data.includes('Error')) {
                        alert('Failed to delete inventory. Please try again.');
                    } else {
                        location.reload(); // Reload the page to see the changes
                    }
                })
                .catch(error => console.error('Error:', error));
            }
        }

        function checkStock(id, inventory) {
            if (inventory < 20 && !alertedStocks.has(id)) {
                alertedStocks.add(id); // Mark this ID as alerted
                alert("Warning: Stock is low (" + inventory + " left)!");
            }
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>