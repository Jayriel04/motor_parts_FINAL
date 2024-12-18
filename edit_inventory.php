<?php
require '../motor-parts/backend/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $itemId = $_POST['id'];
    $itemName = $_POST['name'];
    $itemAttributes = $_POST['attributes'];
    $itemPrice = $_POST['price'];
    $itemInventory = $_POST['inventory'];

    // Update item in the database
    $sql = "UPDATE inventories SET name = '$itemName', attributes = '$itemAttributes', price = '$itemPrice', inventory = '$itemInventory' WHERE id = '$itemId'";

    if ($conn->query($sql) === TRUE) {
        // Redirect to inventory page after updating
        header('location: inventory.php');
        // echo "saging";
        exit; // Ensure no further code execution after redirection
    } else {
        echo "Error updating item: " . $conn->error;
    }

    $conn->close();
}
?>