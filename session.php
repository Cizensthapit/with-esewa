<?php
// Start session
session_start();

// Display session variables
echo "<h2>Session Variables</h2>";
echo "<ul>";
foreach ($_SESSION as $key => $value) {
    echo "<li><strong>$key:</strong> $value</li>";
}
echo "</ul>";
?>
