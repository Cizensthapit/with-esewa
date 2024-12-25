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
    $total_amount = 0;
    $total_quantity = 0;
    $contact = "";
    $location = "";
    $order_date = "";

    while ($order = $order_result->fetch_assoc()) {
        $orders[] = $order;
        $total_amount += $order['product_price'] * $order['quantity'];
        $total_quantity += $order['quantity'];
        $contact = $order['contact'];
        $location = $order['location'];
        $order_date = $order['order_date'];
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
    <style>
        body {
            background: linear-gradient(135deg, #f0f0f0, #d9e4f5);
            font-family: 'Arial', sans-serif;
        }
        .container {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
            padding: 30px;
            margin-top: 30px;
            max-width: 900px;
        }
        h2 {
            font-size: 2.5rem;
            margin-bottom: 25px;
            text-align: center;
            color: #333;
        }
        .table {
            margin-bottom: 30px;
        }
        .table thead th {
            background: #007bff;
            color: #fff;
            text-align: center;
        }
        .table tbody td {
            text-align: center;
        }
        .total-section, .details-section {
            text-align: right;
            margin-top: 20px;
            font-weight: bold;
        }
        .total-section h4, .details-section h5 {
            margin: 5px 0;
            color: #555;
        }
        .details-section {
            text-align: left;
            padding-top: 20px;
            border-top: 1px solid #ccc;
        }
        .list-group-item {
            text-align: center;
            background-color: #f9f9f9;
            border: none;
        }
        .list-group-item form img {
            width: 160px;
            transition: transform 0.3s ease;
        }
        .list-group-item form img:hover {
            transform: scale(1.1);
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Order Details</h2>

        <!-- Display the orders in a table -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Product Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($order['product_name'] ?? ''); ?></td>
                        <td>Rs. <?php echo htmlspecialchars($order['product_price'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($order['quantity'] ?? ''); ?></td>
                        <td>Rs. <?php echo htmlspecialchars($order['product_price'] * $order['quantity'] ?? 0); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Display total amount and quantity -->
        <div class="total-section">
            <h4>Total Quantity: <?php echo $total_quantity; ?></h4>
            <h4>Total Amount: Rs. <?php echo $total_amount; ?></h4>
        </div>

        <!-- Display contact, location, and order date -->
        <div class="details-section">
            <h5>Contact: <?php echo htmlspecialchars($contact); ?></h5>
            <h5>Location: <?php echo htmlspecialchars($location); ?></h5>
            <h5>Order Date: <?php echo htmlspecialchars($order_date); ?></h5>
        </div>

        <!-- eSewa Payment Section -->
        <div class="col-md-6 mt-4 mx-auto">
            <h3 class="text-center">Pay With</h3>
            <ul class="list-group">
                <li class="list-group-item">
                    <form action="https://uat.esewa.com.np/epay/main" method="POST">
                        <input value="<?php echo $total_amount; ?>" name="tAmt" type="hidden">
                        <input value="<?php echo $total_amount; ?>" name="amt" type="hidden">
                        <input value="0" name="txAmt" type="hidden">
                        <input value="0" name="psc" type="hidden">
                        <input value="0" name="pdc" type="hidden">
                        <input value="epay_payment" name="scd" type="hidden">
                        <input value="<?php echo uniqid(); ?>" name="pid" type="hidden">
                        <input value="https://19july/esewa_payment_success.php" type="hidden" name="su">
                        <input value="https://19july/esewa_payment_failed.php" type="hidden" name="fu">
                        <input type="image" src="image/esewa.png" alt="Pay with eSewa">
                    </form>
                </li>
            </ul>
        </div>

    </div>
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
