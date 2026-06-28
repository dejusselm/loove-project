
function openReportModal(profileId, firstName) {
    document.getElementById('reportedProfileId').value = profileId;
    document.getElementById('reportReason').value = '';
    document.getElementById('reportModalMessage').innerHTML = `You are reporting <strong>${firstName}</strong>. Please provide a clear reason.`;
    document.getElementById('reportProfileModal').style.display = 'block';
}

function closeReportModal() {
    document.getElementById('reportProfileModal').style.display = 'none';
}

window.onclick = function (event) {
    const modal = document.getElementById('reportProfileModal');
    if (event.target == modal) {
        modal.style.display = "none";
    }
}
