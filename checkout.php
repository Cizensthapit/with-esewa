<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start or resume the session
session_start();

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

// Assuming you have some way to identify new records, like a timestamp or a flag
$sql = "SELECT product_id, product_name, product_price, quantity FROM checkout WHERE imported_timestamp >= NOW() - INTERVAL 1 MINUTE";

$result = $conn->query($sql);

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Checkout Details</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f5f5f5;
      margin: 0;
      padding: 20px;
    }
    .invoice {
      width: 80%;
      margin: 0 auto;
      background-color: #fff;
      box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
      padding: 20px;
      border-radius: 10px;
    }
    h2 {
      text-align: center;
      margin-bottom: 20px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }
    th, td {
      padding: 12px 15px;
      text-align: left;
      border-bottom: 1px solid #ddd;
    }
    th {
      background-color: #f2f2f2;
    }
    tr:nth-child(even) {
      background-color: #f9f9f9;
    }
    tr:hover {
      background-color: #f1f1f1;
    }
    .total-row td {
      font-weight: bold;
    }
    .contact-info {
      width: 80%;
      margin: 20px auto;
      background-color: #fff;
      box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
      padding: 20px;
      border-radius: 10px;
    }
    .contact-info p {
      margin: 0;
      padding: 10px 0;
    }
    .button-container {
      text-align: center;
      margin-top: 20px;
    }
    .order-button {
      padding: 12px 20px;
      border: none;
      background-color: #4CAF50;
      color: white;
      font-size: 16px;
      cursor: pointer;
      border-radius: 4px;
    }
    .order-button:hover {
      background-color: #45a049;
    }
  </style>
  <script>
    function checkCartEmpty() {
      var table = document.querySelector('.invoice table');
      if (!table) {
        alert("Cannot checkout! Cart empty");
        window.location.href = "index.php";
      }
    }
  </script>
</head>
<body onload="checkCartEmpty()">

<div class="invoice">
  <h2>Your Invoice</h2>
  <?php
  if ($result === false) {
    echo "Error: " . $conn->error;
  } elseif ($result->num_rows > 0) {
    // Output data in a tabulated form
    echo "<table>";
    echo "<tr><th>PN</th><th>Product Name</th><th>Product Price</th><th>Product Quantity</th></tr>";
    $total_quantity = 0;
    $total_price = 0;
    $delivery_fee = 100;
    
    while($row = $result->fetch_assoc()) {
      echo "<tr>";
      echo "<td>" . $row['product_id'] . "</td>";
      // Assuming you have a way to get the product image, replace `product_image_path` with actual path
      echo "<td>" . $row['product_name'] . "</td>";
      echo "<td>NPR" . $row['product_price'] . "</td>";
      echo "<td>" . $row['quantity'] . "</td>";
      echo "</tr>";
      $total_quantity += $row['quantity'];
      $total_price += $row['product_price'] * $row['quantity'];
    }
    
    echo "<tr class='total-row'><td colspan='3'>Total Quantity</td><td>$total_quantity</td></tr>";
    echo "<tr class='total-row'><td colspan='3'>Total Price</td><td>NPR" . ($total_price) . "</td></tr>";
    echo "</table>";
  } else {
    echo "<script>alert('Cannot checkout! Cart empty'); window.location.href = 'index.php';</script>";
  }
  $conn->close();
  ?>
</div>

<div class="contact-info">
  <h2>Contact Information</h2>
  <p>Please check your details</p>
  <p>Contact: <?php echo isset($_SESSION['username']) ? $_SESSION['username'] : 'Not available'; ?></p>

  <form action="order.php" method="post">
    <label for="location">Exact Location:</label><br>
    <input type="text" id="location" name="location" required><br><br>
    <div class="button-container">
      <!--  <a href="index.php"><button type="button">Go back to shopping</button></a>-->
    </div>
    <button type="submit" name="order_button" class="order-button">Proceed to payment</button> 
  </form>
</div>

</body>
</html>
