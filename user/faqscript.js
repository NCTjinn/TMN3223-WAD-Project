// Select all FAQ questions
document.querySelectorAll('.faq-question').forEach((question) => {
    question.addEventListener('click', () => {
        const faqItem = question.parentElement; // Get the parent element (FAQ item)

        // If the clicked FAQ item is active, close it
        if (faqItem.classList.contains('active')) {
            faqItem.classList.remove('active'); // Remove the active state
            const answer = faqItem.querySelector('.faq-answer');
            answer.style.display = 'none'; // Hide the answer
        } else {
            // Close any currently open FAQ items
            document.querySelectorAll('.faq-item.active').forEach((item) => {
                item.classList.remove('active'); // Remove the active class
                const openAnswer = item.querySelector('.faq-answer');
                openAnswer.style.display = 'none'; // Hide the answer
            });

            // Open the clicked FAQ item
            faqItem.classList.add('active'); // Add the active state
            const answer = faqItem.querySelector('.faq-answer');
            answer.style.display = 'block'; // Show the answer
        }
    });
});
