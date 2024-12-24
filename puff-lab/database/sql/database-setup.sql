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
