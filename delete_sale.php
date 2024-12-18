<?php
require '../motor-parts/backend/connection.php';

if (isset($_GET['id'])) {
    $sale_id = intval($_GET['id']);
    
    // Prepare the delete statement
    $stmt = $conn->prepare("DELETE FROM sales_data WHERE id = ?");
    $stmt->bind_param("i", $sale_id);
    
    if ($stmt->execute()) {
        // Redirect back to salesdata.php after deletion
        header("Location: salesdata.php?message=Sale deleted successfully");
        exit();
    } else {
        echo "Error deleting sale: " . $conn->error;
    }
    
    $stmt->close();
}

$conn->close();
?>