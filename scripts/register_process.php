<?php
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
$registrationSuccess = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $mobile = $_POST['mobile'];
    $address = $_POST['address'];
    $postal_code = $_POST['postal_code'];
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
            $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);

            // Concatenate the profile picture name with its extension
            $profile_picture_with_extension = $profile_picture . '.' . $file_extension;

            // Move the uploaded file to the desired location
            $upload_directory = '../uploads/profile_pictures/';
            $file_path = $upload_directory . $profile_picture_with_extension;

            if (!move_uploaded_file($file_tmp, $file_path)) {
                $message = "Failed to upload profile picture.";
                $messageType = "error";
            }
        } else {
            $message = "Profile picture is required.";
            $messageType = "error";
        }

        if (!$message) {
            // Fetch city from postal code using external API (CTT - Portuguese postal service)
            $city = fetchCityFromPostalCode($postal_code); // Implement this function

            // Prepare and execute SQL query to insert user data
            $stmt = $conn->prepare("INSERT INTO users (fullname, email, phone, mobile, address, postal_code, city, password, profile_picture) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssssss", $fullname, $email, $phone, $mobile, $address, $postal_code, $city, $password, $profile_picture_with_extension);

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

function fetchCityFromPostalCode($postalCode) {
    $apiKey = '88d029d3e62c46f7b5e8271a2dc38322'; // Replace with your actual API key
    $postalCodeParts = explode('-', $postalCode);
    if (count($postalCodeParts) === 2) {
        $cp4 = $postalCodeParts[0];
        $cp3 = $postalCodeParts[1];
        $apiUrl = "https://www.cttcodigopostal.pt/api/v1/{$apiKey}/{$cp4}-{$cp3}";

        $response = file_get_contents($apiUrl);
        $data = json_decode($response, true);

        if (!empty($data) && isset($data[0]['localidade'])) {
            return $data[0]['localidade'];
        } else {
            return 'Unknown'; // Default value if city not found
        }
    } else {
        return 'Invalid Postal Code';
    }
}
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
                            window.location.href = '../pages/register.html';
                        }
                    }, 30); // Adjust the speed of the progress bar here
                <?php endif; ?>
            }
        });
    </script>
</body>
</html>
