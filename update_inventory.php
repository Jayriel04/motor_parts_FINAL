<?php
// Establish a connection to your database
require '../motor-parts/backend/connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve the item ID and new quantity from the POST parameters
    $itemId = $_POST['itemId'];
    $newQuantity = $_POST['newQuantity'];

    // Update the inventory quantity in the database
    $sql = "UPDATE inventories SET inventory = $newQuantity WHERE id = $itemId";

    if ($conn->query($sql) === TRUE) {
        echo "Inventory quantity updated successfully";
    } else {
        echo "Error updating inventory quantity: " . $conn->error;
    }
}

// Close the database connection
$conn->close();
?>