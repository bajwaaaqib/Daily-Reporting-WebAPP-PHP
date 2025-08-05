<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

require 'db.php';

if (isset($_POST['employee_id']) && isset($_POST['report_date'])) {
    $employee_id = $_POST['employee_id'];
    $report_date = $_POST['report_date'];

    // Fetch report for the selected employee and the specific date
    $query = $conn->prepare("SELECT report_content FROM reports WHERE employee_id = ? AND report_date = ?");
    $query->bind_param("is", $employee_id, $report_date);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows > 0) {
        $report = $result->fetch_assoc();
        // Convert line breaks to <br> for HTML display
        echo nl2br(htmlspecialchars($report['report_content']));
    } else {
         echo '<p style="color: red;">Report not submitted.</p>';
    }
}
?>
