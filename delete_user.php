<?php
require '../motor-parts/backend/connection.php';

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the user ID from the request body
    $data = json_decode(file_get_contents('php://input'), true);
    $userId = $data['userId'];

    // Prepare and execute the DELETE query
    $sql = "DELETE FROM users WHERE user_id = $userId";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(['message' => 'User deleted successfully']);
    } else {
        echo json_encode(['error' => 'Error deleting user']);
    }
}

$conn->close();
?>