<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAQs & Complaints - MedLife</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }
        header {
            background-color: #000000;
            color: white;
            text-align: center;
            padding: 20px 0;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 0 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
         h2, h3 {
            margin-top: 0;
            color: #333;
        }
        p {
            line-height: 1.6;
            margin-bottom: 20px;
        }
        input[type="text"],
        textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            resize: vertical;
        }
        button {
            background-color: black;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            border: 0.5px solid black;
        }
        button:hover {
            background-color: white;
            color: black;
        }
    </style>
</head>
<body>
    <header>
        <h1>FAQs & Complaints</h1>
    </header>
    <div class="container">
        <h2>Return & Refund Policy</h2>
        <h3>Return:</h3>
        <p>We ensure that the products you order match the specifications you expect. However, if errors occur, you can request a return within a week of delivery.</p>
        <h3>Refund:</h3>
        <p>We strive to satisfy you with our products, but sometimes faults may occur. Refunds are applicable in the following cases:</p>
        <ul>
            <li>If the product has a defect or damage.</li>
            <li>If the product is beyond its expiry date.</li>
            <li>If the product gets damaged during delivery.</li>
        </ul>
        <p>Please note that returns and refunds are subject to our terms and conditions. For further assistance, please contact our customer service team.</p>

        <h2>Complaints</h2>
        <p>If you have any complaints or dissatisfaction, please feel free to share them with us using the form below. Your feedback helps us improve our services.</p>
        <form id="complaintForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <input type="text" id="name" name="name" placeholder="Your Name" required>
            <input type="text" id="contact" name="contact" placeholder="Your Contact Number" required>
            <textarea id="complaint" name="complaint" placeholder="Write your complaint here (up to 1000 words)" required></textarea>
            <button type="submit">Report</button>
        </form>
    </div>

    <?php
    // Display PHP errors
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    // Check if the form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Collect form data
        $name = $_POST["name"];
        $contact = $_POST["contact"];
        $complaint = $_POST["complaint"];

        // Debug: display collected data
        echo "<script>console.log('Name: $name, Contact: $contact, Complaint: $complaint');</script>";

        // Validate and sanitize the data
        if (empty($name) || empty($contact) || empty($complaint)) {
            // Handle empty fields
            echo "<script>alert('Please fill in all the fields.');</script>";
            exit;
        }

        // Database connection
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "medlifemis_db";

        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Check connection
        if ($conn->connect_error) {
            echo "<script>alert('Database connection failed: " . $conn->connect_error . "');</script>";
            exit;
        }

        // Prepare and bind SQL statement
        $stmt = $conn->prepare("INSERT INTO complaints (name, contact, complaint_text) VALUES (?, ?, ?)");
        if ($stmt === false) {
            echo "<script>alert('Prepare failed: " . $conn->error . "');</script>";
            exit;
        }
        $stmt->bind_param("sss", $name, $contact, $complaint);

        // Execute SQL statement
        if ($stmt->execute()) {
            echo "<script>alert('Complaint submitted successfully!');</script>";
        } else {
            echo "<script>alert('Error submitting complaint: " . $stmt->error . "');</script>";
        }

        // Close statement and connection
        $stmt->close();
        $conn->close();
    }
    ?>
</body>
</html>
