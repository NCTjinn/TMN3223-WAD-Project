// Event listener for DOM content loaded to fetch address details
document.addEventListener('DOMContentLoaded', fetchAddress);

// Function to fetch address details from the server
async function fetchAddress() {
    try {
        const response = await fetch('../api/memberAddresses.php', { method: 'GET' });
        if (!response.ok) throw new Error('Failed to fetch address');

        const addressData = await response.json();
        displayAddress(addressData);
    } catch (error) {
        console.error('Error:', error);
        alert('Error fetching address. Please check the console for details.');
    }
}

// Function to display address details in the UI
function displayAddress(data) {
    const addressElement = document.querySelector('.address-details');
    addressElement.innerHTML = `
        <h4>${data.type || 'Default Address'}</h4>
        <p>${data.address_line_1}, ${data.address_line_2 || ''}</p>
        <p>${data.city}, ${data.state} ${data.postcode}</p>
        <p>${data.country}</p>
        <p>Phone: ${data.phone}</p>
    `;
    document.querySelector('.edit-btn').onclick = () => openEditModal(data);
}

// Function to open the edit modal with pre-filled data
function openEditModal(data) {
    const modal = document.getElementById('address-modal');
    modal.style.display = 'flex';
    document.getElementById('address-line-1').value = data.address_line_1;
    document.getElementById('address-line-2').value = data.address_line_2 || '';
    document.getElementById('city').value = data.city;
    document.getElementById('state').value = data.state;
    document.getElementById('postcode').value = data.postcode;
    document.getElementById('country').value = data.country;
    document.getElementById('phone-number').value = data.phone;

    document.querySelector('.save-btn').onclick = function() {
        saveAddress(data.address_id);
    };
}

// Function to close the modal and clear form data
function closeModal() {
    document.getElementById('address-modal').style.display = 'none';
    clearForm();
}

// Function to clear form inputs in the modal
function clearForm() {
    document.getElementById('address-title').value = '';
    document.getElementById('address-line-1').value = '';
    document.getElementById('address-line-2').value = '';
    document.getElementById('city').value = '';
    document.getElementById('state').value = '';
    document.getElementById('postcode').value = '';
    document.getElementById('country').value = '';
    document.getElementById('phone-number').value = '';
}

// Event listeners for closing the modal
document.querySelector('.close-modal').addEventListener('click', closeModal);
document.querySelector('.cancel-btn').addEventListener('click', closeModal);

// Function to save updated address details to the server
async function saveAddress(addressId) {
    const addressLine1 = document.getElementById('address-line-1').value;
    const addressLine2 = document.getElementById('address-line-2').value;
    const city = document.getElementById('city').value;
    const state = document.getElementById('state').value;
    const postcode = document.getElementById('postcode').value;
    const country = document.getElementById('country').value;
    const phone = document.getElementById('phone-number').value;

    try {
        const response = await fetch('../api/memberAddresses.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                address_id: addressId,
                address_line_1: addressLine1,
                address_line_2: addressLine2,
                city: city,
                state: state,
                postcode: postcode,
                country: country,
                phone: phone
            }),
        });

        if (!response.ok) throw new Error('Failed to update address');

        alert('Address updated successfully!');
        window.location.reload(); // Reload the page to show the updated address
    } catch (error) {
        console.error('Error:', error);
        alert('Error updating address. Please check the console for details.');
    }
}
