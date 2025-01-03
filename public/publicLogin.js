document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.login-form');

    // Input sanitization function
    function sanitizeInput(input) {
        return input.replace(/[<>]/g, ''); // Basic XSS prevention
    }

    // Client-side validation
    function validateForm(formData) {
        const usernameRegex = /^[a-zA-Z0-9_]{3,20}$/;
        const passwordRegex = /^.{8,}$/;

        // Validation for user fields
        if (!usernameRegex.test(formData.username)) {
            throw new Error('Please enter a valid username');
        }
        if (!passwordRegex.test(formData.password)) {
            throw new Error('Please enter a valid password');
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
            const response = await fetch('Login.php?action=register', {
                method: 'POST',
                body: JSON.stringify(jsonData),
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest' // CSRF protection
                }
            });

            const result = await response.json();

            if (result.success) {
                // Redirect based on user role
                if (result.role === 'admin') {
                    window.location.href = 'adminDashboard.php';
                } else if (result.role === 'member') {
                    window.location.href = 'memberHome.html';
                }
            } else {
                throw new Error(result.message);
            }

        } catch (error) {
            alert(error.message);
        }
    });
});
