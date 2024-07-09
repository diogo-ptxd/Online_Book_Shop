<?php
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

// Include Composer autoload
require_once '../../vendor/autoload.php';

// Load configuration (ensure this loads correctly based on your setup)
$config = parse_ini_file(__DIR__ . '/../config.ini', true);

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

// Function to fetch city from postal code (mock implementation)
function fetchCityFromPostalCode($postalCode)
{
    // Mock implementation, replace with actual API call or database lookup
    return 'Mock City'; // Replace with actual logic
}

// OAuth handler for GitHub
session_start();

if (isset($_GET['code'])) {
    $code = $_GET['code'];
    $clientId = 'Ov23liDHaRn478Na9MQc'; // Replace with your GitHub Client ID
    $clientSecret = $config['github_oauth']['GITHUB_CLIENT_SECRET'];
    $redirectUri = 'http://127.0.0.1/Online_Book_Shop-master/scripts/github/callback.php';

    // Use GuzzleHttp to make HTTP requests
    $client = new Client();
    try {
        // Exchange code for access token
        $response = $client->post('https://github.com/login/oauth/access_token', [
            'form_params' => [
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'code' => $code,
                'redirect_uri' => $redirectUri
            ],
            'headers' => [
                'Accept' => 'application/json'
            ]
        ]);

        if ($response->getStatusCode() == 200) {
            $responseData = json_decode($response->getBody()->getContents(), true);
            $accessToken = $responseData['access_token'];

            // Fetch user data from GitHub
            $githubClient = new Client();
            $response = $githubClient->get('https://api.github.com/user', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Accept' => 'application/json'
                ]
            ]);

            if ($response->getStatusCode() == 200) {
                $userData = json_decode($response->getBody()->getContents(), true);

                // Extract GitHub user data
                $githubId = $userData['id'];
                $fullname = isset($userData['login']) ? $userData['login'] : 'Unknown'; // GitHub full name, handle if not provided
                $email = isset($userData['email']) ? $userData['email'] : "Unknown"; // GitHub email, handle if not provided
                $address = isset($userData['location']) ? $userData['location'] : 'Unknown'; // GitHub location, with fallback to 'Unknown'
                $profilePicture = $userData['avatar_url']; // GitHub profile picture URL

                // Check if the GitHub user already exists in your database
                $stmt = $conn->prepare('SELECT user_id FROM users WHERE github_id = ?');
                $stmt->bind_param('i', $githubId);
                $stmt->execute();
                $stmt->store_result();
                $userExists = $stmt->num_rows > 0;

                if ($userExists) {
                    // User already exists, update information if needed
                    $message = 'GitHub user already registered.';
                    $messageType = 'error';
                } else {
                    // Insert user data into the database
                    $stmt = $conn->prepare("INSERT INTO users (fullname, email, address, profile_picture, github_id) VALUES (?, ?, ?, ?, ?)");
                    $stmt->bind_param("ssssi", $fullname, $email, $address, $profilePicture, $githubId);

                    if ($stmt->execute()) {
                        $message = 'Registration successful!';
                        $messageType = 'success';
                    } else {
                        $message = 'Error inserting user data: ' . $stmt->error;
                        $messageType = 'error';
                    }
                }
            } else {
                $message = 'Error fetching user info from GitHub.';
                $messageType = 'error';
            }
        } else {
            $message = 'Error exchanging code for access token';
            $messageType = 'error';
        }
    } catch (RequestException $e) {
        $message = 'Error: ' . $e->getMessage();
        $messageType = 'error';
    }
} else {
    $message = 'Authorization code not found.';
    $messageType = 'error';
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GitHub OAuth Callback</title>
    <!-- Include any styling or Bootstrap CSS here -->
</head>
<body>
    <div>
        <?php if (isset($message)): ?>
            <p><?php echo $message; ?></p>
        <?php endif; ?>
    </div>
</body>
</html>
