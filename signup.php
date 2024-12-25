<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 20px;
        }
        .error a {
            color: #721c24;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php
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
            $name = $_POST['Name'];
            $email = $_POST['Email'];
            $contact = $_POST['Contact'];
            $password = $_POST['Password'];

            // Validation rules
            $error_message = '';

            if (empty($name) || empty($email) || empty($contact) || empty($password)) {
                $error_message .= "<p>All fields are required!</p>";
            } elseif (is_numeric($name)) {
                $error_message .= "<p>Name cannot be a number!</p>";
            } elseif (!preg_match("/^[a-zA-Z\s]+$/", $name)) {
                $error_message .= "<p>Name can only contain letters and spaces!</p>";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error_message .= "<p>Invalid email format!</p>";
            } elseif (!preg_match('/^98\d{8}$/', $contact)) {
                $error_message .= "<p>Contact must be numeric, start with 98, and be exactly 10 digits long!</p>";
            } elseif (strlen($password) < 5) {
                $error_message .= "<p>Password must be at least 5 characters long!</p>";
            } else {
                // Check if the user or contact number already exists in the database
                $check_query = "SELECT * FROM users WHERE email=? OR contact=?";
                $check_stmt = $conn->prepare($check_query);
                $check_stmt->bind_param("ss", $email, $contact);
                $check_stmt->execute();
                $check_result = $check_stmt->get_result();

                if ($check_result->num_rows > 0) {
                    $error_message .= "<p>User or contact number already exists! <a href='login.php'>Try again</a></p>";
                } else {
                    // All validations passed and user does not exist, proceed with signup
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Hash password for security

                    // Prepare SQL statement to insert data into users table
                    $stmt = $conn->prepare("INSERT INTO users (name, email, contact, password) VALUES (?, ?, ?, ?)");
                    $stmt->bind_param("ssss", $name, $email, $contact, $hashed_password);

                    if ($stmt->execute()) {
                        // Signup successful, redirect to login page
                        header("Location: login.php");
                        exit(); // Ensure no further output is sent
                    } else {
                        $error_message .= "<p>Error: " . $stmt->error . "</p>";
                    }

                    $stmt->close();
                }

                $check_stmt->close();
            }

            if ($error_message) {
                echo "<div class='error'>$error_message</div>";
            }
        }

        // Close connection
        $conn->close();
        ?>
    </div>
</body>
</html>
