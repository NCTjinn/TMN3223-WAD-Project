// Open the modal when "Add New Address" is clicked
document.querySelector('.add-btn').addEventListener('click', () => {
    document.getElementById('address-modal').style.display = 'flex';
});

// Close the modal when "X" or "Cancel" is clicked
document.querySelector('.close-modal').addEventListener('click', () => {
    closeModal();
});

document.querySelector('.cancel-btn').addEventListener('click', () => {
    closeModal();
});

// Function to close the modal
function closeModal() {
    document.getElementById('address-modal').style.display = 'none';
    clearForm();
}

document.querySelector('.save-btn').addEventListener('click', async function (e) {
    e.preventDefault(); // Prevent the default form behavior

    // Collect form data
    const addressTitle = document.getElementById('address-title').value;
    const addressLine = document.getElementById('address-line').value;
    const phoneNumber = document.getElementById('phone-number').value;

    // Validate inputs
    if (addressTitle && addressLine && phoneNumber) {
        try {
            // Send data to the backend (assuming an API endpoint exists)
            const response = await fetch('/api/addresses', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    title: addressTitle,
                    address: addressLine,
                    phone: phoneNumber,
                }),
            });

            if (!response.ok) {
                const error = await response.json();
                console.error('Error:', error);
                alert(`Failed to save address: ${error.error || 'Unknown error'}`);
                return;
            }

            // If the backend responds successfully
            const newAddress = await response.json();

            // Update the frontend with the new address
            const addressCard = document.createElement('div');
            addressCard.classList.add('address-card');
            addressCard.innerHTML = `
                <div class="address-icon">
                    <i class="fa fa-map-marker-alt"></i>
                </div>
                <div class="address-details">
                    <h4>${newAddress.title}</h4>
                    <p>${newAddress.address}</p>
                    <p>Phone: ${newAddress.phone}</p>
                </div>
                <div class="address-actions">
                    <button class="edit-btn"><i class="fa fa-edit"></i> Edit</button>
                    <button class="delete-btn"><i class="fa fa-trash"></i> Delete</button>
                </div>
            `;

            document.querySelector('.addresses-list').appendChild(addressCard);

            // Close the modal
            document.getElementById('address-modal').style.display = 'none';

            // Clear the form inputs
            document.getElementById('address-title').value = '';
            document.getElementById('address-line').value = '';
            document.getElementById('phone-number').value = '';
        } catch (error) {
            console.error('Fetch Error:', error);
            alert('Failed to save address. Please check the console for details.');
        }
    } else {
        alert('Please fill in all fields!');
    }
});



// Clear the form inputs
function clearForm() {
    document.getElementById('address-title').value = '';
    document.getElementById('address-line').value = '';
    document.getElementById('phone-number').value = '';
}
