-- ============================================
-- ADOPTIONS TABLE SETUP
-- ============================================
-- Run this in phpMyAdmin SQL tab

USE paws_hearts;

-- 1. Create adoptions table to track pet adoptions
CREATE TABLE IF NOT EXISTS adoptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pet_id INT NOT NULL,
    adopter_id INT NOT NULL,
    adoption_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'approved', 'completed', 'rejected') DEFAULT 'pending',
    notes TEXT,
    FOREIGN KEY (pet_id) REFERENCES pets(id) ON DELETE CASCADE,
    FOREIGN KEY (adopter_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_adoption (pet_id, adopter_id),
    INDEX idx_adopter (adopter_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- SAMPLE TEST DATA (OPTIONAL)
-- ============================================
-- This adds sample adoptions for testing
-- Make sure you have users and pets in your database first!

-- Example: User with ID 2 (replace with your actual user ID) adopts some pets
-- To find your user ID, run: SELECT id, first_name, last_name FROM users;

-- Insert sample adoptions (adjust IDs based on your database)
-- Uncomment and modify these lines after checking your user and pet IDs:

-- INSERT INTO adoptions (pet_id, adopter_id, status, adoption_date, notes) VALUES
-- (1, 2, 'completed', '2024-01-15 10:00:00', 'Max found his forever home!'),
-- (3, 2, 'completed', '2024-03-20 14:30:00', 'Charlie is loving his new family'),
-- (5, 2, 'approved', NOW(), 'Daisy adoption approved, pickup scheduled');

-- ============================================
-- VERIFICATION QUERIES
-- ============================================

-- Check if adoptions table exists
SHOW TABLES LIKE 'adoptions';

-- View all adoptions
SELECT 
    a.id,
    p.name as pet_name,
    p.species,
    CONCAT(u.first_name, ' ', u.last_name) as adopter_name,
    a.status,
    a.adoption_date
FROM adoptions a
JOIN pets p ON a.pet_id = p.id
JOIN users u ON a.adopter_id = u.id
ORDER BY a.adoption_date DESC;

-- ============================================
-- HOW TO USE:
-- ============================================
-- 1. Run this SQL in phpMyAdmin
-- 2. Find your user ID: SELECT id, email FROM users WHERE email = 'your@email.com';
-- 3. Find pet IDs: SELECT id, name FROM pets LIMIT 5;
-- 4. Insert test adoption:
--    INSERT INTO adoptions (pet_id, adopter_id, status) 
--    VALUES (1, YOUR_USER_ID, 'completed');
-- 5. Visit profile.php to see your adopted pets!
-- ============================================
