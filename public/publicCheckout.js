document.addEventListener('DOMContentLoaded', function() {
    // Initialize cart display
    cart.updateCartDisplay();

    document.getElementById('checkoutButton').addEventListener('click', function() {
        window.location.href = 'publicLogin.html';
    });
    
});
