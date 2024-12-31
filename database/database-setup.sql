-- Database creation and selection
CREATE DATABASE IF NOT EXISTS PuffLab;
USE PuffLab;

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
('WELCOME10', 10.00, '2024-12-31'),
('BDAY20', 20.00, '2024-12-31'),
('MEMBER15', 15.00, '2024-12-31');

-- Transactions
INSERT INTO Transactions (user_id, total_amount, payment_status, delivery_address, shipping_method, transaction_date) VALUES
(4, 45.00, 'successful', '123 Main St, #01-01, Singapore 123456', 'delivery', '2024-01-01 10:00:00'),
(5, 32.00, 'successful', '456 Orchard Rd, Singapore 234567', 'takeaway', '2024-01-02 11:30:00'),
(6, 28.00, 'successful', '789 Cecil St, Singapore 345678', 'dine_in', '2024-01-03 12:45:00'),
(7, 55.50, 'successful', '321 Victoria St, Singapore 456789', 'delivery', '2024-01-04 14:15:00'),
(8, 42.00, 'successful', '654 Bencoolen St, Singapore 567890', 'takeaway', '2024-01-05 15:30:00'),
(9, 36.00, 'successful', '987 Beach Rd, Singapore 678901', 'dine_in', '2024-01-06 16:45:00'),
(10, 48.50, 'successful', '147 Bugis St, Singapore 789012', 'delivery', '2024-01-07 17:30:00'),
(11, 39.00, 'successful', '258 Somerset Rd, Singapore 890123', 'takeaway', '2024-01-08 18:15:00'),
(12, 33.50, 'successful', '369 Serangoon Rd, Singapore 901234', 'dine_in', '2024-01-09 19:00:00'),
(13, 52.00, 'successful', '159 Tampines St, Singapore 012345', 'delivery', '2024-01-10 10:30:00'),
(14, 44.50, 'successful', '357 Jurong St, Singapore 123450', 'takeaway', '2024-01-11 11:45:00'),
(15, 38.00, 'successful', '486 Yishun St, Singapore 234501', 'dine_in', '2024-01-12 13:00:00'),
(16, 47.50, 'successful', '753 Woodlands Dr, Singapore 345012', 'delivery', '2024-01-13 14:30:00'),
(17, 41.00, 'successful', '951 Clementi Rd, Singapore 450123', 'takeaway', '2024-01-14 15:45:00'),
(18, 35.50, 'successful', '264 Hougang Ave, Singapore 501234', 'dine_in', '2024-01-15 17:00:00'),
(19, 50.00, 'successful', '846 Bedok North St, Singapore 012345', 'delivery', '2024-01-16 18:30:00'),
(20, 43.50, 'successful', '153 Pasir Ris Dr, Singapore 123450', 'takeaway', '2024-01-17 19:45:00'),
(4, 37.00, 'successful', '725 Ang Mo Kio Ave, Singapore 234501', 'dine_in', '2024-01-18 11:00:00'),
(5, 49.50, 'successful', '936 Sengkang West Rd, Singapore 345012', 'delivery', '2024-01-19 12:30:00'),
(6, 40.50, 'successful', '847 Punggol Field, Singapore 450123', 'takeaway', '2024-01-20 13:45:00');

-- Transaction Details
INSERT INTO Transaction_Details (transaction_id, product_id, quantity, price_per_item, subtotal) VALUES
(1, 1, 5, 3.50, 17.50),
(1, 2, 3, 4.00, 12.00),
(2, 4, 1, 28.00, 28.00),
(3, 5, 1, 32.00, 32.00),
(4, 3, 4, 4.00, 16.00),
(4, 7, 2, 5.50, 11.00),
(5, 6, 1, 30.00, 30.00),
(6, 8, 3, 6.00, 18.00);

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

-- Orders
INSERT INTO Orders (transaction_id, tracking_number, status, estimated_delivery) VALUES
(1, 'TRK001', 'delivered', '2024-01-02'),
(2, 'TRK002', 'delivered', '2024-01-03'),
(3, 'TRK003', 'delivered', '2024-01-04'),
(4, 'TRK004', 'delivered', '2024-01-05'),
(5, 'TRK005', 'delivered', '2024-01-06'),
(6, 'TRK006', 'delivered', '2024-01-07'),
(7, 'TRK007', 'delivered', '2024-01-08'),
(8, 'TRK008', 'delivered', '2024-01-09'),
(9, 'TRK009', 'delivered', '2024-01-10'),
(10, 'TRK010', 'delivered', '2024-01-11'),
(11, 'TRK011', 'delivered', '2024-01-12'),
(12, 'TRK012', 'delivered', '2024-01-13'),
(13, 'TRK013', 'delivered', '2024-01-14'),
(14, 'TRK014', 'delivered', '2024-01-15'),
(15, 'TRK015', 'delivered', '2024-01-16'),
(16, 'TRK016', 'shipped', '2024-01-17'),
(17, 'TRK017', 'shipped', '2024-01-18'),
(18, 'TRK018', 'processing', '2024-01-19'),
(19, 'TRK019', 'processing', '2024-01-20'),
(20, 'TRK020', 'processing', '2024-01-21');

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
('2024-01-01', 1, 45.00, 0.00, 45.00, 5.00, 3.15),
('2024-01-02', 1, 32.00, 0.00, 32.00, 0.00, 2.24),
('2024-01-03', 1, 28.00, 0.00, 28.00, 0.00, 1.96),
('2024-01-04', 1, 55.50, 0.00, 55.50, 5.00, 3.89),
('2024-01-05', 1, 42.00, 0.00, 42.00, 0.00, 2.94),
('2024-01-06', 1, 36.00, 0.00, 36.00, 0.00, 2.52),
('2024-01-07', 1, 48.50, 0.00, 48.50, 5.00, 3.40),
('2024-01-08', 1, 39.00, 0.00, 39.00, 0.00, 2.73),
('2024-01-09', 1, 33.50, 0.00, 33.50, 0.00, 2.35),
('2024-01-10', 1, 52.00, 0.00, 52.00, 5.00, 3.64),
('2024-01-11', 1, 44.50, 0.00, 44.50, 0.00, 3.12),
('2024-01-12', 1, 38.00, 0.00, 38.00, 0.00, 2.66),
('2024-01-13', 1, 47.50, 0.00, 47.50, 5.00, 3.33),
('2024-01-14', 1, 41.00, 0.00, 41.00, 0.00, 2.87),
('2024-01-15', 1, 35.50, 0.00, 35.50, 0.00, 2.49),
('2024-01-16', 1, 50.00, 0.00, 50.00, 5.00, 3.50),
('2024-01-17', 1, 43.50, 0.00, 43.50, 0.00, 3.05),
('2024-01-18', 1, 37.00, 0.00, 37.00, 0.00, 2.59),
('2024-01-19', 1, 49.50, 0.00, 49.50, 5.00, 3.47),
('2024-01-20', 1, 40.50, 0.00, 40.50, 0.00, 2.84);