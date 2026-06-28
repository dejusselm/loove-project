<?php
require_once ROOT_PATH . 'stripe/stripe-php/init.php';
require_once ROOT_PATH . 'controllers/Controller.php';


class MembershipController extends Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->requireAuth();

        if (isset($_POST['cancelSubscription'])) {
            $this->cancelSubscriptionRenewal();
        }


        \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);
    }

    public function createCheckoutSession(): void
    {

        try {
            $checkoutSession = \Stripe\Checkout\Session::create([
                'success_url' => 'http://localhost:8080/subscription/success?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => 'http://localhost:8080/subscription/cancel',
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

            header("HTTP/1.1 333 See Other");
            header("Location: " . $checkoutSession->url);
            exit;
        } catch (Exception $exception) {
            echo "<h1>  Stripe error :</h1>";
            echo "<p>" . htmlspecialchars($exception->getMessage()) . "</p>";
            $this->logger->log(LogType::ERROR, $exception->getMessage(), $this->userId);
            exit;
        }
    }

    public function success(): void
    {
        $sessionId = $_GET['session_id'] ?? null;
        if ($sessionId) {
            try {
                $session = \Stripe\Checkout\Session::retrieve($sessionId);

                if ($session->payment_status === 'paid') {

                    $userId = $session->metadata->user_id;

                    $stripeSubscriptionId = $session->subscription;

                    $this->memberRepo->setToPremium($userId, $stripeSubscriptionId);

                    $_SESSION['flashMessage'] = "Congratulations : you are now a Member !";
                    $this->logger->log(LogType::INFO, "Is now a member.", $this->userId);
                    header('Location: /memberships');
                    exit;
                }
            } catch (Exception $exception) {
                $this->logger->log(LogType::ERROR, $exception->getMessage(), null);
            }

            $_SESSION['flashMessage'] = "Payment could not be completed.";
            $this->logger->log(LogType::INFO, "Payment cancelled", $this->userId);
            header('Location: /memberships');
            exit;
        }
    }
    public function cancel(): void
    {
        $_SESSION['flashMessage'] = "Membership payment has been canceled.";

        $this->logger->log(LogType::ERROR, "Membership payment canceled", $this->userId);
        header('Location: /memberships');
        exit;
    }

    public function cancelSubscriptionRenewal()
    {
        unset($_SESSION["errorMessage"]);

        $subId = $this->user->getStripeSubscriptionId();

        if ($subId) {
            try {
                \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);
                \Stripe\Subscription::update($subId, [
                    'cancel_at_period_end' => true
                ]);
                $_SESSION['successMessage'] = "Your automatic renewal has been deactivated.";
            } catch (\Exception $exception) {
                $_SESSION['errorMessage'] = "Stripe Error : " . $exception->getMessage();
                $this->logger->log(LogType::ERROR, $exception->getMessage(), $this->userId);

            }
        } else {
            $_SESSION['errorMessage'] = "No active subscription found.";
            $this->logger->log(LogType::INFO, "Tried to cancel subscription while having the free one", $this->userId);
        }

        header('Location: /parameters');
        exit;
    }
}