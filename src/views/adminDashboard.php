<?php
session_start();


if ($_SESSION["role"] != "admin") {
    header('Location: home.php');
    exit;
}
include "../controllers/adminController.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <title>Admin Dashboard</title>
</head>

<body>
    <header>
    </header>
    <main>
        <table>
            <thead>
                <tr>
                    <th>Avatar</th>
                    <th>First Name / Last Name</th>
                    <th>Email</th>
                    <th>Reports</th>
                    <th>Status</th>
                    <th>Action</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td>
                            <img src="../public/uploads/<?php echo htmlspecialchars($user['avatar']); ?>" class="avatar"
                                alt="Avatar" style="min-width:50px; max-width:100px; clip-path: circle();">
                        </td>
                        <td><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td>
                            <?php if ($user['report_count'] > 0): ?>
                                <span class="report-link" data-username="<?php echo htmlspecialchars($user['first_name']); ?>"
                                    data-count="<?php echo $user['report_count']; ?>" onclick="showReportDetails(this)"
                                    data-reasons="<?php echo htmlspecialchars($user['reasons']); ?>">
                                    /!\ <?php echo $user['report_count']; ?> report(s)
                                </span>
                            <?php else: ?>
                                <span>None</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($user['active']): ?>
                                <span class="badge badge-success"><i class="fa-solid fa-circle" style="color:green"></i></span>
                            <?php else: ?>
                                <span class="badge badge-danger"><i class="fa-regular fa-circle" style="color:red"></i></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($user['active'] == 1) {
                                $buttonClass = 'btn-ban';
                                $buttonText = '<i class="fa-solid fa-toggle-on" style="color:black"></i>';
                            } else {
                                $buttonClass = 'btn-toggle';
                                $buttonText = '<i class="fa-solid fa-toggle-off" "color:black"></i>';
                            } ?>
                            <a href="adminDashboard.php?action=toggle&id=<?php echo $user['id']; ?>"
                                class="btn <?php echo $buttonClass; ?>">
                                <?php echo $buttonText; ?>
                            </a>
                        </td>
                        <td>
                            <a href="adminDashboard.php?action=delete&id=<?php echo $user['id']; ?>" class="btn"
                                onclick="return confirm('Are you absolutely sure you want to permanently delete the account of <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>? This action cannot be undone.');">
                                <i class="fa-solid fa-trash-can" style="color:red"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div id="reportDetailsBox">
            <h3 style="color: #e74c3c; margin-top: 0;">Report Details</h3>

            <p id="reportNumber">Click on report to get details</p>
            <div id="reportDetails"></div>
            <button type="button" onclick="document.getElementById('reportDetailsBox').style.display='none'"
                style="padding: 5px 10px; cursor: pointer;">
                Close
            </button>
        </div>
    </main>
    <p><a href=" login.php">Login page</a></p>
    <script src="scripts/adminScript.js"></script>
</body>

</html>