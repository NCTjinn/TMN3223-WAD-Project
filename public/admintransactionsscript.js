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
        fetch('/api/admin/notifications/unread')
            .then((response) => response.json())
            .then((data) => {
                const unreadCount = data.unreadCount || 0;
                updateNotificationBadge(unreadCount);
                renderNotifications(data.notifications);
            })
            .catch(console.error);
    }
    
    // Call the function periodically or on page load
    fetchNotifications();
    setInterval(fetchNotifications, 60000); // Check every 60 seconds

    // Initial render of notifications
    renderNotifications();

    //main content 
    const applyFiltersBtn = document.getElementById('apply-filters');
    const searchInput = document.getElementById('search-input');
    const searchBtn = document.getElementById('search-btn');
    const prevPageBtn = document.getElementById('prev-page');
    const nextPageBtn = document.getElementById('next-page');
    const currentPageSpan = document.getElementById('current-page');
    const transactionTableBody = document.getElementById('transaction-table-body');
    const dateStart = document.getElementById('date-start');
    const dateEnd = document.getElementById('date-end');

    let currentPage = 1;
    const rowsPerPage = 10;
    let filteredTransactions = []; // Filtered transactions will be stored here.

    // 30 Sample Transaction Records
    const transactions = [
        { date: "2024-12-01 09:00", orders: 15, grossSales: "$500.00", returns: "$20.00", netSales: "$480.00", delivery: "$30.00", tax: "$40.00", totalPaid: "$550.00" },
        { date: "2024-12-02 10:30", orders: 20, grossSales: "$750.00", returns: "$30.00", netSales: "$720.00", delivery: "$50.00", tax: "$60.00", totalPaid: "$830.00" },
        { date: "2024-12-03 11:45", orders: 25, grossSales: "$1,200.00", returns: "$50.00", netSales: "$1,150.00", delivery: "$100.00", tax: "$90.00", totalPaid: "$1,340.00" },
        { date: "2024-12-04 12:00", orders: 18, grossSales: "$900.00", returns: "$40.00", netSales: "$860.00", delivery: "$70.00", tax: "$80.00", totalPaid: "$1,010.00" },
        { date: "2024-12-05 14:00", orders: 30, grossSales: "$1,500.00", returns: "$0.00", netSales: "$1,500.00", delivery: "$120.00", tax: "$110.00", totalPaid: "$1,730.00" },
        { date: "2024-12-01 09:00", orders: 15, grossSales: "$500.00", returns: "$20.00", netSales: "$480.00", delivery: "$30.00", tax: "$40.00", totalPaid: "$550.00" },
        { date: "2024-12-02 10:30", orders: 20, grossSales: "$750.00", returns: "$30.00", netSales: "$720.00", delivery: "$50.00", tax: "$60.00", totalPaid: "$830.00" },
        { date: "2024-12-03 11:45", orders: 25, grossSales: "$1,200.00", returns: "$50.00", netSales: "$1,150.00", delivery: "$100.00", tax: "$90.00", totalPaid: "$1,340.00" },
        { date: "2024-12-04 12:00", orders: 18, grossSales: "$900.00", returns: "$40.00", netSales: "$860.00", delivery: "$70.00", tax: "$80.00", totalPaid: "$1,010.00" },
        { date: "2024-12-05 14:00", orders: 30, grossSales: "$1,500.00", returns: "$0.00", netSales: "$1,500.00", delivery: "$120.00", tax: "$110.00", totalPaid: "$1,730.00" },
        { date: "2024-12-06 15:20", orders: 12, grossSales: "$400.00", returns: "$10.00", netSales: "$390.00", delivery: "$25.00", tax: "$30.00", totalPaid: "$445.00" },
        { date: "2024-12-07 09:30", orders: 22, grossSales: "$1,100.00", returns: "$20.00", netSales: "$1,080.00", delivery: "$90.00", tax: "$100.00", totalPaid: "$1,270.00" },
        { date: "2024-12-08 16:10", orders: 28, grossSales: "$1,800.00", returns: "$100.00", netSales: "$1,700.00", delivery: "$150.00", tax: "$200.00", totalPaid: "$2,050.00" },
        { date: "2024-12-09 13:00", orders: 19, grossSales: "$950.00", returns: "$30.00", netSales: "$920.00", delivery: "$60.00", tax: "$70.00", totalPaid: "$1,050.00" },
        { date: "2024-12-10 11:15", orders: 35, grossSales: "$2,200.00", returns: "$50.00", netSales: "$2,150.00", delivery: "$200.00", tax: "$250.00", totalPaid: "$2,600.00" },
        { date: "2024-12-11 09:45", orders: 24, grossSales: "$1,200.00", returns: "$20.00", netSales: "$1,180.00", delivery: "$85.00", tax: "$90.00", totalPaid: "$1,355.00" },
        { date: "2024-12-12 10:30", orders: 17, grossSales: "$850.00", returns: "$10.00", netSales: "$840.00", delivery: "$40.00", tax: "$50.00", totalPaid: "$930.00" },
        { date: "2024-12-13 12:50", orders: 40, grossSales: "$2,800.00", returns: "$100.00", netSales: "$2,700.00", delivery: "$300.00", tax: "$350.00", totalPaid: "$3,350.00" },
        { date: "2024-12-14 15:45", orders: 10, grossSales: "$300.00", returns: "$0.00", netSales: "$300.00", delivery: "$20.00", tax: "$30.00", totalPaid: "$350.00" },
        { date: "2024-12-15 08:15", orders: 27, grossSales: "$1,500.00", returns: "$70.00", netSales: "$1,430.00", delivery: "$120.00", tax: "$150.00", totalPaid: "$1,700.00" },
        { date: "2024-12-16 09:50", orders: 13, grossSales: "$400.00", returns: "$20.00", netSales: "$380.00", delivery: "$25.00", tax: "$30.00", totalPaid: "$435.00" },
        { date: "2024-12-17 13:20", orders: 33, grossSales: "$2,100.00", returns: "$80.00", netSales: "$2,020.00", delivery: "$150.00", tax: "$200.00", totalPaid: "$2,370.00" },
        { date: "2024-12-18 14:10", orders: 21, grossSales: "$1,000.00", returns: "$50.00", netSales: "$950.00", delivery: "$90.00", tax: "$100.00", totalPaid: "$1,140.00" },
        { date: "2024-12-19 16:00", orders: 29, grossSales: "$1,700.00", returns: "$100.00", netSales: "$1,600.00", delivery: "$200.00", tax: "$250.00", totalPaid: "$2,050.00" },
        { date: "2024-12-20 14:00", orders: 11, grossSales: "$350.00", returns: "$0.00", netSales: "$350.00", delivery: "$15.00", tax: "$25.00", totalPaid: "$390.00" },
        { date: "2024-12-21 10:30", orders: 16, grossSales: "$650.00", returns: "$30.00", netSales: "$620.00", delivery: "$50.00", tax: "$60.00", totalPaid: "$730.00" },
        { date: "2024-12-22 08:20", orders: 23, grossSales: "$1,200.00", returns: "$40.00", netSales: "$1,160.00", delivery: "$85.00", tax: "$90.00", totalPaid: "$1,335.00" },
        { date: "2024-12-23 12:40", orders: 34, grossSales: "$2,400.00", returns: "$100.00", netSales: "$2,300.00", delivery: "$250.00", tax: "$300.00", totalPaid: "$2,850.00" },
        { date: "2024-12-24 15:00", orders: 19, grossSales: "$950.00", returns: "$20.00", netSales: "$930.00", delivery: "$60.00", tax: "$70.00", totalPaid: "$1,060.00" },
        { date: "2024-12-25 10:10", orders: 28, grossSales: "$1,800.00", returns: "$50.00", netSales: "$1,750.00", delivery: "$150.00", tax: "$200.00", totalPaid: "$2,100.00" },
        { date: "2024-12-26 13:30", orders: 15, grossSales: "$550.00", returns: "$10.00", netSales: "$540.00", delivery: "$35.00", tax: "$40.00", totalPaid: "$615.00" },
        { date: "2024-12-27 09:40", orders: 20, grossSales: "$800.00", returns: "$20.00", netSales: "$780.00", delivery: "$50.00", tax: "$60.00", totalPaid: "$890.00" },
        { date: "2024-12-28 11:15", orders: 14, grossSales: "$400.00", returns: "$0.00", netSales: "$400.00", delivery: "$25.00", tax: "$30.00", totalPaid: "$455.00" },
        { date: "2024-12-29 14:25", orders: 32, grossSales: "$2,000.00", returns: "$70.00", netSales: "$1,930.00", delivery: "$150.00", tax: "$200.00", totalPaid: "$2,280.00" },
        { date: "2024-12-30 16:40", orders: 18, grossSales: "$900.00", returns: "$30.00", netSales: "$870.00", delivery: "$60.00", tax: "$70.00", totalPaid: "$1,000.00" },
        { date: "2024-12-31 09:00", orders: 19, grossSales: "$1,050.00", returns: "$0.00", netSales: "$1,050.00", delivery: "$80.00", tax: "$90.00", totalPaid: "$1,220.00" }
    ];
    
    // Filter and Render Table
    function applyFilters() {
        const startDate = new Date(dateStart.value);
        const endDate = new Date(dateEnd.value);
        const searchTerm = searchInput.value.toLowerCase();

        filteredTransactions = transactions.filter(transaction => {
            const transactionDate = new Date(transaction.date);

            // Check if the transaction matches the date range and search term
            const matchesDateRange = (!isNaN(startDate) ? transactionDate >= startDate : true) &&
                                     (!isNaN(endDate) ? transactionDate <= endDate : true);

            const matchesSearch = Object.values(transaction)
                .some(value => value.toString().toLowerCase().includes(searchTerm));

            return matchesDateRange && matchesSearch;
        });

        currentPage = 1; // Reset to the first page after filtering.
        renderTable();
    }

    // Render Transaction Table
    function renderTable() {
        transactionTableBody.innerHTML = '';
        const start = (currentPage - 1) * rowsPerPage;
        const end = start + rowsPerPage;
        const pageData = filteredTransactions.slice(start, end);

        pageData.forEach(transaction => {
            const row = `<tr>
                <td>${transaction.date}</td>
                <td>${transaction.orders}</td>
                <td>${transaction.grossSales}</td>
                <td>${transaction.returns}</td>
                <td>${transaction.netSales}</td>
                <td>${transaction.delivery}</td>
                <td>${transaction.tax}</td>
                <td>${transaction.totalPaid}</td>
            </tr>`;
            transactionTableBody.insertAdjacentHTML('beforeend', row);
        });

        updatePagination();
    }

    // Update Pagination
    function updatePagination() {
        const totalPages = Math.ceil(filteredTransactions.length / rowsPerPage);

        currentPageSpan.textContent = `Page ${currentPage} of ${totalPages || 1}`;
        prevPageBtn.disabled = currentPage === 1;
        nextPageBtn.disabled = currentPage >= totalPages;
    }

    // Pagination Handlers
    prevPageBtn.addEventListener('click', () => {
        if (currentPage > 1) {
            currentPage--;
            renderTable();
        }
    });

    nextPageBtn.addEventListener('click', () => {
        const totalPages = Math.ceil(filteredTransactions.length / rowsPerPage);
        if (currentPage < totalPages) {
            currentPage++;
            renderTable();
        }
    });

    // Filter Handlers
    applyFiltersBtn.addEventListener('click', applyFilters);
    searchBtn.addEventListener('click', applyFilters);

    // Initial Setup
    filteredTransactions = [...transactions]; // Start with all transactions.
    renderTable(); // Initial render.
});
