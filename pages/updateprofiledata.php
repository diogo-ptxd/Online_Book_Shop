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

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = 1; // Example user ID, change as needed
    $fullname = $conn->real_escape_string(trim($_POST['fullname']));
    $email = $conn->real_escape_string(trim($_POST['email']));
    $phone = $conn->real_escape_string(trim($_POST['phone']));
    $mobile = $conn->real_escape_string(trim($_POST['mobile']));
    $address = $conn->real_escape_string(trim($_POST['address']));
    $profile_picture = null;

    // Handle file upload
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $file_name = $_FILES['profile_picture']['name'];
        $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
        $file_size = $_FILES['profile_picture']['size'];
        $file_tmp = $_FILES['profile_picture']['tmp_name'];

        if (in_array($file_ext, $allowed) && $file_size <= 31457280) { // 30MB limit
            $new_filename = uniqid('', true) . "." . $file_ext;
            $file_path = "../uploads/profile_pictures/" . $new_filename;

            if (move_uploaded_file($file_tmp, $file_path)) {
                $profile_picture = $new_filename;
            } else {
                echo "Failed to upload file.";
                exit;
            }
        } else {
            echo "Invalid file type or file size too large.";
            exit;
        }
    }

    // Update the user's data
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
        echo "Profile updated successfully!";
    } else {
        echo "Error updating profile: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();

