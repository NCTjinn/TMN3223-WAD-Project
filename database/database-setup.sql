-- Drop the database if it exists
DROP DATABASE IF EXISTS PuffLab;

-- Database creation and selection
CREATE DATABASE IF NOT EXISTS PuffLab;
USE PuffLab;

-- Drop tables if they exist
DROP TABLE IF EXISTS Users;
DROP TABLE IF EXISTS Product_Categories;
DROP TABLE IF EXISTS notifications;
DROP TABLE IF EXISTS Products;
DROP TABLE IF EXISTS Cart;
DROP TABLE IF EXISTS Vouchers;
DROP TABLE IF EXISTS Transactions;
DROP TABLE IF EXISTS Transaction_Details;
DROP TABLE IF EXISTS Mission_Templates;
DROP TABLE IF EXISTS Rewards;
DROP TABLE IF EXISTS Orders;
DROP TABLE IF EXISTS FAQ_Categories;
DROP TABLE IF EXISTS FAQs;
DROP TABLE IF EXISTS Addresses;
DROP TABLE IF EXISTS Reviews;
DROP TABLE IF EXISTS Community_Posts;
DROP TABLE IF EXISTS User_Favorites;
DROP TABLE IF EXISTS Admin_Actions_Log;
DROP TABLE IF EXISTS Sales_Summary;

-- Users table for storing user information
CREATE TABLE Users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) UNIQUE NOT NULL,
    first_name VARCHAR(255) NOT NULL,
    last_name VARCHAR(255) NOT NULL,
    display_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('public', 'member', 'admin') NOT NULL,
    points INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Product Categories table
CREATE TABLE Product_Categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Notifications table for user alerts
CREATE TABLE Notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    status ENUM('unread', 'read') DEFAULT 'unread',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Products table for inventory management
CREATE TABLE Products (
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    category_id INT,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    stock_quantity INT NOT NULL,
    image_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES Product_Categories(category_id)
);

-- Cart table for tracking items in user carts
CREATE TABLE Cart (
    cart_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES Users(user_id),
    FOREIGN KEY (product_id) REFERENCES Products(product_id)
);

-- Vouchers table for managing discount codes
CREATE TABLE Vouchers (
    voucher_id INT AUTO_INCREMENT PRIMARY KEY,
    voucher_code VARCHAR(50) UNIQUE NOT NULL,
    discount_percentage DECIMAL(5,2) NOT NULL,
    expiry_date DATE NOT NULL,
    redeemed_by INT,
    FOREIGN KEY (redeemed_by) REFERENCES Users(user_id)
);

-- Transactions table for order payments
CREATE TABLE Transactions (
    transaction_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    transaction_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    total_amount DECIMAL(10,2) NOT NULL,
    delivery_fee DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    tax_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    payment_status ENUM('pending', 'successful', 'failed') NOT NULL,
    delivery_address TEXT NOT NULL,
    shipping_method VARCHAR(100) NOT NULL,
    voucher_code VARCHAR(50),
    receipt_url VARCHAR(255),
    FOREIGN KEY (user_id) REFERENCES Users(user_id),
    FOREIGN KEY (voucher_code) REFERENCES Vouchers(voucher_code)
);

-- Transaction details table for itemized order information
CREATE TABLE Transaction_Details (
    transaction_detail_id INT AUTO_INCREMENT PRIMARY KEY,
    transaction_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price_per_item DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (transaction_id) REFERENCES Transactions(transaction_id),
    FOREIGN KEY (product_id) REFERENCES Products(product_id)
);

-- Mission templates table for the rewards system
CREATE TABLE Mission_Templates (
    mission_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    points INT NOT NULL,
    requirements TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Rewards table for user missions and points
CREATE TABLE Rewards (
    reward_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    mission_id INT,
    mission_name VARCHAR(255) NOT NULL,
    status ENUM('pending', 'completed') NOT NULL,
    points_earned INT NOT NULL,
    redeemed BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES Users(user_id),
    FOREIGN KEY (mission_id) REFERENCES Mission_Templates(mission_id)
);

-- Orders table for tracking order status
CREATE TABLE Orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    transaction_id INT NOT NULL,
    tracking_number VARCHAR(100) UNIQUE NOT NULL,
    status ENUM('processing', 'shipped', 'delivered', 'cancelled') NOT NULL,
    estimated_delivery DATE NOT NULL,
    customer_notes TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (transaction_id) REFERENCES Transactions(transaction_id)
);

-- FAQ Categories table
CREATE TABLE FAQ_Categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- FAQs table for product-related questions
CREATE TABLE FAQs (
    faq_id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT,
    question TEXT NOT NULL,
    answer TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES FAQ_Categories(category_id)
);

-- Addresses table for user delivery addresses
CREATE TABLE Addresses (
    address_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    address_line_1 VARCHAR(255) NOT NULL,
    address_line_2 VARCHAR(255),
    city VARCHAR(100) NOT NULL,
    state VARCHAR(100) NOT NULL,
    postcode VARCHAR(20) NOT NULL,
    country VARCHAR(100) NOT NULL,
    is_default BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (user_id) REFERENCES Users(user_id)
);

-- Reviews table for product reviews
CREATE TABLE Reviews (
    review_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    rating INT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES Users(user_id),
    FOREIGN KEY (product_id) REFERENCES Products(product_id)
);

-- Community posts table
CREATE TABLE Community_Posts (
    post_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- User favorites table
CREATE TABLE User_Favorites (
    favorite_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES Users(user_id),
    FOREIGN KEY (product_id) REFERENCES Products(product_id),
    UNIQUE KEY unique_favorite (user_id, product_id)
);

-- Admin actions log table for activity tracking
CREATE TABLE Admin_Actions_Log (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    admin_id INT NOT NULL,
    action TEXT NOT NULL,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES Users(user_id)
);

-- Sales summary table for analytics
CREATE TABLE Sales_Summary (
    summary_id INT AUTO_INCREMENT PRIMARY KEY,
    date DATE NOT NULL,
    total_orders INT NOT NULL,
    gross_sales DECIMAL(10,2) NOT NULL,
    returns DECIMAL(10,2) NOT NULL,
    net_sales DECIMAL(10,2) NOT NULL,
    delivery_fee DECIMAL(10,2) NOT NULL,
    tax DECIMAL(10,2) NOT NULL
);

-- Indexes for optimization
-- User-related indexes
CREATE INDEX idx_user_email ON Users(email);
CREATE INDEX idx_username ON Users(username);
CREATE INDEX idx_user_points ON Users(points);

-- Product-related indexes
CREATE INDEX idx_product_category ON Products(category_id);
CREATE INDEX idx_product_name ON Products(name);
CREATE INDEX idx_product_price ON Products(price);
CREATE INDEX idx_product_stock ON Products(stock_quantity);

-- Order and transaction indexes
CREATE INDEX idx_transaction_date ON Transactions(transaction_date);
CREATE INDEX idx_order_status ON Orders(status);
CREATE INDEX idx_order_tracking ON Orders(tracking_number);
CREATE INDEX idx_order_estimated_delivery ON Orders(estimated_delivery);
CREATE INDEX idx_transaction_payment_status ON Transactions(payment_status);
CREATE INDEX idx_transaction_total ON Transactions(total_amount);

-- Cart and favorites indexes
CREATE INDEX idx_cart_user ON Cart(user_id, added_at);
CREATE INDEX idx_user_favorites ON User_Favorites(user_id, product_id);

-- Address indexes
CREATE INDEX idx_address_user ON Addresses(user_id, is_default);

-- Review indexes
CREATE INDEX idx_review_product ON Reviews(product_id);
CREATE INDEX idx_review_rating ON Reviews(rating);

-- FAQ indexes
CREATE INDEX idx_faq_category ON FAQs(category_id);

-- Sales and analytics indexes
CREATE INDEX idx_sales_date ON Sales_Summary(date);

-- Rewards indexes
CREATE INDEX idx_rewards_status ON Rewards(status);
CREATE INDEX idx_rewards_user ON Rewards(user_id, status);
CREATE INDEX idx_mission_points ON Mission_Templates(points);

-- Voucher indexes
CREATE INDEX idx_voucher_expiry ON Vouchers(expiry_date);

-- Admin Users
INSERT INTO Users (username, first_name, last_name, display_name, email, password, role) VALUES
('admin1', 'John', 'Doe', 'Admin John', 'admin1@pufflab.com', '$2y$10$abc', 'admin'),
('admin2', 'Jane', 'Smith', 'Admin Jane', 'admin2@pufflab.com', '$2y$10$def', 'admin'),
('admin3', 'Mike', 'Johnson', 'Admin Mike', 'admin3@pufflab.com', '$2y$10$ghi', 'admin');

-- Member Users
INSERT INTO Users (username, first_name, last_name, display_name, email, password, role, points) VALUES
('member1', 'Alice', 'Wong', 'Alice W', 'alice@email.com', '$2y$10$jkl', 'member', 100),
('member2', 'Bob', 'Tan', 'Bob T', 'bob@email.com', '$2y$10$mno', 'member', 150),
('member3', 'Charlie', 'Lee', 'Charlie L', 'charlie@email.com', '$2y$10$pqr', 'member', 200),
('member4', 'Diana', 'Chen', 'Diana C', 'diana@email.com', '$2y$10$stu', 'member', 75),
('member5', 'Edward', 'Lim', 'Edward L', 'edward@email.com', '$2y$10$vwx', 'member', 300),
('member6', 'Fiona', 'Ng', 'Fiona N', 'fiona@email.com', '$2y$10$yza', 'member', 250),
('member7', 'George', 'Tan', 'George T', 'george@email.com', '$2y$10$bcd', 'member', 175),
('member8', 'Hannah', 'Wu', 'Hannah W', 'hannah@email.com', '$2y$10$efg', 'member', 125),
('member9', 'Ian', 'Zhang', 'Ian Z', 'ian@email.com', '$2y$10$hij', 'member', 225),
('member10', 'Julia', 'Liu', 'Julia L', 'julia@email.com', '$2y$10$klm', 'member', 350),
('member11', 'Kevin', 'Wang', 'Kevin W', 'kevin@email.com', '$2y$10$nop', 'member', 400),
('member12', 'Linda', 'Goh', 'Linda G', 'linda@email.com', '$2y$10$qrs', 'member', 275),
('member13', 'Michael', 'Ong', 'Michael O', 'michael@email.com', '$2y$10$tuv', 'member', 150),
('member14', 'Nancy', 'Chua', 'Nancy C', 'nancy@email.com', '$2y$10$wxy', 'member', 200),
('member15', 'Oliver', 'Teo', 'Oliver T', 'oliver@email.com', '$2y$10$zab', 'member', 325),
('member16', 'Patricia', 'Koh', 'Patricia K', 'patricia@email.com', '$2y$10$cde', 'member', 275),
('member17', 'Quinn', 'Sim', 'Quinn S', 'quinn@email.com', '$2y$10$fgh', 'member', 225),
('member18', 'Ryan', 'Low', 'Ryan L', 'ryan@email.com', '$2y$10$ijk', 'member', 175),
('member19', 'Sarah', 'Yeo', 'Sarah Y', 'sarah@email.com', '$2y$10$lmn', 'member', 300),
('member20', 'Tom', 'Pang', 'Tom P', 'tom@email.com', '$2y$10$opq', 'member', 250);

-- Product Categories
INSERT INTO Product_Categories (name, description) VALUES
('Puffs', 'Delicious cream puffs in various flavors'),
('Cakes', 'Freshly baked cakes for all occasions'),
('Beverages', 'Refreshing drinks to complement your treats');

-- Products
INSERT INTO Products (name, category_id, description, price, stock_quantity) VALUES
('Classic Cream Puff', 1, 'Original cream puff filled with vanilla custard', 3.50, 100),
('Chocolate Puff', 1, 'Cream puff with rich chocolate filling', 4.00, 80),
('Matcha Puff', 1, 'Green tea flavored cream puff', 4.00, 80),
('Chocolate Cake', 2, 'Rich chocolate cake with ganache', 28.00, 20),
('Vanilla Cheesecake', 2, 'Classic New York style cheesecake', 32.00, 15),
('Red Velvet Cake', 2, 'Red velvet cake with cream cheese frosting', 30.00, 18),
('Classic Milk Tea', 3, 'Traditional milk tea with pearls', 5.50, 150),
('Matcha Latte', 3, 'Green tea latte with milk', 6.00, 120),
('Coffee', 3, 'Freshly brewed coffee', 4.50, 200);

-- Vouchers
INSERT INTO Vouchers (voucher_code, discount_percentage, expiry_date) VALUES
('WELCOME10', 10.00, '2025-12-31'),
('BDAY20', 20.00, '2025-12-31'),
('MEMBER15', 15.00, '2025-12-31');

-- Transactions (December 2024 - January 2025)
INSERT INTO Transactions (user_id, total_amount, delivery_fee, tax_amount, payment_status, delivery_address, shipping_method, transaction_date) VALUES
(4, 42.40, 5.00, 2.24, 'successful', '123 Main St, #01-01, Singapore 123456', 'dine_in', '2024-12-01 10:00:00'),
(5, 65.20, 5.00, 3.62, 'successful', '456 Orchard Rd, Singapore 234567', 'dine_in', '2024-12-01 14:30:00'),
(6, 47.70, 5.00, 2.56, 'successful', '789 Cecil St, Singapore 345678', 'dine_in', '2024-12-02 09:15:00'),
(7, 53.00, 5.00, 2.88, 'successful', '321 Victoria St, Singapore 456789', 'takeaway', '2024-12-02 11:45:00'),
(8, 71.80, 5.00, 4.01, 'successful', '654 Bencoolen St, Singapore 567890', 'takeaway', '2024-12-02 15:20:00'),
(9, 39.50, 5.00, 2.07, 'successful', '987 Beach Rd, Singapore 678901', 'delivery', '2024-12-03 10:30:00'),
(10, 58.90, 5.00, 3.23, 'successful', '147 Bugis St, Singapore 789012', 'delivery', '2024-12-03 13:45:00'),
(11, 44.10, 5.00, 2.34, 'successful', '258 Somerset Rd, Singapore 890123', 'delivery', '2024-12-04 09:00:00'),
(12, 62.30, 5.00, 3.44, 'successful', '369 Serangoon Rd, Singapore 901234', 'delivery', '2024-12-04 14:15:00'),
(13, 51.70, 5.00, 2.80, 'successful', '159 Tampines St, Singapore 012345', 'delivery', '2024-12-05 11:30:00'),
(14, 43.20, 5.00, 2.29, 'successful', '357 Jurong St, Singapore 123450', 'dine_in', '2024-12-05 16:45:00'),
(15, 69.40, 5.00, 3.87, 'successful', '486 Yishun St, Singapore 234501', 'dine_in', '2024-12-06 10:20:00'),
(16, 45.80, 5.00, 2.44, 'successful', '753 Woodlands Dr, Singapore 345012', 'dine_in', '2024-12-06 13:50:00'),
(17, 54.60, 5.00, 2.98, 'successful', '951 Clementi Rd, Singapore 450123', 'takeaway', '2024-12-07 09:45:00'),
(18, 67.90, 5.00, 3.77, 'successful', '264 Hougang Ave, Singapore 501234', 'takeaway', '2024-12-07 14:30:00'),
(19, 41.30, 5.00, 2.18, 'successful', '846 Bedok North St, Singapore 012345', 'delivery', '2024-12-08 11:15:00'),
(20, 56.70, 5.00, 3.10, 'successful', '153 Pasir Ris Dr, Singapore 123450', 'delivery', '2024-12-08 15:40:00'),
(4, 49.20, 5.00, 2.65, 'successful', '123 Main St, #01-01, Singapore 123456', 'dine_in', '2024-12-09 10:00:00'),
(5, 63.80, 5.00, 3.52, 'successful', '456 Orchard Rd, Singapore 234567', 'dine_in', '2024-12-09 14:25:00'),
(6, 44.90, 5.00, 2.38, 'successful', '789 Cecil St, Singapore 345678', 'dine_in', '2024-12-10 09:30:00'),
(7, 57.30, 5.00, 3.13, 'successful', '321 Victoria St, Singapore 456789', 'takeaway', '2024-12-10 13:55:00'),
(8, 72.40, 5.00, 4.04, 'successful', '654 Bencoolen St, Singapore 567890', 'takeaway', '2024-12-11 10:45:00'),
(9, 46.50, 5.00, 2.48, 'successful', '987 Beach Rd, Singapore 678901', 'delivery', '2024-12-11 15:10:00'),
(10, 59.80, 5.00, 3.28, 'successful', '147 Bugis St, Singapore 789012', 'delivery', '2024-12-12 11:20:00'),
(11, 43.70, 5.00, 2.31, 'successful', '258 Somerset Rd, Singapore 890123', 'delivery', '2024-12-12 16:35:00'),
(12, 61.90, 5.00, 3.41, 'successful', '369 Serangoon Rd, Singapore 901234', 'delivery', '2025-01-02 09:15:00'),
(13, 48.30, 5.00, 2.60, 'successful', '159 Tampines St, Singapore 012345', 'delivery', '2025-01-02 13:40:00'),
(14, 66.20, 5.00, 3.67, 'successful', '357 Jurong St, Singapore 123450', 'delivery', '2025-01-03 10:25:00'),
(15, 45.40, 5.00, 2.42, 'successful', '486 Yishun St, Singapore 234501', 'delivery', '2025-01-03 14:50:00'),
(16, 58.60, 5.00, 3.21, 'successful', '753 Woodlands Dr, Singapore 345012', 'delivery', '2025-01-04 11:05:00'),
(17, 42.90, 5.00, 2.27, 'successful', '951 Clementi Rd, Singapore 450123', 'delivery', '2025-01-04 15:30:00'),
(18, 69.80, 5.00, 3.89, 'successful', '264 Hougang Ave, Singapore 501234', 'delivery', '2025-01-05 10:15:00'),
(19, 47.20, 5.00, 2.52, 'successful', '846 Bedok North St, Singapore 012345', 'delivery', '2025-01-05 14:40:00'),
(20, 55.40, 5.00, 3.02, 'successful', '153 Pasir Ris Dr, Singapore 123450', 'delivery', '2025-01-06 09:55:00'),
(4, 64.70, 5.00, 3.58, 'successful', '123 Main St, #01-01, Singapore 123456', 'dine_in', '2025-01-06 14:20:00'),
(5, 43.90, 5.00, 2.33, 'successful', '456 Orchard Rd, Singapore 234567', 'dine_in', '2025-01-07 10:35:00'),
(6, 57.80, 5.00, 3.16, 'successful', '789 Cecil St, Singapore 345678', 'dine_in', '2025-01-07 15:00:00'),
(7, 70.60, 5.00, 3.94, 'successful', '321 Victoria St, Singapore 456789', 'takeaway', '2025-01-08 11:25:00'),
(8, 46.30, 5.00, 2.47, 'successful', '654 Bencoolen St, Singapore 567890', 'takeaway', '2025-01-08 15:50:00'),
(9, 59.40, 5.00, 3.26, 'successful', '987 Beach Rd, Singapore 678901', 'delivery', '2025-01-09 10:05:00'),
(10, 44.80, 5.00, 2.38, 'successful', '147 Bugis St, Singapore 789012', 'delivery', '2025-01-09 14:30:00'),
(11, 62.50, 5.00, 3.45, 'successful', '258 Somerset Rd, Singapore 890123', 'delivery', '2025-01-10 09:45:00'),
(12, 49.70, 5.00, 2.67, 'successful', '369 Serangoon Rd, Singapore 901234', 'delivery', '2025-01-10 14:10:00'),
(13, 67.30, 5.00, 3.74, 'successful', '159 Tampines St, Singapore 012345', 'delivery', '2025-01-11 10:35:00'),
(14, 45.90, 5.00, 2.44, 'successful', '357 Jurong St, Singapore 123450', 'delivery', '2025-01-11 15:00:00'),
(15, 58.20, 5.00, 3.19, 'successful', '486 Yishun St, Singapore 234501', 'delivery', '2025-01-12 11:25:00'),
(16, 43.40, 5.00, 2.30, 'successful', '753 Woodlands Dr, Singapore 345012', 'delivery', '2025-01-12 15:50:00'),
(17, 68.90, 5.00, 3.83, 'successful', '951 Clementi Rd, Singapore 450123', 'delivery', '2025-01-13 10:15:00'),
(18, 47.60, 5.00, 2.54, 'successful', '264 Hougang Ave, Singapore 501234', 'delivery', '2025-01-13 14:40:00'),
(19, 56.80, 5.00, 3.11, 'successful', '846 Bedok North St, Singapore 012345', 'delivery', '2025-01-14 09:55:00'),
(20, 63.40, 5.00, 3.50, 'successful', '153 Pasir Ris Dr, Singapore 123450', 'delivery', '2025-01-14 14:20:00'),
(4, 44.20, 5.00, 2.35, 'successful', '123 Main St, #01-01, Singapore 123456', 'dine_in', '2025-01-15 10:35:00'),
(5, 57.50, 5.00, 3.15, 'successful', '456 Orchard Rd, Singapore 234567', 'dine_in', '2025-01-15 15:00:00');

INSERT INTO Transaction_Details (transaction_id, product_id, quantity, price_per_item, subtotal) VALUES
(1, 1, 4, 3.50, 14.00),
(1, 7, 3, 5.50, 16.50),
(1, 2, 3, 4.00, 12.00),
(2, 4, 2, 28.00, 56.00),
(2, 8, 1, 6.00, 6.00),
(3, 1, 5, 3.50, 17.50),
(3, 3, 4, 4.00, 16.00),
(3, 9, 3, 4.50, 13.50),
(4, 1, 3, 3.50, 10.50),
(4, 2, 4, 4.00, 16.00),
(4, 7, 2, 5.50, 11.00),
(5, 4, 2, 28.00, 56.00),
(5, 7, 1, 5.50, 5.50),
(6, 3, 5, 4.00, 20.00),
(6, 8, 4, 6.00, 24.00),
(7, 5, 1, 32.00, 32.00),
(7, 1, 4, 3.50, 14.00),
(8, 6, 2, 30.00, 60.00),
(8, 9, 2, 4.50, 9.00),
(9, 2, 5, 4.00, 20.00),
(9, 7, 2, 5.50, 11.00),
(10, 4, 1, 28.00, 28.00),
(10, 8, 3, 6.00, 18.00),
(10, 1, 2, 3.50, 7.00),
(11, 3, 4, 4.00, 16.00),
(11, 9, 4, 4.50, 18.00),
(12, 5, 1, 32.00, 32.00),
(12, 2, 4, 4.00, 16.00),
(12, 8, 2, 6.00, 12.00),
(13, 6, 1, 30.00, 30.00),
(13, 1, 3, 3.50, 10.50),
(13, 7, 2, 5.50, 11.00),
(14, 3, 5, 4.00, 20.00),
(14, 9, 4, 4.50, 18.00),
(15, 4, 2, 28.00, 56.00),
(15, 8, 2, 6.00, 12.00),
(16, 2, 4, 4.00, 16.00),
(16, 7, 3, 5.50, 16.50),
(16, 1, 3, 3.50, 10.50),
(17, 5, 1, 32.00, 32.00),
(17, 3, 3, 4.00, 12.00),
(17, 8, 1, 6.00, 6.00),
(18, 6, 2, 30.00, 60.00),
(18, 7, 1, 5.50, 5.50),
(19, 1, 4, 3.50, 14.00),
(19, 2, 3, 4.00, 12.00),
(19, 9, 2, 4.50, 9.00),
(20, 4, 1, 28.00, 28.00),
(20, 8, 3, 6.00, 18.00),
(20, 3, 2, 4.00, 8.00),
(21, 2, 5, 4.00, 20.00),
(21, 7, 4, 5.50, 22.00),
(21, 1, 2, 3.50, 7.00),
(22, 5, 1, 32.00, 32.00),
(22, 9, 4, 4.50, 18.00),
(22, 2, 3, 4.00, 12.00),
(23, 1, 5, 3.50, 17.50),
(23, 3, 4, 4.00, 16.00),
(23, 8, 1, 6.00, 6.00),
(24, 4, 1, 28.00, 28.00),
(24, 2, 4, 4.00, 16.00),
(24, 7, 2, 5.50, 11.00),
(25, 6, 2, 30.00, 60.00),
(25, 9, 2, 4.50, 9.00),
(26, 3, 5, 4.00, 20.00),
(26, 8, 3, 6.00, 18.00),
(26, 1, 2, 3.50, 7.00),
(27, 5, 1, 32.00, 32.00),
(27, 1, 4, 3.50, 14.00),
(27, 8, 2, 6.00, 12.00),
(28, 2, 5, 4.00, 20.00),
(28, 7, 3, 5.50, 16.50),
(28, 3, 2, 4.00, 8.00),
(29, 4, 1, 28.00, 28.00),
(29, 9, 4, 4.50, 18.00),
(29, 1, 4, 3.50, 14.00),
(30, 3, 5, 4.00, 20.00),
(30, 8, 3, 6.00, 18.00),
(30, 2, 2, 4.00, 8.00),
(31, 6, 2, 30.00, 60.00),
(31, 7, 1, 5.50, 5.50),
(32, 4, 1, 28.00, 28.00),
(32, 8, 2, 6.00, 12.00),
(32, 1, 2, 3.50, 7.00),
(33, 5, 1, 32.00, 32.00),
(33, 9, 4, 4.50, 18.00),
(33, 2, 2, 4.00, 8.00),
(34, 3, 4, 4.00, 16.00),
(34, 7, 3, 5.50, 16.50),
(34, 1, 3, 3.50, 10.50),
(35, 6, 2, 30.00, 60.00),
(35, 8, 1, 6.00, 6.00),
(35, 2, 1, 4.00, 4.00),
(36, 4, 1, 28.00, 28.00),
(36, 9, 3, 4.50, 13.50),
(36, 1, 2, 3.50, 7.00),
(37, 5, 1, 32.00, 32.00),
(37, 7, 3, 5.50, 16.50),
(37, 3, 2, 4.00, 8.00),
(38, 6, 2, 30.00, 60.00),
(38, 8, 1, 6.00, 6.00),
(39, 2, 5, 4.00, 20.00),
(39, 7, 3, 5.50, 16.50),
(39, 1, 2, 3.50, 7.00),
(40, 4, 1, 28.00, 28.00),
(40, 9, 4, 4.50, 18.00),
(40, 3, 3, 4.00, 12.00),
(41, 5, 2, 32.00, 64.00),
(41, 7, 1, 5.50, 5.50),
(42, 3, 4, 4.00, 16.00),
(42, 8, 3, 6.00, 18.00),
(42, 2, 3, 4.00, 12.00),
(43, 6, 1, 30.00, 30.00),
(43, 9, 4, 4.50, 18.00),
(43, 1, 3, 3.50, 10.50),
(44, 6, 1, 30.00, 30.00),
(44, 7, 2, 5.50, 11.00),
(44, 1, 3, 3.50, 10.50),
(45, 5, 1, 32.00, 32.00),
(45, 7, 3, 5.50, 16.50),
(45, 2, 2, 4.00, 8.00),
(46, 4, 1, 28.00, 28.00),
(46, 9, 3, 4.50, 13.50),
(46, 1, 4, 3.50, 14.00),
(47, 6, 1, 30.00, 30.00),
(47, 8, 2, 6.00, 12.00),
(47, 1, 4, 3.50, 14.00),
(48, 5, 1, 32.00, 32.00),
(48, 7, 2, 5.50, 11.00),
(48, 1, 3, 3.50, 10.50),
(49, 6, 1, 30.00, 30.00),
(49, 8, 3, 6.00, 18.00),
(49, 2, 2, 4.00, 8.00),
(50, 4, 1, 28.00, 28.00),
(50, 9, 4, 4.50, 18.00),
(50, 3, 3, 4.00, 12.00);

-- Mission Templates
INSERT INTO Mission_Templates (name, description, points, requirements) VALUES
('First Purchase', 'Complete your first order', 50, 'Make 1 purchase'),
('Review Master', 'Leave 5 product reviews', 100, 'Submit 5 reviews'),
('Loyal Customer', 'Make 3 purchases in a month', 150, 'Complete 3 orders within 30 days'),
('Social Butterfly', 'Share 3 products on social media', 75, 'Share products on social platforms'),
('Birthday Special', 'Order on your birthday', 200, 'Place order on birthday date');

-- Rewards
INSERT INTO Rewards (user_id, mission_id, mission_name, status, points_earned) VALUES
(4, 1, 'First Purchase', 'completed', 50),
(5, 1, 'First Purchase', 'completed', 50),
(6, 1, 'First Purchase', 'completed', 50),
(7, 2, 'Review Master', 'pending', 100),
(8, 3, 'Loyal Customer', 'pending', 150);

-- FAQ Categories
INSERT INTO FAQ_Categories (name) VALUES
('Orders'), ('Products'), ('Delivery'), ('Returns'), ('Membership');

-- FAQs
INSERT INTO FAQs (category_id, question, answer) VALUES
(1, 'How do I track my order?', 'You can track your order using the tracking number provided in your order confirmation email.'),
(2, 'How long do the products stay fresh?', 'Our cream puffs are best consumed within 24 hours. Cakes can last up to 3 days when refrigerated.'),
(3, 'What are your delivery areas?', 'We deliver island-wide in Singapore. Additional charges apply for certain postal codes.'),
(4, 'What is your return policy?', 'Due to the nature of our products, we do not accept returns. Please contact us if you receive damaged items.'),
(5, 'How do I earn points?', 'Earn points through purchases, completing missions, and leaving reviews.');

-- Addresses
INSERT INTO Addresses (user_id, address_line_1, city, state, postcode, country, is_default) VALUES
(4, '123 Main St #01-01', 'Singapore', 'Singapore', '123456', 'Singapore', true),
(5, '456 Orchard Rd', 'Singapore', 'Singapore', '234567', 'Singapore', true),
(6, '789 Cecil St', 'Singapore', 'Singapore', '345678', 'Singapore', true);

-- Reviews
INSERT INTO Reviews (user_id, product_id, rating, comment, created_at) VALUES
(4, 1, 5, 'Perfect cream puff! Just the right amount of cream.', '2024-01-02'),
(5, 2, 4, 'Rich chocolate filling, very satisfying.', '2024-01-03'),
(6, 3, 5, 'Best matcha puff in town!', '2024-01-04'),
(7, 4, 5, 'Moist and decadent chocolate cake.', '2024-01-05'),
(8, 5, 4, 'Authentic New York cheesecake taste.', '2024-01-06'),
(9, 6, 5, 'Beautiful red velvet cake, not too sweet.', '2024-01-07'),
(10, 7, 4, 'Perfect bubble tea pearls.', '2024-01-08'),
(11, 8, 5, 'Smooth and rich matcha latte.', '2024-01-09');

-- Community Posts
INSERT INTO Community_Posts (title, image_url, description) VALUES
('New Matcha Series Launch!', '/images/matcha-series.jpg', 'Introducing our new Matcha Series featuring premium Uji matcha'),
('Christmas Collection 2024', '/images/christmas-2024.jpg', 'Pre-order our festive collection now'),
('Customer Appreciation Day', '/images/customer-day.jpg', 'Join us for double points this weekend');

-- User Favorites
INSERT INTO User_Favorites (user_id, product_id) VALUES
(4, 1), (4, 2), (5, 3), (6, 4), (7, 5), (8, 6);

-- Admin Actions Log
INSERT INTO Admin_Actions_Log (admin_id, action) VALUES
(1, 'Updated product prices'),
(2, 'Added new promotion campaign'),
(3, 'Processed refund for order #TRK001');

-- Sales Summary
INSERT INTO Sales_Summary (date, total_orders, gross_sales, returns, net_sales, delivery_fee, tax) VALUES
('2024-12-01', 2, 107.60, 0.00, 107.60, 10.00, 5.86),
('2024-12-02', 3, 172.50, 0.00, 172.50, 15.00, 9.45),
('2024-12-03', 2, 98.40, 0.00, 98.40, 10.00, 5.30),
('2024-12-04', 2, 106.40, 0.00, 106.40, 10.00, 5.78),
('2024-12-05', 2, 94.90, 0.00, 94.90, 10.00, 5.09),
('2024-12-06', 2, 115.20, 0.00, 115.20, 10.00, 6.31),
('2024-12-07', 2, 122.50, 0.00, 122.50, 10.00, 6.75),
('2024-12-08', 2, 98.00, 0.00, 98.00, 10.00, 5.28),
('2024-12-09', 2, 113.00, 0.00, 113.00, 10.00, 6.17),
('2024-12-10', 2, 102.20, 0.00, 102.20, 10.00, 5.51),
('2024-12-11', 2, 118.90, 0.00, 118.90, 10.00, 6.52),
('2024-12-12', 2, 103.50, 0.00, 103.50, 10.00, 5.59),
('2025-01-02', 2, 110.20, 0.00, 110.20, 10.00, 6.01),
('2025-01-03', 2, 111.60, 0.00, 111.60, 10.00, 6.09),
('2025-01-04', 2, 101.50, 0.00, 101.50, 10.00, 5.48),
('2025-01-05', 2, 117.00, 0.00, 117.00, 10.00, 6.41),
('2025-01-06', 2, 120.10, 0.00, 120.10, 10.00, 6.60),
('2025-01-07', 2, 101.70, 0.00, 101.70, 10.00, 5.49),
('2025-01-08', 2, 116.90, 0.00, 116.90, 10.00, 6.41),
('2025-01-09', 2, 104.20, 0.00, 104.20, 10.00, 5.64),
('2025-01-10', 2, 112.20, 0.00, 112.20, 10.00, 6.12),
('2025-01-11', 2, 113.20, 0.00, 113.20, 10.00, 6.18),
('2025-01-12', 2, 101.60, 0.00, 101.60, 10.00, 5.49),
('2025-01-13', 2, 116.50, 0.00, 116.50, 10.00, 6.37),
('2025-01-14', 2, 120.20, 0.00, 120.20, 10.00, 6.61),
('2025-01-15', 2, 101.70, 0.00, 101.70, 10.00, 5.50);