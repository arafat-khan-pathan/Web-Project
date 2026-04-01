-- ============================================
-- CREATE TEST USERS FOR MESSAGING
-- ============================================
-- Run this in phpMyAdmin SQL tab

USE paws_hearts;

-- First, make sure roles include 'shelter' and 'guest'
ALTER TABLE users MODIFY COLUMN role ENUM('adopter', 'shelter', 'admin', 'guest') DEFAULT 'adopter';

-- Delete test users if they exist (to avoid duplicates)
DELETE FROM users WHERE email IN ('shelter@test.com', 'john@test.com');

-- Create Shelter User
-- Email: shelter@test.com
-- Password: shelter123
INSERT INTO users (first_name, last_name, email, password, role) 
VALUES ('Happy Paws', 'Shelter', 'shelter@test.com', '$2y$10$rZ7qhYGvVxZ5y5y5y5y5y5OeJxLxLxLxLxLxLxLxLxLxLxLxLxL.C', 'shelter');

-- Get the ID of the shelter user we just created
SET @shelter_id = LAST_INSERT_ID();

-- Create Adopter User  
-- Email: john@test.com
-- Password: john123
INSERT INTO users (first_name, last_name, email, password, role) 
VALUES ('John', 'Doe', 'john@test.com', '$2y$10$aZ9qhYGvVxZ6y6y6y6y6y6OeJxLxLxLxLxLxLxLxLxLxLxLxLxL.D', 'adopter');

-- Get the ID of the adopter user we just created
SET @adopter_id = LAST_INSERT_ID();

-- Insert sample messages between them
INSERT INTO messages (sender_id, receiver_id, message_text, created_at, is_read) VALUES
(@adopter_id, @shelter_id, 'Hi! I saw your cute puppy listing. Is it still available?', NOW() - INTERVAL 2 HOUR, 1),
(@shelter_id, @adopter_id, 'Yes, Buddy is still available! Would you like to schedule a visit?', NOW() - INTERVAL 1 HOUR, 1),
(@adopter_id, @shelter_id, 'That would be great! What days work for you?', NOW() - INTERVAL 50 MINUTE, 1),
(@shelter_id, @adopter_id, 'We are open Tuesday-Saturday, 9am-5pm. Any of those days work?', NOW() - INTERVAL 30 MINUTE, 1),
(@adopter_id, @shelter_id, 'Perfect! Can I come by on Wednesday at 2pm?', NOW() - INTERVAL 10 MINUTE, 0);

-- Show the created users
SELECT id, first_name, last_name, email, role FROM users WHERE email IN ('shelter@test.com', 'john@test.com');

-- ============================================
-- LOGIN CREDENTIALS:
-- ============================================
-- Adopter: john@test.com / john123
-- Shelter: shelter@test.com / shelter123
-- ============================================
