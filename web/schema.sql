CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS licenses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    plan_type ENUM('free', 'pro') DEFAULT 'free',
    billing_cycle ENUM('lifetime', 'monthly', 'annual') DEFAULT 'lifetime',
    status ENUM('active', 'temporary', 'cancelled') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Default Admin User (Password is: admin123)
-- Make sure to change this in production!
INSERT IGNORE INTO users (id, name, email, password, role) VALUES 
(1, 'Admin', 'admin@kaviz.xcode.lk', '$2y$10$eE/R1c8P/M2k6xZz2dG56OqD/F3Q0q3v7z8r9t.s0V1b2c3d4e5f6', 'admin');
