<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Start the session
session_start();

// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "medlifemis_db";

// Create a database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "You need to log in first.";
    exit();
}

// Get the logged-in user's ID
$user_id = $_SESSION['user_id'];

// Fetch orders for the logged-in user within a 1-5 second timeframe
$order_query = "SELECT * FROM `order` WHERE user_id = '$user_id' AND order_date >= NOW() - INTERVAL 5 SECOND";
$order_result = $conn->query($order_query);

// Check if any orders were found
if ($order_result->num_rows > 0) {
    $orders = [];
    while ($order = $order_result->fetch_assoc()) {
        $orders[] = $order;
    }
} else {
    echo "No orders found in the last 5 seconds.";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout Page</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
</head>
<body>
    <div class="container">
        <div class="pt-md-5">
            <div class="col-md-12">
                <h2>Order Details</h2>

                <!-- Display the orders in a table -->
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Product Name</th>
                            <th>Product Price</th>
                            <th>Quantity</th>
                            <th>Total</th>
                            <th>Contact</th>
                            <th>Location</th>
                            <th>Order Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($order['product_name'] ?? ''); ?></td>
                                <td>Rs. <?php echo htmlspecialchars($order['product_price'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($order['quantity'] ?? ''); ?></td>
                                <td>Rs. <?php echo htmlspecialchars($order['product_price'] * $order['quantity'] ?? 0); ?></td>
                                <td><?php echo htmlspecialchars($order['contact'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($order['location'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($order['order_date'] ?? ''); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- eSewa Payment Section -->
                <div class="col-md-6">
                    <h3>Pay With</h3>
                    <ul class="list-group">
                        <li class="list-group-item">
                            <form action="https://uat.esewa.com.np/epay/main" method="POST">
                                <input value="<?php echo $order['product_price'] * $order['quantity']; ?>" name="tAmt" type="hidden">
                                <input value="<?php echo $order['product_price'] * $order['quantity']; ?>" name="amt" type="hidden">
                                <input value="0" name="txAmt" type="hidden">
                                <input value="0" name="psc" type="hidden">
                                <input value="0" name="pdc" type="hidden">
                                <input value="epay_payment" name="scd" type="hidden">
                                <input value="<?php echo $order['product_id']; ?>" name="pid" type="hidden">
                                <input value="https://19july/esewa_payment_success.php" type="hidden" name="su">
                                <input value="https://19july/esewa_payment_failed.php" type="hidden" name="fu">
                                <input type="image" src="image/esewa.png">
                            </form>
                        </li>
                    </ul>
                </div>

            </div>
        </div>
    </div>
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>