<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Items</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/cdbootstrap/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/cdbootstrap/css/cdb.min.css" />
    <link rel="stylesheet" href="../motor-parts/src/css/styles.css">
</head>

<body>
    <nav class="navbar navbar-expand-lg bg-primary-subtle">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php">SALES AND INVENTORY</a>
            <div class="collapse navbar-collapse" id="navbarNavDropdown">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="user.php">Account Management</a></li>
                    <li class="nav-item"><a class="nav-link" href="items.php">Items</a></li>
                    <li class="nav-item"><a class="nav-link" href="inventory.php">Inventory</a></li>
                    <li class="nav-item"><a class="nav-link active" href="pos.php">Add Sales</a></li>
                    <li class="nav-item"><a class="nav-link" href="salesdata.php">Sales</a></li>
                </ul>
            </div>
            <button class="btn btn-outline-success" type="submit"><a class="logout" href="login.php">Logout</a></button>
        </div>
    </nav>

    <div class="px-3">
        <h3>Add Sale</h3>

        <div class="row">
            <div class="col px-3">
                <h6>Items</h6>
                <select id="item-select" class="form-select">
                    <option value="" disabled selected>Please select an item</option>
                    <?php
                    require '../motor-parts/backend/connection.php';
                    $sql = "SELECT id, name, price, inventory FROM inventories";
                    $result = $conn->query($sql);
                    if ($result && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='" . $row['id'] . "' data-price='" . $row['price'] . "' data-quantity='" . $row['inventory'] . "'>" . $row['name'] . "</option>";
                        }
                    } else {
                        echo "<option value='' disabled>No items available</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="col px-3">
                <h6>Quantity</h6>
                <input id="quantity-input" type="number" class="form-control text-center" min="1" aria-label="Quantity">
            </div>
            <div class="col">
                <button id="add-item-btn" type="button" class="btn btn-dark mt-4">Add Item</button>
            </div>
        </div>

        <!-- Order List Table -->
        <div class="mt-4">
            <h4>Order List</h4>
            <table id="order-list-table" class="table table-bordered text-center">
                <thead>
                    <tr class="table-dark">
                        <th>Item Name</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Dynamically added rows will appear here -->
                </tbody>
            </table>
        </div>

        <button id="receipt-btn" type="button" class="btn btn-success mt-4">Generate Receipt</button>
    </div>

    <!-- Receipt Modal -->
    <div class="modal fade" id="receiptModal" tabindex="-1" aria-labelledby="receiptModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title" id="receiptModalLabel">Receipt</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="receipt-content" class="p-3">
                        <!-- Receipt details will be dynamically populated -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let selectedItems = [];

        document.getElementById('add-item-btn').addEventListener('click', function() {
            const itemSelect = document.getElementById('item-select');
            const quantityInput = document.getElementById('quantity-input');

            const selectedItem = itemSelect.options[itemSelect.selectedIndex];
            const itemId = selectedItem.value;
            const itemName = selectedItem.textContent;
            const price = parseFloat(selectedItem.getAttribute('data-price'));
            const quantity = parseInt(quantityInput.value);
            const currentQuantity = parseInt(selectedItem.getAttribute('data-quantity'));

            if (quantity > 0 && quantity <= currentQuantity) {
                const totalPrice = price * quantity;
                const tableBody = document.querySelector('#order-list-table tbody');
                const row = document.createElement('tr');

                row.innerHTML = `
                    <td>${itemName}</td>
                    <td>${quantity}</td>
                    <td>${price.toFixed(2)}</td>
                    <td>${totalPrice.toFixed(2)}</td>
                    <td><button class="btn btn-danger btn-sm remove-item-btn">Remove</button></td>
                `;
                tableBody.appendChild(row);

                selectedItem.setAttribute('data-quantity', currentQuantity - quantity);
                
                // Clear quantity input after adding item
                quantityInput.value = '';

                // Add event listener for the remove button
                row.querySelector('.remove-item-btn').addEventListener('click', function() {
                    // Remove the row from the table
                    tableBody.removeChild(row);

                    // Update the available quantity of the selected item
                    selectedItem.setAttribute('data-quantity', currentQuantity + quantity);

                    // Remove the item from the selectedItems array
                    selectedItems = selectedItems.filter(item => item.itemId !== itemId);
                });

                selectedItems.push({
                    itemId,
                    itemName,
                    price,
                    quantity,
                    totalPrice
                });
            } else {
                alert('Invalid quantity!');
            }
        });

        document.getElementById('receipt-btn').addEventListener('click', function() {
            if (selectedItems.length === 0) {
                alert('No items added!');
                return;
            }

            const receiptContainer = document.getElementById('receipt-content');
            receiptContainer.innerHTML = ''; // Clear old receipt content
            let receiptContent = `<h5 class="text-center">Sales Receipt</h5><hr>`;
            let totalAmount = 0;

            // Prepare data for the server
            const saleData = selectedItems.map(item => ({
                itemId: item.itemId,
                quantity: item.quantity,
                price: item.price,
                totalPrice: item.totalPrice
            }));

            // Calculate total amount
            totalAmount = saleData.reduce((sum, item) => sum + item.totalPrice, 0);

            // Send data to the server
            fetch('insert_pos_data.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        sales: saleData,
                        totalAmount: totalAmount
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === "success") {
                        // Display the transaction number
                        receiptContent += `<p>Transaction no: ${data.transactionNumber}</p>`;
                        receiptContent += `<table class="table"><thead><tr><th>Item</th><th>Qty</th><th>Price</th><th>Total</th></tr></thead><tbody>`;

                        selectedItems.forEach(item => {
                            receiptContent += `<tr><td>${item.itemName}</td><td>${item.quantity}</td><td>${item.price.toFixed(2)}</td><td>${item.totalPrice.toFixed(2)}</td></tr>`;
                        });

                        receiptContent += `</tbody></table><h5>Total: ${totalAmount.toFixed(2)}</h5>`;
                        receiptContainer.innerHTML = receiptContent;

                        new bootstrap.Modal(document.getElementById('receiptModal')).show();

                        // Clear the order list and selected items
                        selectedItems = [];
                        document.querySelector('#order-list-table tbody').innerHTML = '';
                    } else {
                        alert("Error recording sale: " + data.message);
                    }
                })
                .catch(error => console.error('Error:', error));
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>