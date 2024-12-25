<?php
// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401); // Unauthorized
    exit("User not logged in.");
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "medlifemis_db";
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    http_response_code(500); // Internal Server Error
    exit("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];

// Fetch cart items for the current user from usercart table
$sql = "SELECT cart_id, product_id, product_name, product_price, quantity FROM usercart WHERE user_id = ?";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    http_response_code(500);
    exit("Prepare statement failed: " . $conn->error);
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result === false) {
    http_response_code(500);
    exit("Execute statement failed: " . $stmt->error);
}

while ($row = $result->fetch_assoc()) {
    $cartId = $row['cart_id'];
    $productId = $row['product_id'];
    $productName = $row['product_name'];
    $productPrice = $row['product_price'];
    $quantity = $row['quantity'];

    // Insert item into checkout table
    $insertSql = "INSERT INTO checkout (user_id, product_id, product_name, product_price, quantity) VALUES (?, ?, ?, ?, ?)";
    $insertStmt = $conn->prepare($insertSql);
    if ($insertStmt === false) {
        http_response_code(500);
        exit("Prepare insert statement failed: " . $conn->error);
    }
    $insertStmt->bind_param("iisdi", $user_id, $productId, $productName, $productPrice, $quantity);

    if ($insertStmt->execute() === false) {
        http_response_code(500);
        exit("Execute insert statement failed: " . $insertStmt->error);
    }

    // Remove item from usercart table
    $deleteSql = "DELETE FROM usercart WHERE cart_id = ?";
    $deleteStmt = $conn->prepare($deleteSql);
    if ($deleteStmt === false) {
        http_response_code(500);
        exit("Prepare delete statement failed: " . $conn->error);
    }
    $deleteStmt->bind_param("i", $cartId);

    if ($deleteStmt->execute() === false) {
        http_response_code(500);
        exit("Execute delete statement failed: " . $deleteStmt->error);
    }

    $insertStmt->close();
    $deleteStmt->close();
}

$stmt->close();
$conn->close();

echo "Items moved to checkout successfully.";
?>
