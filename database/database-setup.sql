-- Drop the database if it exists
DROP DATABASE IF EXISTS PuffLab;

-- Database creation and selection
CREATE DATABASE IF NOT EXISTS PuffLab;
USE PuffLab;

-- Drop tables if they exist
DROP TABLE IF EXISTS Users;
DROP TABLE IF EXISTS Product_Categories;
DROP TABLE IF EXISTS Products;
DROP TABLE IF EXISTS Cart;
DROP TABLE IF EXISTS Vouchers;
DROP TABLE IF EXISTS Transactions;
DROP TABLE IF EXISTS Transaction_Details;
DROP TABLE IF EXISTS Orders;
DROP TABLE IF EXISTS FAQ_Categories;
DROP TABLE IF EXISTS FAQs;
DROP TABLE IF EXISTS Addresses;
DROP TABLE IF EXISTS Sales_Summary;

-- Users table for storing user information
CREATE TABLE Users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) UNIQUE NOT NULL,
    first_name VARCHAR(255) NOT NULL,
    last_name VARCHAR(255) NOT NULL,
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
    FOREIGN KEY (product_id) REFERENCES Products(product_id) ON DELETE CASCADE
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
    product_id INT,
    quantity INT NOT NULL,
    price_per_item DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (transaction_id) REFERENCES Transactions(transaction_id),
    FOREIGN KEY (product_id) REFERENCES Products(product_id) ON DELETE SET NULL
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

-- Transaction indexes
CREATE INDEX idx_transaction_date ON Transactions(transaction_date);
CREATE INDEX idx_transaction_payment_status ON Transactions(payment_status);
CREATE INDEX idx_transaction_total ON Transactions(total_amount);

-- Cart and favorites indexes
CREATE INDEX idx_cart_user ON Cart(user_id, added_at);

-- Address indexes
CREATE INDEX idx_address_user ON Addresses(user_id, is_default);

-- FAQ indexes
CREATE INDEX idx_faq_category ON FAQs(category_id);

-- Sales and analytics indexes
CREATE INDEX idx_sales_date ON Sales_Summary(date);

-- Voucher indexes
CREATE INDEX idx_voucher_expiry ON Vouchers(expiry_date);

-- Admin Users
INSERT INTO Users (username, first_name, last_name, email, password, role) VALUES
('admin1', 'John', 'Doe', 'admin1@pufflab.com', '$argon2id$v=19$m=65536,t=4,p=3$QzBRYlp6ZGxuOXF2Sm1Uaw$/rIVH9L4CqkoXXInX1phNH7uop1X1Feb9o6mXHpBWx0', 'admin'),
('admin2', 'Jane', 'Smith', 'admin2@pufflab.com', '$argon2id$v=19$m=65536,t=4,p=3$UG55V1B0ZUZxM0paYUN0VA$qlNuehEO/tLkUc3bbkr+6bJ2I+Smb+NhJZUzBofREIc', 'admin'),
('admin3', 'Mike', 'Johnson', 'admin3@pufflab.com', '$argon2id$v=19$m=65536,t=4,p=3$Sm1mN2xXOVZ4V2xHMk1FZg$LtB+HhWOfG0FEyogmntFGB6CqaK7Onr7w1qixT5zSAs', 'admin');

-- Member Users
INSERT INTO Users (username, first_name, last_name, email, password, role, points) VALUES
('member1', 'Alice', 'Wong', 'alice@email.com', '$argon2id$v=19$m=65536,t=4,p=3$Rld0dUppN1lpSUxoN0FRVA$3iXNG2AxjE1d5F+y666rGrBhfrwDEG1ZY04vvGVu0JM', 'member', 100),
('member2', 'Bob', 'Tan', 'bob@email.com', '$argon2id$v=19$m=65536,t=4,p=3$RnpCRGE0SGJVNnRaMWx5VQ$HOUkujg4TuhnyyJgAcMnWDSh+Gm/Zev1I39oNddKQq0', 'member', 150),
('member3', 'Charlie', 'Lee', 'charlie@email.com', '$argon2id$v=19$m=65536,t=4,p=3$bmk4Ri8wa04ucXYwMWFVRw$MVHvaJO0FcSve5kiqfruPT9LTyaEmxIDNNw34ThlJ1Y', 'member', 200),
('member4', 'Diana', 'Chen', 'diana@email.com', '$argon2id$v=19$m=65536,t=4,p=3$bml0NEkveThrY2JiV0RaMQ$fimv+MLQbjs5LePORGaG/D1WP5OVP+sDorO9xX8UYMQ', 'member', 75),
('member5', 'Edward', 'Lim', 'edward@email.com', '$argon2id$v=19$m=65536,t=4,p=3$QmYuQ250Y21tSTZQVUdHWQ$0CWHNmdLsUt4HYUujt99n6HeZnjot1TpJQYMlcxJjSA', 'member', 300),
('member6', 'Fiona', 'Ng', 'fiona@email.com', '$argon2id$v=19$m=65536,t=4,p=3$SlRGY2JNWnBNY3R2SlA5Yw$RaoAmer9/jsLQaww7KpMSt3N0n0nf0MqdLeg8a/GM6U', 'member', 250),
('member7', 'George', 'Tan', 'george@email.com', '$argon2id$v=19$m=65536,t=4,p=3$NlBWVlo0eFB0NHpZZmJyag$ljbEgk83cTA6Kt5uFitlCKx5sGYD96MKVymwtJxWi1g', 'member', 175),
('member8', 'Hannah', 'Wu', 'hannah@email.com', '$argon2id$v=19$m=65536,t=4,p=3$YW01U1phc2FsYUxCaTRaUw$5b94aTDc1eASBoCV1SvzkmD7JYFt22ccnx8DsabsE0E', 'member', 125),
('member9', 'Ian', 'Zhang', 'ian@email.com', '$argon2id$v=19$m=65536,t=4,p=3$NW5GMXZjLk9FNlRWc05NUw$GgrU7X1g7LEjb/MMfw82YbvKnUBF2IAb0O7+OMro3bw', 'member', 225),
('member10', 'Julia', 'Liu', 'julia@email.com', '$argon2id$v=19$m=65536,t=4,p=3$bWliTy9xQ1RHWmYvQ3hscA$cxnrPamzkXHAGn5TMahEQq5qLGVhgbLBu+j+OZf68kk', 'member', 350),
('member11', 'Kevin', 'Wang', 'kevin@email.com', '$argon2id$v=19$m=65536,t=4,p=3$Rld0dUppN1lpSUxoN0FRVA$3iXNG2AxjE1d5F+y666rGrBhfrwDEG1ZY04vvGVu0JM', 'member', 400),
('member12', 'Linda', 'Goh', 'linda@email.com', '$argon2id$v=19$m=65536,t=4,p=3$RnpCRGE0SGJVNnRaMWx5VQ$HOUkujg4TuhnyyJgAcMnWDSh+Gm/Zev1I39oNddKQq0', 'member', 275),
('member13', 'Michael', 'Ong', 'michael@email.com', '$argon2id$v=19$m=65536,t=4,p=3$bmk4Ri8wa04ucXYwMWFVRw$MVHvaJO0FcSve5kiqfruPT9LTyaEmxIDNNw34ThlJ1Y', 'member', 150),
('member14', 'Nancy', 'Chua', 'nancy@email.com', '$argon2id$v=19$m=65536,t=4,p=3$bml0NEkveThrY2JiV0RaMQ$fimv+MLQbjs5LePORGaG/D1WP5OVP+sDorO9xX8UYMQ', 'member', 200),
('member15', 'Oliver', 'Teo', 'oliver@email.com', '$argon2id$v=19$m=65536,t=4,p=3$QmYuQ250Y21tSTZQVUdHWQ$0CWHNmdLsUt4HYUujt99n6HeZnjot1TpJQYMlcxJjSA', 'member', 325),
('member16', 'Patricia', 'Koh', 'patricia@email.com', '$argon2id$v=19$m=65536,t=4,p=3$SlRGY2JNWnBNY3R2SlA5Yw$RaoAmer9/jsLQaww7KpMSt3N0n0nf0MqdLeg8a/GM6U', 'member', 275),
('member17', 'Quinn', 'Sim', 'quinn@email.com', '$argon2id$v=19$m=65536,t=4,p=3$NlBWVlo0eFB0NHpZZmJyag$ljbEgk83cTA6Kt5uFitlCKx5sGYD96MKVymwtJxWi1g', 'member', 225),
('member18', 'Ryan', 'Low', 'ryan@email.com', '$argon2id$v=19$m=65536,t=4,p=3$YW01U1phc2FsYUxCaTRaUw$5b94aTDc1eASBoCV1SvzkmD7JYFt22ccnx8DsabsE0E', 'member', 175),
('member19', 'Sarah', 'Yeo', 'sarah@email.com', '$argon2id$v=19$m=65536,t=4,p=3$NW5GMXZjLk9FNlRWc05NUw$GgrU7X1g7LEjb/MMfw82YbvKnUBF2IAb0O7+OMro3bw', 'member', 300),
('member20', 'Tom', 'Pang', 'tom@email.com', '$argon2id$v=19$m=65536,t=4,p=3$bWliTy9xQ1RHWmYvQ3hscA$cxnrPamzkXHAGn5TMahEQq5qLGVhgbLBu+j+OZf68kk', 'member', 250);

-- Product Categories
INSERT INTO Product_Categories (name, description) VALUES
('Puffs', 'Delicious cream puffs in various flavors'),
('Cakes', 'Freshly baked cakes for all occasions'),
('Beverages', 'Refreshing drinks to complement your treats');

-- Products
INSERT INTO Products (name, category_id, description, price, stock_quantity, image_url) VALUES
('Vanilla Cream Puff', 1, 'Original cream puff filled with vanilla custard', 3.00, 100, '../assets/images/vanillapuff.jpg'),
('Chocolate Cream Puff', 1, 'Signature cream puff with chocolate cream filling', 3.00, 100, '../assets/images/chocopuff.jpg'),
('Matcha Cream Puff', 1, 'Signature premium cream puff with matcha cream filling', 4.00, 100, '../assets/images/matchapuff.jpg'),
('Earl Grey Cream Puff', 1, 'Signature premium cream puff with Earl Grey cream filling', 4.00, 100, '../assets/images/earlgreypuff.jpg'),
('Lotus Biscoff Cream Puff', 1, 'Signature luxury cream puff with lotus biscoff cream filling', 5.00, 100, '../assets/images/biscoffpuff.png'),
('Tiramisu', 2, 'Classic Italian dessert with layers of coffee-soaked cake and mascarpone', 13.00, 30, '../assets/images/tiramisu.png'),
('Cheese Cake', 2, 'Deliciously creamy cheesecake with a buttery crust', 13.00, 30, '../assets/images/cheesecake.png'),
('Brownies', 2, 'Rich, fudgy brownies with a perfect balance of sweetness', 8.00, 30, '../assets/images/brownies.png'),
('Coffee Latte', 3, 'A rich, aromatic coffee with steamed milk', 6.00, 50, '../assets/images/coffeelatte.png'),
('Chocolate Latte', 3, 'Smooth chocolate latte made with rich cocoa and milk', 8.00, 50, '../assets/images/chocolatte.png'),
('Matcha Latte', 3, 'Creamy matcha latte made with premium matcha powder', 10.00, 50, '../assets/images/matchalatte.png'),
('Americano', 3, 'Strong black coffee made with espresso', 5.00, 50, '../assets/images/americano.png'),
('Strawberry Latte', 3, 'Refreshing strawberry-flavored latte', 7.00, 50, '../assets/images/strawberrylatte.png'),
('Yam Latte', 3, 'Sweet and creamy yam-flavored latte', 7.00, 50, '../assets/images/yamlatte.png'),
('Thai Milk Tea', 3, 'Sweet Thai milk tea with a unique blend of spices', 7.00, 50, '../assets/images/thaimilktea.png'),
('Thai Green Tea', 3, 'Fragrant Thai green tea with a creamy twist', 7.00, 50, '../assets/images/thaigreentea.png'),
('Honey Lemon', 3, 'Refreshing honey lemon drink', 6.00, 50, '../assets/images/honeylemon.png');

-- Vouchers
INSERT INTO Vouchers (voucher_code, discount_percentage, expiry_date) VALUES
('WELCOME10', 10.00, '2025-12-31'),
('BDAY20', 20.00, '2025-12-31'),
('MEMBER15', 15.00, '2025-12-31');

-- Transactions (December 2024 - January 2025)
INSERT INTO Transactions (user_id, total_amount, delivery_fee, tax_amount, payment_status, delivery_address, shipping_method, transaction_date) VALUES
(4, 42.40, 5.00, 2.24, 'successful', '123 Main St, #01-01, Singapore 123456', 'dine_in', '2024-12-21 10:00:00'),
(5, 65.20, 5.00, 3.62, 'successful', '456 Orchard Rd, Singapore 234567', 'dine_in', '2024-12-21 14:30:00'),
(6, 47.70, 5.00, 2.56, 'successful', '789 Cecil St, Singapore 345678', 'dine_in', '2024-12-22 09:15:00'),
(7, 53.00, 5.00, 2.88, 'successful', '321 Victoria St, Singapore 456789', 'takeaway', '2024-12-22 11:45:00'),
(8, 71.80, 5.00, 4.01, 'successful', '654 Bencoolen St, Singapore 567890', 'takeaway', '2024-12-22 15:20:00'),
(9, 39.50, 5.00, 2.07, 'successful', '987 Beach Rd, Singapore 678901', 'delivery', '2024-12-23 10:30:00'),
(10, 58.90, 5.00, 3.23, 'successful', '147 Bugis St, Singapore 789012', 'delivery', '2024-12-23 13:45:00'),
(11, 44.10, 5.00, 2.34, 'successful', '258 Somerset Rd, Singapore 890123', 'delivery', '2024-12-24 09:00:00'),
(12, 62.30, 5.00, 3.44, 'successful', '369 Serangoon Rd, Singapore 901234', 'delivery', '2024-12-24 14:15:00'),
(13, 51.70, 5.00, 2.80, 'successful', '159 Tampines St, Singapore 012345', 'delivery', '2024-12-25 11:30:00'),
(14, 43.20, 5.00, 2.29, 'successful', '357 Jurong St, Singapore 123450', 'dine_in', '2024-12-25 16:45:00'),
(15, 69.40, 5.00, 3.87, 'successful', '486 Yishun St, Singapore 234501', 'dine_in', '2024-12-26 10:20:00'),
(16, 45.80, 5.00, 2.44, 'successful', '753 Woodlands Dr, Singapore 345012', 'dine_in', '2024-12-26 13:50:00'),
(17, 54.60, 5.00, 2.98, 'successful', '951 Clementi Rd, Singapore 450123', 'takeaway', '2024-12-27 09:45:00'),
(18, 67.90, 5.00, 3.77, 'successful', '264 Hougang Ave, Singapore 501234', 'takeaway', '2024-12-27 14:30:00'),
(19, 41.30, 5.00, 2.18, 'successful', '846 Bedok North St, Singapore 012345', 'delivery', '2024-12-28 11:15:00'),
(20, 56.70, 5.00, 3.10, 'successful', '153 Pasir Ris Dr, Singapore 123450', 'delivery', '2024-12-28 15:40:00'),
(4, 49.20, 5.00, 2.65, 'successful', '123 Main St, #01-01, Singapore 123456', 'dine_in', '2024-12-29 10:00:00'),
(5, 63.80, 5.00, 3.52, 'successful', '456 Orchard Rd, Singapore 234567', 'dine_in', '2024-12-29 14:25:00'),
(6, 44.90, 5.00, 2.38, 'successful', '789 Cecil St, Singapore 345678', 'dine_in', '2024-12-30 09:30:00'),
(7, 57.30, 5.00, 3.13, 'successful', '321 Victoria St, Singapore 456789', 'takeaway', '2024-12-30 13:55:00'),
(8, 72.40, 5.00, 4.04, 'successful', '654 Bencoolen St, Singapore 567890', 'takeaway', '2024-12-31 10:45:00'),
(9, 46.50, 5.00, 2.48, 'successful', '987 Beach Rd, Singapore 678901', 'delivery', '2024-12-31 15:10:00'),
(10, 59.80, 5.00, 3.28, 'successful', '147 Bugis St, Singapore 789012', 'delivery', '2025-01-01 11:20:00'),
(11, 43.70, 5.00, 2.31, 'successful', '258 Somerset Rd, Singapore 890123', 'delivery', '2025-01-01 16:35:00'),
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

-- Sales Summary
INSERT INTO Sales_Summary (date, total_orders, gross_sales, returns, net_sales, delivery_fee, tax) VALUES
('2024-12-21', 2, 107.60, 0.00, 107.60, 10.00, 5.86),
('2024-12-22', 3, 172.50, 0.00, 172.50, 15.00, 9.45),
('2024-12-23', 2, 98.40, 0.00, 98.40, 10.00, 5.30),
('2024-12-24', 2, 106.40, 0.00, 106.40, 10.00, 5.78),
('2024-12-25', 2, 94.90, 0.00, 94.90, 10.00, 5.09),
('2024-12-26', 2, 115.20, 0.00, 115.20, 10.00, 6.31),
('2024-12-27', 2, 122.50, 0.00, 122.50, 10.00, 6.75),
('2024-12-28', 2, 98.00, 0.00, 98.00, 10.00, 5.28),
('2024-12-29', 2, 113.00, 0.00, 113.00, 10.00, 6.17),
('2024-12-30', 2, 102.20, 0.00, 102.20, 10.00, 5.51),
('2024-12-31', 2, 118.90, 0.00, 118.90, 10.00, 6.52),
('2025-01-01', 2, 103.50, 0.00, 103.50, 10.00, 5.59),
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
