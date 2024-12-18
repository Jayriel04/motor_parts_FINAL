<?php
require '../motor-parts/backend/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $itemId_edit = $_POST['item_id_edit'];
    $itemName_edit = $_POST['name_edit'];
    $itemQuantity_edit = $_POST['quantity_edit'];
    $itemPrice_edit = $_POST['price_edit'];

    // Update item in the database
    $sql = "UPDATE items SET name = '$itemName_edit', quantity = '$itemQuantity_edit', price = '$itemPrice_edit' WHERE item_id = '$itemId_edit'";

    if ($conn->query($sql) === TRUE) {
        // echo "Item updated successfully";
        header('location: items.php');
    } else {
        echo "Error updating item: " . $conn->error;
    }

    $conn->close();
}
?>