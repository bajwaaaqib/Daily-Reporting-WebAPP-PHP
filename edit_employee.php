<?php
ob_start();
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

require 'db.php';

// Fetch all employees for dropdown
$employees = $conn->query("SELECT id, name FROM employee");

$selected_employee = null;
$success_message = "";

// Check if an employee is selected
if (isset($_POST['selected_employee']) && !empty($_POST['selected_employee'])) {
    $employee_id = $_POST['selected_employee'];
    $result = $conn->query("SELECT * FROM employee WHERE id = $employee_id");
    
    // Ensure that a valid employee is returned
    if ($result && $result->num_rows > 0) {
        $selected_employee = $result->fetch_assoc();
    } else {
        $selected_employee = null;  // No employee found, prevent showing details
    }
}

// Handle employee update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_employee'])) {
    $employee_id = $_POST['employee_id'];
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $designation = $conn->real_escape_string($_POST['designation']);
    
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $conn->query("UPDATE employee SET name='$name', email='$email', password='$password', designation='$designation' WHERE id=$employee_id");
    } else {
        $conn->query("UPDATE employee SET name='$name', email='$email', designation='$designation' WHERE id=$employee_id");
    }

    $_SESSION['success_message'] = "Employee details updated successfully!";
    header("Location: edit_employee.php");
    exit();
}

// Display success message if available
if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']); // Remove message after displaying
}
ob_end_flush();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
            color: #6c757d;
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
    <script>
        // Hide success message after 3 seconds
        setTimeout(function() {
            let alertBox = document.getElementById('successMessage');
            if (alertBox) {
                alertBox.style.display = 'none';
            }
        }, 3000);
    </script>
</head>
<body style="background-color: #f1f3f5;">
    <h1 class="dashboard-heading">EMPLOYEES DAILY REPORTS</h1>
        <div class="subheading">Employee Detail Modifications</div>
    <div class="container-fluid">
	

        <!-- Success Message -->
        <?php if (!empty($success_message)): ?>
            <div id="successMessage" class="alert alert-success text-center">
                <?= $success_message ?>
            </div>
        <?php endif; ?>

        <!-- Employee Selection Dropdown -->
        <form method="POST" class="employee-section">
            <label class="form-label">Select Employee for Modification:</label>
            <select name="selected_employee" class="form-select" onchange="this.form.submit()">
                <option value="">-- Select Employee --</option>
                <?php while ($row = $employees->fetch_assoc()): ?>
                    <option value="<?= $row['id'] ?>" <?= isset($selected_employee) && $selected_employee['id'] == $row['id'] ? 'selected' : '' ?>>
                        <?= $row['name'] ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </form>

        <?php if ($selected_employee): ?>
        <!-- Employee Edit Form -->
        <div class="employee-section">
            <form method="POST">
                <input type="hidden" name="employee_id" value="<?= $selected_employee['id'] ?>">
                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" class="form-control" value="<?= $selected_employee['name'] ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="<?= $selected_employee['email'] ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">New Password (Leave blank to keep current password)</label>
                    <input type="password" name="password" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Designation</label>
                    <input type="text" name="designation" class="form-control" value="<?= $selected_employee['designation'] ?>" required>
                </div>
                <button type="submit" name="update_employee" class="btn btn-success w-100">Update Employee</button>
                <a href="admin_dashboard.php" class="btn btn-danger w-100 mt-2">Back to Dashboard</a>
            </form>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>

