<?php
require 'db.php';

if (isset($_POST['employee_id']) && isset($_POST['report_date'])) {
    $employee_id = $_POST['employee_id'];
    $report_date = $_POST['report_date'];

    // Fetch the report for the selected date
    $query = "SELECT report_content FROM reports WHERE employee_id = $employee_id AND report_date = '$report_date'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $report = $result->fetch_assoc();
        echo nl2br(htmlspecialchars($report['report_content']));
    } else {
        echo '';
    }
} else {
    echo '';
}
?>
