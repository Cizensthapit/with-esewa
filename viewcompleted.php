<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$database = "medlifemis_db";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

if (isset($_SESSION['user_id'])) {
  $user_id = $_SESSION['user_id'];

  $sql = "SELECT * FROM completed_orders WHERE user_id = '$user_id'";
  $result = $conn->query($sql);

  if ($result->num_rows > 0) {
    // Output data of each row
    while ($row = $result->fetch_assoc()) {
      // Display or process data as needed
      echo "Order ID: " . $row["order_id"] . "<br>";
      echo "Product Name: " . $row["product_name"] . "<br>";
      echo "Product Price: " . $row["product_price"] . "<br>";
      // Add more fields as required
      echo "<hr>";
    }
  } else {
    echo "No completed orders found for this user.";
  }
} else {
  echo "User not logged in.";
}

$conn->close();
?>
