<?php

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

require_once '../../vendor/autoload.php';

$config = parse_ini_file(__DIR__ . '/../config.ini', true);

$host = $config["database_user_read"]["hostname"];
$db = $config["database_user_read"]["database"];
$user = $config["database_user_read"]["username"];
$pass = $config["database_user_read"]["password"];

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

session_start();

if (isset($_GET['code'])) {
    $code = $_GET['code'];
    $clientId = $config['github_oauth_login']['GITHUB_CLIENT_ID'];
    $clientSecret = $config['github_oauth_login']['GITHUB_CLIENT_SECRET'];
    $redirectUri = 'http://127.0.0.1/Online_Book_Shop-master/scripts/github/login_callback.php';

    $client = new Client();

    try {
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

            $githubClient = new Client();
            $response = $githubClient->get('https://api.github.com/user', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Accept' => 'application/json'
                ]
            ]);

            var_dump($accessToken);

            if ($response->getStatusCode() == 200) {
                $userData = json_decode($response->getBody()->getContents(), true);

                $githubId = $userData['id'];
                $fullname = isset($userData['login']) ? $userData['login'] : 'Unknown';
                $email = isset($userData['email']) ? $userData['email'] : "Unknown";
                $address = isset($userData['location']) ? $userData['location'] : 'Unknown';
                $profilePicture = $userData['avatar_url'];
                $token = bin2hex(random_bytes(16));
                $stmt = $conn->prepare('SELECT * FROM users WHERE github_id = ?');
                $stmt->bind_param('i', $githubId);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows == 1) {
                    $user = $result->fetch_assoc();

                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['fullname'] = $user['fullname'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['address'] = $user['address'];
                    $_SESSION['profile_picture'] = $user['profile_picture'];

                    // Generate a session token and expiry
                    $_SESSION['token'] = bin2hex(random_bytes(16));
                    $_SESSION['token_expiry'] = time() + 3600;

                    // Redirect to a protected page or user dashboard
                    header('Location: ../../index.php');
                    exit();
                } else {
                    $stmt = $conn->prepare("INSERT INTO users (fullname, email, address, profile_picture, github_id) VALUES (?, ?, ?, ?, ?)");
                    $stmt->bind_param("ssssi", $fullname, $email, $address, $profilePicture, $githubId);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $user = $result->fetch_assoc();

                    var_dump($user);

                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['fullname'] = $user['fullname'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['address'] = $user['address'];
                    $_SESSION['profile_picture'] = $user['profile_picture'];

                    // Generate a session token and expiry
                    $_SESSION['token'] = bin2hex(random_bytes(16));
                    $_SESSION['token_expiry'] = time() + 3600;

                    header('Location: ../../index.php');
                    exit();
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
    <title>GitHub OAuth Login Callback</title>
</head>

<body>
    <div>
        <?php if (isset($message)) : ?>
            <p><?php echo $message; ?></p>
        <?php endif; ?>
    </div>
</body>

</html>