<?php
// db.php – shared database connection

$DB_HOST = 'localhost';
$DB_NAME = 'm_db';      // your real database name in phpMyAdmin
$DB_USER = 'root';          // XAMPP default user is usually root
$DB_PASS = '';              // XAMPP default root password is usually empty

try {
    $pdo = new PDO(
        "mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8mb4",
        $DB_USER,
        $DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}
