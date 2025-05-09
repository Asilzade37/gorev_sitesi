<?php
// Yeni admin şifresi
$password = "admin123";
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

echo "Yeni admin şifresi: " . $password . "\n";
echo "Hash'lenmiş hali: " . $hashedPassword . "\n";

// Veritabanı bağlantısı
$db = new PDO("mysql:host=localhost;dbname=gorev_sitesi;charset=utf8mb4", "root", "");

// Admin şifresini güncelle
$sql = "UPDATE users SET password = ? WHERE is_admin = 1";
$stmt = $db->prepare($sql);
$stmt->execute([$hashedPassword]);

echo "Admin şifresi güncellendi!"; 