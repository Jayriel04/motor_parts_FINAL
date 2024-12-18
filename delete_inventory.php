<?php
require '../motor-parts/backend/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $query = "DELETE FROM pos_data";
    if ($conn->query($query) === TRUE) {
        echo json_encode(['success' => true, 'message' => 'All transactions deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error deleting transactions: ' . $conn->error]);
    }
}
?>
