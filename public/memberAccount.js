document.addEventListener('DOMContentLoaded', function() {
    // Fetch and populate user data on page load
    fetchUserData();
    
    // Tab switching functionality
    document.querySelectorAll('.tab-button').forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all buttons and hide all tab content
            document.querySelectorAll('.tab-button').forEach(btn => {
                btn.classList.remove('active');
                const contentId = btn.getAttribute('data-tab');
                document.getElementById(contentId).classList.add('hidden');
            });
            
            // Activate the clicked tab and show content
            this.classList.add('active');
            const activeTabContentId = this.getAttribute('data-tab');
            document.getElementById(activeTabContentId).classList.remove('hidden');
        });
    });
    
    // Form submission handlers
    const updateDetailsForm = document.querySelector('#personal-info-form');
    const changePasswordForm = document.querySelector('#password-change-form');
    
    // Handle personal details update
    updateDetailsForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        formData.append('update_details', 'true');
        
        fetch('../api/memberAccount.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Details updated successfully.');
                fetchUserData(); // Refresh the displayed data
            } else {
                alert(data.error || 'Failed to update details.');
            }
        })
        .catch(error => alert('Failed to update details: ' + error));
    });
    
    // Password validation function
    function validatePassword(password) {
        // Check length (6-8 characters)
        if (password.length < 6 || password.length > 8) {
            return { isValid: false, message: 'Password must be 6-8 characters long.' };
        }
        
        // Check for uppercase letter
        if (!/[A-Z]/.test(password)) {
            return { isValid: false, message: 'Password must contain at least one uppercase letter.' };
        }
        
        // Check for number
        if (!/[0-9]/.test(password)) {
            return { isValid: false, message: 'Password must contain at least one number.' };
        }
        
        // Check for special character
        if (!/[!@#$%^&*]/.test(password)) {
            return { isValid: false, message: 'Password must contain at least one special character (!@#$%^&*).' };
        }
        
        // Check for spaces
        if (/\s/.test(password)) {
            return { isValid: false, message: 'Password must not contain spaces.' };
        }
        
        return { isValid: true };
    }
    
    // Handle password change with validation
    changePasswordForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const currentPassword = this.querySelector('#current-password').value;
        const newPassword = this.querySelector('#new-password').value;
        const confirmPassword = this.querySelector('#confirm-password').value;
        
        // Validate new password
        const validation = validatePassword(newPassword);
        if (!validation.isValid) {
            alert(validation.message);
            return;
        }
        
        // Check if passwords match
        if (newPassword !== confirmPassword) {
            alert('New passwords do not match.');
            return;
        }
        
        const formData = new FormData();
        formData.append('update_password', 'true');
        formData.append('current_password', currentPassword);
        formData.append('new_password', newPassword);
        formData.append('confirm_password', confirmPassword);
        
        fetch('../api/memberAccount.php', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Password updated successfully.');
                this.reset(); // Clear the form
            } else {
                alert(data.error || 'Failed to update password.');
            }
        })
        .catch(error => alert('Failed to update password: ' + error));
    });
});

// Function to fetch and populate user data
function fetchUserData() {
    fetch('../api/memberAccount.php', {
        method: 'GET'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('first-name').value = data.data.first_name;
            document.getElementById('last-name').value = data.data.last_name;
            document.getElementById('email').value = data.data.email;
        } else {
            alert('Failed to load user data: ' + data.error);
        }
    })
    .catch(error => alert('Failed to load user data: ' + error));
}