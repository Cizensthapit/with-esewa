<?php
// Database connection
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

// Handle delete request
if (isset($_POST['delete_user_id'])) {
    $deleteUserId = $_POST['delete_user_id'];
    $deleteSql = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($deleteSql);
    $stmt->bind_param("i", $deleteUserId);
    $stmt->execute();
    $stmt->close();
    header("Location: admin.php"); // Redirect to the same page to refresh the user list
}

// Fetch user information from the database
$sql = "SELECT * FROM users";
$userResult = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        /* CSS styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 0;
        }
        nav {
            background-color: #333;
            color: white;
            padding: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        nav ul {
            list-style-type: none;
            margin: 0;
            padding: 0;
            display: flex;
        }
        nav ul li {
            margin-right: 20px;
        }
        nav ul li a {
            color: white;
            text-decoration: none;
            font-weight: bold;
        }
        nav ul li a:hover {
            color: #ff9900;
        }
        h1, h2 {
            text-align: center;
            margin-top: 20px;
            color: #333;
        }
        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            background-color: #fff;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .delete-btn {
            background-color: #ff4d4d;
            color: white;
            border: none;
            padding: 8px 12px;
            cursor: pointer;
            border-radius: 4px;
        }
        .delete-btn:hover {
            background-color: #ff1a1a;
        }
        .center {
            text-align: center;
        }
    </style>
    <script>
        function confirmDelete(userId) {
            if (confirm('Are you sure you want to delete this user?')) {
                document.getElementById('deleteUserId').value = userId;
                document.getElementById('deleteForm').submit();
            }
        }
    </script>
</head>
<body>
    <!-- Navigation bar -->
    <nav>
        <ul>
            <li><a href="admin.php">Admin Dashboard</a></li>
            <li><a href="orderfinal.php">Orders</a></li>
            <li><a href="approvedorder.php">Approved orders</a></li>
            <li><a href="sqlproducts.php">Upload Products</a></li>
            
            <!-- Add more links/buttons as needed -->
        </ul>
        <div>
            <a href="logout.php" class="logout">Logout</a>
        </div>
    </nav>

    <!-- Main content area -->
    <h1>Admin Dashboard</h1>
    <h2>User Information</h2>
    <?php if ($userResult->num_rows > 0) { ?>
        <form id="deleteForm" method="POST" action="">
            <input type="hidden" id="deleteUserId" name="delete_user_id" value="">
        </form>
        <table>
            <tr>
                <th>CID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Contact</th>
                <th class="center">Actions</th>
            </tr>
            <?php while ($row = $userResult->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['email']; ?></td>
                    <td><?php echo $row['contact']; ?></td>
                    <td class="center">
                        <button type="button" class="delete-btn" onclick="confirmDelete(<?php echo $row['id']; ?>)">Remove</button>
                    </td>
                </tr>
            <?php } ?>
        </table>
    <?php } else { ?>
        <p style="text-align: center;">No user information found.</p>
    <?php } ?>
</body>
</html>

<?php
$conn->close();
?>
