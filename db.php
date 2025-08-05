
<?php
$servername = "localhost";
$username = "marcoluc_dailyreportingsystem";
$password = "Ard@123PeRf";
$dbname = "marcoluc_dailyreportingsystem";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>


