<?php
require_once ROOT_PATH . 'controllers/ChatController.php';

$chatController = new ChatController();
extract($chatController->index());

$from = $_GET['from'] ?? 'dashboard';
$backUrl = $from;
include ROOT_PATH . 'views/components/head.php';
?>
<title><?= $totalUnread > 0 ? "({$totalUnread}) Messages" : "Messages" ?></title>
<link href="/views/style/messages.css" rel="stylesheet">
</head>

<body>
    <header>
        <a href="<?= $backUrl ?>"><i class="fa-solid fa-arrow-left"></i></a>
        <h1>Messages</h1>
    </header>
    <main>
        <?php if (isset($_SESSION['flashMessage'])): ?>
            <div class="flash-modal"">
                <div>
                    <i class=" fa-solid fa-circle-exclamation"></i>
                <span>
                    <?= htmlspecialchars($_SESSION['flashMessage']) ?>
                </span>
            </div>
            <button class="close-flash" onclick="this.closest('.flash-modal').remove()" ">&times;</button>
                                    </div>
                                     <?php unset($_SESSION['flashMessage']); ?>
        <?php endif; ?>
        <ul>
            <?php if (!empty($conversations)): ?>
                                                 <?php foreach ($conversations as $conversation): ?>
                                                                                 <?php $isClosed = $conversation['isClosed'] ?? false; ?>
                                                                                    <li  style=" display:flex;
            flex-direction:row; gap:1em; align-items:center; margin-bottom:2em; list-style-type:none;">
                    <?php if ($isClosed): ?>
                        <img style="width:50px; height: 50px; object-fit: cover; clip-path:circle();"
                            src="/public/uploads/<?= $conversation['profile']->getAvatar() ?>">
                    <?php else: ?>
                        <a href="otherProfile?id=<?= $conversation['profile']->getId() ?>&from=messages">
                            <img style="width:50px; height: 50px; object-fit: cover; clip-path:circle();"
                                src="/public/uploads/<?= $conversation['profile']->getAvatar() ?>">
                        </a>
                    <?php endif; ?>
                    <?php if ($isClosed): ?>
                        <div style="display:flex; flex-direction: column; justify-content:center;">
                            <h4 style="margin: 0; color: #888;"><?= htmlspecialchars($conversation['profile']->getFirstName()); ?>
                            </h4>
                            <p style="margin: 0; font-style: italic; color: #ff4d4d; font-size: 0.9em;">
                                <i class="fa-solid fa-ban"></i> This user has closed the conversation.
                            </p>
                        </div>
                    <?php else: ?>
                        <a style="text-decoration:none;" href="/user/chat?id=<?= $conversation['profile']->getId() ?>">
                            <div style="display:flex; flex-direction: column; justify-content:center;">
                                <h4 style="margin: 0;"><?= htmlspecialchars($conversation['profile']->getFirstName()); ?></h4>
                                <?php if ($conversation['unreadCount'] > 0): ?>
                                    <?php if ($conversation['unreadCount'] < 4): ?>
                                        <?php if ($conversation['unreadCount'] == 1): ?>
                                            <i class="fa-circle fa-solid"></i>
                                            <?= $conversation['lastChat']->getContent() ?>
                                            <?= $conversation['lastChat']->getFormattedDate() ?>
                                        <?php else: ?>
                                            <p style="margin: 0;"><?= $conversation['unreadCount'] . ' unread messages'; ?></p>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <p style="margin: 0;">
                                            <?= '+4 unread messages'; ?>
                                        </p>
                                    <?php endif ?>
                                <?php elseif ($conversation['lastChat']): ?>
                                    <?= $conversation['lastChat']->getContent() ?>
                                    <?= $conversation['lastChat']->getFormattedDate() ?>
                                <?php endif ?>
                            </div>
                        </a>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No conversations yet. Keep swiping!</p>
            <?php endif; ?>
            </ul>
    </main>
    <script src="/views/scripts/notificationsScript.js"></script>
</body>

</html>