-- ============================================
-- MESSAGES SYSTEM SETUP FOR PAWS & HEARTS
-- ============================================
-- Run this in phpMyAdmin SQL tab to set up messaging

USE paws_hearts;

-- 1. Create messages table
CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    message_text TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_conversation (sender_id, receiver_id, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Add 'shelter' role to users table if not exists
ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'adopter', 'shelter', 'guest') DEFAULT 'adopter';

-- 3. SAMPLE DATA: Create test users (OPTIONAL - for testing)
-- Note: Password for all test users is 'password123'

-- Insert a shelter user
INSERT IGNORE INTO users (id, first_name, last_name, email, password, role) 
VALUES (100, 'Happy Paws', 'Shelter', 'shelter@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'shelter');

-- Insert an adopter user
INSERT IGNORE INTO users (id, first_name, last_name, email, password, role) 
VALUES (101, 'John', 'Doe', 'john@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'adopter');

-- 4. SAMPLE MESSAGES (OPTIONAL - for testing)
-- Messages between shelter and adopter
INSERT INTO messages (sender_id, receiver_id, message_text, created_at) VALUES
(101, 100, 'Hi! I saw your cute puppy listing. Is it still available?', '2026-01-19 10:00:00'),
(100, 101, 'Yes, Buddy is still available! Would you like to schedule a visit?', '2026-01-19 10:05:00'),
(101, 100, 'That would be great! What days work for you?', '2026-01-19 10:10:00'),
(100, 101, 'We are open Tuesday-Saturday, 9am-5pm. Any of those days work?', '2026-01-19 10:15:00'),
(101, 100, 'Perfect! Can I come by on Wednesday at 2pm?', '2026-01-19 10:20:00');

-- ============================================
-- VERIFICATION QUERIES
-- ============================================
-- Run these to check if everything is set up correctly:

-- Check if messages table exists
SHOW TABLES LIKE 'messages';

-- Check messages
SELECT 
    m.id,
    CONCAT(u1.first_name, ' ', u1.last_name) as sender,
    CONCAT(u2.first_name, ' ', u2.last_name) as receiver,
    m.message_text,
    m.created_at
FROM messages m
JOIN users u1 ON m.sender_id = u1.id
JOIN users u2 ON m.receiver_id = u2.id
ORDER BY m.created_at DESC;

-- ============================================
-- DONE! Now you can:
-- 1. Login as john@test.com / password123
-- 2. Click "Messages" to see conversation
-- 3. Send messages back and forth
-- ============================================
