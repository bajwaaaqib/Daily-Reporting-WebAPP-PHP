<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

require 'db.php';

$success_message = "";
$error_message = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $designation = trim($_POST['designation']);
    $created_by_admin_id = $_SESSION['admin_id'];

    // Check if email already exists
    $check_email = $conn->prepare("SELECT id FROM employee WHERE email = ?");
    $check_email->bind_param("s", $email);
    $check_email->execute();
    $check_email->store_result();

    if ($check_email->num_rows > 0) {
        $error_message = "Email already exists! Please use a different email.";
    } else {
        // Insert new employee
        $stmt = $conn->prepare("INSERT INTO employee (name, email, password, designation, created_by_admin_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssi", $name, $email, $password, $designation, $created_by_admin_id);

        if ($stmt->execute()) {
            $success_message = "Employee created successfully!";
        } else {
            $error_message = "Error creating employee. Please try again.";
        }

        $stmt->close();
    }

    $check_email->close();
}
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
</head>
<body style="background-color: #f1f3f5;">
        <h1 class="dashboard-heading">EMPLOYEES DAILY REPORTS</h1>
        <div class="subheading">ARD PERFUMES FACTORY LLC.</div>
    <div class="container-fluid">
       

        <?php if ($success_message): ?>
            <div class="alert alert-success"><?= $success_message ?></div>
        <?php endif; ?>

        <?php if ($error_message): ?>
            <div class="alert alert-danger"><?= $error_message ?></div>
        <?php endif; ?>

        <!-- Centered form on larger screens and full width on mobile -->
        <div class="row justify-content-center">
            <div class="content container-fluid employee-section">
			
                <form method="POST" action="create_employee.php" class="card p-4 shadow-sm">
			
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>

                    <div class="mb-3">
                        <label for="designation" class="form-label">Designation</label>
                        <input type="text" class="form-control" id="designation" name="designation" required>
                    </div>

                    <button type="submit" class="btn btn-success w-100">Create Employee</button>
                    <a href="admin_dashboard.php" class="btn btn-danger w-100 mt-2">Back to Dashboard</a>
                      <?php if ($success_message): ?>
            <div class="alert alert-success"><?= $success_message ?></div>
        <?php endif; ?>

        <?php if ($error_message): ?>
            <div class="alert alert-danger"><?= $error_message ?></div>
        <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
	<?php include 'footer.php'; ?>

</body>
</html>
