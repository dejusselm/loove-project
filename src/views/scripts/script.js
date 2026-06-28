let currentTab = 0;
showTab(currentTab);

function showTab(n) {
    let tabs = document.getElementsByClassName("tab");
    tabs[n].style.display = "block";
    if (n == 0) {
        document.getElementById("prevBtn").style.display = "none";
    } else {
        document.getElementById("prevBtn").style.display = "inline";
    }
    if (n == (tabs.length - 1)) {
        document.getElementById("nextBtn").innerHTML = "Submit";
    } else {
        document.getElementById("nextBtn").innerHTML = "Next";
    }
    fixStepIndicator(n)
}

function nextPrev(n) {
    let tabs = document.getElementsByClassName("tab");
    if (n == 1 && !validateForm()) return false;
    tabs[currentTab].style.display = "none";
    currentTab = currentTab + n;
    if (currentTab >= tabs.length) {
        document.getElementById("regForm").submit();
        return false;
    }
    showTab(currentTab);
}

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

function fixStepIndicator(n) {
    // This function removes the "active" class of all steps
    let i, steps = document.getElementsByClassName("step");
    for (i = 0; i < steps.length; i++) {
        steps[i].className = steps[i].className.replace(" active", "");
    }
    // and adds the "active" class to the current step:
    steps[n].className += " active";
}

// Deletes old error messages
function clearError(input) {
    input.classList.remove('invalid');
    let errorSpan = input.parentNode.querySelector('.error-msg');
    if (errorSpan) {
        errorSpan.remove();
    }
}


