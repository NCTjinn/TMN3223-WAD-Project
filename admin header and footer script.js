/* General Reset */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Poppins', sans-serif;
}

/* Header Styling */
.admin-header {
    background-color: #C2C9AD; /* Light greenish tone */
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 20px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

/* Logo Section */
.admin-header .logo img {
    height: 40px;
    width: auto;
}

/* Utility Icons Section */
.admin-header .utilities {
    display: flex;
    align-items: center;
    gap: 25px; /* Add space between icons */
}

/* Icon Styles - Notification & Profile */
.admin-header .utilities .icon,
.admin-header .user-dropdown i {
    font-size: 32px; /* Same size for both icons */
    color: #1F1F1F; /* Consistent color */
    cursor: pointer;
    transition: transform 0.3s, color 0.3s;
}

.admin-header .utilities .icon:hover,
.admin-header .user-dropdown i:hover {
    color: #6c7a5d; /* Hover effect */
    transform: scale(1.1);
}

/* User Dropdown */
.admin-header .user-dropdown {
    position: relative;
}

.admin-header .user-dropdown i {
    font-size: 36px; /* Adjust icon size */
    color: #1F1F1F; /* Match theme color */
    cursor: pointer;
    transition: transform 0.3s, color 0.3s;
}

.admin-header .user-dropdown i:hover {
    color: #6c7a5d; /* Hover effect matching the theme */
    transform: scale(1.1);
}

/* Fade-in Animation for Dropdown */
.dropdown-menu {
    display: none; /* Hidden by default */
    position: absolute;
    top: calc(100% + 10px); /* Slightly below the trigger */
    right: 0;
    width: 300px;
    background-color: #fff;
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
    border-radius: 10px;
    z-index: 10;
    overflow: hidden;
    opacity: 0;
    transform: translateY(-10px);
    transition: opacity 0.3s ease, transform 0.3s ease;
}

.dropdown-menu.active {
    display: block;
    opacity: 1;
    transform: translateY(0);
}

/* Badge Styles */
.badge {
    position: absolute;
    top: 5px; /* Adjust for perfect alignment */
    right: 5px; /* Adjust for perfect alignment */
    background-color: #ff5722; /* Bright color for notifications */
    color: white;
    font-size: 12px;
    font-weight: bold;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    display: flex;
    justify-content: center;
    align-items: center;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    visibility: hidden; /* Hidden by default */
}

/* Badge Parent Container for Positioning */
.notification-icon {
    position: relative;
}


/* Active State for Profile Icon */
.user-dropdown i.active {
    color: #6c7a5d; /* Active color */
    transform: scale(1.1); /* Slight enlargement for active state */
}

/* Dropdown Header */
.dropdown-header {
    background-color: #C2C9AD;
    padding: 10px 15px;
    font-size: 16px;
    font-weight: bold;
    color: #1F1F1F;
    text-align: center;
    border-bottom: 1px solid #aab79f;
}


/* Dropdown Items */
.dropdown-menu a,
.notification-item {
    display: flex;
    align-items: center;
    padding: 12px 20px;
    text-decoration: none;
    color: #1F1F1F;
    font-size: 14px;
    border-bottom: 1px solid #f0f0f0;
    transition: background-color 0.3s ease, transform 0.2s ease;
}

.notification-item:last-child,
.dropdown-menu a:last-child {
    border-bottom: none;
}

.dropdown-menu a:hover,
.notification-item:hover {
    background-color: #f5f5f5;
    transform: scale(1.02);
}

/* Unread Indicator */
.notification-item .unread-indicator {
    width: 10px;
    height: 10px;
    background-color: #ff5722;
    border-radius: 50%;
    margin-right: 15px;
}

/* Notification Text Section */
.notification-text {
    flex: 1;
    display: flex;
    flex-direction: column;
}

/* Notification Title */
.notification-title {
    font-weight: bold;
    margin-bottom: 2px;
}

/* Notification Time */
.notification-time {
    font-size: 12px;
    color: #777;
}

/* Mark All as Read Button */
button.mark-all-btn {
    font-size: 14px;
    padding: 10px 15px;
    background-color: #6c7a5d;
    color: white;
    border: none;
    width: 100%;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

button.mark-all-btn:hover {
    background-color: #556d4f;
}

/* Footer Styling */
.admin-footer {
    background-color: #C2C9AD;
    text-align: center;
    padding: 20px 10px;
    color: #1F1F1F;
    margin-top: 20px;
    box-shadow: 0 -2px 4px rgba(0, 0, 0, 0.1);
}
