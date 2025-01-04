document.addEventListener('DOMContentLoaded', function () {
    let products = []; // Initialize products array

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

    let editIndex = null;
    let tempImage = '';

    // Add validation message container after each form field
    function addValidationMessage(inputElement) {
        const messageDiv = document.createElement('div');
        messageDiv.className = 'validation-message';
        messageDiv.style.color = '#d9534f';
        messageDiv.style.fontSize = '12px';
        messageDiv.style.marginTop = '5px';
        messageDiv.style.display = 'none';
        inputElement.parentNode.appendChild(messageDiv);
    }

    // Initialize validation messages
    function initializeValidationMessages() {
        const inputs = productForm.querySelectorAll('input, textarea');
        inputs.forEach(input => {
            addValidationMessage(input);
            
            // Add input event listener to hide message when user starts typing
            input.addEventListener('input', () => {
                const messageDiv = input.parentNode.querySelector('.validation-message');
                messageDiv.style.display = 'none';
            });
        });
    }

    // Show validation message for a specific field
    function showValidationMessage(inputElement, message) {
        const messageDiv = inputElement.parentNode.querySelector('.validation-message');
        messageDiv.textContent = message;
        messageDiv.style.display = 'block';

        // Highlight the input field
        inputElement.style.borderColor = '#d9534f';
        
        // Remove highlight when user starts typing
        inputElement.addEventListener('input', function removeHighlight() {
            inputElement.style.borderColor = '';
            inputElement.removeEventListener('input', removeHighlight);
        }, { once: true });
    }

    function validateForm() {
        let isValid = true;
        const requiredFields = {
            'productName': 'Product name is required',
            'productPrice': 'Price is required',
            'productStock': 'Stock quantity is required',
            'productDescription': 'Description is required'
        };
    
        // Clear all previous validation messages
        document.querySelectorAll('.validation-message').forEach(msg => {
            msg.style.display = 'none';
        });
    
        // Check each required field
        for (const [fieldId, message] of Object.entries(requiredFields)) {
            const field = document.getElementById(fieldId);
            if (!field.value.trim()) {
                showValidationMessage(field, message);
                isValid = false;
            }
        }
    
        // Validate price is positive
        const priceField = document.getElementById('productPrice');
        if (parseFloat(priceField.value) <= 0) {
            showValidationMessage(priceField, 'Price must be greater than 0');
            isValid = false;
        }
    
        // Validate stock is non-negative
        const stockField = document.getElementById('productStock');
        if (parseInt(stockField.value) < 0) {
            showValidationMessage(stockField, 'Stock cannot be negative');
            isValid = false;
        }
    
        // Validate image (only required for new products)
        if (!editIndex && !tempImage) {
            const imageMessage = uploadArea.querySelector('.validation-message') || 
                                   (addValidationMessage(uploadArea), uploadArea.querySelector('.validation-message'));
            imageMessage.textContent = 'Product image is required';
            imageMessage.style.display = 'block';
            isValid = false;
    
            // Add a popup warning (alert)
            alert('Product image is required!');
        }
    
        return isValid;
    }

    function showConfirmationMessage(message, type = 'success') {
        confirmationMessage.textContent = message;
        confirmationMessage.className = `confirmation-message ${type}`;
        confirmationMessage.classList.remove('hidden');
        setTimeout(() => confirmationMessage.classList.add('hidden'), 3000);
    }

    function renderProducts() {
        productTableBody.innerHTML = '';
        if (products.length === 0) {
            productTableBody.innerHTML = '<tr><td colspan="6">No products available.</td></tr>';
            return;
        }
        products.forEach((product, index) => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td><img src="${product.image_url || 'https://via.placeholder.com/50'}" alt="Product Image"></td>
                <td>${product.name}</td>
                <td>$${parseFloat(product.price).toFixed(2)}</td>
                <td>${product.stock_quantity}</td>
                <td>${product.description}</td>
                <td>
                    <button class="action-btn edit-btn" data-index="${index}">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <button class="action-btn delete-btn" data-index="${index}">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </td>
            `;
            productTableBody.appendChild(row);
        });
    }

    async function fetchProducts() {
        try {
            const response = await fetch('productmanagement.php?action=fetch');
            if (!response.ok) throw new Error('Network response was not ok');
            const data = await response.json();
            products = data;
            renderProducts();
        } catch (error) {
            console.error('Error fetching products:', error);
            showConfirmationMessage('Error loading products', 'error');
        }
    }

    function handleFile(file) {
        if (file && file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function () {
                tempImage = reader.result;
                imagePreview.src = tempImage;
                imagePreview.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        } else {
            alert('Please upload a valid image file.');
        }
    }

    function openModal(title = 'Add New Product', editing = false, product = null) {
        modalTitle.textContent = title;
        productModal.classList.add('active');
        
        if (editing && product) {
            productForm.productName.value = product.name;
            productForm.productPrice.value = parseFloat(product.price).toFixed(2);
            productForm.productStock.value = product.stock_quantity;
            productForm.productDescription.value = product.description;
            tempImage = product.image_url;
            if (tempImage) {
                imagePreview.src = tempImage;
                imagePreview.classList.remove('hidden');
            }
        } else {
            productForm.reset();
            imagePreview.classList.add('hidden');
            tempImage = '';
        }
    }

    function closeModal() {
        productModal.classList.remove('active');
        productForm.reset();
        imagePreview.src = '';
        imagePreview.classList.add('hidden');
        editIndex = null;
    }

    async function saveProduct(formData) {
        const action = editIndex !== null ? 'update' : 'add';
        try {
            const response = await fetch(`productmanagement.php?action=${action}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(formData)
            });
    
            const data = await response.json();
            if (!response.ok) {
                throw new Error(data.error || 'Failed to save product');
            }
    
            if (data.success) {
                showConfirmationMessage(data.message);
                await fetchProducts(); // Refresh the product list
                closeModal();
                return true;
            } else {
                throw new Error(data.error || 'Failed to save product');
            }
        } catch (error) {
            console.error('Error:', error);
            showConfirmationMessage(`Error: ${error.message}`, 'error');
            throw error;
        }
    }

    async function deleteProduct(productId) {
        try {
            const response = await fetch(`productmanagement.php?action=delete`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ product_id: productId })
            });

            const data = await response.json();
            if (!response.ok) {
                throw new Error(data.error || 'Failed to delete product');
            }

            if (data.success) {
                await fetchProducts();
                showConfirmationMessage(data.message);
            } else {
                throw new Error(data.error || 'Failed to delete product');
            }
        } catch (error) {
            console.error('Error:', error);
            showConfirmationMessage(`Error: ${error.message}`, 'error');
        }
    }

    // Event Listeners
    addProductBtn.addEventListener('click', () => openModal('Add New Product'));
    cancelModal.addEventListener('click', closeModal);

    productForm.addEventListener('submit', async (e) => {
        e.preventDefault();
    
        const formData = {
            name: productForm.productName.value.trim(),
            price: parseFloat(productForm.productPrice.value),
            stock_quantity: parseInt(productForm.productStock.value),
            description: productForm.productDescription.value.trim(),
            image_url: tempImage || 'https://via.placeholder.com/50',
            category_id: 1 // Default category
        };
    
        if (editIndex !== null) {
            formData.product_id = products[editIndex].product_id;
        }
    
        try {
            await saveProduct(formData);
        } catch (error) {
            console.error('Error saving product:', error);
        }
    });

    // Image upload events
    productImageInput.addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (file) handleFile(file);
    });

    uploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadArea.classList.add('drag-over');
    });

    uploadArea.addEventListener('dragleave', () => {
        uploadArea.classList.remove('drag-over');
    });

    uploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadArea.classList.remove('drag-over');
        const file = e.dataTransfer.files[0];
        if (file) handleFile(file);
    });

    uploadArea.addEventListener('click', () => {
        productImageInput.click();
    });

    // Handle product actions (Edit/Delete)
    productTableBody.addEventListener('click', async (e) => {
        const button = e.target.closest('.action-btn');
        if (!button) return;

        const index = parseInt(button.dataset.index);
        const product = products[index];

        if (button.classList.contains('edit-btn')) {
            editIndex = index;
            openModal('Edit Product', true, product);
        } else if (button.classList.contains('delete-btn')) {
            if (confirm('Are you sure you want to delete this product?')) {
                await deleteProduct(product.product_id);
            }
        }
    });
    // Initialize validation messages when the page loads
    initializeValidationMessages();
    // Initialize products table
    fetchProducts();
});
