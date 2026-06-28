function closeModal(name) {
    document.getElementById(name + "-modal").style.display = "none";
}

function openModal(name) {
    document.getElementById(name + "-modal").style.display = "flex";
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