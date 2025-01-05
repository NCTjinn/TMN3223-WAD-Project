// publicMenuProductPage.js
document.addEventListener('DOMContentLoaded', function() {
    // Handle thumbnail image switching if needed
    const thumbnails = document.querySelectorAll('.thumbnail');
    thumbnails.forEach(thumbnail => {
        thumbnail.addEventListener('click', function() {
            const mainImage = document.querySelector('.main-image img');
            mainImage.src = this.src;
        });
    });
});
