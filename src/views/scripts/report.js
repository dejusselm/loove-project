function openReportModal(profileId) {
    document.getElementById("reportedUserId").value = profileId;
    document.getElementById("reportReason").value = "";
    document.getElementById("report-modal").style.display = "flex";
}

function closeReportModal() {
    document.getElementById("report-modal").style.display = "none";
}

function closeFlashModal() {
    const modal = document.querySelector(".flash-modal");

    if (modal) {
        modal.style.display = "none";
    }
}

window.onclick = function (event) {
    const modal = document.getElementById("report-modal");
    if (event.target === modal) {
        modal.style.display = "none";
    }
}