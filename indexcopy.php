<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Medlife - User Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div style="margin-left: 30px;">
            <img src="logo.png" alt="Medlife" style="width: 300px; height: 140px;">
        </div>
        <div class="nav-section">
            <ul>
                <li><a href="#">All Products</a></li> 
                <li><a href="navbardata/hospitals.html">Hospitals</a></li>
                <li><a href="navbardata/ambulances.html">Ambulances</a></li>
                <li><a href="navbardata/bloodbanks.html">Blood Banks</a></li>
                <li><a href="navbardata/vets.html">Veterinary Services</a></li>
                <li><a href="navbardata/aboutus.html">About Us</a></li>
                <li><a href="account.php">Account</a></li>
                <?php
                // Start session
                session_start();

                // Check if user is logged in
                if (isset($_SESSION['user_id'])) {
                    // Fetch user name or any other relevant information from session
                    $username = $_SESSION['username']; // Assuming you stored username during login
                    $user_id = $_SESSION['user_id'];

                    // Display username in the navbar
                   // echo '<li>Welcome, ' . htmlspecialchars($username) . '</li>';
                    echo '<li><a href="logout.php">Logout</a></li>'; // Add logout link
                } else {
                    // If not logged in, show login/register links
                    echo '<li><a href="login.php">Login</a></li>';
                    echo '<li><a href="login.php">Register</a></li>';
                
                }
                ?>
            </ul>
        </div>
    </header>

    <div class="container">
        <img src="cetaphilslide.jpeg" alt="" height="400px" width="1500px">
        <h2>Our Products</h2>

        <?php
        // Database connection
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "medlife1_db";
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Types of products
        $types = ["Skin care", "Babies", "OTC", "Health Supplement"];

        foreach ($types as $ptype) {
            echo "<h3>" . htmlspecialchars($ptype) . "</h3>";
            echo '<div class="product-list">';
            
            // Query to fetch products of the current type
            $sql = "SELECT pid, pname, pprice, pimage FROM products WHERE ptype=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $ptype);
            $stmt->execute();
            $result = $stmt->get_result();

            // Display products
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="product-item">';
                    echo '<img class="product-image" src="' . htmlspecialchars($row["pimage"]) . '" alt="' . htmlspecialchars($row["pname"]) . '">';
                    echo '<h5>' . htmlspecialchars($row["pname"]) . '</h5>';
                    echo '<p>Rs' . htmlspecialchars($row["pprice"]) . '</p>';
                    echo '<div class="add-to-cart">';
                    echo '<input type="number" id="quantity_' . htmlspecialchars($row["pid"]) . '" class="quantity" value="1" min="1">';
                    echo '<button onclick="addToCart(' . htmlspecialchars($row["pid"]) . ', \'' . htmlspecialchars($row["pname"]) . '\', ' . htmlspecialchars($row["pprice"]) . ')">Add to Cart</button>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo "No products found in this category.";
            }

            echo '</div>';
            $stmt->close();
        }

        // Close database connection
        $conn->close();
        ?>
    </div>

    <!-- Cart Items Section -->
    <div class="container">
        <h2>Cart Items</h2>
        <div id="cartItemList">
            <?php
            // Display cart items if user is logged in
            if (isset($_SESSION['user_id'])) {
                include 'fetchCartitems.php'; // Include script to fetch and display cart items
            } else {
                echo "<p>Please log in to view your cart.</p>";
            }
            ?>
        </div>
    </div>

    <footer class="footer">
        <div class="footercontainer">
            <div class="row">
                <div class="footer-col">
                    <ul>
                        <h4>Company</h4>
                        <li><a href="navbardata/aboutus.html">About Us</a></li>
                        <li><a href="#">Our Services</a></li>
                        <li><a href="#">Privacy Policy</a></li>
                        <li><a href="#">Affiliate Program</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <ul>
                        <h4>Get Help</h4>
                        <li><a href="#">FAQ</a></li>
                        <li><a href="#">Shipping</a></li>
                        <li><a href="#">Return</a></li>
                        <li><a href="#">Order Status</a></li>
                        <li><a href="#">Payment Options</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <ul> 
                        <h4>Product Description</h4>
                        <li><a href="#">Skin Care</a></li>
                        <li><a href="#">Babies</a></li>
                        <li><a href="#">Over The Counter</a></li>
                        <li><a href="#">Health Supplements</a></li>
                        <li><a href="#">Beurer Devices</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>

    <script>
        function addToCart(productId, productName, productPrice) {
            var quantity = document.getElementById('quantity_' + productId).value;
            var params = "productId=" + productId + "&productName=" + productName + "&productPrice=" + productPrice + "&quantity=" + quantity;

            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'addTocart.php', true);
            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

            xhr.onload = function () {
                if (xhr.status == 200) {
                    console.log('Product added to cart.');
                    // After adding to cart, refresh cart items
                    refreshCartItems();
                } else {
                    console.log('Failed to add product to cart. Status: ' + xhr.status);
                }
            };

            xhr.onerror = function () {
                console.log('Request failed.');
            };

            xhr.send(params);
        }

        function refreshCartItems() {
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'fetchCartitems.php', true);

            xhr.onload = function () {
                if (xhr.status == 200) {
                    document.getElementById('cartItemList').innerHTML = xhr.responseText;
                } else {
                    console.log('Failed to fetch cart items. Status: ' + xhr.status);
                }
            };

            xhr.onerror = function () {
                console.log('Request failed.');
            };

            xhr.send();
        }

        // Initial call to fetch cart items when the page loads
        window.onload = function () {
            refreshCartItems();
        };
    </script>
</body>
</html>
