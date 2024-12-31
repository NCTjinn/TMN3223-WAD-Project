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
        fetch('/api/admin/notifications')
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


    //main content
    // Initialize Chart.js
    const ctx = document.getElementById('transactionChart').getContext('2d');
    const transactionChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'], // Dynamic labels
            datasets: [{
                label: 'Gross Sales',
                data: [500, 700, 800, 600, 1000, 1200], // Dynamic data
                borderColor: '#6c7a5d',
                backgroundColor: 'rgba(108, 122, 93, 0.2)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            }
        }
    });

    // Filter table functionality
    const searchInput = document.getElementById('searchTransactions');
    const tableRows = document.querySelectorAll('#transactionTable tbody tr');
    searchInput.addEventListener('input', function () {
        const searchText = searchInput.value.toLowerCase();
        tableRows.forEach(row => {
            const rowText = row.textContent.toLowerCase();
            row.style.display = rowText.includes(searchText) ? '' : 'none';
        });
    });

    // Export chart
    window.exportChart = function () {
        const link = document.createElement('a');
        link.download = 'transaction-overview.png';
        link.href = transactionChart.toBase64Image();
        link.click();
    };

    // Print chart
    window.printChart = function () {
        const win = window.open();
        win.document.write('<img src="' + transactionChart.toBase64Image() + '">');
        win.print();
    };

    // Save chart (placeholder for backend integration)
    window.saveChart = function () {
        alert('Saving chart functionality coming soon!');
    };

    // Functionality for the "Edit Columns" button
    window.customizeColumns = function () {
        const table = document.getElementById('transactionTable');
        const columnHeaders = Array.from(table.querySelectorAll('thead th'));
        const bodyRows = Array.from(table.querySelectorAll('tbody tr'));
    
        // Create a modal for selecting columns
        const columnOptions = columnHeaders.map((header, index) => {
            return `
                <label>
                    <input type="checkbox" data-index="${index}">
                    ${header.textContent}
                </label>
            `;
        }).join('<br>');
    
        const modalContent = `
            <div id="editColumnsModal" style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); z-index: 1000;">
                <h3 style="margin-bottom: 10px;">Select Columns to Edit</h3>
                ${columnOptions}
                <button id="applyEdit" style="margin-top: 10px; padding: 10px 15px; background-color: #6c7a5d; color: white; border: none; border-radius: 4px; cursor: pointer;">Apply</button>
                <button id="cancelEdit" style="margin-top: 10px; margin-left: 10px; padding: 10px 15px; background-color: #ff5c5c; color: white; border: none; border-radius: 4px; cursor: pointer;">Cancel</button>
            </div>
        `;
    
        const modal = document.createElement('div');
        modal.innerHTML = modalContent;
        document.body.appendChild(modal);
    
        // Add event listeners for the modal buttons
        document.getElementById('applyEdit').addEventListener('click', function () {
            const checkboxes = modal.querySelectorAll('input[type="checkbox"]');
    
            // Enable editing for selected columns
            checkboxes.forEach((checkbox, index) => {
                const isEditable = checkbox.checked;
                if (isEditable) {
                    columnHeaders[index].classList.add('editable-column');
                    bodyRows.forEach(row => {
                        row.cells[index].contentEditable = 'true';
                        row.cells[index].style.backgroundColor = '#F9FAEB';
                    });
                } else {
                    columnHeaders[index].classList.remove('editable-column');
                    bodyRows.forEach(row => {
                        row.cells[index].contentEditable = 'false';
                        row.cells[index].style.backgroundColor = ''; // Reset background
                    });
                }
            });
    
            // Show the "Save Changes" button
            document.getElementById('saveChanges').style.display = 'block';
    
            modal.remove(); // Close modal
        });
    
        document.getElementById('cancelEdit').addEventListener('click', function () {
            modal.remove(); // Close modal without making changes
        });
    };
    
    // Save button functionality
    window.saveChanges = function () {
        const table = document.getElementById('transactionTable');
        const bodyRows = Array.from(table.querySelectorAll('tbody tr'));
        
        // Collect data from editable cells
        const updatedData = [];
        bodyRows.forEach(row => {
            const rowData = Array.from(row.cells).map(cell => cell.contentEditable === 'true' ? cell.textContent : null);
            updatedData.push(rowData);
        });
    
        console.log('Updated Table Data:', updatedData); // You can send this data to the server via an API
    
        // Disable editing after saving
        bodyRows.forEach(row => {
            Array.from(row.cells).forEach(cell => {
                cell.contentEditable = 'false';
                cell.style.backgroundColor = ''; // Reset background
            });
        });
    
        // Hide the "Save Changes" button
        document.getElementById('saveChanges').style.display = 'none';
    
        alert('Changes saved successfully!');
    };
    

});
