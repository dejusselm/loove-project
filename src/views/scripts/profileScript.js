function openDescriptionModal() {
    document.getElementById("description-modal").style.display = "flex";
}

function closeDescriptionModal() {
    document.getElementById("description-modal").style.display = "none";
}

function openHobbyModal() {
    document.getElementById("hobby-modal").style.display = "flex";
}


function closeHobbyModal() {
    document.getElementById("hobby-modal").style.display = "none";
}

document.addEventListener("DOMContentLoaded", () => {
    const checkboxes = document.querySelectorAll(".hobby-checkbox");
    const maxSelection = 5;

    checkboxes.forEach(checkbox => {
        checkbox.addEventListener("change", () => {
            const checkedCount = document.querySelectorAll(".hobby-checkbox:checked").length;

            if (checkedCount > maxSelection) {
                checkbox.checked = false;
                alert("You cannot select more than " + maxSelection + " hobbies.");
            }
        });
    });
});