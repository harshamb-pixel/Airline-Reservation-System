<?php
// Simple test file to check if PHP and database are working
echo "<h1>PHP Test</h1>";
echo "PHP Version: " . phpversion() . "<br>";

// Test database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "airline";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo "<p style='color:red'>❌ Database Connection Failed: " . $conn->connect_error . "</p>";
    echo "<p>Please check:</p>";
    echo "<ul>";
    echo "<li>MySQL is running in XAMPP</li>";
    echo "<li>Database 'airline' exists</li>";
    echo "<li>Database credentials are correct</li>";
    echo "</ul>";
} else {
    echo "<p style='color:green'>✅ Database Connected Successfully!</p>";
    
    // Check if required tables exist
    $tables = ['flight', 'airplane', 'seat', 'reservation'];
    foreach($tables as $table) {
        $result = $conn->query("SHOW TABLES LIKE '$table'");
        if($result->num_rows > 0) {
            echo "<p style='color:green'>✅ Table '$table' exists</p>";
        } else {
            echo "<p style='color:orange'>⚠️ Table '$table' missing</p>";
        }
    }
}

echo "<br><a href='make_reservation.php'>→ Go to Make Reservation</a>";
?>
