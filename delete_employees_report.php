<?php
include 'db.php'; // Ensure this file contains database connection details

try {
    // Disable foreign key checks
    $conn->query("SET FOREIGN_KEY_CHECKS = 0;");

    // Delete all records from employees
    $conn->query("DELETE FROM employee;");
    $conn->query("ALTER TABLE employee AUTO_INCREMENT = 1;");

    // Truncate reports table
    $conn->query("TRUNCATE TABLE reports;");

    // Re-enable foreign key checks
    $conn->query("SET FOREIGN_KEY_CHECKS = 1;");

    echo "Tables cleaned successfully.";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

$conn->close();
?>