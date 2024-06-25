<?php

// Requires
require_once __DIR__ . '/../vendor/autoload.php';
$config = parse_ini_file(__DIR__ . '/../scripts/config.ini', true);

// Database connection parameters from the correct section
$host = $config["database_book_write"]["hostname"];
$db = $config["database_book_write"]["database"];
$user = $config["database_book_write"]["username"];
$pass = $config["database_book_write"]["password"];

// Connect to the database
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set the Stripe API key dynamically from the config file
\Stripe\Stripe::setApiKey($config["stripe"]["stripe_secret_key"]);

// Retrieve book IDs and quantities from the GET request
$bookIds = isset($_GET['book_ids']) ? $_GET['book_ids'] : [];
$quantities = isset($_GET['quantities']) ? $_GET['quantities'] : [];

if (empty($bookIds) || empty($quantities) || count($bookIds) != count($quantities)) {
    http_response_code(400);
    echo "Book ID(s) or quantity(ies) are missing or mismatched";
    exit;
}

// Prepare the SQL statement to retrieve book details
$placeholders = implode(',', array_fill(0, count($bookIds), '?'));
$sql = "SELECT book_id, title, author, price FROM books WHERE book_id IN ($placeholders)";
$stmt = $conn->prepare($sql);
$stmt->bind_param(str_repeat('i', count($bookIds)), ...$bookIds);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    http_response_code(404);
    echo "Book(s) not found";
    exit;
}

$lineItems = [];
while ($book = $result->fetch_assoc()) {
    $bookId = $book['book_id'];
    $quantityIndex = array_search($bookId, $bookIds);
    $quantity = $quantities[$quantityIndex];
    $priceInCents = $book['price'] * 100;

    $lineItems[] = [
        "quantity" => $quantity,
        "price_data" => [
            "currency" => "eur",
            "unit_amount" => $priceInCents,
            "product_data" => [
                "name" => $book['title'],
                "description" => "by " . $book['author'],
            ]
        ]
    ];
}

$checkout_session = \Stripe\Checkout\Session::create([
    "mode" => "payment",
    "success_url" => "http://localhost/Online_Book_Shop-master/scripts/success.php",
    "cancel_url" => "http://localhost/Online_Book_Shop-master/scripts/cancel.php",
    "line_items" => $lineItems
]);

$conn->close();

// Send a 303 See Other response and redirect to the Stripe Checkout URL
http_response_code(303);
header("Location: " . $checkout_session->url);
exit;
?>
