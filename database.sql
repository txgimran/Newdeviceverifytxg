-- Create database
CREATE DATABASE IF NOT EXISTS telegram_verify;
USE telegram_verify;

-- Create configuration table
CREATE TABLE IF NOT EXISTS verify_config (
    id INT PRIMARY KEY AUTO_INCREMENT,
    config_key VARCHAR(50) UNIQUE NOT NULL,
    config_value TEXT NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default configuration
INSERT INTO verify_config (config_key, config_value) VALUES
('loading', '{"enabled":true,"backgroundColor":"#667eea","gradientFrom":"#667eea","gradientTo":"#764ba2","logoText":"VERIFY","logoIcon":"fa-shield-check","spinnerColor":"#667eea","dotColor":"#667eea"}'),
('messages', '{"verified":"Verified","same_device":"Same Device","already_verified":"Already Verified","vpn":"VPN Detected","error":"Verification Failed"}'),
('videos', '{"loading":"","verified":"","same_device":"","already_verified":"","vpn":"","error":""}'),
('colors', '{"verified":"#10b981","same_device":"#f59e0b","already_verified":"#6366f1","vpn":"#ef4444","error":"#dc2626"}'),
('autoClose', 'true'),
('closeDelay', '3000');
