<?php
include ROOT_PATH . 'controllers/AdminController.php';

$controller = new AdminController();
extract($controller->adminData());

include ROOT_PATH . 'views/components/head.php';
?>
<title>Admin Dashboard</title>
<link href="/views/style/profile.css" rel="stylesheet">
</head>

<body>
    <header>
        <a href="adminDashboard">
            <h3>Users</h3>
        </a>
        <h3>Statitics</h3>
    </header>
    <main>
        <section>
            <article>
                <h3>Memberships</h3>
                <div class="revenue-card">
                    <h3>Total revenues</h3>
                    <p class="revenue-amount"><?= number_format($totalRevenue, 2, ',', ' ') ?> €</p>
                </div>

                <div class="revenue-table-container">
                    <h3>Transactions history (Stripe)</h3>

                    <?php if (!empty($revenueHistory)): ?>
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>ID Transaction</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Devise</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($revenueHistory as $tx): ?>
                                    <tr>
                                        <td><code><?= htmlspecialchars($tx['id']) ?></code></td>
                                        <td><?= $tx['date'] ?></td>
                                        <td class="text-success">+ <?= number_format($tx['amount'], 2, ',', ' ') ?> €</td>
                                        <td><?= $tx['currency'] ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>No transaction registered, or connexion error with Stripe.</p>
                    <?php endif; ?>
                </div>
            </article>
            <h3>User data</h3>
            <article>
                <p>Total users : <?= $totalUsers ?></p>
                <p>Number of matches : <?= $totalMatches ?></p>
                <p>Premium members : <?= $totalPremium ?></p>
            </article>
        </section>
    </main>
</body>

</html>