<?php

session_start(); // Include the session handler


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

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: ../pages/login.html");
    exit;
}

// Fetch user data from the database
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // User data found, fetch and display
    $row = $result->fetch_assoc();
    $username = htmlspecialchars($row['fullname']); // Sanitize output
    $email = htmlspecialchars($row['email']);
    $phoneNumber = htmlspecialchars($row['phone']);
    $mobilePhoneNumber = htmlspecialchars($row['mobile']);
    $location = htmlspecialchars($row['address']);
    $profile_picture = htmlspecialchars($row['profile_picture']);

    // Fetch job field if available
    if (isset($row['job'])) {
        $job = htmlspecialchars($row['job']);
    } else {
        $job = ''; // Default value if job is not set
    }
} else {
    // User data not found
    echo "User data not found.";
    exit;
}

$stmt->close();
$conn->close();

// Determine profile image path and check if github profile pic was attributed
if (!empty($profile_picture)) {
    $search_for = "github";
    if (str_contains($profile_picture, $search_for) !== false && $search_for !== '') {
        $profile_image_path = $profile_picture;
    } else {
        $profile_image_path = '../uploads/profile_pictures/' . $profile_picture;
    }
} else {
    // Default image if profile picture is not set
    $profile_image_path = 'https://bootdey.com/img/Content/avatar/avatar7.png';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css'>

    <link rel="stylesheet" href="../assets/css/profile.css">
    <link rel="icon" type="image/x-icon" href="../assets/images/bookwise_logo.png">
    <script src='https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.bundle.min.js'></script>

    <title>BookWise - Profile</title>
</head>

<body>
    <div class="container">
        <div class="main-body">

            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="main-breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">User Profile</li>
                </ol>
            </nav>
            <!-- /Breadcrumb -->

            <div class="row gutters-sm">
                <div class="col-md-4 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex flex-column align-items-center text-center">
                                <img src="<?php echo htmlspecialchars($profile_image_path); ?>" alt="Profile Picture" class="rounded-circle" width="150">
                                <div class="mt-3">
                                    <h4><?php echo $username; ?></h4>
                                    <!-- Display other user information -->
                                    <p class="text-secondary mb-1"><?php echo $job; ?></p>
                                    <p class="text-muted font-size-sm"><?php echo $location; ?></p>
                                    <button class="btn btn-primary">Follow</button>
                                    <button class="btn btn-outline-primary">Message</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mt-3">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                <h6 class="mb-0"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-github mr-2 icon-inline">
                                        <path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22">
                                        </path>
                                    </svg>Github</h6>
                                <span class="text-secondary"><a href="https://github.com/diogo-ptxd" target="_blank">https://github.com/diogo-ptxd</a></span>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-3">
                                    <h6 class="mb-0">Full Name</h6>
                                </div>
                                <div class="col-sm-9 text-secondary">
                                    <?php echo $username; ?>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-3">
                                    <h6 class="mb-0">Email</h6>
                                </div>
                                <div class="col-sm-9 text-secondary">
                                    <?php echo $email; ?>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-3">
                                    <h6 class="mb-0">Phone</h6>
                                </div>
                                <div class="col-sm-9 text-secondary">
                                    <?php echo $phoneNumber; ?>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-3">
                                    <h6 class="mb-0">Mobile</h6>
                                </div>
                                <div class="col-sm-9 text-secondary">
                                    <?php echo $mobilePhoneNumber; ?>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-3">
                                    <h6 class="mb-0">Address</h6>
                                </div>
                                <div class="col-sm-9 text-secondary">
                                    <?php echo $location; ?>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-12">
                                    <a class="btn btn-info " target="__blank" href="./editprofiledata.php">Edit</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--  Book Status  -->
                    <div class="row gutters-sm">
                        <div class="col-sm-6 mb-3">
                            <div class="card h-100">

                                <!-- $host = 'localhost';
                                    $db = 'bookwise';
                                    $user = 'book_read';
                                    $pass = 'password';

                                    // Connect to the database
                                    $conn = new mysqli($host, $user, $pass, $db);
                                    if ($conn->connect_error) {
                                        die("Connection failed: " . $conn->connect_error);
                                    }
                                        -->
                                <div class="card-body">
                                    <h6 class="d-flex align-items-center mb-3"><i class="material-icons text-info mr-2">Status</i>Currently Selling</h6>
                                    <div class="row">
                                        <?php
                                        $host = 'localhost';
                                        $db = 'bookwise';
                                        $user = 'book_read';
                                        $pass = 'password';

                                        // Connect to the database
                                        $conn = new mysqli($host, $user, $pass, $db);
                                        if ($conn->connect_error) {
                                            die("Connection failed: " . $conn->connect_error);
                                        }                                        // Ensure $user_id is set from session

                                        // Query to fetch books associated with the current user
                                        $sql = "SELECT * FROM books WHERE user_id = ?";
                                        $stmt = $conn->prepare($sql);
                                        $stmt->bind_param("i", $user_id);
                                        $stmt->execute();
                                        $result = $stmt->get_result();

                                        if ($result->num_rows > 0) {
                                            while ($row = $result->fetch_assoc()) {
                                                // Display each book in the specified format
                                                echo '<div class="col-md-3 mb-3" style="flex: 100%; max-width: 100%;">'; // Ensure each card is within a column and add margin for spacing
                                                echo '<div class="card h-100">';
                                                echo '<img src="../uploads/book_covers/' . htmlspecialchars($row['cover_image']) . '" class="card-img-top" alt="' . htmlspecialchars($row['title']) . '" style="padding: 20px 20px 5px 20px; width: 100%;object-fit:scale-down; height: auto; max-height: 200px;">';
                                                echo '<div class="card-body">';
                                                echo '<h5 class="card-title">' . htmlspecialchars($row['title']) . '</h5>';
                                                echo '<p class="card-text">' . htmlspecialchars($row['price']) . '€</p>';
                                                echo '</div>';
                                                echo '</div>';
                                                echo '</div>';
                                            }
                                        } else {
                                            echo '<div class="col-md-12">'; // Display a message across the entire width if no books are found
                                            echo '<p>No books currently listed for sale.</p>';
                                            echo '</div>';
                                        }

                                        $stmt->close();
                                        ?>
                                    </div>
                                </div>




                            </div>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h6 class="d-flex align-items-center mb-3"><i class="material-icons text-info mr-2">Status</i>Sold Books</h6>
                                    <!-- Placeholder content -->
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Book Status End -->
                </div>
            </div>
        </div>

    </div>
</body>

</html>