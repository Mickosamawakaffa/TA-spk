-- Setup Admin dan Super Admin di Database (tabel ADMINS)
-- Jalankan dengan MySQL direktly atau phpMyAdmin

-- Hapus admin lama jika ada
DELETE FROM admins WHERE email IN ('superadmin@gmail.com', 'admin@gmail.com');

-- Create Super Admin
-- Password: password (hash dengan bcrypt cost 12)
INSERT INTO admins (name, email, password, role, email_verified_at, created_at, updated_at) 
VALUES (
  'Super Admin',
  'superadmin@gmail.com',
  '$2y$12$gCSDNqZBTwTgPkNFKPxqKONnN6EUWZfLFJC3Z7K1Z7K1.pDkQZcJW',
  'super_admin',
  NOW(),
  NOW(),
  NOW()
);

-- Create Admin
-- Password: password 
INSERT INTO admins (name, email, password, role, email_verified_at, created_at, updated_at) 
VALUES (
  'Admin Bisnis',
  'admin@gmail.com',
  '$2y$12$gCSDNqZBTwTgPkNFKPxqKONnN6EUWZfLFJC3Z7K1Z7K1.pDkQZcJW',
  'admin',
  NOW(),
  NOW(),
  NOW()
);

-- Verifikasi
SELECT id, name, email, role FROM admins;
SELECT COUNT(*) as 'Total Admin' FROM admins;
