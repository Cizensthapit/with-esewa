<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "Please log in to add items to your cart.";
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "medlifemis_db";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve POST data
$product_id = $_POST['productId'];
$product_name = $_POST['productName'];
$product_price = $_POST['productPrice'];
$quantity = $_POST['quantity'];
$pimage = ''; // Assuming you will handle the image separately or fetch it based on product_id

$user_id = $_SESSION['user_id'];

// Check if the item is already in the user's cart
$sql_check = "SELECT * FROM usercart WHERE user_id=? AND product_id=?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("ii", $user_id, $product_id);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows > 0) {
    // Update the quantity if the item already exists in the cart
    $sql_update = "UPDATE usercart SET quantity=quantity+? WHERE user_id=? AND product_id=?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("iii", $quantity, $user_id, $product_id);
    $stmt_update->execute();
    $stmt_update->close();
} else {
    // Insert new item into the cart
    $sql_insert = "INSERT INTO usercart (user_id, product_id, product_name, product_price, quantity, pimage) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param("iisdis", $user_id, $product_id, $product_name, $product_price, $quantity, $pimage);
    $stmt_insert->execute();
    $stmt_insert->close();
}

$stmt_check->close();
$conn->close();

echo "Product added to cart.";
?>
