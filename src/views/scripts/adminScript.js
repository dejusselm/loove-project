function showReportDetails(element) {
    const box = document.getElementById('reportDetailsBox');
    const number = document.getElementById('reportNumber');
    const details = document.getElementById('reportDetails')

    const username = element.getAttribute('data-username');
    const count = element.getAttribute('data-count');
    const reasons = element.getAttribute('data-reasons');

    if (reasons) {
        const reasonsList = reasons.split('||');
        details.textContent = "";
        for (i = 0; i < reasonsList.length; i++) {
            let paragraph = document.createElement("p");
            const reportParts = reasonsList[i].split(":");
            console.log(reportParts);
            paragraph.textContent = `${reportParts[0]} : ${reportParts[1]} | ${reportParts[2]}`;
            details.appendChild(paragraph);
        }
    }

    number.innerHTML = `User <strong>${username}</strong> was reported <strong>${count} times</strong> by community.<br><br>`;

    box.style.display = 'block';

    box.scrollIntoView({ behavior: 'smooth' });
}