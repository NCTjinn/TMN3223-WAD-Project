document.addEventListener('DOMContentLoaded', function() {
    // Fetch address details
    fetchAddress();

    // Set up button event listeners
    setUpModalEventListeners();
});

async function fetchAddress() {
    try {
        const response = await fetch('../api/memberAddresses.php', { method: 'GET' });
        if (!response.ok) throw new Error('Failed to fetch address');

        const addressData = await response.json();
        displayAddress(addressData);
    } catch (error) {
        console.error('Error fetching address:', error);
        alert('Error fetching address. Please check the console for details.');
    }
}

function displayAddress(data) {
    const addressElement = document.querySelector('.address-details');
    addressElement.innerHTML = `
        <h4>${data.type || 'Default Address'}</h4>
        <p>${data.address_line_1}, ${data.address_line_2 || ''}</p>
        <p>${data.city}, ${data.state} ${data.postcode}</p>
        <p>${data.country}</p>
    `;
    document.querySelector('.edit-btn').onclick = () => openEditModal(data);
}

function openEditModal(data) {
    const modal = document.getElementById('address-modal');
    modal.style.display = 'flex';
    document.getElementById('address-line-1').value = data.address_line_1;
    document.getElementById('address-line-2').value = data.address_line_2 || '';
    document.getElementById('city').value = data.city;
    document.getElementById('state').value = data.state;
    document.getElementById('postcode').value = data.postcode;
    document.getElementById('country').value = data.country;

    document.querySelector('.save-btn').onclick = function() {
        saveAddress(data.address_id);
    };
}

function closeModal() {
    document.getElementById('address-modal').style.display = 'none';
    clearForm();
}

function clearForm() {
    document.getElementById('address-line-1').value = '';
    document.getElementById('address-line-2').value = '';
    document.getElementById('city').value = '';
    document.getElementById('state').value = '';
    document.getElementById('postcode').value = '';
    document.getElementById('country').value = '';
}

function setUpModalEventListeners() {
    document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', function() {
            const modal = document.getElementById('address-modal');
            modal.style.display = 'flex';
        });
    });

    document.querySelector('.close-modal').addEventListener('click', function() {
        closeModal();
    });

    document.querySelector('.cancel-btn').addEventListener('click', function() {
        closeModal();
    });

    window.addEventListener('click', function(event) {
        const modal = document.getElementById('address-modal');
        if (event.target === modal) {
            closeModal();
        }
    });
}

async function saveAddress(addressId) {
    const addressLine1 = document.getElementById('address-line-1').value;
    const addressLine2 = document.getElementById('address-line-2').value;
    const city = document.getElementById('city').value;
    const state = document.getElementById('state').value;
    const postcode = document.getElementById('postcode').value;
    const country = document.getElementById('country').value;

    try {
        const response = await fetch('../api/memberAddresses.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({
                address_id: addressId,
                address_line_1: addressLine1,
                address_line_2: addressLine2,
                city: city,
                state: state,
                postcode: postcode,
                country: country,
            }),
        });

        const result = await response.json();
        console.log("Response status:", response.ok);
        console.log("Server response:", result);

        if (response.ok && result.success) {
            alert('Address updated successfully!');
            window.location.reload(); // Refresh to show new data
        } else {
            throw new Error(result.message || 'Failed to update address.');
        }
    } catch (error) {
        console.error('Error updating address:', error);
        alert('Address updated successfully!');
    }
}
