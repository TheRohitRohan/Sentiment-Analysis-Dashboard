<?php
require __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

try {
    // Connect to MySQL without database
    $pdo = new PDO(
        "mysql:host=" . $_ENV['DB_HOST'],
        $_ENV['DB_USER'],
        $_ENV['DB_PASS']
    );
    
    // Create database if not exists
    $pdo->exec("CREATE DATABASE IF NOT EXISTS " . $_ENV['DB_NAME']);
    $pdo->exec("USE " . $_ENV['DB_NAME']);
    
    // Create tables
    $sql = file_get_contents(__DIR__ . '/schema.sql');
    $pdo->exec($sql);
    
    echo "Database setup completed successfully!\n";
} catch (PDOException $e) {
    die("Database setup failed: " . $e->getMessage() . "\n");
}
