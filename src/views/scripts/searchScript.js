document.addEventListener("DOMContentLoaded", () => {
    const searchInput = document.getElementById("searchCity");
    const selectElements = document.querySelectorAll(".radiusSelect");

    searchInput.addEventListener("input", () => {
        if (searchInput.value.trim() == "") {
            selectElements.forEach(element => {
                element.style.display = "none";
            })

        } else {
            selectElements.forEach(element => {
                element.style.display = "flex";
            })
        }
    })
})

let coll = document.getElementsByClassName("collapsible");
let collI = document.getElementById("collapsible-i");
let i;

for (i = 0; i < coll.length; i++) {
    coll[i].addEventListener("click", function () {
        this.classList.toggle("active");
        var content = this.nextElementSibling;
        if (content.style.display === "block") {
            content.style.display = "none";
            collI.className = 'fa-solid fa-caret-down';
        } else {
            content.style.display = "block";
            collI.className = 'fa-solid fa-caret-up';
        }
    });
}


