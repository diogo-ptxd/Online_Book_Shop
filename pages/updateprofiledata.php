<?php
include('../scripts/session_handler.php'); // Include the session handler

if (!is_session_valid()) {
    header("Location: login.html");
    exit();
}

$config = parse_ini_file(__DIR__ . '/../scripts/config.ini', true);

// Database connection parameters from the correct section
$host = $config["database_user_write"]["hostname"];
$db = $config["database_user_write"]["database"];
$user = $config["database_user_write"]["username"];
$pass = $config["database_user_write"]["password"];


// Connect to the database
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize message and type
$message = '';
$messageType = '';
$updateSuccess = false;

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id']; // Use user ID from the session
    $fullname = $conn->real_escape_string(trim($_POST['fullname']));
    $email = $conn->real_escape_string(trim($_POST['email']));
    $phone = $conn->real_escape_string(trim($_POST['phone']));
    $mobile = $conn->real_escape_string(trim($_POST['mobile']));
    $address = $conn->real_escape_string(trim($_POST['address']));
    $profile_picture = null;

    // Handle file upload
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == UPLOAD_ERR_OK) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $file_name = $_FILES['profile_picture']['name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $file_size = $_FILES['profile_picture']['size'];
        $file_tmp = $_FILES['profile_picture']['tmp_name'];

        if (in_array($file_ext, $allowed) && $file_size <= 31457280) { // 30MB limit
            $new_filename = uniqid('', true) . "." . $file_ext;
            $file_path = "../uploads/profile_pictures/" . $new_filename;

            if (move_uploaded_file($file_tmp, $file_path)) {
                $profile_picture = $new_filename;
            } else {
                $message = "Failed to upload file.";
                $messageType = "error";
            }
        } else {
            $message = "Invalid file type or file size too large.";
            $messageType = "error";
        }
    }

    // Update the user's data
    if (!$message) {
        $sql = "UPDATE users SET fullname=?, email=?, phone=?, mobile=?, address=?";
        if ($profile_picture) {
            $sql .= ", profile_picture=?";
        }
        $sql .= " WHERE user_id=?";

        $stmt = $conn->prepare($sql);
        if ($profile_picture) {
            $stmt->bind_param('ssssssi', $fullname, $email, $phone, $mobile, $address, $profile_picture, $user_id);
        } else {
            $stmt->bind_param('sssssi', $fullname, $email, $phone, $mobile, $address, $user_id);
        }

        if ($stmt->execute()) {
            $message = "Profile updated successfully!";
            $messageType = "success";
            $updateSuccess = true;
        } else {
            $message = "Error updating profile: " . $stmt->error;
            $messageType = "error";
        }

        $stmt->close();
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
            background-color: #4158d0;
            background-image: linear-gradient(43deg, #4158d0 0%, #c850c0 46%, #ffcc70 100%);
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

                <?php if ($updateSuccess): ?>
                let width = 100;
                const interval = setInterval(function() {
                    width -= 1;
                    progressBar.style.width = width + '%';
                    if (width <= 0) {
                        clearInterval(interval);
                        window.location.href = './profile.php'; // Redirect to profile page
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
                        window.location.href = './editprofile.php'; // Redirect back to edit profile page
                    }
                }, 30); // Adjust the speed of the progress bar here
                <?php endif; ?>
            }
        });
    </script>
</body>
</html>
