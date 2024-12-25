<?php
// Start session (if needed)
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

// Get parameters from POST request
$cartId = $_POST['cartId'];
$productId = $_POST['productId'];

// Check if the product is in the cart
$sql = "SELECT quantity FROM usercart WHERE cart_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $cartId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $currentQuantity = $row['quantity'];

    if ($currentQuantity > 1) {
        // If more than one, decrease quantity by 1
        $newQuantity = $currentQuantity - 1;
        $updateSql = "UPDATE usercart SET quantity = ? WHERE cart_id = ?";
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->bind_param("ii", $newQuantity, $cartId);

        if ($updateStmt->execute()) {
            echo "Item quantity updated in cart.";
        } else {
            echo "Failed to update item quantity in cart.";
        }

        $updateStmt->close();
    } else {
        // If exactly one, remove the item from the cart
        $deleteSql = "DELETE FROM usercart WHERE cart_id = ?";
        $deleteStmt = $conn->prepare($deleteSql);
        $deleteStmt->bind_param("i", $cartId);

        if ($deleteStmt->execute()) {
            echo "Item removed from cart.";
        } else {
            echo "Failed to remove item from cart.";
        }

        $deleteStmt->close();
    }
} else {
    echo "Item not found in cart.";
}

$stmt->close();
$conn->close();
?>
