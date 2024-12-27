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

});
