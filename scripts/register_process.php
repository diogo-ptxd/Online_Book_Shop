<?php
// Database connection parameters
$host = 'localhost';
$db = 'bookwise';
$user = 'user_write';
$pass = 'password';

// Connect to the database
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize message and type
$message = '';
$messageType = '';
$registrationSuccess = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $mobile = $_POST['mobile'];
    $address = $_POST['address'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate password
    if (empty($fullname) || empty($email) || empty($address) || empty($password) || empty($confirm_password)) {
        $message = "All fields except phone and mobile are required.";
        $messageType = "error";
    } elseif ($password !== $confirm_password) {
        $message = "Passwords do not match.";
        $messageType = "error";
    } else {
        $password = password_hash($password, PASSWORD_DEFAULT);

        // Handle file upload
        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
            $file_name = $_FILES['profile_picture']['name'];
            $file_tmp = $_FILES['profile_picture']['tmp_name'];

            // Generate a unique identifier for the profile picture
            $profile_picture = uniqid('', true);

            // Move the uploaded file to the desired location
            $upload_directory = '../uploads/profile_pictures/';
            $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);
            $file_path = $upload_directory . $profile_picture . '.' . $file_extension;

            if (!move_uploaded_file($file_tmp, $file_path)) {
                $message = "Failed to upload profile picture.";
                $messageType = "error";
            }
        } else {
            $message = "Profile picture is required.";
            $messageType = "error";
        }

        if (!$message) {
            // Prepare and execute SQL query to insert user data
            $stmt = $conn->prepare("INSERT INTO users (fullname, email, phone, mobile, address, password, profile_picture) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssss", $fullname, $email, $phone, $mobile, $address, $password, $profile_picture);

            if ($stmt->execute()) {
                $message = "Registration successful!";
                $messageType = "success";
                $registrationSuccess = true;
            } else {
                $message = "Error: " . $stmt->error;
                $messageType = "error";
            }

            $stmt->close();
        }
    }
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
                                        window.location.href = '../pages/login.html';
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
                                        window.location.href= "../pages/register.html"
                                    }
                                }, 30); // Adjust the speed of the progress bar here
                <?php endif; ?>
            }
        });
    </script>
</body>
</html>
