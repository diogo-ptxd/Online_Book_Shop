<?php
session_start();
// include_once "../scripts/session_handler.php";
// $config = parse_ini_file(__DIR__ . '/../scripts/config.ini', true);

if (!isset($_SESSION['token']) || !isset($_SESSION['token_expiry']) || !isset($_SESSION['user_id'])) {
    header("Location: pages\login.html");
    exit();
}

// Database connection parameters
$host = 'localhost';
$db = 'bookwise';
$user = 'user_write';
$pass = 'password';

// Connect to the database
                    // Connect to the database
                    $conn = new mysqli($host, $user, $pass, $db);
                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }
// Fetch books from the database
$sql = "SELECT * FROM books";
$result = $conn->query($sql);

// Fetch user data
$user_id = $_SESSION['user_id'];
$sql_user = "SELECT profile_picture FROM users WHERE user_id = ?";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
$profile_picture = '';

if ($result_user->num_rows > 0) {
    $row_user = $result_user->fetch_assoc();
    $profile_picture = $row_user['profile_picture'];
}

// Close the statement
$stmt_user->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Store</title>
    <link rel="icon" type="image/x-icon" href="http://127.0.0.1/Online_Book_Shop-master/assets/images/bookwise_logo.png">
    <link rel="stylesheet" href="http://127.0.0.1/Online_Book_Shop-master/assets/css/main.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        .cart-popup {
            position: absolute;
            top: 50px;
            right: 20px;
            width: 300px;
            background-color: white;
            border: 1px solid #ddd;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            display: none;
        }

        .cart-content {
            padding: 20px;
        }

        .cart-content h3 {
            margin-top: 0;
        }

        .header_top {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .item_top {
            margin-right: 10px;
        }

        .filter-wrap {
            display: none;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out;
        }

        .filter-wrap.show {
            display: block;
            max-height: 100%;
        }

        .hero-section {
            background-color: #f8f9fa;
            padding: 2rem;
            text-align: center;
        }

        .hero-section h1 {
            margin-bottom: 1rem;
        }

        .hero-section p {
            margin-bottom: 2rem;
        }

        .card img {
            max-height: 250px;
            object-fit: cover;
        }

        .price {
            text-decoration: line-through;
            color: gray;
        }

        .new-price {
            font-weight: bold;
            color: #d9534f;
        }

        .news-section .card img {
            max-height: 150px;
            object-fit: cover;
        }

        .footer {
            background-color: #f8f9fa;
            padding: 2rem 0;
            text-align: center;
        }

        .footer a {
            color: #000;
            margin: 0 10px;
        }

        .notification {
            position: fixed;
            top: 10px;
            right: 45%;
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
    </style>
</head>
<body>
    <!-- Header Section -->
    <nav class="navbar navbar-expand-lg navbar-light" style="background-color: #ffffff">
        <div class="container">
            <a class="navbar-brand" href="#">BookWise</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <div class="dropdown mr-auto">
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Menu
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <a class="dropdown-item" href="#">Action</a>
                        <a class="dropdown-item" href="#">Another action</a>
                        <a class="dropdown-item" href="#">Something else here</a>
                    </div>
                </div>
                <form class="form-inline mr-sm-2" action="search.php" method="GET">
                    <input class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search" name="query" style="max-width: 300px" />
                    <button class="btn btn-outline-success my-2 my-sm-0" type="submit"><i class="fas fa-search"></i></button>
                </form>
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fas fa-heart"></i></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <?php
                            if (str_contains($profile_picture, 'github')) {
                                echo '<div class="item_top"><a href="http://127.0.0.1/Online_Book_Shop-master/pages/profile.php"><img src="' . $profile_picture . '" alt="profile icon" style="max-width: 40px"></a></div>';
                            } else {
                                echo '<div class="item_top"><a href="http://127.0.0.1/Online_Book_Shop-master/pages/profile.php"><img src="../uploads/profile_pictures/' . $profile_picture . '" alt="profile icon" style="max-width: 40px"></a></div>';
                            }
                            ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="javascript:void(0);" onclick="toggleCart()">
                            <i class="fas fa-shopping-cart"></i>
                            <span id="cartItemCount" class="cart-item-count">0</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../Online_Book_Shop-master/pages/register_book.php" class="btn btn-primary" style="color:white;">Register Book</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="hero-section">
        <h1>New Releases This Week</h1>
        <p>
            It's time to update your reading list with some of the latest and
            greatest releases in the literary world. From heart-pumping thrillers to
            captivating memoirs, this week's new releases offer something for
            everyone.
        </p>
        <button class="btn btn-warning">Subscribe</button>
    </div>

    <div class="container mt-5">
        <h2 class="mb-4">Top Sellers</h2>
        <div class="form-group">
            <select class="form-control" id="genreSelect">
                <option>Choose a genre</option>
                <!-- Add genre options here -->
            </select>
        </div>
        <div class="row">
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="col-md-3">
                    <div class="card mb-4">
                        <img src="<?php echo $row['cover_image']; ?>" class="card-img-top" alt="Book Cover">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $row['title']; ?></h5>
                            <p class="card-text"><?php echo $row['author']; ?></p>
                            <p class="card-text">
                                <span class="price">$<?php echo $row['original_price']; ?></span>
                                <span class="new-price">$<?php echo $row['discounted_price']; ?></span>
                            </p>
                            <button class="btn btn-primary" onclick="addToCart('<?php echo $row['id']; ?>', '<?php echo $row['title']; ?>', '<?php echo $row['discounted_price']; ?>')">Add to Cart</button>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <div class="container mt-5 news-section">
        <h2 class="mb-4">News</h2>
        <div class="row">
            <!-- News items here -->
            <!-- Repeat the above block for each news item -->
        </div>
    </div>

    <footer class="footer mt-5">
        <div class="container">
            <p>&copy; 2023 Your Book Store. All rights reserved.</p>
            <div>
                <a href="#">Privacy Policy</a>
                <a href="#">Terms of Service</a>
                <a href="#">Contact Us</a>
            </div>
        </div>
    </footer>

    <!-- Cart Popup -->
    <div class="cart-popup" id="cartPopup">
        <div class="cart-content">
            <h3>Shopping Cart</h3>
            <ul id="cartItems">
                <!-- Cart items will be dynamically added here -->
            </ul>
            <p>Total: $<span id="cartTotal">0.00</span></p>
            <a href="checkout.php" class="btn btn-primary">Checkout</a>
        </div>
    </div>

    <!-- Notification -->
    <div class="notification" id="notification">
        <span id="notificationMessage"></span>
        <div class="progress">
            <div class="progress-bar" id="notificationProgressBar" style="width: 0;"></div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#genreSelect').change(function () {
                var selectedGenre = $(this).val();
                // Implement filtering logic based on selectedGenre
                console.log('Selected genre:', selectedGenre);
            });
        });

        function toggleCart() {
            var cartPopup = document.getElementById('cartPopup');
            if (cartPopup.style.display === 'none' || cartPopup.style.display === '') {
                cartPopup.style.display = 'block';
            } else {
                cartPopup.style.display = 'none';
            }
        }

        function addToCart(id, title, price) {
            var cartItems = document.getElementById('cartItems');
            var cartItemCount = document.getElementById('cartItemCount');
            var cartTotal = document.getElementById('cartTotal');
            var total = parseFloat(cartTotal.textContent);

            var li = document.createElement('li');
            li.textContent = title + ' - $' + price;
            cartItems.appendChild(li);

            total += parseFloat(price);
            cartTotal.textContent = total.toFixed(2);

            var itemCount = parseInt(cartItemCount.textContent);
            cartItemCount.textContent = itemCount + 1;

            showNotification('Added ' + title + ' to cart', 'success');
        }

        function showNotification(message, type) {
            var notification = document.getElementById('notification');
            var notificationMessage = document.getElementById('notificationMessage');
            var notificationProgressBar = document.getElementById('notificationProgressBar');

            notificationMessage.textContent = message;
            notification.className = 'notification ' + type;

            notification.style.display = 'block';
            notificationProgressBar.style.width = '100%';

            setTimeout(function () {
                notificationProgressBar.style.width = '0';
                notification.style.display = 'none';
            }, 3000);
        }
    </script>
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
