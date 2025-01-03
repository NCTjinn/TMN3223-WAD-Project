document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.registration-form');

    // Input sanitization function
    function sanitizeInput(input) {
        return input.replace(/[<>]/g, ''); // Basic XSS prevention
    }

    // Client-side validation
    function validateForm(formData) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        const usernameRegex = /^[a-zA-Z0-9_]{3,20}$/;
        const passwordRegex = /^.{8,}$/;

        if (!usernameRegex.test(formData.username)) {
            throw new Error('Username must be 3-20 characters and contain only letters, numbers, and underscores');
        }
        if (!emailRegex.test(formData.email)) {
            throw new Error('Please enter a valid email address');
        }
        if (!passwordRegex.test(formData.password)) {
            throw new Error('Password must be at least 8 characters long');
        }
        if (formData.password !== formData.confirm_password) {
            throw new Error('Passwords do not match');
        }
    }

    form.addEventListener('submit', async function(event) {
        event.preventDefault();

        try {
            const formData = new FormData(form);
            const jsonData = {};

            // Sanitize inputs and convert FormData to JSON
            for (let pair of formData.entries()) {
                jsonData[pair[0]] = sanitizeInput(pair[1]);
            }

            // Validate form
            validateForm(jsonData);

            // Send data to server
            const response = await fetch('publicRegistration.php?action=register', {
                method: 'POST',
                body: JSON.stringify(jsonData),
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest' // CSRF protection
                }
            });

            const result = await response.json();

            if (result.success) {
                // Show modal
                const modal = document.getElementById("successModal");
                modal.style.display = "block";

                // Close the modal after a delay and redirect to the home page
                setTimeout(function() {
                    modal.style.display = "none";
                    window.location.href = 'publicHome.html';
                }, 3000); // 3 seconds delay
            } else {
                throw new Error(result.message);
            }

        } catch (error) {
            alert(error.message);
        }
    });

    // Get the modal
    const modal = document.getElementById("successModal");

    // Get the <span> element that closes the modal
    const span = document.getElementsByClassName("close-button")[0];

    // When the user clicks on <span> (x), close the modal
    span.onclick = function() {
        modal.style.display = "none";
    }

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
});
