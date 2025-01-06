// faq.js
document.addEventListener('DOMContentLoaded', function() {
    const faqSection = document.querySelector('.faq-section');
    const loadingSpinner = document.getElementById('loading-spinner');

    // Function to show error messages
    function showError(message) {
        faqSection.innerHTML = `
            <div class="error-message">
                <p>${message}</p>
                <button onclick="location.reload()" class="retry-button">Try Again</button>
            </div>
        `;
    }

    // Function to show success message
    function showSuccess(message) {
        const successDiv = document.createElement('div');
        successDiv.className = 'success-message';
        successDiv.textContent = message;
        document.body.appendChild(successDiv);
        setTimeout(() => successDiv.remove(), 3000);
    }

    // Function to create FAQ HTML element
    function createFaqElement(faq) {
        const faqItem = document.createElement('div');
        faqItem.className = 'faq-item';
        faqItem.dataset.faqId = faq.faq_id;
        faqItem.innerHTML = `
            <div class="faq-question">
                <h3>${faq.question}</h3>
                <i class="fa fa-chevron-down"></i>
            </div>
            <div class="faq-answer">
                <p>${faq.answer}</p>
            </div>
        `;

        // Add click event for expand/collapse
        const questionDiv = faqItem.querySelector('.faq-question');
        const answerDiv = faqItem.querySelector('.faq-answer');
        const icon = questionDiv.querySelector('i');

        questionDiv.addEventListener('click', () => {
            const isExpanded = faqItem.classList.contains('active');

            // Close all other FAQs
            document.querySelectorAll('.faq-item').forEach(item => {
                if (item !== faqItem) {
                    item.classList.remove('active');
                    item.querySelector('.faq-answer').style.maxHeight = null;
                    item.querySelector('i').className = 'fa fa-chevron-down';
                }
            });

            // Toggle current FAQ
            if (!isExpanded) {
                faqItem.classList.add('active');
                answerDiv.style.maxHeight = answerDiv.scrollHeight + "px";
                icon.className = 'fa fa-chevron-up';
            } else {
                faqItem.classList.remove('active');
                answerDiv.style.maxHeight = null;
                icon.className = 'fa fa-chevron-down';
            }
        });

        return faqItem;
    }

    // Function to fetch and display FAQs
    async function fetchAndDisplayFaqs() {
        try {
            const response = await fetch('faqsmanagement.php?action=fetch');
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            
            const data = await response.json();
            
            if (loadingSpinner) {
                loadingSpinner.style.display = 'none';
            }

            // Check if the response has the expected structure
            if (!data || !data.status === 'success' || !Array.isArray(data.data)) {
                throw new Error('Invalid response format');
            }

            const faqs = data.data;

            if (faqs.length === 0) {
                faqSection.innerHTML = '<p class="empty-message">No FAQs available at the moment.</p>';
                return;
            }

            const faqContainer = document.createElement('div');
            faqContainer.className = 'faq-container';
            
            faqs.forEach(faq => {
                const faqElement = createFaqElement(faq);
                faqContainer.appendChild(faqElement);
            });

            faqSection.innerHTML = '';
            faqSection.appendChild(faqContainer);

        } catch (error) {
            console.error('Error fetching FAQs:', error);
            showError('Unable to load FAQs. Please try again later.');
        }
    }

    // Initialize FAQ display
    fetchAndDisplayFaqs();
});