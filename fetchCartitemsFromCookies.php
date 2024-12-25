<?php
$cart = json_decode($_COOKIE['cart'], true);

if (!empty($cart)) {
    echo '<table>';
    echo '<tr><th>Product Name</th><th>Price</th><th>Quantity</th></tr>';
    foreach ($cart as $item) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($item['productName']) . '</td>';
        echo '<td>Rs' . htmlspecialchars($item['productPrice']) . '</td>';
        echo '<td>' . htmlspecialchars($item['quantity']) . '</td>';
        echo '</tr>';
    }
    echo '</table>';
} else {
    echo "<p>Your cart is empty.</p>";
}
?>
