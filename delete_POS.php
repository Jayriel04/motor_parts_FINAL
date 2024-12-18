<?php
require '../motor-parts/backend/connection.php';

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the user ID from the request body
    $data = json_decode(file_get_contents('php://input'), true);
    $itemId = $data['itemId'];

    // Prepare and execute the DELETE query
    $sql = "DELETE FROM pos_data WHERE pos_id = $itemId";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(['message' => 'Item deleted successfully']);
    } else {
        echo json_encode(['error' => 'Error deleting item']);
    }
}

$conn->close();
?>