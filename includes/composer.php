<?php
require 'vendor/autoload.php'; // Make sure to include the Stripe PHP library

\Stripe\Stripe::setApiKey('sk_test_51P1QwWRq4uEDYFFly6Mo8HiENPgnfE06VtrBfIqcszpBVkopKaRZOfiYBicrSZpYqXer9YIXKdZl8zA3yuvk7qXC00v2Q0sfy6');

header('Content-Type: application/json');

$domain = 'http://127.0.0.1:5500/index.html';

$checkout_session = \Stripe\Checkout\Session::create([
    'payment_method_types' => ['card'],
    'line_items' => [[
        'price_data' => [
            'currency' => 'usd',
            'product_data' => [
                'name' => 'Book 1',
            ],
            'unit_amount' => 2000, // Amount in cents
        ],
        'quantity' => 1,
    ]],
    'mode' => 'payment',
    'success_url' => $domain . '/success.html',
    'cancel_url' => $domain . '/cancel.html',
]);

echo json_encode(['id' => $checkout_session->id]);
?>
