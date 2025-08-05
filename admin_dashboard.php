<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

require 'db.php';

// Set the correct timezone
date_default_timezone_set('Asia/Dubai'); // Change to your correct timezone

// Fetch all employees
$query = "SELECT * FROM employee";
$result = $conn->query($query);

// Get selected employee details
$employee = null;
if (isset($_GET['employee_id'])) {
    $employee_id = $_GET['employee_id'];
    $employee = $conn->query("SELECT * FROM employee WHERE id = $employee_id")->fetch_assoc();
}

$current_date = date('Y-m-d');
$current_time = date('H:i'); // Get the current time for debugging

// Debugging: Check if the date and time are correct
error_log("Current Date: $current_date");
error_log("Current Time: $current_time");

// Fetch report for the selected employee and current date
$report_content = "Report not submitted yet!";
if ($employee) {
    $report_query = $conn->query("SELECT report_content FROM reports WHERE employee_id = $employee_id AND report_date = '$current_date'");
    if ($report_query->num_rows > 0) {
        $report_content = $report_query->fetch_assoc()['report_content'];
    }
}

// Convert report content to handle line breaks
$report_content = nl2br(htmlspecialchars($report_content));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .dashboard-heading {
            background: #00308F;
            color: #ffffff;
            text-align: center;
            padding: 15px 0;
            font-weight: bold;
            width: cover;
        }
        .subheading {
            background-color: #f1f3f5;
            text-align: center;
            font-size: 1.2rem;
            color: #BA0021;
            padding: 10px;
            font-weight: bold;
        }
        .container-box {
            max-width: 600px;
            margin: auto;
            padding: 2%;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
			margin: 5% auto;
        }
    </style>
</head>
<body style="background-color:#f1f3f5;">
    <h1 class="dashboard-heading">EMPLOYEES DAILY REPORTS</h1>
        <div class="subheading">ARD PERFUMES FACTORY LLC.</div>
    <div class="container-fluid">
        
        <div class="container-box">
            <label for="employeeSelect" class="form-label">Select Employee:</label>
            <select id="employeeSelect" class="form-select">
                <option value="">-- Choose Employee --</option>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <option value="<?= $row['id'] ?>"><?= $row['name'] ?></option>
                <?php endwhile; ?>
            </select>
            
            <label for="reportDate" class="form-label mt-3">Report of Specific Date:</label>
            <input type="date" id="reportDate" class="form-control" value="<?= date('Y-m-d') ?>">  <!-- Get today's date format -->   
            
            <div id="reportContent" class="mt-4 p-3 border rounded bg-light">Select an employee and date to view the report.</div>
			 <!-- Create New Employee Button -->
			 
			 
			  <!-- Setting Dropdown -->          
                <div class="dropdown center d-flex justify-content-center mt-3">
                    <button class="btn btn-outline-danger dropdown-toggle"  id="settingsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    Settings
                    </button>
                        <ul class="dropdown-menu" aria-labelledby="settingsDropdown">
                            <li><a class="dropdown-item" href="create_employee.php">New Employee</a></li>
							<li><a class="dropdown-item" href="edit_employee.php">Edit Employee</a></li>
                            <li><a class="dropdown-item" href="change_password_admin.php">Change Password</a></li>
                            <li><a class="dropdown-item text-danger" href="logout.php">Logout</a></li>
                        </ul>
                </div>
        </div> 
    </div>
    
    <script>
        $(document).ready(function() {
            $('#employeeSelect, #reportDate').change(function() {
                var employee_id = $('#employeeSelect').val();
                var date = $('#reportDate').val();
                if (employee_id) {
                    $.ajax({
                        url: 'fetch_report.php',
                        type: 'POST',
                        data: { employee_id: employee_id, report_date: date },
                        success: function(response) {
                            $('#reportContent').html(response);
                        }
                    });
                } else {
                    $('#reportContent').html('Please select an employee.');
                }
            });
        });
    </script>
	<?php include 'footer.php'; ?>
</body>
</html>