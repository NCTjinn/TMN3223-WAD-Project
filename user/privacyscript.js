document.querySelector('.search-btn').addEventListener('click', function(e) {
    var input = document.querySelector('.search-txt');
    if (input.classList.contains('open')) {
        // If already open and clicked, do nothing (or close if you prefer)
        input.classList.remove('open');
        input.style.width = '0';
        input.style.opacity = '0';
        input.style.visibility = 'hidden';
    } else {
        // Open the search box and keep it open
        input.classList.add('open');
        input.style.width = '200px';
        input.style.opacity = '1';
        input.style.visibility = 'visible';
        input.focus();
    }
    e.preventDefault(); // Prevent default link behavior
});

document.addEventListener('click', function(e) {
    var input = document.querySelector('.search-txt');
    if (!input.contains(e.target) && !document.querySelector('.search-btn').contains(e.target)) {
        // If clicking outside of input and button, close it
        input.classList.remove('open');
        input.style.width = '0';
        input.style.opacity = '0';
        input.style.visibility = 'hidden';
    }
});
