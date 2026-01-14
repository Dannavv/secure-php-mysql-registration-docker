CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    roll VARCHAR(50) UNIQUE NOT NULL,
    webmail VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(15) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE app_logs (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    log_time DATETIME NOT NULL,
    level ENUM('INFO', 'WARNING', 'ERROR', 'SECURITY') NOT NULL,
    file VARCHAR(255) NOT NULL,
    line INT NULL,
    `function` VARCHAR(100) NULL, 
    message TEXT NOT NULL,
    query_text TEXT NULL,
    trace JSON NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_log_time (log_time),
    INDEX idx_level (level),
    INDEX idx_file (file)
);