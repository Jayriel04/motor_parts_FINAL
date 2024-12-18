<?php
require '../motor-parts/backend/connection.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set the content type to JSON
header('Content-Type: application/json');

// Remove rows where transaction_no is 0
$deleteQuery = "DELETE FROM sales_data WHERE transaction_no = 0";
if ($conn->query($deleteQuery) !== TRUE) {
    echo json_encode(["status" => "error", "message" => "Error deleting rows: " . $conn->error]);
    exit; // Exit if there's an error
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Decode the incoming JSON data
    $data = json_decode(file_get_contents("php://input"), true);

    // Check if sales data is provided
    if (isset($data['sales']) && is_array($data['sales'])) {
        $totalAmount = $data['totalAmount'];

        // Start a transaction
        $conn->begin_transaction();

        try {
            // Fetch the last transaction number
            $transactionQuery = $conn->query("SELECT MAX(transaction_no) AS last_transaction_no FROM sales_data");
            $transactionRow = $transactionQuery->fetch_assoc();
            $transactionNumber = $transactionRow['last_transaction_no'] ? $transactionRow['last_transaction_no'] + 1 : 1; // Start at 1 if no transactions exist

            // Format the transaction number with leading zeros
            $transactionNumber = str_pad($transactionNumber, 6, '0', STR_PAD_LEFT); // Change 6 to the desired length

            // Insert each sale into the sales_data table and pos_data table
            foreach ($data['sales'] as $sale) {
                $itemId = $sale['itemId'];
                $quantity = $sale['quantity'];
                $price = $sale['price'];
                $totalPrice = $sale['totalPrice'];

                // Fetch the item name based on itemId
                $itemQuery = $conn->prepare("SELECT name FROM inventories WHERE id = ?");
                $itemQuery->bind_param("i", $itemId);
                $itemQuery->execute();
                $itemResult = $itemQuery->get_result();
                $itemRow = $itemResult->fetch_assoc();
                $itemName = $itemRow['name'];

                // Insert into sales_data table
                $insertSalesDetailQuery = $conn->prepare("INSERT INTO sales_data (transaction_no, item_id, item_name, quantity, sales_price, total_price, total_amount, sales_date) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
                $insertSalesDetailQuery->bind_param("iisddds", $transactionNumber, $itemId, $itemName, $quantity, $price, $totalPrice, $totalAmount);
                $insertSalesDetailQuery->execute();

                // Insert into pos_data table
                $insertPosDataQuery = $conn->prepare("INSERT INTO pos_data (item_id, quantity, created_at) VALUES (?, ?, NOW())");
                $insertPosDataQuery->bind_param("ii", $itemId, $quantity);
                $insertPosDataQuery->execute();

                // Update inventory
                $updateQuery = $conn->prepare("UPDATE inventories SET inventory = inventory - ? WHERE id = ?");
                $updateQuery->bind_param("ii", $quantity, $itemId);
                $updateQuery->execute();
            }

            // Commit the transaction
            $conn->commit();

            // Return success response with transaction number
            echo json_encode(["status" => "success", "message" => "Sales recorded successfully.", "transactionNumber" => $transactionNumber]);
        } catch (Exception $e) {
            // Rollback the transaction on error
            $conn->rollback();
            echo json_encode(["status" => "error", "message" => "Transaction failed: " . $e->getMessage()]);
        }
    } else {
        // Return error response if sales data is invalid
        echo json_encode(["status" => "error", "message" => "Invalid sales data."]);
    }
} else {
    // Return error response if request method is not POST
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
}

// Close the database connection
$conn->close();
?>