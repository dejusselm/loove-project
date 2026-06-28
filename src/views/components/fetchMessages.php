<?php
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__, 2) . '/');
}
require_once ROOT_PATH . 'config.php';
require_once ROOT_PATH . 'controllers/ChatController.php';
require_once ROOT_PATH . 'models/Enums/ChatStatus.php';

$chatController = new ChatController();
$contactId = isset($_GET['id']) ? intval($_GET['id']) : null;

if ($contactId) {
    if ($chatController->isRejected($contactId)) {
        echo "<script>window.location.href = '/messages';</script>";
        exit;
    }
}

extract($chatController->show());

$lastSenderId = null;

if (!empty($chats)):
    foreach ($chats as $chat):

        $isSender = ($chat->getSenderId() === $user->getId());
        $messageClass = $isSender ? 'sender' : 'recipient';
        ?>
        <article class="message <?= $messageClass ?>">
            <div>
                <?php if ($lastSenderId != $chat->getSenderId()):
                    $timeDisplay = $chat->getFormattedDate();
                    ?>
                    <h4>
                        <?php if ($isSender): ?>
                            You
                        <?php else: ?>
                            <?= htmlspecialchars($chat->getSender()->getFirstName()) ?>
                        <?php endif; ?>
                    </h4>
                    <h5>
                        <?= $timeDisplay; ?>
                    </h5>
                <?php endif; ?>
            </div>
            <p>
                <?= htmlspecialchars($chat->getContent()) ?>
            </p>
            <?php $lastSenderId = $chat->getSenderId(); ?>
            <?php if ($chat->getSenderId() == $user->getId()): ?>
                <span class="status-ticks">
                    <?php
                    $statusValue = $chat->getStatus()->value;
                    ?>

                    <?php if ($statusValue === 'sent'): ?>
                        <i class="fa-solid fa-check text-muted"></i>

                    <?php elseif ($statusValue === 'delivered'): ?>
                        <i class="fa-solid fa-check-double text-muted"></i>

                    <?php elseif ($statusValue === 'read'): ?>
                        <i class="fa-solid fa-check-double text-blue"></i>
                    <?php endif; ?>
                </span>
            <?php endif; ?>
        </article>
    <?php endforeach; ?>
<?php else: ?>
    <div class="no-messages">
        <p>It's pretty deserted here...</p>
    </div>
<?php endif; ?>