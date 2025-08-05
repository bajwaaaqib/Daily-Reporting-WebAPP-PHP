<?php
ob_start();
session_start();
if (isset($_SESSION['admin_id']) || isset($_SESSION['employee_id'])) {
    header("Location: " . (isset($_SESSION['admin_id']) ? "admin_dashboard.php" : "employee_dashboard.php"));
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require 'db.php';

    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role = $_POST['role'];

    $table = ($role == 'admin') ? 'admin' : 'employee';

    // Use prepared statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM $table WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Check if password matches (if stored as a hashed value)
        if (password_verify($password, $user['password'])) {
            $_SESSION[$role . '_id'] = $user['id'];
            $_SESSION[$role . '_name'] = $user['name'];
            header("Location: " . ($role == 'admin' ? "admin_dashboard.php" : "employee_dashboard.php"));
            exit();
        } else {
            echo "<script>alert('Invalid email or password!');</script>";
        }
    } else {
        echo "<script>alert('Invalid email or password!');</script>";
    }

    $stmt->close();
}
ob_end_flush();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporting</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .dashboard-heading {
            background: #00308F; 
            color: #ffffff; /* White text */
            text-align: center;
            padding: 15px 0px;
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

        /* Centering and styling the form container */
        .login-container {
            max-width: 90%;  /* Adjust to 90% for mobile */
            width: 400px;    /* Maximum width of 400px */
            margin: 5% auto;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        /* Adjusting form padding and text on mobile */
        @media (max-width: 576px) {
            .login-container {
                padding: 15px;
                width: 90%;  /* Ensure the form fills most of the mobile screen */
            }

            .dashboard-heading h1 {
                font-size: 24px;  /* Adjust the heading size on mobile */
            }
        }
    </style>
</head>
<body style="background-color: #f1f3f5;">
        <!-- Header styled like the Admin Dashboard -->
    <div class="dashboard-heading">
      <h1>DAILY REPORTING SYSTEM</h1>
    </div>
    <div class="subheading">ARD PERFUMES FACTORY LLC.</div>
    <div class="container-fluid">
        <!-- Centering the form below the header -->
        <div class="d-flex justify-content-center">
            <div class="login-container">
                <h2 align="center">Login</h2>
                <form method="POST" action="" class="mt-4">
                    <input type="email" name="email" placeholder="Email" class="form-control mb-3" required>
                    <input type="password" name="password" placeholder="Password" class="form-control mb-3" required>
                     <a href="forgot_password.php" class="text-primary">Forgot Password?</a>
                    <select name="role" class="form-control mb-3" required>
                        <option value="">--Select Role--</option>
                        <option value="employee">Employee</option>
                        <option value="admin">Admin</option>
                    </select>
                    <button type="submit" class="btn btn-success w-100">Login</button>
                </form>
            </div>
        </div>
    </div>
    <?php include 'footer.php'; ?>

</body>
</html>



