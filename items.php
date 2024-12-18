<!-- saving the data to items table -->
<?php
require '../motor-parts/backend/connection.php';
// $items = array();
// Retrieve user input
if (isset($_POST['add'])) {
    $item_name = $_POST['name'];
    $item_quantity = $_POST['quantity'];
    $item_price = $_POST['price'];

    $sql = "INSERT INTO items (item_id, name, quantity, price) 
        VALUES (null, '$item_name', '$item_quantity', '$item_price')";

    if ($conn->query($sql) === TRUE) {
        // Redirect to the items page
        header('location: items.php');
        exit;
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
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
            <h1>Item List</h1>
        </div>
        <div class="col-auto mx-4 mt-2"> <!-- Button trigger modal -->
            <button type="button" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
                Add Item
            </button>
        </div>
    </div>

    <table class="table table-bordered text-center">
    <thead class="table-primary">
        <tr>
            <th>Id</th>
            <th>Name</th>
            <th>Quantity</th>
            <th>Price</th>
            <th>Action</th>
        </tr>
        <tbody>
        <?php
            $sql_select = "SELECT * FROM items"; 
            $result = $conn->query($sql_select);

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr id='row_" . $row["item_id"] . "'>";
                    echo "<th scope='row'>" . $row["item_id"] . "</th>";
                    echo "<td>" . $row["name"] . "</td>";
                    echo "<td>" . $row["quantity"] . "</td>";
                    echo "<td>" . $row["price"] . "</td>";
                    echo "<td>
                            <button type='button' class='btn btn-primary mr-2' data-bs-toggle='modal' data-bs-target='#staticBackdrop-1' onclick='test(". $row['item_id'] .")'>Edit</button>
                            <button type='button' class='btn btn-danger' onclick='deleteItem(" . $row['item_id'] . ")'>Delete</button>
                        </td>";
                    echo "</tr>";
                }                
            } else {
                echo "0 results";
            }
        ?>
        </tbody>
    </table>

    <!-- Modal for adding item-->
    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <form action="" method="post">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="staticBackdropLabel">Add Item</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3 row">
                            <label for="name" class="col-sm-2 col-form-label">Name:</label>
                            <div class="col-sm-10 mb-3">
                                <input type="text" class="form-control" id="name" name ="name">
                            </div>
                            <label for="quantity" class="col-sm-2 col-form-label">Quantity:</label>
                            <div class="col-sm-10 mb-3">
                                <input type="number" class="form-control" id="quantity" name="quantity">
                            </div>
                            <label for="price" class="col-sm-2 col-form-label">Price:</label>
                            <div class="col-sm-10 mb-3">
                                <input type="number" class="form-control" id="price" name="price">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary" name="add">Add</button>
                        </div>
                    </div>
                </div>
            </div>
            </form>
        </div>

        <!-- Modal for editing item-->
        <div class="modal fade" id="staticBackdrop-1" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <form action="edit_item.php" method="post">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="staticBackdropLabel">Edit</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3 row">
                                <label for="iditem" class="col-sm-2 col-form-label">Id Item:</label>
                                <div class="col-sm-10 mb-3">
                                    <input type="text" readonly class="form-control" id="iditem_edit" name="item_id_edit">
                                </div>
                                <label for="name" class="col-sm-2 col-form-label">Name:</label>
                                <div class="col-sm-10 mb-3">
                                    <input type="text" class="form-control" id="itemName_edit" name="name_edit">
                                </div>
                                <label for="quantity" class="col-sm-2 col-form-label">Quantity:</label>
                                <div class="col-sm-10 mb-3">
                                    <input type="number" class="form-control" id="itemQuantity_edit" name="quantity_edit">
                                </div>
                                <label for="price" class="col-sm-2 col-form-label">Price:</label>
                                <div class="col-sm-10 mb-3">
                                    <input type="number" class="form-control" id="itemPrice_edit" name="price_edit">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary" name="edit">Edit</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>

    <script>
        function test(item_id_edit){
            console.log(document.getElementById('iditem_edit'))
            document.getElementById('iditem_edit').value = item_id_edit;
        }

        function deleteItem(itemId) {
                if (confirm("Are you sure you want to delete this user?")) {
                    // Send an AJAX request to delete the user
                    fetch('delete_item.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ itemId: itemId }),
                    })
                    .then(response => {
                        if (response.ok) {
                            // Reload the page after successful deletion
                            location.reload();
                        } else {
                            alert('Failed to delete user');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
                }
            }

            // Open edit modal and populate fields with item data
            function openEditModal(itemId_edit, itemName_edit, itemQuantity_edit, itemPrice_edit) {
                document.getElementById('iditem_edit').value = itemId_edit;
                document.getElementById('itemName_edit').value = itemName_edit;
                document.getElementById('itemQuantity_edit').value = itemQuantity_edit;
                document.getElementById('itemPrice_edit').value = itemPrice_edit;


                document.getElementById('staticBackdrop-1').style.display = 'block';

                // Close edit modal
                document.querySelector('.btn-close').addEventListener('click', function() {
                    document.getElementById('staticBackdrop-1').style.display = 'none';
                });
            }
    </script>
</body>

</html>