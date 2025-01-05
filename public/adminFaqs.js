document.addEventListener("DOMContentLoaded", function () {
    const faqModal = document.getElementById("faqModal");
    const faqForm = document.getElementById("faqForm");
    const addFaqButton = document.getElementById("addFaqButton");
    const closeModalButtons = document.querySelectorAll(".close-modal, .cancel-btn");
    const faqContainer = document.getElementById("faqContainer");
    let currentFaqId = null;
    let newFaqId = null;

    // Profile dropdown functionality
    const profileIcon = document.getElementById('profile-icon');
    const profileDropdown = document.getElementById('dropdown-menu');

    profileIcon.addEventListener('click', (e) => {
        e.stopPropagation();
        profileDropdown.classList.toggle('active');
    });

    document.addEventListener('click', (e) => {
        if (!e.target.closest('.user-dropdown')) {
            profileDropdown.classList.remove('active');
        }
    });

    // Modal handlers
    addFaqButton.addEventListener("click", () => {
        currentFaqId = null;
        faqForm.reset();
        
        fetch("faqsmanagement.php?action=getMaxId")
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    newFaqId = data.data.maxId;
                    faqModal.classList.add("active");
                } else {
                    alert(data.message || "Error fetching max FAQ ID");
                }
            })
            .catch(error => console.error("Error fetching max FAQ ID:", error));
    });

    closeModalButtons.forEach(button => {
        button.addEventListener("click", () => {
            faqModal.classList.remove("active");
            newFaqId = null;
        });
    });

    // Form submission
    document.querySelector(".save-btn").addEventListener("click", function(e) {
        e.preventDefault();
        
        const formData = {
            question: document.getElementById("faqQuestion").value,
            answer: document.getElementById("faqAnswer").value,
            faq_id: currentFaqId ? currentFaqId : newFaqId
        };

        const action = currentFaqId ? 'update' : 'add';

        fetch(`faqsmanagement.php`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                action: action,
                faq_id: formData.faq_id,
                question: formData.question,
                answer: formData.answer
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                faqModal.classList.remove("active");
                newFaqId = null;
                fetchFAQs();
            } else {
                alert(data.message || "An error occurred");
            }
        })
        .catch(error => console.error("Error:", error));
    });

    // Fetch and render FAQs
    function fetchFAQs() {
        fetch("faqsmanagement.php?action=fetch")
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    faqContainer.innerHTML = data.data.map(faq => `
                        <div class="faq-item">
                            <div class="faq-content">
                                <div class="faq-question">${faq.question}</div>
                                <div class="faq-answer">${faq.answer}</div>
                            </div>
                            <div class="faq-actions">
                                <button class="edit-btn" data-id="${faq.faq_id}">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <button class="delete-btn" data-id="${faq.faq_id}">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </div>
                        </div>
                    `).join('');
                    attachEventListeners();
                } else {
                    alert(data.message || "Error fetching FAQs");
                }
            })
            .catch(error => {
                console.error("Error fetching FAQs:", error);
                alert("There was an error fetching the FAQs. Please try again later.");
            });
    }
    
    function attachEventListeners() {
        document.querySelectorAll(".edit-btn").forEach(btn => {
            btn.addEventListener("click", (e) => {
                const faqItem = e.target.closest('.faq-item');
                currentFaqId = btn.dataset.id;
                document.getElementById("faqQuestion").value = 
                    faqItem.querySelector(".faq-question").textContent;
                document.getElementById("faqAnswer").value = 
                    faqItem.querySelector(".faq-answer").textContent;
                faqModal.classList.add("active");
            });
        });

        /* Delete button event listener */
        document.querySelectorAll(".delete-btn").forEach(btn => {
            btn.addEventListener("click", () => {
                if (confirm("Are you sure you want to delete this FAQ?")) {
                    fetch("faqsmanagement.php", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json"
                        },
                        body: JSON.stringify({
                            action: 'delete',
                            faq_id: btn.dataset.id
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            fetchFAQs();
                        } else {
                            alert(data.message || "An error occurred");
                        }
                    })
                    .catch(error => console.error("Error:", error));
                }
            });
        });
    }

    // Initial fetch
    fetchFAQs();
});
