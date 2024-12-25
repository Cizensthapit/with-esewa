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

if (isset($_POST['complete_order'])) {
    $order_id = $_POST['complete_order'];
    
    // Fetch order details from `order` table
    $sql_select_order = "SELECT * FROM `order` WHERE order_id = ?";
    $stmt_select_order = $conn->prepare($sql_select_order);
    $stmt_select_order->bind_param("i", $order_id);
    $stmt_select_order->execute();
    $result_order = $stmt_select_order->get_result();
    
    if ($result_order && $result_order->num_rows > 0) {
        $order_data = $result_order->fetch_assoc();
        
        // Calculate dispatch date (example: current date + 2 days)
        $dispatch_date = date('Y-m-d', strtotime('+2 days'));
        
        // Insert order into `completed_orders` table
        $sql_insert_completed = "INSERT INTO completed_orders (order_id, user_id, product_id, product_name, product_price, quantity, order_date, dispatch_date)
                                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt_insert_completed = $conn->prepare($sql_insert_completed);
        $stmt_insert_completed->bind_param("iiisisss", $order_data['order_id'], $order_data['user_id'], $order_data['product_id'], $order_data['product_name'], $order_data['product_price'], $order_data['quantity'], $order_data['order_date'], $dispatch_date);
        
        if ($stmt_insert_completed->execute()) {
            // Insert order into `approvedorder_db` table
            $sql_insert_approved = "INSERT INTO approvedorder_db (order_id, user_id, product_id, product_name, product_price, quantity, order_date, dispatch_date, contact)
                                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt_insert_approved = $conn->prepare($sql_insert_approved);
            $stmt_insert_approved->bind_param("iiisissss", $order_data['order_id'], $order_data['user_id'], $order_data['product_id'], $order_data['product_name'], $order_data['product_price'], $order_data['quantity'], $order_data['order_date'], $dispatch_date, $order_data['contact']);
            
            if ($stmt_insert_approved->execute()) {
                $message = "Order has been confirmed and added to the approved list.";
            } else {
                $message = "Error inserting order into approvedorder_db: " . $stmt_insert_approved->error;
            }
        } else {
            $message = "Error inserting order into completed_orders: " . $stmt_insert_completed->error;
        }
    } else {
        $message = "Order not found.";
    }
    
    $stmt_select_order->close();
    $stmt_insert_completed->close();
    $stmt_insert_approved->close();
    $conn->close();
} elseif (isset($_POST['delete_order'])) {
    $order_id = $_POST['delete_order'];
    
    // Delete order from `order` table
    $sql_delete_order = "DELETE FROM `order` WHERE order_id = ?";
    $stmt_delete_order = $conn->prepare($sql_delete_order);
    $stmt_delete_order->bind_param("i", $order_id);
    
    if ($stmt_delete_order->execute()) {
        $message = "Order has been deleted.";
    } else {
        $message = "Error deleting order: " . $stmt_delete_order->error;
    }
    
    $stmt_delete_order->close();
    $conn->close();
} else {
    $message = "Invalid action.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Status</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7f6;
            margin: 0;
            padding: 20px;
            text-align: center;
        }
        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: auto;
        }
        .message {
            font-size: 18px;
            color: #333;
            margin: 20px 0;
        }
        .go-to-admin-btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            font-size: 16px;
        }
        .go-to-admin-btn:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="message">
            <?php echo $message; ?>
        </div>
        <a href="orderfinal.php" class="go-to-admin-btn">Go to Order</a>
    </div>
</body>
</html>
