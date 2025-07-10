<?php
require 'db.php';

try {
    //user table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS user (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            role ENUM('admin', 'user') NOT NULL DEFAULT 'user'
        );
    ");

    //category table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS category (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL
        );
    ");

    //Auction table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS auction (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            description TEXT NOT NULL,
            categoryId INT NOT NULL,
            userId INT NOT NULL,
            endDate DATETIME NOT NULL,
            imagePath VARCHAR(255) NOT NULL,
            FOREIGN KEY (categoryId) REFERENCES category(id),
            FOREIGN KEY (userId) REFERENCES user(id)
        );
    ");

    //Review table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS review (
            id INT AUTO_INCREMENT PRIMARY KEY,
            reviewerId INT NOT NULL,
            revieweeId INT NOT NULL,
            reviewText TEXT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (reviewerId) REFERENCES user(id),
            FOREIGN KEY (revieweeId) REFERENCES user(id)
        );
    ");

    //Bid table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS bid (
            id INT AUTO_INCREMENT PRIMARY KEY,
            auctionId INT NOT NULL,
            userId INT NOT NULL,
            amount DECIMAL(10,2) NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (auctionId) REFERENCES auction(id),
            FOREIGN KEY (userId) REFERENCES user(id)
        );
    ");

    echo "Table created successfully";
} 

catch (PDOException $e) {
    echo "Error creating table: " . $e->getMessage();
}
?>
