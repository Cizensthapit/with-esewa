<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start(); // Start the session to access session variables

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

// Get user_id from session
if (isset($_SESSION['user_id'])) {
  $user_id = $_SESSION['user_id'];
} else {
  die("User ID not found in session.");
}

// Get username (contact) from session
if (isset($_SESSION['username'])) {
  $contact = $_SESSION['username'];
} else {
  die("Username not found in session.");
}

// Get exact location from POST request
if (isset($_POST['location'])) {
  $location = $conn->real_escape_string($_POST['location']);
} else {
  die("Location not found in POST request.");
}

// Check if payment button has been clicked
if (isset($_POST['payment'])) {
    $payment_method = $_POST['payment'];

    // Handle "Cash on Delivery" (COD)
    if ($payment_method === 'cod') {
        // Move data from checkout to order table
        $sql_move_to_order = "INSERT INTO `order` (user_id, product_id, product_name, product_price, quantity, contact, location)
                              SELECT $user_id, product_id, product_name, product_price, quantity, '$contact', '$location'
                              FROM checkout
                              WHERE imported_timestamp >= NOW() - INTERVAL 1 MINUTE";

        if ($conn->query($sql_move_to_order) === TRUE) {
            // Clear the checkout table after moving data to order
            $sql_clear_checkout = "DELETE FROM checkout
                                   WHERE imported_timestamp >= NOW() - INTERVAL 1 MINUTE";

            if ($conn->query($sql_clear_checkout) === TRUE) {
                // Display a success message with a "Go to Home" button
                echo '
                <!DOCTYPE html>
                <html lang="en">
                <head>
                  <meta charset="UTF-8">
                  <meta name="viewport" content="width=device-width, initial-scale=1.0">
                  <title>Order Placed</title>
                  <style>
                    body {
                      font-family: Arial, sans-serif;
                      background-color: #f5f5f5;
                      margin: 0;
                      display: flex;
                      justify-content: center;
                      align-items: center;
                      height: 100vh;
                    }
                    .message {
                      padding: 30px;
                      background-color: #dff0d8;
                      border: 1px solid #c3e6cb;
                      color: #155724;
                      border-radius: 8px;
                      text-align: center;
                      box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
                    }
                    .button-container {
                      margin-top: 20px;
                    }
                    .button-container button {
                      padding: 12px 24px;
                      background-color: #007bff;
                      border: none;
                      color: #fff;
                      cursor: pointer;
                      border-radius: 5px;
                      font-size: 16px;
                      text-transform: uppercase;
                    }
                    .button-container button:hover {
                      background-color: #0056b3;
                    }
                  </style>
                </head>
                <body>
                  <div class="message">
                    <h2>Order Placed Successfully</h2>
                    <p>Thank you for your purchase. Your order has been placed and will be processed shortly.</p>
                    <div class="button-container">
                      <a href="index.php"><button>Go to Home</button></a>
                    </div>
                  </div>
                </body>
                </html>';
            } else {
                echo "Error clearing checkout table: " . $conn->error;
            }
        } else {
            echo "Error moving data to order table: " . $conn->error;
        }
    }
    // Handle "Pay via Esewa"
    elseif ($payment_method === 'esewa') {
        // Move data from checkout to order table for Esewa
        $sql_move_to_order = "INSERT INTO `order` (user_id, product_id, product_name, product_price, quantity, contact, location)
                              SELECT $user_id, product_id, product_name, product_price, quantity, '$contact', '$location'
                              FROM checkout
                              WHERE imported_timestamp >= NOW() - INTERVAL 1 MINUTE";

        if ($conn->query($sql_move_to_order) === TRUE) {
            // Clear the checkout table after moving data to order
            $sql_clear_checkout = "DELETE FROM checkout
                                   WHERE imported_timestamp >= NOW() - INTERVAL 1 MINUTE";

            if ($conn->query($sql_clear_checkout) === TRUE) {
                // Redirect to payment.php for Esewa payment
                header("Location: payment.php");
                exit(); // Stop further script execution after redirect
            } else {
                echo "Error clearing checkout table: " . $conn->error;
            }
        } else {
            echo "Error moving data to order table: " . $conn->error;
        }
    }
} else {
    // Display payment options form if no payment method is chosen yet
    echo '
    <!DOCTYPE html>
    <html lang="en">
    <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Choose Payment Method</title>
      <style>
        body {
          font-family: Arial, sans-serif;
          background-color: #f5f5f5;
          margin: 0;
          display: flex;
          justify-content: center;
          align-items: center;
          height: 100vh;
        }
        .message {
          padding: 20px;
          background-color: #dff0d8;
          border: 1px solid #c3e6cb;
          color: #155724;
          border-radius: 4px;
          text-align: center;
        }
        .button-container {
          margin-top: 20px;
        }
        .button-container button {
          padding: 10px 20px;
          background-color: #007bff;
          border: none;
          color: #fff;
          cursor: pointer;
          border-radius: 4px;
          font-size: 16px;
          margin: 5px;
        }
        .button-container button:hover {
          background-color: #0056b3;
        }
      </style>
    </head>
    <body>
      <div class="message">
        <h2>Choose your payment option</h2>
        <form method="POST" action="">
          <input type="hidden" name="location" value="' . $location . '">
          <div class="button-container">
            <button type="submit" name="payment" value="cod">Cash on Delivery (COD)</button>
            <button type="submit" name="payment" value="esewa">Pay via Esewa</button>
          </div>
        </form>
      </div>
    </body>
    </html>';
}

$conn->close();
?>