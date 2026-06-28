function handlePhotoChange(input, photoType) {
    const preview = document.getElementById(photoType + 'Preview');
    const maxSize = 2097152; // 2 Mo in octets

    // Verifies if a file was selected
    if (!input.files || !input.files[0]) {
        preview.src = '#';
        preview.style.display = 'none';
        return;
    }

    // Size verification
    if (input.files[0].size > maxSize) {
        alert("File is too big! Maximum size allowed is 2MB.");
        input.value = ""; // Clears input to prevent submitting incorrect file
        preview.src = '#'; // Clears preview
        preview.style.display = 'none'; // Hides preview
        return;
    }

    // Else, displays preview with FileReader API
    const reader = new FileReader();
    reader.onload = function (event) {
        preview.src = event.target.result;
        preview.style.display = 'block';
    }
    reader.readAsDataURL(input.files[0]);
}

