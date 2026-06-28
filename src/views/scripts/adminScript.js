function showReportDetails(element) {
    const box = document.getElementById('reportDetailsBox');
    const number = document.getElementById('reportNumber');
    const details = document.getElementById('reportDetails')

    const name = element.getAttribute('data-name');
    const count = element.getAttribute('data-count');
    const reasons = element.getAttribute('data-reasons');

    if (reasons) {
        const reasonsList = reasons.split('||');
        details.textContent = "";
        for (i = 0; i < reasonsList.length; i++) {
            let paragraph = document.createElement("p");
            const reportParts = reasonsList[i].split(":");
            paragraph.textContent = `${reportParts[1]} : ${reportParts[0]}`;
            details.appendChild(paragraph);
        }
    }
    document.getElementById("reportModal").style.display = "block";
    number.textContent = `User ${name} was reported ${count} time(s) by community.`;

}

function openActionModal(action, userId, name) {
    const modal = document.getElementById('adminActionModal');
    const title = document.getElementById('modalActionTitle');
    const message = document.getElementById('modalActionMessage');
    const submitBtn = document.getElementById('modalSubmitBtn');

    document.getElementById('hiddenAction').value = action;
    document.getElementById('hiddenUserId').value = userId;

    document.getElementById('adminReason').value = '';

    if (action === 'toggle') {
        title.innerText = "Restrict User Account";
        message.textContent = `You are about to suspend ${name}'s account.`;
        submitBtn.textContent = "Suspend Account";
    } else if (action === 'delete') {
        title.textContent = "Permanent Account Deletion";
        message.textContent = `Are you absolutely sure you want to permanently delete ${name}'s account?`;
        submitBtn.textContent = "Delete Permanently";
    }

    modal.style.display = 'block';
}

function closeActionModal() {
    document.getElementById('adminActionModal').style.display = 'none';
}

// Sécurité pour fermer le modal si on clique à côté
window.onclick = function (event) {
    const actionModal = document.getElementById('adminActionModal');
    const reportModal = document.getElementById('reportModal');
    if (event.target == actionModal) {
        actionModal.style.display = "none";
    }
    if (event.target == reportModal) {
        reportModal.style.display = "none";
    }
}