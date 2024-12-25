<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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

// Handle form submission to insert new product
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $type = $_POST['type'];

    // Directory where files will be uploaded
    $target_dir = "uploads/";
    // Ensure the directory exists
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);
    }
    // Path of the uploaded file
    $target_file = $target_dir . basename($_FILES["image"]["name"]);
    // File upload success flag
    $uploadOk = 1;
    // File type
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if file already exists
    if (file_exists($target_file)) {
        echo "Sorry, file already exists.";
        $uploadOk = 0;
    }

    // Check file size (e.g., 5MB limit)
    if ($_FILES["image"]["size"] > 5000000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    // Allow certain file formats
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
    // If everything is ok, try to upload file
    } else {
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            // Prepare and bind parameters for insertion
            $stmt = $conn->prepare("INSERT INTO products (pname, pprice, pimage, ptype) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("sdss", $name, $price, $target_file, $type);

            // Execute the statement
            if ($stmt->execute()) {
                echo "The file " . htmlspecialchars(basename($_FILES["image"]["name"])) . " has been uploaded and record added.";
            } else {
                echo "Error: " . $stmt->error;
            }

            $stmt->close();
        } else {
            echo "Sorry, there was an error uploading your file. Error code: " . $_FILES["image"]["error"];
        }
    }
}

// Handle product deletion
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $sql = "DELETE FROM products WHERE pid=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) {
        echo "Product deleted successfully.";
    } else {
        echo "Error deleting product: " . $stmt->error;
    }
    $stmt->close();
}

// Handle product editing
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id'])) {
    $edit_id = $_POST['edit_id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $type = $_POST['type'];

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        
        if ($_FILES["image"]["size"] > 5000000) {
            echo "Sorry, your file is too large.";
            $uploadOk = 0;
        }

        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $stmt = $conn->prepare("UPDATE products SET pname=?, pprice=?, pimage=?, ptype=? WHERE pid=?");
            $stmt->bind_param("sdssi", $name, $price, $target_file, $type, $edit_id);
        } else {
            echo "Sorry, there was an error uploading your file. Error code: " . $_FILES["image"]["error"];
        }
    } else {
        $stmt = $conn->prepare("UPDATE products SET pname=?, pprice=?, ptype=? WHERE pid=?");
        $stmt->bind_param("sdsi", $name, $price, $type, $edit_id);
    }

    if ($stmt->execute()) {
        echo "Product updated successfully.";
    } else {
        echo "Error updating product: " . $stmt->error;
    }
    $stmt->close();
}

// Query to fetch products from the database
$sql = "SELECT pid, pname, pprice, ptype, pimage FROM products";
$result = $conn->query($sql);

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload File and Product List</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f2f2f2;
        }
        h2 {
            margin-bottom: 15px;
            color: #333;
        }
        form {
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            color: #333;
        }
        td img {
            max-width: 100px;
            max-height: 100px;
            border-radius: 5px;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f2f2f2;
        }
        .error {
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h2>Upload Product</h2>
    <form action="" method="post" enctype="multipart/form-data">
        Name: <input type="text" name="name" required><br>
        Price: <input type="number" step="0.01" name="price" required><br>
        Type: <input type="text" name="type" required><br>
        Select image to upload: <input type="file" name="image" required><br>
        <input type="submit" value="Upload Product" name="submit">
    </form>

    <h2>Product List</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Price</th>
            <th>Type</th>
            <th>Image</th>
            <th>Actions</th>
        </tr>
        <?php
        // Connect to database
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "medlifemis_db";
        $conn = new mysqli($servername, $username, $password, $dbname);
        
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Query to fetch products
        $sql = "SELECT pid, pname, pprice, ptype, pimage FROM products";
        $result = $conn->query($sql);

        // Output data of each row
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row["pid"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["pname"]) . "</td>";
                echo "<td>Rs" . htmlspecialchars($row["pprice"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["ptype"]) . "</td>";
                echo '<td><img src="' . htmlspecialchars($row["pimage"]) . '" alt="' . htmlspecialchars($row["pname"]) . '"></td>';
                echo '<td>';
                echo '<form style="display:inline;" method="POST" action="">';
                echo '<input type="hidden" name="edit_id" value="' . htmlspecialchars($row["pid"]) . '">';
                echo '<input type="text" name="name" value="' . htmlspecialchars($row["pname"]) . '" required>';
                echo '<input type="number" step="0.01" name="price" value="' . htmlspecialchars($row["pprice"]) . '" required>';
                echo '<input type="text" name="type" value="' . htmlspecialchars($row["ptype"]) . '" required>';
                echo '<input type="file" name="image">';
                echo '<input type="submit" value="Edit">';
                echo '</form>';
                echo '<a href="?delete_id=' . htmlspecialchars($row["pid"]) . '" onclick="return confirm(\'Are you sure you want to delete this product?\')">Delete</a>';
                echo '</td>';
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='6'>No products found</td></tr>";
        }
        
        // Close the database connection
        $conn->close();
        ?>
    </table>

    <!-- Add button with inline CSS -->
    <button onclick="window.location.href='admin.php'" style="margin-top: 20px; padding: 10px 20px; background-color: #4CAF50; color: white; border: none; border-radius: 5px; cursor: pointer;">Go to Admin Page</button>
</body>
</html>
