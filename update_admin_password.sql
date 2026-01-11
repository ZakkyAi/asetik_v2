-- Update admin password to '123'
-- Generated hash for password: 123
-- Run this SQL in phpMyAdmin or MySQL command line

UPDATE `users` 
SET `password_user` = '$2y$10$/76SAp7taRw77002xJMBE.1hE6rH4BiLnLs55rDuBIwVs..fQOXR2'
WHERE `username` = 'admin';

-- Verify the update
SELECT id, username, name, level, 
       SUBSTRING(password_user, 1, 20) as password_hash_preview 
FROM `users` 
WHERE `username` = 'admin';
