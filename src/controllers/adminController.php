<?php

require_once 'Controller.php';
require_once ROOT_PATH . 'repositories/UserRepository.php';
require_once ROOT_PATH . 'repositories/SearchRepository.php';
require_once ROOT_PATH . 'stripe/stripe-php/init.php';
use Stripe\StripeClient;

class AdminController extends Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->requireAdmin();

    }

    public function adminData()
    {
        $users = $this->getUsers();
        $stripeData = $this->getStripeRevenueData();
        return [
            'users' => $users,
            'totalRevenue' => $stripeData['totalRevenue'],
            'revenueHistory' => $stripeData['history'],
            'stripeError' => $stripeData['error'] ?? null,
            'totalUsers' => count($this->getUsers()),
            'totalPremium' => $this->memberRepo->countTotalPremium(),
            'totalMatches' => $this->matchRepo->countTotalMatches()
        ];
    }
    public function getUsers(): array
    {
        return $this->searchRepo->getAll([
            "includeReports" => true,
            "except" => "admin"
        ]);
    }

    public function getProfile(): ?User
    {
        $profileId = isset($_GET["profileId"]) ?
            intval($_GET["profileId"]) : null;
        if ($profileId) {
            return $this->userRepo->findOne($profileId);
        }
        return null;
    }


    public function handleAction(): void
    {
        if (isset($_GET['action']) && isset($_GET['id'])) {
            $userId = intval($_GET['id']);
            $user = $this->userRepo->findOne($userId);
            $action = $_GET['action'] ?? null;
            $reason = $_GET['reason'] ?? null;

            if ($action === 'toggle') {

                $this->handleToggle($user->getId(), $reason);
            } elseif ($action === 'delete') {

                $to = $user->getEmail();
                $subject = "Your account has been deleted.";
                $message = "Your account has been deleted for 
                the following reasons : " . $reason;
                $headers = "From: no-reply@loove.local";

                mail($to, $subject, $message, $headers);

                $this->logger->log(
                    LogType::ADMIN,
                    "User " . $userId . "'s account deleted",
                    $this->userId
                );

                $this->userRepo->deleteUser($userId);
            }

            header('Location: adminDashboard');
            exit;
        }
    }

    private function handleToggle(int $userId, ?string $reason)
    {
        $this->userRepo->updateActive($userId);
        $user = $this->userRepo->findOne($userId);
        if (!$user->getActive()) {
            $to = $user->getEmail();
            $subject = "Your account has been deactivated.";
            $message = "Your account has been deactivated for the following 
            reasons : " . $reason;
            $headers = "From: no-reply@loove.local";

            mail($to, $subject, $message, $headers);
            $this->notifRepo->registerNotification(
                $this->userId,
                $reason,
                'admin'
            );
            $this->logger->log(
                LogType::ADMIN,
                "User " . $userId . "'s Account deactivated",
                $this->userId
            );
        }
        $this->logger->log(
            LogType::ADMIN,
            "User " . $userId . "'s Account activated",
            $this->userId
        );
    }



    public function getStripeRevenueData(): array
    {
        $stripe = new StripeClient(STRIPE_SECRET_KEY);

        try {
            $payments = $stripe->paymentIntents->all([
                'limit' => 100,
            ]);

            $totalRevenue = 0;
            $revenueHistory = [];

            foreach ($payments->data as $payment) {
                if ($payment->status === 'succeeded') {

                    $amount = $payment->amount / 100;
                    $totalRevenue += $amount;

                    $date = date('d/m/Y H:i', $payment->created);

                    $revenueHistory[] = [
                        'id' => $payment->id,
                        'amount' => $amount,
                        'currency' => strtoupper($payment->currency),
                        'date' => $date,
                        'email' => $payment->receipt_email ?? 'Not specified'
                    ];
                }
            }

            return [
                'totalRevenue' => $totalRevenue,
                'history' => $revenueHistory
            ];

        } catch (\Exception $exception) {
            $this->logger->log(
                LogType::ERROR,
                $exception->getMessage(),
                $this->userId
            );
            return [
                'totalRevenue' => 0,
                'history' => [],
                'error' => $exception->getMessage()
            ];

        }
    }
}
