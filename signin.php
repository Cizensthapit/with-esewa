<?php
// Start session
session_start();

// Database connection parameters
$servername = "localhost"; // Change this if your database is hosted elsewhere
$username = "root"; // Replace with your MySQL username
$password = ""; // Replace with your MySQL password
$dbname = "medlifemis_db"; // Replace with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Process form data on submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate form inputs
    $contact = $_POST['Contact'];
    $password = $_POST['Password'];

    // Validation rules
    if (empty($contact) || empty($password)) {
        echo "Both contact and password are required!";
    } else {
        if ($contact === '9803122520' && $password === 'cizenadmin') {
            // Redirect to admin.php for admin login
            $_SESSION['admin'] = true; // Example of setting admin session
            header("Location: admin.php");
            exit();
        } else {
            // Fetch hashed password based on contact
            $stmt = $conn->prepare("SELECT id, password FROM users WHERE contact = ?");
            $stmt->bind_param("s", $contact);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 1) {
                $user = $result->fetch_assoc();
                // Verify hashed password
                if (password_verify($password, $user['password'])) {
                    // Correct credentials, set session variables
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $contact; // Example of storing username
                    // Optionally, you can store more user information in session

                    // Redirect to user dashboard
                    header("Location: index.php");
                    exit();
                } else {
                    echo "Incorrect password!";
                }
            } else {
                echo "User not found!";
            }

            $stmt->close();
        }
    }
}

// Close connection
$conn->close();
?>

<!-- Display session values -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Session Values</title>
</head>
<body>
    <h2>Session Values</h2>
    <?php
    // Start session again to access session variables
    session_start();

    // Check if session variables are set
    if (isset($_SESSION['user_id']) && isset($_SESSION['username'])) {
        $userId = $_SESSION['user_id'];
        $username = $_SESSION['username'];
        echo "User ID: $userId<br>";
        echo "Username: $username<br>";
        // You can display more session variables as needed
    } else {
        echo "Session variables not set. Please log in.";
    }
    ?>
</body>
</html>
