document.addEventListener('DOMContentLoaded', function() {
    // Modal Elements
    const faqModal = document.getElementById('faqModal');
    const sectionModal = document.getElementById('sectionModal');
    const closeButtons = document.querySelectorAll('.close-modal');
    const cancelButtons = document.querySelectorAll('.cancel-btn');

    // Initialize FAQ data from localStorage or set default
    let faqData = JSON.parse(localStorage.getItem('faqData')) || {
        sections: Array.from(document.querySelectorAll('.faq-section')).map(section => ({
            id: section.id || 'section-' + Date.now(),
            title: section.querySelector('.section-title').firstChild.textContent.trim(),
            faqs: Array.from(section.querySelectorAll('.faq-item')).map(faq => ({
                id: faq.id || 'faq-' + Date.now(),
                question: faq.querySelector('.faq-question').textContent,
                answer: faq.querySelector('.faq-answer').textContent
            }))
        }))
    };

    // Save data to localStorage
    function saveData() {
        localStorage.setItem('faqData', JSON.stringify(faqData));
    }

    // Render FAQ sections from data
    function renderFAQs() {
        const mainContent = document.querySelector('.admin-content');
        mainContent.innerHTML = '<h1>FAQ Management</h1>';

        faqData.sections.forEach(section => {
            const sectionElement = document.createElement('div');
            sectionElement.className = 'faq-section';
            sectionElement.id = section.id;

            sectionElement.innerHTML = `
                <div class="section-header">
                    <h2 class="section-title">
                        ${section.title}
                        <button class="edit-section-btn">
                            <i class="fas fa-edit"></i> Edit Section
                        </button>
                    </h2>
                </div>
            `;

            section.faqs.forEach(faq => {
                const faqItem = createFaqItem(faq.question, faq.answer);
                faqItem.id = faq.id;
                sectionElement.appendChild(faqItem);
            });

            const addButton = document.createElement('button');
            addButton.className = 'add-faq-btn';
            addButton.id = 'add-btn-' + section.id;
            addButton.innerHTML = '<i class="fas fa-plus"></i> Add New FAQ';
            sectionElement.appendChild(addButton);

            mainContent.appendChild(sectionElement);
        });

        // Reattach event listeners
        attachEventListeners();
    }

    // Create new FAQ item
    function createFaqItem(question, answer) {
        const faqItem = document.createElement('div');
        faqItem.className = 'faq-item';
        faqItem.id = 'faq-' + Date.now();

        faqItem.innerHTML = `
            <div class="faq-content">
                <div class="faq-question">${question}</div>
                <div class="faq-answer">${answer}</div>
            </div>
            <div class="faq-actions">
                <button class="edit-btn">
                    <i class="fas fa-edit"></i> Edit
                </button>
                <button class="delete-btn">
                    <i class="fas fa-trash"></i> Delete
                </button>
            </div>
        `;

        return faqItem;
    }

    // Attach event listeners to all interactive elements
    function attachEventListeners() {
        // Edit FAQ Buttons
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', () => {
                const faqItem = button.closest('.faq-item');
                const question = faqItem.querySelector('.faq-question').textContent;
                const answer = faqItem.querySelector('.faq-answer').textContent;

                document.getElementById('faqQuestion').value = question;
                document.getElementById('faqAnswer').value = answer;
                faqModal.style.display = 'block';
                faqModal.dataset.editingFaq = faqItem.id;
                faqModal.dataset.sectionId = faqItem.closest('.faq-section').id;
            });
        });

        // Edit Section Buttons
        document.querySelectorAll('.edit-section-btn').forEach(button => {
            button.addEventListener('click', () => {
                const section = button.closest('.faq-section');
                const sectionTitle = section.querySelector('.section-title').firstChild.textContent.trim();
                document.getElementById('sectionTitle').value = sectionTitle;
                sectionModal.style.display = 'block';
                sectionModal.dataset.editingSection = section.id;
            });
        });

        // Delete FAQ Buttons
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', () => {
                if (confirm('Are you sure you want to delete this FAQ?')) {
                    const faqItem = button.closest('.faq-item');
                    const sectionId = faqItem.closest('.faq-section').id;
                    const sectionIndex = faqData.sections.findIndex(s => s.id === sectionId);
                    const faqIndex = faqData.sections[sectionIndex].faqs.findIndex(f => f.id === faqItem.id);
                    
                    faqData.sections[sectionIndex].faqs.splice(faqIndex, 1);
                    saveData();
                    renderFAQs();
                }
            });
        });

        // Add New FAQ Buttons
        document.querySelectorAll('.add-faq-btn').forEach(button => {
            button.addEventListener('click', () => {
                document.getElementById('faqQuestion').value = '';
                document.getElementById('faqAnswer').value = '';
                faqModal.style.display = 'block';
                delete faqModal.dataset.editingFaq;
                faqModal.dataset.sectionId = button.closest('.faq-section').id;
            });
        });
    }

    // Close Modals
    function closeModals() {
        faqModal.style.display = 'none';
        sectionModal.style.display = 'none';
        delete faqModal.dataset.editingFaq;
        delete faqModal.dataset.sectionId;
        delete sectionModal.dataset.editingSection;
    }

    closeButtons.forEach(button => {
        button.addEventListener('click', closeModals);
    });

    cancelButtons.forEach(button => {
        button.addEventListener('click', closeModals);
    });

    // Close modal when clicking outside
    window.addEventListener('click', (event) => {
        if (event.target === faqModal || event.target === sectionModal) {
            closeModals();
        }
    });

    // Handle form submissions
    document.querySelectorAll('.modal-form').forEach(form => {
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            
            if (form.closest('#faqModal')) {
                const question = document.getElementById('faqQuestion').value;
                const answer = document.getElementById('faqAnswer').value;
                const sectionId = faqModal.dataset.sectionId;
                const sectionIndex = faqData.sections.findIndex(s => s.id === sectionId);
                
                if (faqModal.dataset.editingFaq) {
                    // Edit existing FAQ
                    const faqId = faqModal.dataset.editingFaq;
                    const faqIndex = faqData.sections[sectionIndex].faqs.findIndex(f => f.id === faqId);
                    faqData.sections[sectionIndex].faqs[faqIndex] = {
                        id: faqId,
                        question,
                        answer
                    };
                } else {
                    // Add new FAQ
                    const newFaq = {
                        id: 'faq-' + Date.now(),
                        question,
                        answer
                    };
                    faqData.sections[sectionIndex].faqs.push(newFaq);
                }
            } else if (form.closest('#sectionModal')) {
                // Edit section title
                const sectionId = sectionModal.dataset.editingSection;
                const sectionIndex = faqData.sections.findIndex(s => s.id === sectionId);
                faqData.sections[sectionIndex].title = document.getElementById('sectionTitle').value;
            }
            
            saveData();
            renderFAQs();
            closeModals();
        });
    });

    // Initial render
    renderFAQs();
});