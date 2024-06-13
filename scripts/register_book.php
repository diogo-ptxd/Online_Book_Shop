<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to the login page
    header("Location: ../pages/login.html");
    exit;
}

// Database connection parameters
$host = 'localhost';
$db = 'bookwise';
$user = 'book_write';
$pass = 'password';

// Connect to the database
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and retrieve form data
    $title = $conn->real_escape_string(trim($_POST['title']));
    $author = $conn->real_escape_string(trim($_POST['author']));
    $price = $conn->real_escape_string(trim($_POST['price']));
    $description = $conn->real_escape_string(trim($_POST['description']));
    $cover_image = null;
    $genre = $conn->real_escape_string(trim($_POST['genre']));
    // check desc
    if (strlen($description) > 255) {
        echo "Description must be less than or equal to 255 characters.";
        exit;
    }

    // Handle file upload
    if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $file_name = $_FILES['cover_image']['name'];
        $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
        $file_size = $_FILES['cover_image']['size'];
        $file_tmp = $_FILES['cover_image']['tmp_name'];

        if (in_array($file_ext, $allowed) && $file_size <= 31457280) { // 30MB limit
            $new_filename = uniqid('', true) . "." . $file_ext;
            $upload_dir = "../uploads/book_covers/";
            
            // Ensure the upload directory exists
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $file_path = $upload_dir . $new_filename;

            if (move_uploaded_file($file_tmp, $file_path)) {
                $cover_image = $new_filename;
            } else {
                echo "Failed to upload file.";
                exit;
            }
        } else {
            echo "Invalid file type or file size too large.";
            exit;
        }
    }

    // Insert book data into the database
    $sql = "INSERT INTO books (title, author, price, description, cover_image, genre, user_id) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssssssi', $title, $author, $price, $description, $cover_image, $genre, $_SESSION['user_id']);

    if ($stmt->execute()) {
        echo "Book registered successfully!";
    } else {
        echo "Error registering book: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
