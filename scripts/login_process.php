<?php
// Database connection parameters
$host = 'localhost';
$db = 'bookwise';
$user = 'user_read';
$pass = 'password';

// Start the session
session_start();

// Connect to the database
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize message and type
$message = '';
$messageType = '';
$registrationSuccess = false;

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $conn->real_escape_string(trim($_POST['email']));
    $password = trim($_POST['password']);

    // Fetch the user's hashed password from the database
    $sql = "SELECT user_id, password FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($user_id, $hashed_password);
    $stmt->fetch();

    if ($stmt->num_rows > 0 && password_verify($password, $hashed_password)) {
        // Password is correct, generate a session token
        $token = bin2hex(random_bytes(16));
        $_SESSION['token'] = $token;
        $_SESSION['user_id'] = $user_id;
        $_SESSION['token_expiry'] = time() + 3600; // Token expires in 1 hour

        // Successful login
        $message = "Login successful!";
        $messageType = "success";
        $registrationSuccess = true;
    } else {
        // Invalid credentials
        $message = "Invalid email or password.";
        $messageType = "error";
    }


    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/entrypages.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.0/dist/css/bootstrap.min.css">
    <title>Notification</title>
    <style>
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            width: 300px;
            padding: 15px;
            border-radius: 5px;
            color: #fff;
            display: none;
        }
        .notification.success {
            background-color: #28a745;
        }
        .notification.error {
            background-color: #dc3545;
        }
        .notification .progress {
            margin-top: 10px;
            height: 5px;
            background-color: rgba(255, 255, 255, 0.4);
        }
        .notification .progress-bar {
            background-color: #fff;
        }
        body {
  background-color: #a1c4fd;
  min-height: 100%;
}
html {

  min-height: 100%;
}

    </style>
</head>
<body>
    <div class="notification <?php echo $messageType; ?>" id="notification">
        <?php echo $message; ?>
        <div class="progress">
            <div class="progress-bar" id="progress-bar" role="progressbar" style="width: 100%;"></div>
        </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const notification = document.getElementById('notification');
            const progressBar = document.getElementById('progress-bar');

            if (notification.innerText.trim() !== '') {
                notification.style.display = 'block';

                <?php if ($registrationSuccess): ?>
                                    let width = 100;
                                    const interval = setInterval(function() {
                                        width -= 1;
                                        progressBar.style.width = width + '%';
                                        if (width <= 0) {
                                            clearInterval(interval);
                                            window.location.href = '../index.html';
                                        }
                                    }, 30); // Adjust the speed of the progress bar here
                <?php else: ?>
                                    let width = 100;
                                    const interval = setInterval(function() {
                                        width -= 1;
                                        progressBar.style.width = width + '%';
                                        if (width <= 0) {
                                            clearInterval(interval);
                                            notification.style.display = 'none';
                                            window.location.href = "../pages/login.html";
                                        }
                                    }, 30); // Adjust the speed of the progress bar here
                <?php endif; ?>
            }
        });
    </script>
</body>
</html>
