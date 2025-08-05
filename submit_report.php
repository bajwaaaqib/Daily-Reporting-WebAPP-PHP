<?php
ob_start();
session_start();
if (!isset($_SESSION['employee_id'])) {
    header("Location: index.php");
    exit();
}

require 'db.php';

// Set the correct timezone
date_default_timezone_set('Asia/Dubai'); // Change as per your region



$employee_id = $_SESSION['employee_id'];
$current_date = date('Y-m-d'); // Format: YYYY-MM-DD
$current_time = date('H:i'); // Get the current time (for 24-hour limit check)
$report_content = $_POST['report_content'];

// Debugging: Check if the date and time are correct
error_log("Current Date: $current_date");
error_log("Current Time: $current_time");

// Escape the report content to prevent SQL injection errors
$report_content = $conn->real_escape_string($report_content);

// Convert newlines to <br> tags (if not already done by browser)
$report_content = nl2br($report_content);

$result = $conn->query("SELECT * FROM reports WHERE employee_id = $employee_id AND report_date = '$current_date'");

if ($result->num_rows > 0) {
    // Report exists, check if it's still editable (before 5:00 PM today)
     // Allow editing before 5:00 PM
    $can_edit = (strtotime($current_time) <= strtotime('17:00'));

    if ($can_edit) {
        // Update existing report
        $conn->query("UPDATE reports SET report_content = '$report_content' WHERE employee_id = $employee_id AND report_date = '$current_date'");
        
        // Set success message for re-submission
        $_SESSION['success_message'] = "Re-submitted successfully";
    } else {
        // Report is no longer editable
        $_SESSION['error_message'] = "You can no longer edit the report for today. Please submit a new report for tomorrow.";
        header("Location: employee_dashboard.php");
        exit();
    }
} else {
    // Insert new report (if none exists for today)
    $conn->query("INSERT INTO reports (employee_id, report_date, report_content) VALUES ($employee_id, '$current_date', '$report_content')");
    
    // Set success message for first submission
    $_SESSION['success_message'] = "Report submitted successfully";
}

header("Location: https://ardperfumes.com/reporting/employee_dashboard.php");
ob_end_flush();
?>