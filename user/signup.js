document.querySelector(".signup-form").addEventListener("submit", function (event) {
    event.preventDefault(); // Prevent form submission to the server

    // Check if the checkbox is ticked
    const termsCheckbox = document.getElementById("terms");
    if (!termsCheckbox.checked) {
        alert("You must agree to the terms and conditions before signing up!");
        return; // Stop form submission
    }

    // Redirect to home.html if the checkbox is ticked
    window.location.href = "successful.html";
});
