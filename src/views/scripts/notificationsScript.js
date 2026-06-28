function checkNotifications() {
    fetch('/views/components/notifications.php')
        .then(response => response.json())
        .then(notifications => {
            if (notifications.length > 0) {
                notifications.forEach(notif => {
                    if (notif.type == 'message') {
                        const chatPage = document.getElementById('chat-page');
                        if (chatPage) {
                            const chatUserId = chatPage.getAttribute('data-chat-user-id');
                            if (String(notif.sender_id) === String(chatUserId))
                                return;
                        }
                    }
                    showPushNotification(notif.message);
                });
            }
        })
        .catch(error => console.error("Notification error :", error));
}

function showPushNotification(message) {
    if (Notification.permission === "granted") {
        new Notification("Loove App", { body: message, icon: "/public/assets/logo.png" });
    }

}

if (typeof Notification !== "undefined" && Notification.permission !== "granted") {
    Notification.requestPermission();
}

setInterval(checkNotifications, 5000);