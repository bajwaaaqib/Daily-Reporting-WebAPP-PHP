<?php
require 'db.php';

$email = "ceo@ardperfumes.com";
$entered_password = "Ard@25Com"; // The password you used to create the admin

$stmt = $conn->prepare("SELECT password FROM admin WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $hashed_password = $row['password'];

    if (password_verify($entered_password, $hashed_password)) {
        echo "✅ Password matches!";
    } else {
        echo "❌ Password does not match!";
    }
} else {
    echo "❌ No admin found with this email!";
}

$stmt->close();
$conn->close();
?>
