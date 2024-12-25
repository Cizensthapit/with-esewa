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
$sql = "SELECT cart_id, product_name, product_price, quantity FROM usercart WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Prepare HTML table for cart items
$cartItemsHTML = '
<div class="cart-table-container">
    <table class="cart-table">
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Total</th>
                <th></th>
            </tr>
        </thead>
        <tbody>';

$totalPrice = 0;

// Initialize array to store cart items for checkout
$checkoutItems = [];

// Fetch results and create table rows
while ($row = $result->fetch_assoc()) {
    $cartId = $row['cart_id'];
    $productName = htmlspecialchars($row['product_name']);
    $productPrice = htmlspecialchars($row['product_price']);
    $quantity = htmlspecialchars($row['quantity']);

    // Calculate total price for each item
    $itemTotalPrice = $productPrice * $quantity;
    $totalPrice += $itemTotalPrice;

    // Add row to HTML table
    $cartItemsHTML .= '<tr>';
    $cartItemsHTML .= '<td>' . $productName . '</td>';
    $cartItemsHTML .= '<td>Rs ' . $productPrice . '</td>';
    $cartItemsHTML .= '<td>' . $quantity . '</td>';
    $cartItemsHTML .= '<td>Rs ' . number_format($itemTotalPrice, 2) . '</td>';
    $cartItemsHTML .= '<td><button class="remove-btn" onclick="removeFromCart(' . $cartId . ')">Remove</button></td>';
    $cartItemsHTML .= '</tr>';

    // Prepare item for insertion into checkoutItems array
    $checkoutItems[] = [
        'pname' => $productName,
        'quantity' => $quantity,
    ];
}

// Close statement
$stmt->close();

// Add total row and checkout button
$cartItemsHTML .= '
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" class="text-right"><strong>Total Price:</strong></td>
                <td><span id="totalPrice">Rs ' . number_format($totalPrice, 2) . '</span></td>
            </tr>
            <tr>
                <td colspan="5" class="text-right"><button class="checkout-btn" onclick="checkout()">Checkout</button></td>
            </tr>
        </tfoot>
    </table>
</div>';

// Display cart items and total price
echo $cartItemsHTML;

?>

<!-- JavaScript for cart functionality -->
<script>
    function removeFromCart(cartId) {
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "removeFromCart.php", true);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function () {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    alert("Item removed from cart.");
                    // Refresh cart items after removal
                    window.location.reload();
                } else {
                    alert("Failed to remove item from cart.");
                }
            }
        };
        xhr.send("cartId=" + cartId);
    }
    function checkout() {
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "movetocheckout.php", true);
    xhr.setRequestHeader("Content-type", "application/json");

    // Convert checkoutItems array to JSON
    var checkoutItemsJSON = JSON.stringify(<?php echo json_encode($checkoutItems); ?>);

    xhr.onreadystatechange = function () {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                // Show a confirmation dialog
                var proceed = confirm("Would you like to proceed?");
                if (proceed) {
                    // Redirect to checkoutfinal.php for invoice
                    window.location.href = "checkout.php";
                } else {
                    // Redirect to index.html or handle cancellation
                    window.location.href = "index.php";
                }
            } else {
                alert("Failed to proceed.");
            }
        }
    };
    xhr.send(checkoutItemsJSON);
}
</script>

    

<style>
    /* CSS styles for the cart table */
    .cart-table-container {
        width: 100%;
        margin-top: 25px;
        background-color: #ffff;
        border: 1px solid #ddd;
        border-radius: 5px;
        overflow: hidden;
    }

    .cart-table {
        width: 100%;
        border-collapse: collapse;
        border: 1px solid black;
    }

    .cart-table th,
    .cart-table td {
        padding: 10px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }

    .cart-table th {
        background-color: black;
        color: #ffffff

    }

    .cart-table tbody tr:nth-child(even) {
        background-color: white;
    }

    .cart-table tfoot {
        background-color: #f2f2f2;
        font-weight: bold;
    }

    .cart-table tfoot td {
        padding: 10px;
        text-align: left;

    }


    .cart-table tfoot td .checkout-btn {
        padding: 10px;
        text-align: center;

    }

    .remove-btn {
        background-color: #45a049;
        color: white;
        padding: 6px 12px;
        cursor: pointer;
        border-radius: 50px;
        border: 0.5px solid black;
    }

    .remove-btn:hover {
        background-color: #ffffff;
        color: black;
        border: 0.5px solid black;

    }

    .checkout-btn {
        background-color: #000000;
        color: #ffffff;
        padding: 10px 20px;
        cursor: pointer;
        border-radius: 5px;
        border: 0.5px solid black;
    }

    .checkout-btn:hover {
        background-color: #ffffff;
        color: #000000;
        border : 0.5px solid black;
    }

    .text-right {
        text-align: right;
    }
</style>
