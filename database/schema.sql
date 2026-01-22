-- Create database
CREATE DATABASE IF NOT EXISTS ecommerce_db;
USE ecommerce_db;

-- Products table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    image_path VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Cart items table
CREATE TABLE IF NOT EXISTS cart_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    session_id VARCHAR(255) NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_session_product (session_id, product_id)
);

-- Performance indexes
CREATE INDEX idx_products_created_at ON products(created_at);
CREATE INDEX idx_products_price ON products(price);
CREATE INDEX idx_cart_items_session_id ON cart_items(session_id);
CREATE INDEX idx_cart_items_product_id ON cart_items(product_id);
CREATE INDEX idx_cart_items_updated_at ON cart_items(updated_at);

-- Insert sample products
INSERT INTO products (name, description, price, image_path) VALUES
('Laptop', 'High-performance laptop for work and gaming', 999.99, 'https://picsum.photos/300/200?random=1'),
('Smartphone', 'Latest smartphone with advanced features', 699.99, 'https://picsum.photos/300/200?random=2'),
('Headphones', 'Wireless noise-cancelling headphones', 199.99, 'https://picsum.photos/300/200?random=3'),
('Tablet', '10-inch tablet perfect for productivity', 399.99, 'https://picsum.photos/300/200?random=4'),
('Smart Watch', 'Fitness and health tracking smartwatch', 299.99, 'https://picsum.photos/300/200?random=5');
