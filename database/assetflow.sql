CREATE DATABASE IF NOT EXISTS assetflow_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE assetflow_db;
CREATE TABLE IF NOT EXISTS users(
 id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(100) NOT NULL,
 email VARCHAR(150) NOT NULL UNIQUE, password VARCHAR(255) NOT NULL,
 role ENUM('admin','customer') NOT NULL DEFAULT 'customer',
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP);
CREATE TABLE IF NOT EXISTS categories(
 id INT AUTO_INCREMENT PRIMARY KEY, category_name VARCHAR(100) NOT NULL UNIQUE,
 description TEXT, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP);
CREATE TABLE IF NOT EXISTS assets(
 id INT AUTO_INCREMENT PRIMARY KEY, user_id INT NOT NULL, category_id INT NULL,
 asset_name VARCHAR(150) NOT NULL, asset_type VARCHAR(100), purchase_date DATE NULL, warranty_date DATE NULL,
 price DECIMAL(10,2) DEFAULT 0.00,
 status ENUM('Active','Inactive','Under Maintenance','Disposed') NOT NULL DEFAULT 'Active',
 description TEXT, image VARCHAR(255) NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
 FOREIGN KEY(category_id) REFERENCES categories(id) ON DELETE SET NULL);
INSERT IGNORE INTO categories(category_name,description) VALUES
('Electronics','Laptop, phone, tablet'),('Furniture','Table, chair, cabinet'),('Vehicle','Car, bike');
-- Admin account (admin@assetflow.com / Admin@123) is created automatically by the app on first run.
CREATE TABLE IF NOT EXISTS activity_log(id INT AUTO_INCREMENT PRIMARY KEY,user_id INT NULL,user_name VARCHAR(100),action VARCHAR(60) NOT NULL,detail VARCHAR(255),asset_id INT NULL,created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP);
CREATE TABLE IF NOT EXISTS login_attempts(id INT AUTO_INCREMENT PRIMARY KEY,email VARCHAR(150),ip VARCHAR(45),created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,INDEX(email,ip));
CREATE TABLE IF NOT EXISTS favorites(user_id INT NOT NULL,asset_id INT NOT NULL,PRIMARY KEY(user_id,asset_id),FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,FOREIGN KEY(asset_id) REFERENCES assets(id) ON DELETE CASCADE);
CREATE TABLE IF NOT EXISTS maintenance(id INT AUTO_INCREMENT PRIMARY KEY,asset_id INT NOT NULL,title VARCHAR(150) NOT NULL,due_date DATE NOT NULL,notes TEXT,done TINYINT(1) NOT NULL DEFAULT 0,done_at DATETIME NULL,created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,FOREIGN KEY(asset_id) REFERENCES assets(id) ON DELETE CASCADE);
