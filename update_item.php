<?php
require '../motor-parts/backend/connection.php';
// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $quantity = $_POST['quantity'];
    $price = $_POST['price'];

    // Validate and sanitize the data if needed

    // Construct SQL query to update the item
    $sql = "UPDATE items SET name='$name', quantity='$quantity', price='$price' WHERE id='$id'";

    // Execute the SQL query
    if ($conn->query($sql) === TRUE) {
        echo "Item updated successfully";
    } else {
        echo "Error updating item: " . $conn->error;
    }
}

// Close database connection
$conn->close();
?>