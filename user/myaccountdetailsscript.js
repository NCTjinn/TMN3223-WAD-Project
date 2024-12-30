// Tab switching logic
document.querySelectorAll('.tab-button').forEach(button => {
    button.addEventListener('click', () => {
        // Remove active class from all buttons
        document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
        // Hide all tab contents
        document.querySelectorAll('.tab-content').forEach(content => content.classList.add('hidden'));
        // Show the selected tab content
        const tabId = button.getAttribute('data-tab');
        document.getElementById(tabId).classList.remove('hidden');
        // Add active class to the clicked button
        button.classList.add('active');
    });
});
