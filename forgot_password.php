<?php
session_start();
require 'db.php'; // Database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);

    // Validate email input
    if (empty($email)) {
        echo "<div class='alert alert-danger'>Please enter your email.</div>";
    } else {
        // Check if email exists in the employee table
        $stmt = $conn->prepare("SELECT * FROM employee WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            // Generate a secure reset token
            $token = bin2hex(random_bytes(32));
            $expiry_time = time() + 3600; // Token expires in 1 hour

            // Store the token in session
            $_SESSION['reset_token'] = $token;
            $_SESSION['reset_token_expiry'] = $expiry_time;
            $_SESSION['reset_email'] = $email;

            // Create password reset link
            $reset_link = "https://ardperfumes.com/reporting/reset_password.php?token=$token";

            // Email details
            $subject = "Password Reset Request";
            $message = "
                <html>
                <head>
                    <title>Password Reset Request</title>
                </head>
                <body>
                    <p>Dear User,</p>
                    <p>You have requested to reset your password for ARD PERFUMES REPORTING SYSTEM</p>
                    <p>Click the link below to reset your password:</p>
                    <p><a href='$reset_link'>$reset_link</a></p>
                    <p>If you did not request this, please ignore this email.</p>
                    <p>Regards,<br>ARD PERFUMES Support Team</p>
                </body>
                </html>
            ";

            $headers = "From: no-reply@ardperfumes.com\r\n";
            $headers .= "Reply-To: no-reply@ardperfumes.com\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

            // Send the email
            if (mail($email, $subject, $message, $headers)) {
                echo "<div class='alert alert-success'>A password reset link has been sent to your email.</div>";
            } else {
                echo "<div class='alert alert-danger'>Failed to send email. Please try again later.</div>";
            }
        } else {
            echo "<div class='alert alert-danger'>Email not found in our records.</div>";
        }
        $stmt->close();
    }
}
?>





<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }
        
        .container {
            max-width: 500px;
            margin-top: 50px;
        }
        
        .form-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        .form-container h2 {
            margin-bottom: 20px;
            text-align: center;
        }
        
        .form-label {
            font-weight: bold;
        }
        
        .btn-primary {
            width: 100%;
            padding: 10px;
            font-size: 16px;
        }

        @media (max-width: 576px) {
            .container {
                margin-top: 20px;
                padding: 15px;
            }
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="form-container">
            <h2>Forgot Password</h2>
            
            <form method="POST">
                <!-- Email Address Field -->
                <div class="mb-3">
                    <label for="email" class="form-label">Enter your email address:</label>
                    <input type="email" name="email" id="email" class="form-control" required>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-primary">Send Reset Link</button>
            </form>
                  <!-- Display success or error messages -->
            <?php
            // Check if a message exists in the session
            if (isset($_SESSION['message'])) {
                $message_type = $_SESSION['message_type'];
                $message = $_SESSION['message'];
                $alert_class = $message_type == 'success' ? 'alert-success' : 'alert-danger';
                echo "<div class='alert $alert_class' role='alert'>$message</div>";
                
                // Clear the message after displaying
                unset($_SESSION['message']);
                unset($_SESSION['message_type']);
            }
            ?>
        </div>
    </div>

</body>
</html>