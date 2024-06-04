<?php
// Database connection parameters
$host = 'localhost';
$db = 'bookwise';
$user = 'user_read';
$pass = 'password';

// Connect to the database
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch the profile picture's unique identifier for the user with ID 7
$user_id = 8;
$sql = "SELECT profile_picture FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($profile_picture);
$stmt->fetch();

$stmt->close();
$conn->close();

// Display the image if a profile picture exists
if ($profile_picture) {
    $image_path = '../uploads/profile_pictures/' . $profile_picture . '.png'; // Corrected file extension to PNG
    if (file_exists($image_path)) {
        echo '<img src="' . $image_path . '" alt="Profile Picture">';
    } else {
        echo 'Profile picture not found.';
    }
} else {
    echo 'No profile picture found for user with ID 7.';
}
?>
