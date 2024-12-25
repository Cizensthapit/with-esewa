<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="login.css">
    <title>Medlife</title>
</head>
<body>

    <nav class="navbar">
        <div class="logo">
            <a href="index.php">
                <img src="logo.png" alt="Medlife">
            </a> 
            </div>               

        <div class="nav-section">
            <ul>
                <li><a href="navbardata/hospitals.html">Hospitals</a></li>
                <li><a href="navbardata/ambulances.html">Ambulances</a></li>
                <li><a href="navbardata/bloodbanks.html">Blood Banks</a></li>
                <li><a href="navbardata/vets.html">Veterinary Services</a></li>
                <li><a href="navbardata/aboutus.html">About Us</a></li>
            </ul>
        </div>
    </nav>

    <div class="container" id="container">
        <div class="form-container sign-up">
            <form id="signup-form" action="signup.php" method="post">
                <h1>Create Account</h1>
                <input type="text" placeholder="Name" name="Name">
                <input type="email" placeholder="Email" name="Email">
                <input type="text" placeholder="Contact" name="Contact">
                <input type="password" placeholder="Password" name="Password">
                <button type="submit" name="signup">Sign Up</button>
            </form>
        </div>
        <div class="form-container sign-in">
            <form action="signin.php" method="post">
                <h1>Sign In</h1>
                <input type="text" placeholder="Contact" name="Contact">
                <input type="password" placeholder="Password" name="Password">
                <button type="submit" name="signin">Sign In</button>
            </form>
        </div>
        <div class="toggle-container">
            <div class="toggle">
                <div class="toggle-panel toggle-left">
                    <h1>"Been here before? Let's pick up where we left off! </h1>
                    <p>Click here to sign in! </p>
                    <button class="hidden" id="login">Sign In</button>
                </div>
                <div class="toggle-panel toggle-right">
                    <h1>Hello!</h1>
                    <p>Ready to elevate your health? Register now for exclusive benefits!</p>
                    <button class="hidden" id="register">Sign Up</button>
                </div>
            </div>
        </div>
    </div>

    <script src="login.js"></script>
</body>
</html>
