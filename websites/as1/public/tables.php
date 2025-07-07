<?php
require 'db.php';

try {
    // Create user table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS user (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            role ENUM('admin', 'user') NOT NULL DEFAULT 'user'
        );
    ");
    echo "Table created successfully";
} 

catch (PDOException $e) {
    echo "Error creating table: " . $e->getMessage();
}
?>
