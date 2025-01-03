document.addEventListener('DOMContentLoaded', function () {
    const profileIcon = document.getElementById('profile-icon');
    const profileDropdown = document.getElementById('dropdown-menu');

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
            !e.target.closest('.icon') 
        ) {
            closeAllDropdowns();
        }
    });

    // Helper function to close all dropdowns
    function closeAllDropdowns() {
        profileDropdown.classList.remove('active');
    }


    // ------------------ Member Management Table ------------------
    const members = [];
    const rowsPerPage = 10;
    let currentPage = 1;
    let filteredMembers = [...members];

    // DOM Elements
    const memberTableBody = document.querySelector("#memberTable tbody");
    const pageNumbers = document.getElementById("pageNumbers");
    const prevPageBtn = document.getElementById("prevPageBtn");
    const nextPageBtn = document.getElementById("nextPageBtn");
    const searchBar = document.getElementById("searchBar");
    const filterDropdown = document.getElementById("filterDropdown");
    const filterOptions = document.querySelectorAll(".filter-option");
 

    let currentFilter = "all";

    function fetchMembers(attempts = 5) {
        console.log(`Fetching members, attempt: ${6 - attempts}`);
        fetch('./fetchMembers.php')
            .then(response => {
                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                return response.json();
            })
            .then(data => {
                if (data.error) {
                    throw new Error(data.error);
                }
                console.log("Data loaded successfully:", data);
                members.length = 0;
                members.push(...data);
                applyFilters();
                renderTable();
            })
            .catch(error => {
                console.error("Error fetching members:", error);
                if (attempts > 1) {
                    setTimeout(() => fetchMembers(attempts - 1), 3000);
                } else {
                    document.getElementById('error-message').innerText = "Failed to load members. Click to retry.";
                    document.getElementById('error-message').onclick = () => fetchMembers(5);
                }
            });
    }
    

    // Render table
    function renderTable() {
        memberTableBody.innerHTML = ''; // Clear existing rows
        const start = (currentPage - 1) * rowsPerPage;
        const end = start + rowsPerPage;
        const pageData = filteredMembers.slice(start, end); // Use dynamically filtered data
    
        pageData.forEach((member, index) => {
            const row = `
                <tr>
                    <td>${member.id}</td>
                    <td>${member.username}</td>
                    <td>${member.creationDate}</td>
                    <td>${member.points}</td>
                    <td>$${member.totalSpent}</td>
                    <td>${member.lastTransaction}</td>
                </tr>
            `;
            memberTableBody.innerHTML += row;
        });
    
        updatePagination();
        attachRowListeners();
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
            applyFilters(); // Apply the selected filter
            filterDropdown.classList.remove('active'); // Close dropdown
        });
    });
    

    // Apply filters
    function applyFilters() {
        const query = searchBar.value.toLowerCase(); // Get search query
    
        filteredMembers = members.filter((member) => {
            const matchesSearch = 
                member.username.toLowerCase().includes(query) || 
                member.id.toString().includes(query);
    
            // Handle dropdown filters
            if (currentFilter === "top-spenders") {
                return matchesSearch; // Filtering handled by sorting below
            } else if (currentFilter === "recently-active") {
                const lastTransactionDate = new Date(member.lastTransaction);
                const thirtyDaysAgo = new Date();
                thirtyDaysAgo.setDate(thirtyDaysAgo.getDate() - 30);
                return matchesSearch && lastTransactionDate >= thirtyDaysAgo;
            } else if (currentFilter === "inactive") {
                const lastTransactionDate = new Date(member.lastTransaction);
                const sixMonthsAgo = new Date();
                sixMonthsAgo.setMonth(sixMonthsAgo.getMonth() - 6);
                return matchesSearch && (!member.lastTransaction || lastTransactionDate < sixMonthsAgo);
            }
    
            return matchesSearch; // Default case (e.g., "reset" or no filter)
        });
    
        // Handle sorting for Top Spenders
        if (currentFilter === "top-spenders") {
            filteredMembers.sort((a, b) => b.totalSpent - a.totalSpent);
        }
    
        currentPage = 1; // Reset to the first page
        renderTable(); // Re-render the table
    }
    

    // Initial render
    fetchMembers();

});
