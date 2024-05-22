<?php

//requires
require __DIR__ . "../../vendor/autoload.php";
$config = parse_ini_file(__DIR__ . "/config.ini", true);

$mysqli = new mysqli(
    $config["database"]["hostname"],
    $config["database"]["username"],
    $config["database"]["password"],
    $config["database"]["database"],
);


$stripe_secret_key = "sk_test_51P1QwWRq4uEDYFFly6Mo8HiENPgnfE06VtrBfIqcszpBVkopKaRZOfiYBicrSZpYqXer9YIXKdZl8zA3yuvk7qXC00v2Q0sfy6";

\Stripe\Stripe::setApiKey($stripe_secret_key); // setting the API key

$checkout_session = \Stripe\Checkout\Session::create([
    "mode" => "payment",
    "success_url" => "http://localhost/Online_Book_Shop-master/scripts/success.php",
    "cancel_url" => "http://localhost/Online_Book_Shop-master/scripts/cancel.php", // Adding cancel URL as it's required
    "line_items" => [
        [
            "quantity" => 1,
            "price_data" => [
                "currency" => "eur",
                "unit_amount" => 2000, // cents, meaning 20 eur
                "product_data" => [
                    "name" => "T-shirt",
                    "description" => "shirt",

                ]
            ]
        ],
        [
            "quantity" => 2,
            "price_data" => [
                "currency" => "eur",
                "unit_amount" => 700, // cents, meaning 20 eur
                "product_data" => [
                    "name" => "Wine"
                ]
            ]
        ]
    ]

]);

http_response_code(303);
header("Location: " . $checkout_session->url);

?>
