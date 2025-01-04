document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.registration-form');

    // Input sanitization function
    function sanitizeInput(input) {
        return input.replace(/[<>]/g, ''); // Basic XSS prevention
    }

    // Client-side validation
    function validateForm(formData) {
        const usernameRegex = /^[a-zA-Z0-9_]{3,20}$/;
        const firstNameRegex = /^[a-zA-Z]+$/;
        const lastNameRegex = /^[a-zA-Z]+$/;
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        const passwordRegex = /^(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#$%^&*])(?=.*[^\s]).{8,}$/;
        

        // Validation for user fields
        if (!usernameRegex.test(formData.username)) {
            throw new Error('Username must be 3-20 characters and contain only letters, numbers, and underscores');
        }
        if (!firstNameRegex.test(formData.first_name)) {
            throw new Error('First name must contain only letters');
        }
        if (!lastNameRegex.test(formData.last_name)) {
            throw new Error('Last name must contain only letters');
        }
        if (!emailRegex.test(formData.email)) {
            throw new Error('Please enter a valid email address');
        }
        if (!passwordRegex.test(formData.password)) {
            throw new Error('Password must be at least 8 characters long and contain at least one uppercase letter, one number, one special character, and no spaces');
        }
        if (formData.password !== formData.confirm_password) {
            throw new Error('Passwords do not match');
        }

        // Validation for address fields
        if (!formData.address_line_1) {
            throw new Error('Address line 1 is required');
        }
        if (!formData.city) {
            throw new Error('City is required');
        }
        if (!formData.state) {
            throw new Error('State is required');
        }
        if (!formData.postcode) {
            throw new Error('Postcode is required');
        }
        if (!formData.country) {
            throw new Error('Country is required');
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

            // Convert is_default checkbox value to boolean
            jsonData['is_default'] = form.querySelector('input[name="is_default"]').checked;

            // Validate form
            validateForm(jsonData);

            // Show loading modal
            loadingModal.style.display = "block"

            // Send data to server
            const response = await fetch('Registration.php?action=register', {
                method: 'POST',
                body: JSON.stringify(jsonData),
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest' // CSRF protection
                }
            });

            const result = await response.json();

            // Hide loading modal
            loadingModal.style.display = "none"

            if (result.success) {
                // Show modal
                const modal = document.getElementById("successModal");
                modal.style.display = "block";

                // Close the modal after a delay and redirect to the home page
                setTimeout(function() {
                    modal.style.display = "none";
                    window.location.href = 'publicLogin.html';
                }, 3000); // 3 seconds delay
            } else {
                throw new Error(result.message);
            }

        } catch (error) {
            // Hide loading modal in case of error
            loadingModal.style.display = "none"
            alert(error.message);
        }
    });
});
