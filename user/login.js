// Handle form submission on login page
document.getElementById('loginForm').addEventListener('submit', function(event) {
    event.preventDefault();  // Prevent form from refreshing the page
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;

    // Perform login action (e.g., check credentials, show success)
    if (email && password) {
        alert('Login Successful');
        window.location.href = 'welcome.html';  // Redirect to welcome page
    } else {
        alert('Please fill in all fields.');
    }
});