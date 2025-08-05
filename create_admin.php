<?php
require 'db.php';

$name = "Abdifetah";
$email = "ceo@ardperfumes.com";
$password = password_hash("admin@0125", PASSWORD_DEFAULT); // Hash the password

// Use the exact table name as in your database (case-sensitive)
$query = "INSERT INTO admin (name, email, password) VALUES ('$name', '$email', '$password')";

if ($conn->query($query) === TRUE) {
    echo "Admin created successfully with hashed password!";
} else {
    echo "Error: " . $conn->error;
}
?>

