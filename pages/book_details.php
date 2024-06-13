<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="../assets/images/bookwise_logo.png">
    <link rel="stylesheet" href="../assets/css/main.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <title>BookWise - Book Details</title>
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
            <!-- Links -->
            <div class="item_top item">
                <a href="">placeholder address</a>
            </div>
            <!-- Search Placeholder -->
            <div class="item_top item">placeholder search</div>
            <!-- Language Selector -->
            <div class="item_top"><a href="#"><img src="../assets/images/ukflag.png" alt="uk flag" style="max-width: 40px;"></a></div>
            <!-- Profile -->
            <div class="item_top"><a href="#"><img src="../assets/images/profile.png" alt="profile icon" style="max-width: 40px"></a></div>
            <!-- Cart -->
            <div class="item_top"><a href="#"><img src="../assets/images/cart.png" alt="" style="max-width: 40px;"></a></div>
            <!-- Register Book Button -->
            <div class="item_top item">
                <a href="../pages/register_book.php" class="btn btn-primary">Register Book</a>
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

        // Retrieve book ID from the URL parameter
        $book_id = $_GET['book_id'];

        // Build SQL query to retrieve book details
        $sql = "SELECT title, author, price, description, genre, cover_image FROM books WHERE book_id = $book_id";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            // Output data of the selected book
            $row = $result->fetch_assoc();
            echo "<div class='book-details'>";
            echo "<img src='../uploads/book_covers/" . $row["cover_image"] . "' alt='" . $row["title"] . "' class='book-cover'>";
            echo "<h1>" . $row["title"] . "</h1>";
            echo "<p>by " . $row["author"] . "</p>";
            echo "<p>Genre: " . $row["genre"] . "</p>";
            echo "<p>€" . $row["price"] . "</p>";
            echo "<p>" . $row["description"] . "</p>";
            echo "<a href='../scripts/create_checkout_session.php?book_id=" . $book_id . "' class='btn btn-primary'>Buy</a>";
            echo "</div>";
        } else {
            echo "0 results";
        }
        $conn->close();
        ?>
    </div>
    <!-- Scripts -->
    <script>
        // JavaScript function for filter toggle functionality
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
    </script>
</body>
</html>
