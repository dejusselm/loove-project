
function validatePassword(type) {
    const pswd = document.getElementById("password");
    const confirmPswd = document.getElementById("confirmPassword");
    const errorSpan = document.getElementById("pswdErrorSpan");
    const nextBtn = document.getElementById("nextBtn") ?? null;
    console.log(pswd.value, confirmPswd.value);
    if (type == register) {
        if (pswd.value !== confirmPswd.value) {
            errorSpan.innerText = "Passwords do not match.";
            nextBtn.disabled = true;
        }
        else {
            errorSpan.style.display = "none";
            nextBtn.disabled = false;
        }
    } else {
        if (pswd.value !== confirmPswd.value) {
            errorSpan.innerText = "Passwords do not match.";
        }
        else {
            errorSpan.style.display = "none";
        }
    }
}

function validateForm() {
    let tab, field, i, valid = true;
    tab = document.getElementsByClassName("tab");
    field = tab[currentTab].getElementsByTagName("input");

    // Cleans all current tab old error messages before re testing
    let oldErrors = tab[currentTab].querySelectorAll(".error-msg");
    oldErrors.forEach(function (error) { error.remove(); });

    for (i = 0; i < field.length; i++) {
        // Verifies empty field, spaces and regex
        if (!field[i].checkValidity()) {
            field[i].className += " invalid";

            // Creates the error message to display
            let errorSpan = document.createElement("span");
            errorSpan.className = "error-msg";

            if (field[i].value === "") {
                errorSpan.innerText = "Field is mandatory.";
            } else {
                errorSpan.innerText = "Incorrect format.";
            }
            // Adds an error message below the incorrect input 
            field[i].parentNode.insertBefore(errorSpan, field[i].nextSibling);

            valid = false;
        }
    }

    if (valid) {
        document.getElementsByClassName("step")[currentTab].className += " finish";
    }
    return valid; // Returns the valid status
}


// Deletes old error messages
function clearError(input) {
    input.classList.remove('invalid');
    let errorSpan = input.parentNode.querySelector('.error-msg');
    if (errorSpan) {
        errorSpan.remove();
    }
}


