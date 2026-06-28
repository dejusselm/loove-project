<?php

require_once ROOT_PATH . 'controllers/ChatController.php';
$chatController = new ChatController();
extract($chatController->show());

$lastSenderId = null;

include ROOT_PATH . 'views/components/head.php';
?>
<title><?= $contact->getFirstName() ?></title>
<link href="/views/style/chat.css" rel="stylesheet">
</head>

<body>
    <header>
        <a href="messages"><i class="fa-solid fa-arrow-left"></i></a>
        <h1><?= $contact->getFirstName() ?></h1>
        <a href="otherProfile?id=<?= $contactId ?>&from=chat?id=<?= $contactId ?>"><img
                src="/public/uploads/<?php echo htmlspecialchars($contact->getAvatar()) ?>"></a>
    </header>
    <main id="chat-page" data-chat-user-id="<?= $contactId ?>">
        <section id="chat-box">
            <?php include ROOT_PATH . 'views/components/fetchMessages.php'; ?>
        </section>
        <section>
            <article>
                <form action="chat?id=<?= $contactId ?>" method="POST">
                    <input type="hidden" name="receiver_id" value="<?= $contactId ?>">

                    <input type="text" name="content" placeholder="Send chat" maxlength="500" required
                        autocomplete="off" autofocus>
                    <button type="submit"><i class="fa-paper-plane fa-solid"></i></button>
                </form>
            </article>
        </section>
    </main>
    <script src="/views/scripts/chatScript.js"></script>
    <script src="views/scripts/notificationsScript.js"></script>
    <script>
        getContactId(<?= $contactId ?>);
    </script>
</body>

</html>