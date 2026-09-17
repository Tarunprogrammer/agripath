<?php
// Database configuration for XAMPP
$host = 'localhost';
$db   = 'agripath'; // Change this to your actual database name
$user = 'root';        // Default XAMPP username
$pass = '';            // Default XAMPP password (empty)
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     $conn = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
     // If connection fails, show a user-friendly message
     die("Database connection failed. Please ensure your database 'agripath_db' exists in phpMyAdmin.");
}
?>