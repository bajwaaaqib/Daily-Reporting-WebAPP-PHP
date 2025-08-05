<?php
session_start();
if (!isset($_SESSION['employee_id'])) {
    header("Location: index.php");
    exit();
}
 //date of modification 12-02-25 time: 09:48AM
// Check if the login date is stored in session
if (isset($_SESSION['login_date'])) {
    // Get the current date
    $current_date = date('Y-m-d');
    
    // Compare stored login date with current date
    if ($_SESSION['login_date'] !== $current_date) {
        // If the date has changed, destroy the session and redirect to login page
        session_destroy();
        header("Location: index.php"); // Redirect to login page
        exit(); // Make sure the script stops execution
    }
} else {
    // Set the login date if it's not already set
    $_SESSION['login_date'] = date('Y-m-d'); // Store the current date when the user logs in
}


require 'db.php';

// Set the correct timezone
date_default_timezone_set('Asia/Dubai'); // Change to your correct timezone

$employee_id = $_SESSION['employee_id'];
$current_date = date('Y-m-d'); // Format: YYYY-MM-DD
$current_time = date('H:i');

// Debugging: Check if the date and time are correct
error_log("Current Date: $current_date");
error_log("Current Time: $current_time");

// Fetch employee details
$employee = $conn->query("SELECT * FROM employee WHERE id = $employee_id")->fetch_assoc();

// Fetch today's report
$report = $conn->query("SELECT * FROM reports WHERE employee_id = $employee_id AND report_date = '$current_date'")->fetch_assoc();

// Check if success message exists
$success_message = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : null;
if ($success_message) {
    unset($_SESSION['success_message']);
}

// Fetch previous reports for the employee
$previous_reports_query = $conn->query("SELECT report_date FROM reports WHERE employee_id = $employee_id ORDER BY report_date DESC");
$previous_reports = [];
while ($row = $previous_reports_query->fetch_assoc()) {
    $previous_reports[] = $row['report_date'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .dashboard-heading {
            background: #00308F;
            color: #ffffff;
            text-align: center;
            padding: 15px 0;
            font-weight: bold;
            width: 100%;
            position: relative;
        }

        .subheading {
            background-color: #f1f3f5;
            text-align: center;
            margin-top: -10px;
            font-size: 1.2rem;
            color: #BA0021;
            padding: 10px;
            font-weight: bold;
        }

       

        .content {
            margin-top: 20px;
        }

        .dashboard-card {
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 5px;
            background-color: #f8f9fa;
        }

        .employee-info {
            text-align: center;
            margin-bottom: 20px;
        }

        .employee-info h2, .employee-info h5, .employee-info p {
            margin: 5px 0;
        }

        .success-message {
            color: green;
            text-align: center;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .employee-section {
            width: 100%;
            max-width: 600px; /* Adjust for larger screens */
            margin: 5% auto;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        /* Responsive Styles for smaller screens */
        @media (max-width: 768px) {
            .employee-section {
                width: 90%;
                padding: 15px;
            }

            .dashboard-heading h1 {
                font-size: 24px;
            }

            .employee-info h2 {
                font-size: 20px;
            }

            .employee-info h5 {
                font-size: 16px;
            }

            .form-control {
                font-size: 14px; /* Ensure form inputs are legible */
            }

            .btn-primary {
                padding: 10px; /* Make button padding smaller */
                font-size: 14px;
            }

            .logout-btn {
                padding: 6px 12px;
                font-size: 14px;
            }
        }

        @media (max-width: 576px) {
            .employee-section {
                width: 100%;
                padding: 10px;
            }

            .dashboard-heading h1 {
                font-size: 20px;
            }

            .employee-info h2 {
                font-size: 18px;
            }

            .employee-info h5 {
                font-size: 14px;
            }

            .btn-primary {
                font-size: 12px;
            }

            .logout-btn {
                padding: 4px 8px;
                font-size: 12px;
            }
        }
    </style>
</head>
<body style="background-color:#f1f3f5;">
    <h1 class="dashboard-heading">EMPLOYEES DAILY REPORTS</h1>
        <div class="subheading">ARD PERFUMES FACTORY LLC.</div>
    <div class="container-fluid">

        <div class="content container-fluid employee-section">
            <div class="dashboard-card">
                <?php if ($success_message): ?>
                    <div class="success-message"><?= $success_message ?></div>
                <?php endif; ?>

                <div class="employee-info">
                    <h2><?= $employee['name'] ?></h2>
                    <h5 class="text-muted"><?= $employee['designation'] ?></h5>
                    <p>Today: <?= $current_date ?></p>
                </div>
       
                <!-- Report Submission Form -->
             <form method="POST" action="submit_report.php">
                    <textarea name="report_content" class="form-control mb-3" rows="5" placeholder="Write is bullets (with Line Break)"><?= $report['report_content'] ?? '' ?></textarea>
                    <button type="submit" class="btn btn-primary w-100">Submit Report</button>
                </form>
            </div>

            <!-- Date Picker to select previous reports -->
            <div class="dashboard-card mt-4">
                <label for="previousReportsDate">View Previous Reports:</label>
                <input type="date" id="previousReportsDate" class="form-control mb-7" value="<?= $current_date ?>">

                <!-- Display Previous Report -->
                <div id="previousReportContent" class="mt-3">
                    <!-- Previous report will be displayed here -->
                </div>
            </div>
			<!-- Setting Dropdown -->          
            <div class="dropdown center d-flex justify-content-center mt-3">
                    <button class="btn btn-outline-danger dropdown-toggle"  id="settingsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    Settings
                    </button>
                        <ul class="dropdown-menu" aria-labelledby="settingsDropdown">
                            <li><a class="dropdown-item" href="change_password.php">Change Password</a></li>
                            <li><a class="dropdown-item text-danger" href="logout.php">Logout</a></li>
                        </ul>
            </div>
        </div>
			
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#previousReportsDate').change(function() {
                var selectedDate = $(this).val();
                if (selectedDate) {
                    $.ajax({
                        url: 'fetch_previous_report.php',
                        type: 'POST',
                        data: {
                            employee_id: <?= $employee_id ?>,
                            report_date: selectedDate
                        },
                        success: function(response) {
                            if (response) {
                                $('#previousReportContent').html('<p>' + response + '</p>');
                            } else {
                                $('#previousReportContent').html('<p>You did not submit.</p>');
                            }
                        }
                    });
                }
            });
        });
    </script>
	<?php include 'footer.php'; ?>

</body>
</html>
