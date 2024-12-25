<?php
// epay.php

// Retrieve POST data
$user_id = $_POST['user_id'];
$total_amount = $_POST['total_amount'];
$dispatch_time = $_POST['dispatch_time'];

// Set tax amount and product code
$tax_amount = 0;  // Add your tax calculation logic here
$product_code = "EPAYTEST";  // Replace this with your product code

// Generate unique transaction UUID
$transaction_uuid = uniqid('trx_', true);

// Generate signature (adjust this as per eSewa's requirements)
$merchant_code = "your_merchant_code";  // Your eSewa merchant code
$secret_key = "your_secret_key";  // Your eSewa secret key
$signature_string = $merchant_code . $total_amount . $transaction_uuid;
$signature = hash_hmac('sha256', $signature_string, $secret_key);

// eSewa Success and Failure URLs
$success_url = "https://yourwebsite.com/success";  // Replace with your actual success URL
$failure_url = "https://yourwebsite.com/failure";  // Replace with your actual failure URL
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>eSewa Payment</title>
</head>
<body>

<h1>Pay via eSewa</h1>

<form action="https://epay.esewa.com.np/api/epay/main/v2/form" method="POST">
    <input type="hidden" name="amount" value="<?php echo $total_amount; ?>" required>
    <input type="hidden" name="tax_amount" value="<?php echo $tax_amount; ?>" required>
    <input type="hidden" name="transaction_uuid" value="<?php echo $transaction_uuid; ?>" required>
    <input type="hidden" name="product_code" value="<?php echo $product_code; ?>" required>
    <input type="hidden" name="success_url" value="<?php echo $success_url; ?>" required>
    <input type="hidden" name="failure_url" value="<?php echo $failure_url; ?>" required>
    <input type="hidden" name="signature" value="<?php echo $signature; ?>" required>
    <input type="submit" value="Pay with eSewa">
</form>

</body>
</html>
