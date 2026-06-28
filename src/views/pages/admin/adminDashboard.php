<?php
session_start();

include ROOT_PATH . 'controllers/AdminController.php';

$controller = new AdminController();
$controller->handleAction();

include ROOT_PATH . 'views/components/head.php';
?>
<title>Admin Dashboard</title>
<link href="/views/style/profile.css" rel="stylesheet">
</head>

<body>
    <header>
        <h3>Users</h3>
        <a href="adminData">
            <h3>Statistics</h3>
        </a>
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
                <?php foreach ($controller->getUsers() as $user): ?>
                    <tr>
                        <td>
                            <a href="adminUserProfile?id=<?= $user->getId() ?>"><img
                                    src="/public/uploads/<?php echo htmlspecialchars($user->getAvatar()); ?>" class="avatar"
                                    alt="Avatar" style="min-width:50px; max-width:100px; clip-path: circle();"></a>
                        </td>
                        <td><?php echo htmlspecialchars($user->getFirstName() . ' ' . $user->getLastName()); ?></td>
                        <td><?php echo htmlspecialchars($user->getEmail()); ?></td>
                        <td>
                            <?php if ($user->report_count > 0): ?>
                                <span class="report-link" data-name="<?php echo htmlspecialchars($user->getFirstName()); ?>"
                                    data-count="<?php echo $user->report_count; ?>" onclick="showReportDetails(this)"
                                    data-reasons="<?php echo htmlspecialchars($user->reasons); ?>">
                                    /!\ <?php echo $user->report_count; ?> report(s)
                                </span>
                            <?php else: ?>
                                <span>None</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($user->getActive() == 1): ?>
                                <button type="button" class="btn btn-ban"
                                    style="background: none; border: none; cursor: pointer;"
                                    onclick="openActionModal('toggle', <?= $user->getId(); ?>, '<?= htmlspecialchars($user->getFirstName()); ?>')">
                                    <i class="fa-solid fa-toggle-on" style="color:black"></i>
                                </button>
                            <?php else: ?>
                                <a href="adminDashboard?action=toggle&id=<?= $user->getId(); ?>" class="btn btn-toggle">
                                    <i class="fa-solid fa-toggle-off" style="color:black"></i>
                                </a>
                            <?php endif; ?>
                        </td>

                        <td>
                            <button type="button" class="btn" style="background: none; border: none; cursor: pointer;"
                                onclick="openActionModal('delete', <?= $user->getId(); ?>, '<?= htmlspecialchars($user->getFirstName()); ?>')">
                                <i class="fa-solid fa-trash-can" style="color:red"></i>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div id="reportModal" class="modal" style="display:none">
            <div class="modal-content">
                <h3 style="color: #e74c3c;">Report Details</h3>

                <p id="reportNumber">Click on report to get details</p>
                <div id="reportDetails"></div>
                <button type="button" onclick="document.getElementById('reportModal').style.display='none'"
                    style="padding: 5px 10px; cursor: pointer;">
                    Close
                </button>
            </div>
        </div>

        <div id="adminActionModal" class="modal"
            style="display:none; position: fixed; z-index: 999; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5);">
            <div class="modal-content"
                style="background-color: #fff; margin: 15% auto; padding: 20px; border-radius: 8px; width: 400px; box-shadow: 0 4px 8px rgba(0,0,0,0.2);">
                <h3 id="modalActionTitle" style="color: #2c3e50; margin-top: 0;">Administrative Action</h3>

                <form action="adminDashboard" method="GET" id="adminActionForm">
                    <input type="hidden" name="action" id="hiddenAction" value="">
                    <input type="hidden" name="id" id="hiddenUserId" value="">

                    <p id="modalActionMessage"></p>

                    <div style="margin-bottom: 15px;">
                        <label for="adminReason" style="display: block; font-weight: bold; margin-bottom: 5px;">Reason /
                            Message to the user :</label>
                        <textarea name="reason" id="adminReason" rows="4"
                            style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; resize: none;"
                            placeholder="Type the justification here..." required></textarea>
                    </div>

                    <div style="display: flex; justify-content: flex-end; gap: 10px;">
                        <button type="button" onclick="closeActionModal()"
                            style="padding: 8px 15px; background-color: #bdc3c7; color: white; border: none; border-radius: 4px; cursor: pointer;">
                            Cancel
                        </button>
                        <button type="submit" id="modalSubmitBtn"
                            style="padding: 8px 15px; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">
                            Confirm Action
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
    <p><a href="/">Log out</a></p>
    <script src="/views/scripts/adminScript.js"></script>
</body>

</html>