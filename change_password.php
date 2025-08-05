<?php
session_start();

if (!isset($_SESSION['employee_id'])) {
    header("Location: index.php");
    exit();
}

require 'db.php';

$employee_id = $_SESSION['employee_id'];
$error_message = "";
$success_message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if the new password and confirm password match
    if ($new_password === $confirm_password) {
        // Hash the new password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // Update the password in the database
        if ($conn->query("UPDATE employee SET password = '$hashed_password' WHERE id = $employee_id")) {
            $success_message = "Password updated successfully.";
        } else {
            $error_message = "Failed to update the password. Please try again.";
        }
    } else {
        $error_message = "New password and confirmation do not match.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
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
</head>
<body style="background-color:#f1f3f5;">
    <div class="content container-fluid employee-section">
        <h2 class="text-center">Change password</h2>

        <?php if ($error_message): ?>
            <div class="alert alert-danger"><?= $error_message ?></div>
        <?php endif; ?>

        <?php if ($success_message): ?>
            <div class="alert alert-success"><?= $success_message ?></div>
        <?php endif; ?>

        <form method="POST" action="change_password.php" class="card p-4 shadow-sm">
            <div class="mb-3">
                <label for="new_password" class="form-label">New Password</label>
                <input type="password" class="form-control" id="new_password" name="new_password" required>
            </div>

            <div class="mb-3">
                <label for="confirm_password" class="form-label">Confirm New Password</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
            </div>

            <button type="submit" class="btn btn-success w-100">Update Password</button>
            <a href="employee_dashboard.php" class="btn btn-danger w-100 mt-2">Back to Dashboard</a>
        </form>
    </div>
</body>
</html>
