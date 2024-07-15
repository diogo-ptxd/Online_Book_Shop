<?php

session_start();
// include_once "../scripts/session_handler.php";
// $config = parse_ini_file(__DIR__ . '/../scripts/config.ini', true);

if (!isset($_SESSION['token']) || !isset($_SESSION['token_expiry']) || !isset($_SESSION['user_id'])) {
    header("Location: pages\login.html");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="http://127.0.0.1/Online_Book_Shop-master/assets/images/bookwise_logo.png">
    <link rel="stylesheet" href="http://127.0.0.1/Online_Book_Shop-master/assets/css/main.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <title>BookWise</title>
    <style>
        .cart-popup {
            position: absolute;
            top: 50px;
            /* Adjust as needed to position below cart icon */
            right: 20px;
            /* Align to the right */
            width: 300px;
            /* Adjust width as needed */
            background-color: white;
            border: 1px solid #ddd;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            display: none;
            /* Initially hidden */
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
            /* Initially hidden */
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out;
        }

        .filter-wrap.show {
            display: block;
            max-height: 100%;
        }
    </style>
</head>

<body>
    <!-- Header Section -->
    <div class="header_wrap">
        <!-- Header Top -->
        <div class="header_top">
            <!-- Logo -->
            <div class="item_top item">
                <a href="#">BookWise</a>
            </div>
            <!-- Address -->
            <div class="item_top item">
                <a href="#">
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

                    // Fetch user address
                    $user_id = $_SESSION['user_id']; // Use user ID from the session
                    $sql = "SELECT address FROM users WHERE user_id=?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param('i', $user_id);
                    $stmt->execute();
                    $stmt->bind_result($address);
                    $stmt->fetch();
                    $stmt->close();
                    $conn->close();

                    // Truncate address if longer than 20 characters
                    if (strlen($address) > 20) {
                        $address = substr($address, 0, 20) . '...';
                    }
                    echo htmlspecialchars($address);
                    ?>
                </a>
            </div>
            <!-- Search Placeholder -->
            <div class="item_top item">placeholder search</div>
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

            // Check if user is logged in
            if (!isset($_SESSION['user_id'])) {
                // Redirect to login page if not logged in
                header("Location: ../pages/login.html");
                exit;
            }

            // Fetch user data from the database
            $user_id = $_SESSION['user_id'];
            $sql = "SELECT profile_picture FROM users WHERE user_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $profile_picture = $row['profile_picture'];
                $search_for = 'github';
                if (str_contains($profile_picture, $search_for) !== false && $search_for !== '') {
                    echo '<div class="item_top"><a href="http://127.0.0.1/Online_Book_Shop-master/pages/profile.php"><img src="' . $profile_picture . '" alt="profile icon" style="max-width: 40px"></a></div>';
                } else {
                    echo '<div class="item_top"><a href="http://127.0.0.1/Online_Book_Shop-master/pages/profile.php"><img src="' . '../uploads/profile_pictures/' . $profile_picture . '" alt="profile icon" style="max-width: 40px"></a></div>';
                }
            } else {
                echo "No profile picture found.";
            }
            // Close the statement and connection
            $stmt->close();
            $conn->close();
            ?>

            <!-- Cart -->
            <div class="item_top">
                <a href="javascript:void(0);" onclick="toggleCart()">
                    <img src="http://127.0.0.1/Online_Book_Shop-master/assets/images/cart.png" alt="cart icon" style="max-width: 40px;">
                    <span id="cartItemCount" class="cart-item-count">0</span>
                </a>
            </div>
            <!-- Register Book Button -->
            <div class="item_top item">
                <a href="../Online_Book_Shop-master/pages/register_book.php" class="btn btn-primary" style="color:white;">Register Book</a>
            </div>
        </div>
        <!-- Header Bottom -->
        <div class="header_bottom">
            <!-- Filter Toggle Button -->
            <div class="item_bottom item">
                <button onclick="toggleFilters()" id="filterButton">
                    <i class="material-icons">menu</i>
                    <p>Filters</p>
                </button>
            </div>
            <!-- Placeholder Links -->
            <div class="item_bottom item"><a href="#">placeholder_text</a></div>
            <div class="item_bottom item"><a href="#">placeholder_text</a></div>
            <div class="item_bottom item"><a href="#">placeholder_text</a></div>
            <div class="item_bottom item"><a href="#">placeholder_text</a></div>
            <div class="item_bottom item"><a href="#">placeholder_text</a></div>
            <div class="item_bottom item"><a href="#">placeholder_text</a></div>
            <div class="item_bottom item"><a href="#">placeholder_text</a></div>
        </div>
    </div>
    <!-- Main Content Section -->
    <div class="maincontent">
        <!-- Filter Section -->
        <div class="filter-wrap">
            <div class="filter-content">
                <!-- Filter Title -->
                <div class="filter-title">
                    <p>Filters</p>
                </div>
                <!-- Filters Form -->
                <form method="GET" id="filterForm">
                    <!-- Author Filter -->
                    <div class="filter">
                        <button class="title" onclick="show(0)" type="button">Author</button>
                        <div class="options">
                            <input type="text" name="author" class="form-control" placeholder="Author Name">
                        </div>
                    </div>
                    <!-- Price Filter -->
                    <div class="filter">
                        <button class="title" onclick="show(1)" type="button">Price Range</button>
                        <div class="options">
                            <input type="number" name="min_price" class="form-control" placeholder="Min Price">
                            <input type="number" name="max_price" class="form-control" placeholder="Max Price">
                        </div>
                    </div>
                    <!-- Genre Filter -->
                    <div class="filter">
                        <button class="title" onclick="show(2)" type="button">Genre</button>
                        <div class="options">
                            <select name="genre" class="form-control">
                                <option value="">Select Genre</option>
                                <option value="Horror">Horror</option>
                                <option value="Romance">Romance</option>
                                <option value="Fantasy">Fantasy</option>
                                <!-- Add more genres as needed -->
                            </select>
                        </div>
                    </div>
                    <!-- Add more filters as needed -->
                    <!-- Search Button -->
                    <div class="settings">
                        <div class="clear-search">
                            <button class="btn btn-primary" type="submit">Search</button>
                        </div>
                        <button class="btn btn-secondary" type="button" onclick="clearAll()">Clear</button>
                    </div>
                </form>
            </div>
        </div>
        <!-- Book Container -->
        <div class="book-container">
            <?php
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

            // Retrieve filter values from the GET request
            $author = isset($_GET['author']) ? $_GET['author'] : '';
            $min_price = isset($_GET['min_price']) ? $_GET['min_price'] : '';
            $max_price = isset($_GET['max_price']) ? $_GET['max_price'] : '';
            $genre = isset($_GET['genre']) ? $_GET['genre'] : '';

            // Build SQL query with filters
            $sql = "SELECT book_id, title, author, price FROM books WHERE 1=1";

            if (!empty($author)) {
                $sql .= " AND author LIKE '%" . $conn->real_escape_string($author) . "%'";
            }

            if (!empty($min_price)) {
                $sql .= " AND price >= " . $conn->real_escape_string($min_price);
            }

            if (!empty($max_price)) {
                $sql .= " AND price <= " . $conn->real_escape_string($max_price);
            }

            if (!empty($genre)) {
                $sql .= " AND genre = '" . $conn->real_escape_string($genre) . "'";
            }

            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                // Output data of each row
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='book'>
        <p>" . $row["title"] . " by " . $row["author"] . " - €" . $row["price"] . "</p>
        <button class='btn btn-primary' onclick='redirectToStripe(" . $row["book_id"] . ")'>Buy</button>
        <button class='btn btn-secondary' onclick='addToCart(" . $row["book_id"] . ", \"" . $row["title"] . "\", " . $row["price"] . ")'>Add to Cart</button>
    </div>";
                }
            } else {
                echo "0 results";
            }
            $conn->close();
            ?>

        </div>
    </div>
    <!-- Cart Popup -->
    <div id="cartPopup" class="cart-popup">
        <div class="cart-content">
            <h3>Cart</h3>
            <div id="cartItems"></div>
            <p id="totalPrice">Total Price: €0.00</p>
            <button class="btn btn-primary" onclick="redirectToCheckout()">Checkout</button>
            <button class="btn btn-secondary" onclick="clearCart()">Clear Cart</button>
            <button class="btn btn-secondary" onclick="toggleCart()">Close</button>
        </div>
    </div>

    <!-- Notification -->
    <div class="notification" id="notification" style="display: none;">
        <p id="notificationText"></p>
        <div class="progress">
            <div class="progress-bar" id="progress-bar" role="progressbar"></div>
        </div>

    </div>
    <!-- Scripts -->
    <script>
        let cart = {};

        function show(index) {
            var options = document.querySelectorAll('.filter')[index].querySelector('.options');
            var titles = document.querySelectorAll('.filter .title');

            if (options.style.display === "none" || options.style.display === "") {
                options.style.display = "block";
                titles[index].classList.add('active');
            } else {
                options.style.display = "none";
                titles[index].classList.remove('active');
            }
        }

        function clearAll() {
            document.getElementById('filterForm').reset();
        }

        function toggleFilters() {
            var filterWrap = document.querySelector(".filter-wrap");
            var filterButton = document.getElementById("filterButton");
            var icon = filterButton.querySelector("i");

            if (filterWrap.style.maxHeight === "0px" || filterWrap.style.maxHeight === "") {
                filterWrap.style.display = "block";
                filterWrap.style.maxHeight = "100%";
                icon.textContent = "close";
            } else {
                filterWrap.style.maxHeight = "0px";
                icon.textContent = "menu";
            }
        }

        function addToCart(bookId, title, price) {
            // Check if the item is already in the cart
            if (cart[bookId]) {
                // Optionally, you can show a message or notification here
                showNotification(`${title} is already in the cart.`);
                return; // Exit function to prevent adding another one
            }

            // Add item to cart
            cart[bookId] = {
                quantity: 1,
                title: title,
                price: price
            };

            // Update cart item count display
            let cartItemCount = document.getElementById('cartItemCount');
            cartItemCount.textContent = Object.keys(cart).length;

            // Update cart popup content
            updateCartPopup();

            showNotification(`Added ${title} to cart.`);
        }

        function clearCart() {
            cart = {}; // Clear the cart object

            // Update cart item count display
            let cartItemCount = document.getElementById('cartItemCount');
            cartItemCount.textContent = '0';

            // Update cart popup content
            updateCartPopup();

            showNotification('Cart cleared.');
        }


        let notificationTimeout; // Variable to store the timeout reference
        let notificationAnimating = false; // Flag to track if notification is currently animating

        function showNotification(message, type = 'success') {
            const notification = document.getElementById('notification');
            const notificationText = document.getElementById('notificationText');
            const progressBar = document.getElementById('progress-bar');

            // Check if notification is currently animating
            if (notificationAnimating) {
                // If animating, return early to prevent multiple animations
                return;
            }

            // Set flag to indicate animation is starting
            notificationAnimating = true;

            notificationText.innerText = message;
            notification.className = `notification ${type}`;
            notification.style.display = 'block';
            progressBar.style.width = '100%'; // Reset progress bar width

            // Clear previous timeout if exists
            if (notificationTimeout) {
                clearTimeout(notificationTimeout);
            }

            let width = 100;
            const interval = setInterval(function() {
                width -= 1;
                progressBar.style.width = width + '%';
                if (width <= 0) {
                    clearInterval(interval);
                    notification.style.display = 'none';
                    notificationAnimating = false; // Reset animation flag
                }
            }, 30); // Adjust the speed of the progress bar here

            // Set timeout to hide notification after animation completes
            notificationTimeout = setTimeout(function() {
                clearInterval(interval); // Stop animation interval if not already stopped
                notification.style.display = 'none';
                notificationAnimating = false; // Reset animation flag
            }, 3000); // Adjust the time (in milliseconds) to keep the notification visible
        }

        function updateCartPopup() {
            let cartItemsContainer = document.getElementById('cartItems');
            cartItemsContainer.innerHTML = '';
            let totalPrice = 0;

            for (const [bookId, item] of Object.entries(cart)) {
                cartItemsContainer.innerHTML += `<p>${item.title} - €${item.price}</p>`;
                totalPrice += item.price * item.quantity;
            }

            document.getElementById('totalPrice').textContent = `Total Price: €${totalPrice.toFixed(2)}`;
        }

        function toggleCart() {
            var cartPopup = document.getElementById('cartPopup');
            if (cartPopup.style.display === 'block') {
                cartPopup.style.display = 'none';
            } else {
                cartPopup.style.display = 'block';
            }
        }


        function redirectToStripe(bookId) {
            const params = new URLSearchParams();
            params.append('book_ids[]', bookId);
            params.append('quantities[]', 1); // Buy one book at a time
            window.top.location.href = `http://localhost/Online_Book_Shop-master/scripts/create_checkout_session.php?${params.toString()}`;
        }

        function redirectToCheckout() {
            // Check if cart is empty
            if (Object.keys(cart).length === 0) {
                showNotification('Cannot checkout an empty cart.', 'error');
                return;
            }

            const params = new URLSearchParams();
            for (const [bookId, item] of Object.entries(cart)) {
                params.append('book_ids[]', bookId);
                params.append('quantities[]', item.quantity);
            }
            window.top.location.href = `http://localhost/Online_Book_Shop-master/scripts/create_checkout_session.php?${params.toString()}`;
        }
    </script>
    <style>
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
</body>

</html>