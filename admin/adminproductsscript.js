document.addEventListener('DOMContentLoaded', function () {
    const profileIcon = document.getElementById('profile-icon');
    const profileDropdown = document.getElementById('dropdown-menu');
    const notificationIcon = document.querySelector('.fa-bell');
    const notificationDropdown = document.createElement('div');

    // Dynamically create notification dropdown
    notificationDropdown.className = 'dropdown-menu';
    notificationDropdown.id = 'notification-dropdown';

    // Notifications data
    const notifications = [
        { message: "New user registered", isRead: false },
        { message: "System backup completed", isRead: true },
        { message: "New comment on blog post", isRead: false },
    ];

    // Render notifications
    function renderNotifications() {
        notificationDropdown.innerHTML = `
            <div class="dropdown-header">Notifications</div>
            <button class="mark-all-btn">Mark All as Read</button>
        `;

        notifications.forEach((notification, index) => {
            const unreadClass = notification.isRead
                ? ''
                : `<span class="unread-indicator"></span>`;
            notificationDropdown.innerHTML += `
                <div class="notification-item" data-index="${index}">
                    ${unreadClass}
                    <div class="notification-text">
                        <span class="notification-title">${notification.message}</span>
                        <span class="notification-time">5 mins ago</span>
                    </div>
                </div>
            `;
        });

        const markAllBtn = notificationDropdown.querySelector('.mark-all-btn');
        markAllBtn.addEventListener('click', () => {
            notifications.forEach((n) => (n.isRead = true));
            renderNotifications();
        });

        const notificationItems = notificationDropdown.querySelectorAll('.notification-item');
        notificationItems.forEach((item) => {
            item.addEventListener('click', (e) => {
                const index = e.currentTarget.getAttribute('data-index');
                notifications[index].isRead = true;
                renderNotifications();
            });
        });
    }

    // Append notification dropdown to the body
    document.body.appendChild(notificationDropdown);

    // Position notification dropdown
    function positionNotificationDropdown() {
        const rect = notificationIcon.getBoundingClientRect();
        let dropdownTop = rect.bottom + window.scrollY + 10; // Add spacing
        let dropdownLeft = rect.left;

        if (dropdownTop + 300 > window.innerHeight + window.scrollY) {
            dropdownTop = rect.top + window.scrollY - 300 - 10;
        }
        if (dropdownLeft + 300 > window.innerWidth) {
            dropdownLeft = window.innerWidth - 310;
        }

        notificationDropdown.style.position = 'absolute';
        notificationDropdown.style.top = `${dropdownTop}px`;
        notificationDropdown.style.left = `${dropdownLeft}px`;
    }

    // Toggle notification dropdown
    notificationIcon.addEventListener('click', function (e) {
        e.stopPropagation();
        const isActive = notificationDropdown.classList.contains('active');
        closeAllDropdowns();
        if (!isActive) {
            positionNotificationDropdown();
            notificationDropdown.classList.add('active');
        }
    });

    // Toggle profile dropdown
    profileIcon.addEventListener('click', function (e) {
        e.stopPropagation();
        const isActive = profileDropdown.classList.contains('active');
        closeAllDropdowns();
        if (!isActive) {
            profileDropdown.classList.add('active');
        }
    });

    // Close all dropdowns when clicking outside
    document.addEventListener('click', function (e) {
        if (
            !e.target.closest('.user-dropdown') &&
            !e.target.closest('.icon') &&
            !e.target.closest('#notification-dropdown')
        ) {
            closeAllDropdowns();
        }
    });

    // Helper function to close all dropdowns
    function closeAllDropdowns() {
        notificationDropdown.classList.remove('active');
        profileDropdown.classList.remove('active');
    }

    // Update notification badge
    function updateNotificationBadge(count) {
        const badge = document.getElementById('notification-badge');
        
        if (count > 0) {
            badge.textContent = count; // Update badge number
            badge.style.visibility = 'visible'; // Show the badge
        } else {
            badge.style.visibility = 'hidden'; // Hide the badge
        }
    }
    
    // Simulate fetching unread notifications
    function fetchNotifications() {
        // Replace this with your actual API call
        fetch('/api/notifications/unread')
            .then((response) => response.json())
            .then((data) => {
                const unreadCount = data.unreadCount || 0; // Default to 0 if no unreadCount is present
                updateNotificationBadge(unreadCount);
            })
            .catch(console.error);
    }
    
    // Call the function periodically or on page load
    fetchNotifications();
    setInterval(fetchNotifications, 60000); // Check every 60 seconds

    // Initial render of notifications
    renderNotifications();

    const addProductBtn = document.getElementById('addProductBtn');
    const productModal = document.getElementById('productModal');
    const cancelModal = document.getElementById('cancelModal');
    const productForm = document.getElementById('productForm');
    const productTableBody = document.getElementById('productTableBody');
    const confirmationMessage = document.getElementById('confirmation-message');
    const productImageInput = document.getElementById('productImage');
    const uploadArea = document.getElementById('uploadArea');
    const imagePreview = document.getElementById('imagePreview');
    const modalTitle = document.getElementById('modalTitle');
    const historyLogBtn = document.getElementById('historyLogBtn');
    const historyModal = document.getElementById('historyModal');
    const historyTableBody = document.getElementById('historyTableBody');
    const closeHistoryModal = document.getElementById('closeHistoryModal');

    let editIndex = null; // Tracks product being edited
    let tempImage = ''; // Temporary image URL for new uploads

    // Dummy products for initial rendering
    const products = [
        {
            id: 1,
            image: 'https://via.placeholder.com/50',
            name: 'Sample Product 1',
            price: 10.99,
            stock: 20,
            description: 'A description of Sample Product 1.',
        },
        {
            id: 2,
            image: 'https://via.placeholder.com/50',
            name: 'Sample Product 2',
            price: 15.49,
            stock: 0,
            description: 'A description of Sample Product 2.',
        },
    ];
    const history = []; // Track deleted products

    // Show confirmation message
    function showConfirmationMessage(message, type = 'success') {
        confirmationMessage.textContent = message;
        confirmationMessage.className = `confirmation-message ${type}`;
        confirmationMessage.classList.remove('hidden');
        setTimeout(() => confirmationMessage.classList.add('hidden'), 3000);
    }

    // Render the products table
    function renderProducts() {
        productTableBody.innerHTML = '';
        if (products.length === 0) {
            productTableBody.innerHTML = '<tr><td colspan="6">No products available.</td></tr>';
            return;
        }
        products.forEach((product, index) => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td><img src="${product.image}" alt="Product Image"></td>
                <td>${product.name}</td>
                <td>${product.price.toFixed(2)}</td>
                <td>${product.stock}</td>
                <td>${product.description}</td>
                <td>
                    <button class="action-btn edit-btn" data-index="${index}">Edit</button>
                    <button class="action-btn delete-btn" data-index="${index}">Delete</button>
                </td>
            `;
            productTableBody.appendChild(row);
        });
    }

    // Render the history log
    function renderHistory() {
        historyTableBody.innerHTML = '';
        if (history.length === 0) {
            historyTableBody.innerHTML = '<tr><td colspan="4">No history available.</td></tr>';
            return;
        }
        history.forEach((entry, index) => {
            const [date, time] = entry.deletedAt.split(' ');
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${date}</td>
                <td>${time}</td>
                <td>${entry.product.name}</td>
                <td>
                    <button class="action-btn restore-btn" data-index="${index}">Restore</button>
                </td>
            `;
            historyTableBody.appendChild(row);
        });
    }

    // Open modal for adding or editing products
    function openModal(title = 'Add New Product', editing = false, product = null) {
        modalTitle.textContent = title;
        productModal.classList.add('active');

        if (editing && product) {
            productForm.productName.value = product.name;
            productForm.productPrice.value = product.price.toFixed(2);
            productForm.productStock.value = product.stock;
            productForm.productDescription.value = product.description;
            tempImage = product.image; // Ensure existing image is stored in tempImage if editing
            imagePreview.src = tempImage;
            imagePreview.classList.remove('hidden');
        } else {
            productForm.reset();
            imagePreview.classList.add('hidden');
            tempImage = ''; // Clear tempImage if not editing
        }
    }

    

    // Close modal
    function closeModal() {
        productModal.classList.remove('active');
        productImageInput.value = '';
        imagePreview.src = '';
        imagePreview.classList.add('hidden');
        editIndex = null; // Reset editIndex
    }

    // Open the history modal
    function openHistoryModal() {
        renderHistory();
        historyModal.classList.add('active');
    }

    // Close the history modal
    function closeHistoryModalHandler() {
        historyModal.classList.remove('active');
    }

    // Handle File Selection via Click or Drag-and-Drop
    function handleFile(file) {
        if (file && file.type.startsWith('image/')) { // Ensure it's an image
            const reader = new FileReader();
            reader.onload = function () {
                tempImage = reader.result; // Store the uploaded image as Base64
                imagePreview.src = tempImage; // Update the preview image source
                imagePreview.classList.remove('hidden'); // Show the preview
            };
            reader.readAsDataURL(file); // Read the file as a data URL
        } else {
            alert('Please upload a valid image file.');
        }
    }
    // Handle File Input Change
    productImageInput.addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (file) {
            handleFile(file);
        }
    });

    // Drag-and-Drop Support
    uploadArea.addEventListener('dragover', (e) => {
        e.preventDefault(); // Prevent default behavior to allow drop
        uploadArea.classList.add('drag-over');
    });

    uploadArea.addEventListener('dragleave', () => {
        uploadArea.classList.remove('drag-over');
    });

    uploadArea.addEventListener('drop', (e) => {
        e.preventDefault(); // Prevent default behavior
        uploadArea.classList.remove('drag-over');
        const file = e.dataTransfer.files[0];
        if (file) {
            handleFile(file);
        }
    });

    // Click Event to Trigger File Input
    uploadArea.addEventListener('click', () => {
        productImageInput.click();
    });
    

    // Save product (Add or Edit)
    productForm.addEventListener('submit', (e) => {
        e.preventDefault();

        const newProduct = {
            id: editIndex !== null ? products[editIndex].id : Date.now(),
            image: tempImage || (editIndex !== null && products[editIndex].image ? products[editIndex].image : 'https://via.placeholder.com/50'),
            name: productForm.productName.value,
            price: parseFloat(productForm.productPrice.value),
            stock: parseInt(productForm.productStock.value, 10),
            description: productForm.productDescription.value,
        };

        if (editIndex !== null) {
            products[editIndex] = newProduct;
            showConfirmationMessage('Product updated successfully!');
        } else {
            products.push(newProduct);
            showConfirmationMessage('Product added successfully!');
        }

        renderProducts();
        closeModal();
    });


// When opening modal for editing, ensure the existing image is shown even if no new image is uploaded
// Handle product actions (Edit/Delete)
productTableBody.addEventListener('click', (e) => {
    const index = e.target.getAttribute('data-index');

    if (e.target.classList.contains('edit-btn')) {
        const product = products[parseInt(index, 10)];
        editIndex = parseInt(index, 10); // Set the edit index

        openModal('Edit Product', true, product);
    } else if (e.target.classList.contains('delete-btn')) {
        if (confirm('Are you sure you want to delete this product?')) {
            const deletedAt = new Date().toLocaleString();
            history.push({ product: products.splice(index, 1)[0], deletedAt });
            renderProducts();
            showConfirmationMessage('Product deleted successfully!', 'error');
        }
    }
});


    // Handle history actions (Restore)
    historyTableBody.addEventListener('click', (e) => {
        const index = e.target.getAttribute('data-index');
        if (e.target.classList.contains('restore-btn')) {
            const restoredProduct = history.splice(index, 1)[0].product;
            products.push(restoredProduct);
            renderProducts();
            renderHistory();
            showConfirmationMessage('Product restored successfully!');
        }
    });

    // Event listeners
    addProductBtn.addEventListener('click', () => openModal());
    cancelModal.addEventListener('click', closeModal);
    historyLogBtn.addEventListener('click', openHistoryModal);
    closeHistoryModal.addEventListener('click', closeHistoryModalHandler);

    // Initial render
    renderProducts();
});
