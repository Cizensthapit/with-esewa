<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "medlifemis_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL query to fetch all data from the approvedorder_db table, joined with users table, sorted by user_id and order_date in descending order
$sql = "SELECT a.order_id, a.user_id, a.product_id, a.product_name, a.product_price, a.quantity, a.order_date, a.dispatch_date, u.contact
        FROM approvedorder_db a
        JOIN users u ON a.user_id = u.id
        ORDER BY a.user_id, a.order_date DESC";

$result = $conn->query($sql);

// Calculate the total number of orders
$totalOrders = ($result) ? $result->num_rows : 0;

if ($result === false) {
    echo "Error: " . $conn->error;
} elseif ($totalOrders > 0) {
    // Output data in tabulated form, grouped by user_id
    $current_user_id = null;
    $current_order_date = null;
    $totalAmount = 0;
    $user_contact = '';

    echo '
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Approved Orders</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f4f7f6;
                margin: 0;
                padding: 20px;
            }
            h1 {
                text-align: center;
                color: #333;
            }
            .total-orders-container {
                width: 80%;
                margin: 20px auto;
                padding: 10px;
                background-color: #007bff;
                color: white;
                text-align: center;
                border-radius: 5px;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            }
            h2 {
                font-size: 1.5em;
                color: #007bff;
                margin: 20px auto;
                text-align: center;
            }
            table {
                width: 80%;
                margin: 20px auto;
                border-collapse: collapse;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                background-color: white;
            }
            th, td {
                padding: 12px;
                text-align: left;
                border: 1px solid #ddd;
            }
            th {
                background-color: #007bff;
                color: white;
            }
            tr:nth-child(even) {
                background-color: #f9f9f9;
            }
            tr:hover {
                background-color: #f1f1f1;
            }
            .bold {
                font-weight: bold;
            }
            .action-btn {
                padding: 8px 16px;
                border: none;
                border-radius: 4px;
                cursor: pointer;
                color: white;
                font-size: 14px;
            }
            .go-to-admin-btn {
                display: block;
                width: 200px;
                margin: 20px auto;
                padding: 10px;
                background-color: #4CAF50;
                color: white;
                border: none;
                border-radius: 5px;
                cursor: pointer;
                text-align: center;
                text-decoration: none;
                font-size: 16px;
            }
            .go-to-admin-btn:hover {
                background-color: #45a049;
            }
        </style>
    </head>
    <body>
        <h1>Approved Orders</h1>
        <div class="total-orders-container">
            Total Approved Orders: ' . $totalOrders . '
        </div>';

    echo '<form action="approvedorder.php" method="post">';
    
    while ($row = $result->fetch_assoc()) {
        if ($current_user_id !== $row['user_id']) {
            if ($current_user_id !== null) {
                // Add total amount row before closing the table
                echo '<tr>
                        <td colspan="8" class="bold" style="text-align: right;">Total Amount:</td>
                        <td colspan="2" class="bold">' . number_format($totalAmount, 2) . '</td>
                      </tr>';
                // Add contact row after the total amount
                echo '<tr>
                        <td colspan="8" class="bold" style="text-align: right;">Contact:</td>
                        <td colspan="2" class="bold">' . $user_contact . '</td>
                      </tr>';
                echo '</table><br>';
            }
            $current_user_id = $row['user_id'];
            $current_order_date = null;
            $totalAmount = 0; // Reset total amount for new user
            $user_contact = $row['contact']; // Set user contact for new user
            echo "<h2>Orders for User ID: " . $row['user_id'] . "</h2>";
            echo '<table>
                    <tr>
                      <th>Order ID</th>
                      <th>User ID</th>
                      <th>Product ID</th>
                      <th>Product Name</th>
                      <th>Price</th>
                      <th>Quantity</th>
                      <th>Order Date</th>
                      <th>Dispatch Date</th>
                    </tr>';
        }
        
        if ($current_order_date !== $row['order_date']) {
            $current_order_date = $row['order_date'];
            echo '<tr><td colspan="8" class="bold">Date: ' . $current_order_date . '</td></tr>';
        }
        
        // Calculate total amount
        $totalAmount += $row['product_price'] * $row['quantity'];
        
        echo "<tr>";
        echo "<td>" . $row['order_id'] . "</td>";
        echo "<td>" . $row['user_id'] . "</td>";
        echo "<td>" . $row['product_id'] . "</td>";
        echo "<td>" . $row['product_name'] . "</td>";
        echo "<td>" . $row['product_price'] . "</td>";
        echo "<td>" . $row['quantity'] . "</td>";
        echo "<td class='bold'>" . $row['order_date'] . "</td>";
        echo "<td class='bold'>" . $row['dispatch_date'] . "</td>";
        echo "</tr>";
    }
    
    // Add total amount and contact row for the last user table
    if ($current_user_id !== null) {
        echo '<tr>
                <td colspan="8" class="bold" style="text-align: right;">Total Amount:</td>
                <td colspan="2" class="bold">' . number_format($totalAmount, 2) . '</td>
              </tr>';
        echo '<tr>
                <td colspan="8" class="bold" style="text-align: right;">Contact:</td>
                <td colspan="2" class="bold">' . $user_contact . '</td>
              </tr>';
        echo '</table>';
    }

    echo '</form>';
    echo '
    <div style="display: flex; justify-content: center; margin-top: 20px;">
        <a href="admin.php" class="go-to-admin-btn">Go to Admin Page</a>
    </div>
    </body>
    </html>';
} else {
    echo "No approved orders found.";
}

$conn->close();
?>
