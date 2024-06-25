<?php
require_once 'vendor/autoload.php'; // Assuming you have Composer's autoload file

use Google\Client as Google_Client;

// Database connection parameters
$host = 'localhost';
$db = 'bookwise';
$user = 'user_write';
$pass = 'password';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = '';
$success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = json_decode(file_get_contents('php://input'), true);
    $credential = $data['credential'];

    $client = new Google_Client(['client_id' => '187707263768-789qph62c3arfn64rpjhu30fabmcl6ct.apps.googleusercontent.com']);
    $payload = $client->verifyIdToken($credential);

    if ($payload) {
        $google_id = $payload['sub'];
        $fullname = $payload['name'];
        $email = $payload['email'];
        $profile_picture = $payload['picture'];

        // Check if the user already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows == 0) {
            // Insert new user
            $stmt->close();
            $stmt = $conn->prepare("INSERT INTO users (fullname, email, google_id, profile_picture) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $fullname, $email, $google_id, $profile_picture);
            
            if ($stmt->execute()) {
                $success = true;
            } else {
                $message = "Error: " . $stmt->error;
            }
        } else {
            $message = "User already exists.";
        }
        
        $stmt->close();
    } else {
        $message = "Invalid Google ID token.";
    }
}

$conn->close();

echo json_encode(['success' => $success, 'message' => $message]);
?>
