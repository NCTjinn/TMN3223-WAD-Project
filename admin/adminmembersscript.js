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

    // ------------------ Member Management Table ------------------
    const members = generateDummyData(30);
    const rowsPerPage = 10;
    let currentPage = 1;
    let filteredMembers = [...members];
    let memberHistory = []; // Array to store member changes

    // DOM Elements
    const memberTableBody = document.querySelector("#memberTable tbody");
    const pageNumbers = document.getElementById("pageNumbers");
    const prevPageBtn = document.getElementById("prevPageBtn");
    const nextPageBtn = document.getElementById("nextPageBtn");
    const searchBar = document.getElementById("searchBar");
    const addMemberBtn = document.getElementById("addMemberBtn");
    const addMemberModal = document.getElementById("addMemberModal");
    const updateMemberModal = document.getElementById("updateMemberModal");
    const saveMemberBtn = document.getElementById("saveMemberBtn");
    const updateMemberSaveBtn = document.getElementById("updateMemberSaveBtn");
    const filterDropdown = document.getElementById("filterDropdown");
    const filterOptions = document.querySelectorAll(".filter-option");
    const addUsernameInput = document.getElementById("addUsername");
    const addPointsInput = document.getElementById("addPoints");
    const addSpentInput = document.getElementById("addSpent");
    const updateUsernameInput = document.getElementById("updateUsername");
    const updatePointsInput = document.getElementById("updatePoints");
    const updateSpentInput = document.getElementById("updateSpent");

    let selectedMemberIndex = null;
    let currentFilter = "all";

    // History tracking functions
    function addToHistory(action, data) {
        const timestamp = new Date().toISOString();
        memberHistory.push({
            timestamp,
            action,
            data: JSON.parse(JSON.stringify(data)) // Deep copy to prevent reference issues
        });
    }

    // Generate dummy data
    function generateDummyData(count) {
        const data = [];
        for (let i = 1; i <= count; i++) {
            data.push({
                id: `M${i.toString().padStart(3, '0')}`,
                username: `user${i}`,
                creationDate: `2023-${(i % 12 + 1).toString().padStart(2, '0')}-${(i % 28 + 1).toString().padStart(2, '0')}`,
                points: Math.floor(Math.random() * 1000),
                totalSpent: Math.floor(Math.random() * 5000),
                lastTransaction: `T${Math.random().toString(36).substr(2, 5).toUpperCase()}`,
                isActive: i % 2 === 0,
            });
        }
        return data;
    }

    // Render table
    function renderTable() {
        memberTableBody.innerHTML = '';
        const start = (currentPage - 1) * rowsPerPage;
        const end = start + rowsPerPage;
        const pageData = filteredMembers.slice(start, end);

        pageData.forEach((member, index) => {
            const row = `
                <tr>
                    <td>${member.id}</td>
                    <td>${member.username}</td>
                    <td>${member.creationDate}</td>
                    <td>${member.points}</td>
                    <td>$${member.totalSpent}</td>
                    <td>${member.lastTransaction}</td>
                    <td>
                        <button class="update-btn" data-index="${index + start}">Update</button>
                        <button class="delete-btn" data-index="${index + start}">Delete</button>
                    </td>
                </tr>
            `;
            memberTableBody.innerHTML += row;
        });

        updatePagination();
        attachRowListeners();
    }

    // Attach listeners to row buttons
    function attachRowListeners() {
        document.querySelectorAll(".update-btn").forEach((btn) => {
            btn.addEventListener("click", (e) => {
                const index = e.target.dataset.index;
                openUpdateModal(index);
            });
        });

        document.querySelectorAll(".delete-btn").forEach((btn) => {
            btn.addEventListener("click", (e) => {
                const index = e.target.dataset.index;
                confirmDelete(index);
            });
        });
    }

    // Confirmation dialog for delete
    function confirmDelete(index) {
        const member = filteredMembers[index];
        const confirmed = confirm(`Are you sure you want to delete member ${member.username}?`);
        if (confirmed) {
            deleteMember(index);
        }
    }

    // Pagination handlers
    prevPageBtn.addEventListener('click', () => {
        currentPage--;
        renderTable();
    });

    nextPageBtn.addEventListener('click', () => {
        currentPage++;
        renderTable();
    });

    function updatePagination() {
        pageNumbers.textContent = `Page ${currentPage} of ${Math.ceil(filteredMembers.length / rowsPerPage)}`;
        prevPageBtn.disabled = currentPage === 1;
        nextPageBtn.disabled = currentPage === Math.ceil(filteredMembers.length / rowsPerPage);
    }

    // Add Member
    addMemberBtn.addEventListener("click", () => {
        addMemberModal.style.display = "flex";
    });

    saveMemberBtn.addEventListener("click", () => {
        const confirmed = confirm("Are you sure you want to add this member?");
        if (confirmed) {
            const newMember = {
                id: `M${(members.length + 1).toString().padStart(3, '0')}`,
                username: addUsernameInput.value,
                creationDate: new Date().toISOString().split("T")[0],
                points: parseInt(addPointsInput.value, 10),
                totalSpent: parseFloat(addSpentInput.value),
                lastTransaction: `T${Math.random().toString(36).substr(2, 5).toUpperCase()}`,
                isActive: true,
            };

            addToHistory('add', newMember);
            members.push(newMember);
            applyFilters();
            renderTable();
            closeModal(addMemberModal);
        }
    });

    // Open Update Modal
    function openUpdateModal(index) {
        selectedMemberIndex = index;
        const member = filteredMembers[index];

        updateUsernameInput.value = member.username;
        updatePointsInput.value = member.points;
        updateSpentInput.value = member.totalSpent;

        updateMemberModal.style.display = "flex";
    }

    updateMemberSaveBtn.addEventListener("click", () => {
        if (selectedMemberIndex !== null) {
            const confirmed = confirm("Are you sure you want to update this member?");
            if (confirmed) {
                const originalMember = { ...filteredMembers[selectedMemberIndex] };
                const updatedMember = {
                    ...originalMember,
                    username: updateUsernameInput.value,
                    points: parseInt(updatePointsInput.value, 10),
                    totalSpent: parseFloat(updateSpentInput.value),
                };

                addToHistory('update', {
                    before: originalMember,
                    after: updatedMember
                });

                filteredMembers[selectedMemberIndex] = updatedMember;
                const originalIndex = members.findIndex(m => m.id === updatedMember.id);
                if (originalIndex !== -1) {
                    members[originalIndex] = updatedMember;
                }

                applyFilters();
                renderTable();
                closeModal(updateMemberModal);
            }
        }
    });

    // Delete member
    function deleteMember(index) {
        const deletedMember = { ...filteredMembers[index] };
        addToHistory('delete', deletedMember);
        
        const originalIndex = members.findIndex(m => m.id === deletedMember.id);
        if (originalIndex !== -1) {
            members.splice(originalIndex, 1);
        }
        
        applyFilters();
        renderTable();
    }

    // History Log Modal
    const historyModal = document.createElement('div');
    historyModal.className = 'modal';
    historyModal.innerHTML = `
        <div class="modal-content" style="width: 80%; max-height: 80vh; overflow-y: auto;">
            <span class="close">&times;</span>
            <h2>Member History Log</h2>
            <div id="historyContent"></div>
            <div class="modal-actions">
                <button id="closeHistoryBtn" class="cancel-btn">Close</button>
            </div>
        </div>
    `;
    document.body.appendChild(historyModal);

    // Show History Log
    function showHistoryLog() {
        const historyContent = historyModal.querySelector('#historyContent');
        historyContent.innerHTML = memberHistory.map(entry => {
            let actionDescription;
            switch (entry.action) {
                case 'add':
                    actionDescription = `Added member ${entry.data.username}`;
                    break;
                case 'update':
                    actionDescription = `Updated member ${entry.data.before.username} to ${entry.data.after.username}`;
                    break;
                case 'delete':
                    actionDescription = `Deleted member ${entry.data.username}`;
                    break;
            }
            return `
                <div class="history-entry" style="padding: 10px; border-bottom: 1px solid #ddd;">
                    <div><strong>Time:</strong> ${new Date(entry.timestamp).toLocaleString()}</div>
                    <div><strong>Action:</strong> ${actionDescription}</div>
                    <div><strong>Details:</strong> ${JSON.stringify(entry.data, null, 2)}</div>
                </div>
            `;
        }).join('');

        historyModal.style.display = 'flex';
    }

    // Close Modal
    document.querySelectorAll(".modal .close").forEach((closeBtn) => {
        closeBtn.addEventListener("click", () => {
            closeModal(closeBtn.closest(".modal"));
        });
    });

    function closeModal(modal) {
        modal.style.display = "none";
    }

    // History Log Button Functionality
    const historyLogBtn = document.getElementById("historyLogBtn");
    historyLogBtn.addEventListener("click", showHistoryLog);

    // Close history modal with button
    const closeHistoryBtn = document.getElementById("closeHistoryBtn");
    if (closeHistoryBtn) {
        closeHistoryBtn.addEventListener("click", () => {
            closeModal(historyModal);
        });
    }

    // Search functionality
    searchBar.addEventListener("input", () => {
        applyFilters();
        renderTable();
    });

    // Filter dropdown behavior
    document.querySelector('.dropdown-btn').addEventListener('click', function (e) {
        e.stopPropagation();
        filterDropdown.classList.toggle('active');
    });

    document.addEventListener('click', () => {
        filterDropdown.classList.remove('active');
    });

    filterOptions.forEach(option => {
        option.addEventListener('click', () => {
            currentFilter = option.getAttribute("data-filter");
            applyFilters();
            renderTable();
            filterDropdown.classList.remove('active');
        });
    });

    // Apply filters
    function applyFilters() {
        const query = searchBar.value.toLowerCase();

        filteredMembers = members.filter((member) => {
            const matchesSearch = member.username.toLowerCase().includes(query) || member.id.toLowerCase().includes(query);

            if (currentFilter === "active") {
                return matchesSearch && member.isActive;
            } else if (currentFilter === "inactive") {
                return matchesSearch && !member.isActive;
            } else if (currentFilter === "high-spenders") {
                return matchesSearch && member.totalSpent > 3000;
            } else if (currentFilter === "high-to-low") {
                return matchesSearch;
            }

            return matchesSearch;
        });

        if (currentFilter === "high-to-low") {
            filteredMembers.sort((a, b) => b.totalSpent - a.totalSpent);
        }

        currentPage = 1;
    }

    // Initial render
    renderTable();

    // Cancel Button Functionality
    const cancelAddMemberBtn = document.getElementById("cancelAddMemberBtn");
    const cancelUpdateMemberBtn = document.getElementById("cancelUpdateMemberBtn");

    cancelAddMemberBtn.addEventListener("click", () => {
        closeModal(addMemberModal);
    });

    cancelUpdateMemberBtn.addEventListener("click", () => {
        closeModal(updateMemberModal);
    });
});
