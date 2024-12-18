<?php
// Connect to your database
require '../motor-parts/backend/connection.php';
//check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch data from the user table
$sql = "SELECT * FROM users";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/cdbootstrap/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/cdbootstrap/css/cdb.min.css" />
    <link rel="stylesheet" href="../motor-parts/src/css/styles.css">
</head>
<style>
    body {
        background-color: lightgray;
    }
</style>

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
                        <a class="nav-link" href="sales.php">Sales</a>
                    </li>
                </ul>
            </div>
            <button class="btn btn-outline-success" type="submit"><a class="logout" href="login.php">Logout</a></button>
        </div>
    </nav>

    <div class="row">
        <div class="col-auto me-auto mx-3">
            <h1>Account Management</h1>
        </div>
        <!-- <div class="col-auto mx-4 mt-2">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
                Add User
            </button>
        </div> -->
    </div>

    <table class="table table-bordered text-center">
        <tr>
            <th>Id</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Username</th>
            <th>Last Login</th>
            <th>Action</th>
        </tr>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['user_id'] . "</td>";
                    echo "<td>" . $row['first_name'] . "</td>";
                    echo "<td>" . $row['last_name'] . "</td>";
                    echo "<td>" . $row['username'] . "</td>";
                    echo "<td>" . $row['last_login'] . "</td>";
                    echo "<td><button type='button' class='btn btn-danger' onclick='deleteUser(" . $row['user_id'] . ")'>Delete</button></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6'>No users found</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <!-- Modal -->
    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Add User</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3 row">
                        <label for="inputnumber" class="col-sm-2 col-form-label">Id:</label>
                        <div class="col-sm-10 mb-3">
                            <input type="number" class="form-control" id="inputnumber">
                        </div>
                        <label for="inputtext" class="col-sm-2 col-form-label">First Name:</label>
                        <div class="col-sm-10 mb-3">
                            <input type="text" class="form-control" id="inputtext">
                        </div>
                        <label for="inputtext" class="col-sm-2 col-form-label">Last Name:</label>
                        <div class="col-sm-10 mb-3">
                            <input type="text" class="form-control" id="inputtext">
                        </div>
                        <label for="inputtext" class="col-sm-2 col-form-label">Username:</label>
                        <div class="col-sm-10 mb-3">
                            <input type="text" class="form-control" id="inputtext">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary">Add</button>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
        <script>
            function deleteUser(userId) {
                if (confirm("Are you sure you want to delete this user?")) {
                    // Send an AJAX request to delete the user
                    fetch('delete_user.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ userId: userId }),
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
        </script>
    </body>

</html>
<?php
// Close the database connection
$conn->close();
?>