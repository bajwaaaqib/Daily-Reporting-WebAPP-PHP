<?php
session_start();

// Variable to store messages
$message = "";

// Check if the token is provided in the URL
if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Check if token exists in the session
    if (isset($_SESSION['reset_token']) && $_SESSION['reset_token'] === $token) {
        // Check if the token has expired
        $expiry_time = $_SESSION['reset_token_expiry'];
        if ($expiry_time > time()) {
            // Token is valid and not expired

            // Process the password reset form submission
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                // Check if password is provided and matches the confirmation
                if (!empty($_POST['password']) && !empty($_POST['confirm_password']) && $_POST['password'] === $_POST['confirm_password']) {
                    // Hash the new password (Ensure it is hashed correctly)
                    $new_password = password_hash($_POST['password'], PASSWORD_BCRYPT);

                    // Database connection
                    require 'db.php'; // Your database connection file
                    $email = $_SESSION['reset_email'] ?? null; // Get email from session
                    
                    // Debugging: Ensure that the email is correctly set
                    if (!$email) {
                        $message = "<div class='alert alert-danger'>Session email is not set. Something went wrong.</div>";
                    } else {
                        // Prepare SQL query to update the password
                        if ($stmt = $conn->prepare("UPDATE employee SET password = ? WHERE email = ?")) {
                            $stmt->bind_param("ss", $new_password, $email);

                            // Debugging: Check if query executes successfully
                            if (!$stmt->execute()) {
                                $message = "<div class='alert alert-danger'>Error executing query: " . $stmt->error . "</div>";
                            } else {
                                // Clear session variables for security
                                unset($_SESSION['reset_token']);
                                unset($_SESSION['reset_token_expiry']);
                                unset($_SESSION['reset_email']);

                                // Success message
                                $message = "<div class='alert alert-success'>Your password has been updated. You can now <a href='index.php'>login</a>.</div>";
                            }

                            // Close the statement
                            $stmt->close();
                        } else {
                            $message = "<div class='alert alert-danger'>Error preparing the query: " . $conn->error . "</div>";
                        }
                    }
                } else {
                    $message = "<div class='alert alert-danger'>Passwords do not match or are empty. Please try again.</div>";
                }
            }
        } else {
            // Token has expired
            $message = "<div class='alert alert-danger'>This password reset link has expired.</div>";
        }
    } else {
        // Token doesn't match or isn't valid
        $message = "<div class='alert alert-danger'>Invalid token.Use Same Broser</div>";
    }
} else {
    $message = "<div class='alert alert-danger'>No token provided.</div>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset</title>
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
            <h2>Reset Your Password</h2>

            <form method="POST">
                <!-- New Password Field -->
                <div class="mb-3">
                    <label for="password" class="form-label">Enter New Password:</label>
                    <input type="password" name="password" id="password" class="form-control" required>
                </div>

                <!-- Confirm Password Field -->
                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Confirm New Password:</label>
                    <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
            <!-- Display Message -->
            <?php if (!empty($message)) echo $message; ?>
        </div>
    </div>

    <script>
        // JavaScript to check if both passwords match
        document.querySelector("form").addEventListener("submit", function(event) {
            const password = document.getElementById("password").value;
            const confirmPassword = document.getElementById("confirm_password").value;

            if (password !== confirmPassword) {
                alert("Passwords do not match.");
                event.preventDefault(); // Prevent form submission
            }
        });
    </script>

</body>
</html>
