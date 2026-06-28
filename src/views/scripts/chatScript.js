const chatBox = document.getElementById('chat-box');
let contactId = 0;

function getContactId(newContactId) {
    contactId = newContactId;
}

function scrollToBottom() {
    if (chatBox) {
        setTimeout(() => {
            chatBox.scrollTop = chatBox.scrollHeight;
        }, 50);
    }
}

window.addEventListener('DOMContentLoaded', () => {
    scrollToBottom();
});

function loadMessages() {
    fetch('/views/components/fetchMessages.php?id=' + contactId)
        .then(response => response.text())
        .then(html => {
            if (chatBox) {
                const isAtBottom = chatBox.scrollHeight - chatBox.clientHeight <= chatBox.scrollTop + 80;

                chatBox.innerHTML = html;

                if (isAtBottom) {
                    scrollToBottom();
                }
            }
        });
}

setInterval(loadMessages, 2000);