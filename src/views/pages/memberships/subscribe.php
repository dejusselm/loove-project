<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once ROOT_PATH . 'stripe/stripe-php/init.php';

\Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

try {
    $checkoutSession = \Stripe\Checkout\Session::create([
        'success_url' => 'http://localhost:8080/memberships/success?session_id={CHECKOUT_SESSION_ID}',
        'cancel_url' => 'http://localhost:8080/memberships/cancel',
        'payment_method_types' => ['card'],
        'mode' => 'subscription',
        'line_items' => [
            [
                'price' => 'price_1TmCGyGbfvYQKJFYuoS7Lyoa',
                'quantity' => 1,
            ]
        ],
        'metadata' => [
            'user_id' => $_SESSION['userId']
        ]
    ]);

    // 5. Redirection brute
    header("Location: " . $checkoutSession->url);
    exit;

} catch (\Throwable $e) {
    echo "<h1>Script crash :</h1>";
    echo "<p><b>Message :</b> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><b>File :</b> " . $e->getFile() . " (Ligne " . $e->getLine() . ")</p>";
    exit;
}
