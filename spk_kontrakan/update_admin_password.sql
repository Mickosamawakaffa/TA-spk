-- Update admin passwords dengan hash bcrypt yang benar
-- Hash untuk password "password" dengan bcrypt cost 12

UPDATE admins SET 
  password = '$2y$12$HpGkKLEARVDXe0pWB1V1bOx3xPVF4uVIHT6qP/c4pzJPG.1kYFqRi'
WHERE email IN ('superadmin@gmail.com', 'admin@gmail.com');

-- Verifikasi
SELECT id, name, email, role, LENGTH(password) as pwd_length FROM admins 
WHERE email IN ('superadmin@gmail.com', 'admin@gmail.com');
